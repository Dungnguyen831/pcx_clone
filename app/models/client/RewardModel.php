<?php
class RewardModel
{
    private $conn;
    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }
    //Thay đổi hàm lấy danh sách mã giảm giá để kèm trạng thái đã dùng/chưa dùng của user
    // --- LOGIC TỰ ĐỘNG QUÉT ĐIỂM TỪ ĐƠN HÀNG HOÀN THÀNH ---
    //lấy những đơn hàng có trạng thái là hoàn thành nhưng chưa được tính điểm
    //tính điểm cập nhật vào bảng customers
    //đánh dấu đơn hàng đã được tính điểm vào bảng orders
    public function updatePointsFromOrders($user_id)
    {
        // --- PHẦN 1: TỰ ĐỘNG KHÓA VÀ HOÀN MÃ VOUCHER ---

        // A. KHÓA MÃ (Chuyển is_used = 1): Áp dụng cho đơn hàng Đang xử lý/Giao (status != 4)
        $sqlLock = "UPDATE user_coupons uc 
                JOIN coupons c ON uc.coupon_id = c.coupon_id
                SET uc.is_used = 1 
                WHERE uc.user_id = :uid 
                AND uc.is_used = 0 
                AND c.code IN (
                    SELECT coupon_code FROM orders 
                    WHERE user_id = :uid AND status != 4 AND coupon_code IS NOT NULL
                )";
        $this->conn->prepare($sqlLock)->execute([':uid' => $user_id]);

        // B. HOÀN MÃ (Chuyển is_used = 0): Áp dụng cho đơn hàng Đã hủy (status = 4)
        // Giúp khách hàng lấy lại mã để dùng cho đơn khác nếu đơn cũ bị hủy
        $sqlUnlock = "UPDATE user_coupons uc 
                  JOIN coupons c ON uc.coupon_id = c.coupon_id
                  SET uc.is_used = 0 
                  WHERE uc.user_id = :uid 
                  AND uc.is_used = 1 
                  AND c.code IN (
                      SELECT coupon_code FROM orders 
                      WHERE user_id = :uid AND status = 4 AND coupon_code IS NOT NULL
                  )";
        $this->conn->prepare($sqlUnlock)->execute([':uid' => $user_id]);


        // --- PHẦN 2: LOGIC TÍNH ĐIỂM THƯỞNG (GIỮ NGUYÊN) ---
        $sqlPoints = "SELECT order_id, final_money FROM orders 
                  WHERE user_id = :uid AND status = 3 AND is_points_calculated = 0";
        $stmt = $this->conn->prepare($sqlPoints);
        $stmt->execute([':uid' => $user_id]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($orders) {
            foreach ($orders as $order) {
                try {
                    $this->conn->beginTransaction();
                    // 100.000đ = 1 điểm
                    $pointsToEarn = floor($order['final_money'] / 100000);

                    if ($pointsToEarn > 0) {
                        $this->conn->prepare("UPDATE customers SET reward_points = reward_points + ? WHERE user_id = ?")
                            ->execute([$pointsToEarn, $user_id]);
                    }

                    $this->conn->prepare("UPDATE orders SET is_points_calculated = 1 WHERE order_id = ?")
                        ->execute([$order['order_id']]);

                    $this->conn->commit();
                } catch (Exception $e) {
                    $this->conn->rollBack();
                }
            }
        }
    }

    public function getAllCoupons($user_id)
    {
        $sql = "SELECT * FROM coupons WHERE status = 1 AND (end_date > NOW() OR end_date IS NULL) AND used_count < usage_limit ORDER BY points_cost ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $coupons = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($coupons as &$c) {
            $usedInOrder = $this->checkUserUsed($user_id, $c['code']);
            $inWallet = $this->checkInWallet($user_id, $c['coupon_id']);
            $c['is_used_by_me'] = ($usedInOrder || $inWallet);
        }
        return $coupons;
    }

    public function getOwnedCoupons($user_id)
    {
        // Trước khi lấy danh sách, gọi hàm quét để cập nhật trạng thái is_used mới nhất
        $this->updatePointsFromOrders($user_id);

        // Bây giờ chỉ cần lấy những mã có is_used = 0
        // Vì những mã đang "treo" ở đơn hàng (status != 4) đã bị hàm trên chuyển thành is_used = 1 rồi
        $sql = "SELECT c.*, uc.created_at as owned_at 
            FROM user_coupons uc
            JOIN coupons c ON uc.coupon_id = c.coupon_id
            WHERE uc.user_id = :uid 
            AND uc.is_used = 0 
            ORDER BY uc.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':uid' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function checkUserUsed($user_id, $code)
    {
        $sql = "SELECT COUNT(*) FROM orders WHERE user_id = :uid AND coupon_code = :code AND status != 4";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':uid' => $user_id, ':code' => $code]);
        return $stmt->fetchColumn() > 0;
    }

    private function checkInWallet($user_id, $coupon_id)
    {
        $sql = "SELECT COUNT(*) FROM user_coupons WHERE user_id = :uid AND coupon_id = :cid AND is_used = 0";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':uid' => $user_id, ':cid' => $coupon_id]);
        return $stmt->fetchColumn() > 0;
    }

    public function redeem($user_id, $coupon_id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM coupons WHERE coupon_id = ?");
        $stmt->execute([$coupon_id]);
        $coupon = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$coupon || $this->checkUserUsed($user_id, $coupon['code']) || $this->checkInWallet($user_id, $coupon_id)) return ['status' => false, 'msg' => 'Mã không khả dụng hoặc đã sở hữu'];
        try {
            $this->conn->beginTransaction();
            if ($coupon['points_cost'] > 0) {
                $stmtPt = $this->conn->prepare("SELECT reward_points FROM customers WHERE user_id = ? FOR UPDATE");
                $stmtPt->execute([$user_id]);
                $curr = $stmtPt->fetchColumn() ?: 0;
                if ($curr < $coupon['points_cost']) {
                    $this->conn->rollBack();
                    return ['status' => false, 'msg' => 'Bạn không đủ điểm!'];
                }
                $this->conn->prepare("UPDATE customers SET reward_points = reward_points - ? WHERE user_id = ?")->execute([$coupon['points_cost'], $user_id]);
            }
            $this->conn->prepare("INSERT INTO user_coupons (user_id, coupon_id, is_used) VALUES (?, ?, 0)")->execute([$user_id, $coupon_id]);
            $this->conn->commit();
            return ['status' => true, 'code' => $coupon['code'], 'type' => $coupon['points_cost'] > 0 ? 'redeem' : 'free'];
        } catch (Exception $e) {
            $this->conn->rollBack();
            return ['status' => false, 'msg' => 'Lỗi'];
        }
    }

    public function getCurrentPoints($user_id)
    {
        $stmt = $this->conn->prepare("SELECT reward_points FROM customers WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn() ?: 0;
    }
}
