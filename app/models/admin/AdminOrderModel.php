<?php
class AdminOrderModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getAllOrders($search_id = null, $search_name = null)
    {
        $sql = "SELECT * FROM orders WHERE 1=1";
        $params = [];
        if ($search_id) {
            $sql .= " AND order_id = :id";
            $params[':id'] = $search_id;
        }
        if ($search_name) {
            $sql .= " AND customer_name LIKE :name";
            $params[':name'] = "%$search_name%";
        }
        $sql .= " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderById($order_id)
    {
        $sql = "SELECT * FROM orders WHERE order_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$order_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getOrderDetails($order_id)
    {
        $sql = "SELECT od.*, p.name, p.image 
                FROM order_details od
                JOIN products p ON od.product_id = p.product_id
                WHERE od.order_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$order_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- HÀM NÂNG CẤP QUAN TRỌNG NHẤT ---
    public function updateStatusAndInventory($order_id, $new_status)
    {
        try {
            $this->conn->beginTransaction();

            // 1. Lấy trạng thái cũ của đơn hàng trước khi cập nhật
            $stmt = $this->conn->prepare("SELECT status FROM orders WHERE order_id = ? FOR UPDATE");
            $stmt->execute([$order_id]);
            $current_status = $stmt->fetchColumn();

            // 2. Cập nhật trạng thái mới cho đơn hàng
            $sql = "UPDATE orders SET status = ? WHERE order_id = ?";
            $this->conn->prepare($sql)->execute([$new_status, $order_id]);

            // 3. XỬ LÝ KHO (Dựa trên cấu trúc bảng inventory bạn gửi)

            // TH1: Từ Mới (0) -> Xác nhận (1): TRỪ KHO
            if ($current_status == 0 && $new_status == 1) {
                $details = $this->getOrderDetails($order_id);
                foreach ($details as $item) {
                    $sqlInv = "UPDATE inventory SET quantity = quantity - ? 
                               WHERE product_id = ? AND quantity >= ?";
                    $stmtInv = $this->conn->prepare($sqlInv);
                    $stmtInv->execute([$item['quantity'], $item['product_id'], $item['quantity']]);

                    if ($stmtInv->rowCount() == 0) {
                        throw new Exception("Sản phẩm ID " . $item['product_id'] . " không đủ hàng!");
                    }
                }
            }

            // TH2: Từ Đã xác nhận/Giao (1,2) -> Hủy (4): HOÀN KHO
            if (($current_status == 1 || $current_status == 2) && $new_status == 4) {
                $details = $this->getOrderDetails($order_id);
                foreach ($details as $item) {
                    $sqlInv = "UPDATE inventory SET quantity = quantity + ? WHERE product_id = ?";
                    $this->conn->prepare($sqlInv)->execute([$item['quantity'], $item['product_id']]);
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return $e->getMessage(); // Trả về lỗi nếu không đủ kho
        }
    }
}
