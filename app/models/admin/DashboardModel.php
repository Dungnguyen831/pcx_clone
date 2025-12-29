<?php
// app/models/DashboardModel.php
require_once 'app/config/database.php';

class DashboardModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // 1. Đếm đơn hàng mới (Status = 0)
    public function countNewOrders() {
        $sql = "SELECT COUNT(*) as total FROM orders WHERE status = 0";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // 2. Đếm tổng sản phẩm
    public function countProducts() {
        $sql = "SELECT COUNT(*) as total FROM products";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // 3. Đếm tổng khách hàng (Role = 0)
    public function countCustomers() {
        $sql = "SELECT COUNT(*) as total FROM users WHERE role = 0";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // 4. Tính doanh thu tháng này (Status = 3: Hoàn thành)
    public function getMonthlyRevenue() {
        // Chỉ tính đơn Hoàn thành (3) và trong tháng hiện tại
        $sql = "SELECT SUM(final_money) as total 
                FROM orders 
                WHERE status = 3 
                AND MONTH(created_at) = MONTH(CURRENT_DATE()) 
                AND YEAR(created_at) = YEAR(CURRENT_DATE())";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ? $result['total'] : 0;
    }

    // 5. Lấy 5 đơn hàng mới nhất để hiện bảng
    public function getRecentOrders() {
        $sql = "SELECT * FROM orders ORDER BY created_at DESC LIMIT 5";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>