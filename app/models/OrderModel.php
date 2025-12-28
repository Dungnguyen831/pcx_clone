<?php
class OrderModel {
     private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function getOrdersByUser($user_id) {
        $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thông tin đơn
    public function getOrderById($id) {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE order_id=?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Chi tiết đơn
    public function getOrderItems($order_id) {
        $sql = "SELECT od.*, p.name, p.image
                FROM order_details od
                JOIN products p ON od.product_id = p.product_id
                WHERE od.order_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$order_id]);
        return $stmt->fetchAll();
    }

    // Hủy đơn + hoàn kho
    public function cancelOrder($order_id) {
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
}
