<?php
// app/models/ProductModel.php
require_once 'app/config/database.php';

class ProductModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Hàm lấy danh sách sản phẩm cho trang chủ (Có kèm Tồn kho và Thương hiệu)
    public function getHomeProducts($limit = 8) {
        // SQL JOIN 3 bảng: products, brands, inventory
        $sql = "SELECT p.*, b.name as brand_name, i.quantity 
                FROM products p
                LEFT JOIN brands b ON p.brand_id = b.brand_id
                LEFT JOIN inventory i ON p.product_id = i.product_id
                WHERE p.status = 1 
                ORDER BY p.created_at DESC 
                LIMIT :limit";

        $stmt = $this->conn->prepare($sql);
        
        // Bind tham số để tránh SQL Injection
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>