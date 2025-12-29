<?php
class OrderModel {
     private $db;

    // public function __construct($pdo) {
    //     $this->db = $pdo;
    // }
    public function __construct() {
        // Thay vì nhận biến từ ngoài, ta tự khởi tạo kết nối ở đây
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getOrdersByUser($user_id) {
        $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thông tin đơn
    // app/models/OrderModel.php

// Lấy thông tin đơn hàng
public function getOrderById($id) {
    $sql = "SELECT * FROM orders WHERE order_id = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

    // app/models/OrderModel.php

// Lấy danh sách sản phẩm (Phải dùng INNER JOIN để lấy tên và ảnh)
public function getOrderItems($order_id) {
    $sql = "SELECT od.*, p.name, p.image 
            FROM order_details od
            INNER JOIN products p ON od.product_id = p.product_id 
            WHERE od.order_id = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$order_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    // Hủy đơn + hoàn kho
    public function cancelOrder($id) {
        $this->db->beginTransaction();

        // Cập nhật trạng thái
        $this->db->prepare(
            "UPDATE orders SET status=4 WHERE order_id=? AND status=0"
        )->execute([$order_id]);

        // Hoàn kho
        $items = $this->getOrderItems($order_id);
        foreach ($items as $item) {
            $this->db->prepare(
                "UPDATE inventory 
                 SET quantity = quantity + ?
                 WHERE product_id = ?"
            )->execute([$item['quantity'], $item['product_id']]);
        }

        $this->db->commit();
    }
     public function createOrder($user_id, $data, $cart_items) {
    try {
        $this->db->beginTransaction();

        // 1. Lưu vào bảng orders (Dựa theo ảnh image_ffa757.png)
        // Lưu ý: Không chèn discount_amount nếu không có để nhận mặc định là 0
        $sqlOrder = "INSERT INTO orders (user_id, customer_name, customer_phone, shipping_address, note, total_money, final_money, payment_method, status, created_at) 
                     VALUES (:uid, :name, :phone, :address, :note, :total, :final, :method, 0, NOW())";
        
        $stmtOrder = $this->db->prepare($sqlOrder);
        $stmtOrder->execute([
            ':uid'     => $user_id,
            ':name'    => $data['customer_name'],
            ':phone'   => $data['customer_phone'],
            ':address' => $data['shipping_address'],
            ':note'    => $data['note'] ?? null,
            ':total'   => $data['total_money'], // Đảm bảo Controller truyền đúng tên này
            ':final'   => $data['total_money'], // Nếu không có giảm giá thì final = total
            ':method'  => $data['payment_method'] ?? 'COD'
        ]);
        
        $order_id = $this->db->lastInsertId();

        // 2. Lưu vào bảng order_details (Dựa theo ảnh image_ffaa9f.png)
        // CHÚ Ý: Bỏ cột total_price vì nó là cột TỰ ĐỘNG (GENERATED)
        $sqlDetail = "INSERT INTO order_details (order_id, product_id, price, quantity) 
                      VALUES (:oid, :pid, :price, :qty)";
        $stmtDetail = $this->db->prepare($sqlDetail);

        foreach ($cart_items as $item) {
            $stmtDetail->execute([
                ':oid'   => $order_id,
                ':pid'   => $item['product_id'],
                ':price' => $item['price'],
                ':qty'   => $item['quantity']
            ]);

            // 3. Trừ kho (Bảng inventory)
            $sqlInv = "UPDATE inventory SET quantity = quantity - ? WHERE product_id = ?";
            $this->db->prepare($sqlInv)->execute([$item['quantity'], $item['product_id']]);
        }

        // 4. Xóa giỏ hàng sau khi đặt thành công
        $sqlClear = "DELETE FROM carts WHERE user_id = ?";
        $this->db->prepare($sqlClear)->execute([$user_id]);

        $this->db->commit();
        return $order_id;

    } catch (Exception $e) {
        $this->db->rollBack();
        // Ghi log lỗi để kiểm tra nếu cần
        error_log("Lỗi CreateOrder: " . $e->getMessage());
        return false;
    }
}
}
