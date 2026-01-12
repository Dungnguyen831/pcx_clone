<?php
class RewardModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // --- 1. LẤY DANH SÁCH MÃ TỪ ADMIN ---
    public function getAllCoupons($user_id)
    {
        // Lấy mã đang kích hoạt, còn hạn, còn lượt dùng chung
        $sql = "SELECT * FROM coupons 
                WHERE status = 1 
                AND (end_date > NOW() OR end_date IS NULL)
                AND used_count < usage_limit
                ORDER BY points_cost ASC"; // Sắp xếp: Miễn phí lên đầu

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $coupons = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Duyệt qua từng mã để check xem User này đã từng dùng chưa
        foreach ($coupons as &$c) {
            $c['is_used_by_me'] = $this->checkUserUsed($user_id, $c['code']);
        }
        return $coupons;
    }

    // --- 2. LẤY LỊCH SỬ ĐÃ DÙNG (Để hiện ở Ví của tôi) ---
    public function getUsedHistory($user_id)
    {
        // Join bảng orders để lấy thông tin mã đã áp dụng thành công
        $sql = "SELECT c.*, o.created_at as used_at 
                FROM orders o
                JOIN coupons c ON o.coupon_code = c.code
                WHERE o.user_id = :uid 
                AND o.status != 4 -- 4 là trạng thái Hủy (đơn hủy ko tính)
                ORDER BY o.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':uid' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- 3. CHECK XEM USER ĐÃ DÙNG MÃ NÀY CHƯA ---
    private function checkUserUsed($user_id, $code)
    {
        $sql = "SELECT COUNT(*) FROM orders 
                WHERE user_id = :uid AND coupon_code = :code AND status != 4";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':uid' => $user_id, ':code' => $code]);
        return $stmt->fetchColumn() > 0;
    }

    // --- 4. XỬ LÝ ĐỔI MÃ / LẤY MÃ ---
    public function redeem($user_id, $coupon_id)
    {
        // A. Lấy thông tin mã
        $stmt = $this->conn->prepare("SELECT * FROM coupons WHERE coupon_id = :id");
        $stmt->execute([':id' => $coupon_id]);
        $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$coupon) return ['status' => false, 'msg' => 'Mã không tồn tại'];

        // B. Check: Đã dùng chưa?
        if ($this->checkUserUsed($user_id, $coupon['code'])) {
            return ['status' => false, 'msg' => 'Bạn đã sử dụng mã này rồi!'];
        }

        // C. Xử lý trừ điểm (Nếu mã tốn điểm)
        if ($coupon['points_cost'] > 0) {
            // Lấy điểm hiện tại
            $stmtPt = $this->conn->prepare("SELECT reward_points FROM customers WHERE user_id = :uid");
            $stmtPt->execute([':uid' => $user_id]);
            $currentPoints = $stmtPt->fetchColumn() ?: 0;

            if ($currentPoints < $coupon['points_cost']) {
                return ['status' => false, 'msg' => 'Bạn không đủ điểm!'];
            }

            // Trừ điểm
            try {
                $this->conn->beginTransaction();
                $newPoints = $currentPoints - $coupon['points_cost'];
                $this->conn->prepare("UPDATE customers SET reward_points = :pts WHERE user_id = :uid")
                    ->execute([':pts' => $newPoints, ':uid' => $user_id]);
                $this->conn->commit();
            } catch (Exception $e) {
                $this->conn->rollBack();
                return ['status' => false, 'msg' => 'Lỗi hệ thống'];
            }
        }

        // Trả về mã code để Controller hiển thị
        return [
            'status' => true,
            'msg' => 'Thành công!',
            'code' => $coupon['code'],
            'type' => $coupon['points_cost'] > 0 ? 'redeem' : 'free'
        ];
    }

    public function getCurrentPoints($user_id)
    {
        $stmt = $this->conn->prepare("SELECT reward_points FROM customers WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn() ?: 0;
    }
}
