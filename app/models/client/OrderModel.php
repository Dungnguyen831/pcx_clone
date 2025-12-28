<?php
class OrderModel {
    private $db;

    public function __construct() {
        // Khởi tạo kết nối giống các Model khác của bạn
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Lấy danh sách đơn hàng của một người dùng
    public function getOrdersByUser($user_id) {
        $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy thông tin tổng quát của 1 đơn hàng
    public function getOrderById($id) {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE order_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy chi tiết các sản phẩm trong đơn hàng (JOIN với bảng products để lấy ảnh và tên)
    public function getOrderItems($order_id) {
        $sql = "SELECT od.*, p.name, p.image
                FROM order_details od
                JOIN products p ON od.product_id = p.product_id
                WHERE od.order_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$order_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Logic tạo đơn hàng mới (Đã sửa theo cấu trúc bảng của bạn)
    public function createOrder($user_id, $data, $cart_items) {
        try {
            $this->db->beginTransaction();

            // 1. Lưu vào bảng orders
            $sqlOrder = "INSERT INTO orders (user_id, customer_name, customer_phone, shipping_address, note, total_money, final_money, status, created_at) 
                         VALUES (:uid, :name, :phone, :address, :note, :total, :final, 0, NOW())";
            $stmtOrder = $this->db->prepare($sqlOrder);
            $stmtOrder->execute([
                ':uid'      => $user_id,
                ':name'     => $data['customer_name'],
                ':phone'    => $data['customer_phone'],
                ':address'  => $data['shipping_address'],
                ':note'     => $data['note'],
                ':total'    => $data['total_money'],
                ':final'    => $data['total_money']
            ]);
            
            $order_id = $this->db->lastInsertId();

            // 2. Lưu vào bảng order_details và TRỪ KHO
            foreach ($cart_items as $item) {
                $total_price = $item['price'] * $item['quantity'];
                
                // Lưu chi tiết
                $sqlDetail = "INSERT INTO order_details (order_id, product_id, price, quantity, total_price) 
                              VALUES (:oid, :pid, :price, :qty, :tprice)";
                $stmtDetail = $this->db->prepare($sqlDetail);
                $stmtDetail->execute([
                    ':oid'    => $order_id,
                    ':pid'    => $item['product_id'],
                    ':price'  => $item['price'],
                    ':qty'    => $item['quantity'],
                    ':tprice' => $total_price
                ]);

                // Trừ kho (Bảng inventory)
                $sqlInv = "UPDATE inventory SET quantity = quantity - ? WHERE product_id = ?";
                $this->db->prepare($sqlInv)->execute([$item['quantity'], $item['product_id']]);
            }

            // 3. Xóa giỏ hàng database
            $sqlClear = "DELETE FROM carts WHERE user_id = ?";
            $this->db->prepare($sqlClear)->execute([$user_id]);

            $this->db->commit();
            return $order_id;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // Hủy đơn (Trạng thái 4) + Hoàn lại số lượng vào kho
    public function cancelOrder($order_id) {
        try {
            $this->db->beginTransaction();

            // Cập nhật trạng thái đơn hàng thành 4 (Đã hủy)
            // Chỉ cho phép hủy nếu đơn hàng đang ở trạng thái 0 (Chờ xử lý)
            $stmtUpdate = $this->db->prepare("UPDATE orders SET status = 4 WHERE order_id = ? AND status = 0");
            $stmtUpdate->execute([$order_id]);

            // Nếu cập nhật thành công (có dòng bị ảnh hưởng) thì mới hoàn kho
            if ($stmtUpdate->rowCount() > 0) {
                $items = $this->getOrderItems($order_id);
                foreach ($items as $item) {
                    $sqlInv = "UPDATE inventory SET quantity = quantity + ? WHERE product_id = ?";
                    $this->db->prepare($sqlInv)->execute([$item['quantity'], $item['product_id']]);
                }
                $this->db->commit();
                return true;
            }
            
            $this->db->rollBack();
            return false;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}