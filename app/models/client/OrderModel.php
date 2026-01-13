<?php
class OrderModel
{
    private $db;

    public function __construct($pdo = null)
    {
        if ($pdo) {
            $this->db = $pdo;
        } else {
            // Fallback nếu không truyền pdo
            $database = new Database();
            $this->db = $database->getConnection();
        }
    }

    // Lấy danh sách đơn hàng của một người dùng
    public function getOrdersByUser($user_id, $status = null) {
        $sql = "SELECT * FROM orders WHERE user_id = ?";
        $params = [$user_id];
        if ($status !== null) {
        $sql .= " AND status = ?";
        $params[] = $status;
        }
        $sql .= " ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE order_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getOrderItems($order_id)
    {
        $sql = "SELECT od.*, p.name, p.image
                FROM order_details od
                JOIN products p ON od.product_id = p.product_id
                WHERE od.order_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$order_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- SỬA LẠI ĐỂ KHÔNG BỊ LỖI SQL & THÊM LOGIC MỚI ---
    public function createOrder($user_id, $data, $cart_items)
    {
        try {
            $this->db->beginTransaction();

            // 1. Insert bảng orders
            $sqlOrder = "INSERT INTO orders (
                            user_id, customer_name, customer_phone, shipping_address, note, 
                            total_money, discount_amount, final_money, coupon_code, 
                            payment_method, status, created_at
                        ) VALUES (
                            :uid, :name, :phone, :address, :note, 
                            :total, :discount, :final, :code, :method, 
                            0, NOW()
                        )";

            $stmtOrder = $this->db->prepare($sqlOrder);
            $stmtOrder->execute([
                ':uid'      => $user_id,
                ':name'     => $data['customer_name'],
                ':phone'    => $data['customer_phone'],
                ':address'  => $data['shipping_address'],
                ':note'     => $data['note'],
                ':total'    => $data['total_money'],
                ':discount' => $data['discount_amount'],
                ':final'    => $data['final_money'],
                ':code'     => $data['coupon_code'],
                ':method'   => $data['payment_method'] ?? 'COD'
            ]);

            $order_id = $this->db->lastInsertId();

            // 2. Insert chi tiết & Trừ kho
            // QUAN TRỌNG: Đã bỏ cột total_price ra khỏi câu lệnh INSERT để tránh lỗi
            $sqlDetail = "INSERT INTO order_details (order_id, product_id, price, quantity) 
                          VALUES (:oid, :pid, :price, :qty)";
            $stmtDetail = $this->db->prepare($sqlDetail);

            $sqlInv = "UPDATE inventory SET quantity = quantity - ? WHERE product_id = ?";
            $stmtInv = $this->db->prepare($sqlInv);

            foreach ($cart_items as $item) {
                // Làm sạch giá tiền
                $price_clean = preg_replace('/[^0-9]/', '', $item['price']);

                $stmtDetail->execute([
                    ':oid'    => $order_id,
                    ':pid'    => $item['product_id'],
                    ':price'  => $price_clean,
                    ':qty'    => $item['quantity']
                ]);

                // Trừ kho
                $stmtInv->execute([$item['quantity'], $item['product_id']]);
            }

            // 3. Cập nhật Coupon (Nếu có dùng)
            if (!empty($data['coupon_code'])) {
                $sqlCoupon = "UPDATE coupons SET used_count = used_count + 1 WHERE code = ?";
                $this->db->prepare($sqlCoupon)->execute([$data['coupon_code']]);
            }

            // 4. Xóa giỏ hàng
            $sqlClear = "DELETE FROM carts WHERE user_id = ?";
            $this->db->prepare($sqlClear)->execute([$user_id]);

            $this->db->commit();
            return $order_id;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // // --- SỬA LẠI ĐỂ HOÀN KHO KHI HỦY ---
    // public function cancelOrder($order_id)
    // {
    //     try {
    //         $this->db->beginTransaction();

    //         $order = $this->getOrderById($order_id);
    //         $items = $this->getOrderItems($order_id);

    //         if (!$order) throw new Exception("Order not found");

    //         // Cập nhật trạng thái Hủy (4)
    //         $sqlUpdate = "UPDATE orders SET status = 4 WHERE order_id = ?";
    //         $this->db->prepare($sqlUpdate)->execute([$order_id]);

    //         // BỔ SUNG: Hoàn lại kho
    //         $sqlRestoreInv = "UPDATE inventory SET quantity = quantity + ? WHERE product_id = ?";
    //         $stmtRestore = $this->db->prepare($sqlRestoreInv);
    //         foreach ($items as $item) {
    //             $stmtRestore->execute([$item['quantity'], $item['product_id']]);
    //         }

    //         // BỔ SUNG: Hoàn lại mã giảm giá
    //         if (!empty($order['coupon_code'])) {
    //             $sqlRestoreCoupon = "UPDATE coupons SET used_count = used_count - 1 WHERE code = ? AND used_count > 0";
    //             $this->db->prepare($sqlRestoreCoupon)->execute([$order['coupon_code']]);
    //         }

    //         $this->db->commit();
    //         return true;
    //     } catch (Exception $e) {
    //         $this->db->rollBack();
    //         return false;
    //     }
    // }

    // Hủy đơn (Trạng thái 4) + Hoàn lại số lượng vào kho
    public function cancelOrder($order_id) {
        // 1. Lấy thông tin đơn hàng để xem nó dùng mã gì
        $sqlGet = "SELECT coupon_code FROM orders WHERE order_id = :oid";
        $stmtGet = $this->db->prepare($sqlGet);
        $stmtGet->execute([':oid' => $order_id]);
        $order = $stmtGet->fetch(PDO::FETCH_ASSOC);

        // 2. Cập nhật trạng thái đơn hàng thành Hủy 
        $sqlUpdate = "UPDATE orders SET status = 4 WHERE order_id = :oid";
        $this->db->prepare($sqlUpdate)->execute([':oid' => $order_id]);

        // 3. LOGIC HOÀN MÃ: Nếu đơn có dùng mã, hãy cộng lại lượt dùng
        if (!empty($order['coupon_code'])) {
            // Cộng lại 1 lượt dùng vào bảng coupons
            // usage_limit: Số lượng mã được phép dùng
            // used_count: Số lượng đã dùng thực tế (nếu bạn có cột này)
            
            // Cách 1: Nếu bạn trừ trực tiếp vào usage_limit lúc đặt hàng
            $sqlRestore = "UPDATE coupons SET usage_limit = usage_limit + 1 WHERE code = :code";
            
            // Cách 2: Nếu bạn dùng cột used_count để đếm
            // $sqlRestore = "UPDATE coupons SET used_count = used_count - 1 WHERE code = :code";

            $this->db->prepare($sqlRestore)->execute([':code' => $order['coupon_code']]);
        }
    }

    //Nhận
    public function updateStatus($orderId, $status)
    {
        $sql = "UPDATE orders SET status = :status WHERE order_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $orderId);
        return $stmt->execute();
    }
}
