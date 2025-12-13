<?php
// app/controllers/client/HomeController.php
require_once 'app/config/database.php';

class HomeController {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function index() {
        // 1. Câu lệnh SQL "Thần thánh" kết hợp 3 bảng
        // Lấy thông tin sản phẩm, tên thương hiệu, và số lượng tồn kho
        $sql = "SELECT p.*, b.name as brand_name, i.quantity 
                FROM products p
                LEFT JOIN brands b ON p.brand_id = b.brand_id
                LEFT JOIN inventory i ON p.product_id = i.product_id
                WHERE p.status = 1 
                ORDER BY p.created_at DESC 
                LIMIT 8"; // Lấy 8 sản phẩm mới nhất

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 2. Gửi dữ liệu sang View
        require_once 'views/client/home/index.php';
    }
}
?>