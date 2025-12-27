<?php
// app/models/ProductModel.php
require_once 'app/config/database.php';

class ProductModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Hàm lấy danh sách sản phẩm cho trang chủ (Có kèm Tồn kho và Thương hiệu)
    public function getHomeProducts($limit = 8)
    {
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
    public function getAllProducts() {
        // 1. Giữ nguyên các phép JOIN để lấy tên thương hiệu (brand_name) và số lượng (quantity)
        // 2. Loại bỏ LIMIT để lấy toàn bộ danh sách
        $sql = "SELECT p.*, b.name as brand_name, i.quantity 
                FROM products p
                LEFT JOIN brands b ON p.brand_id = b.brand_id
                LEFT JOIN inventory i ON p.product_id = i.product_id
                WHERE p.status = 1 
                ORDER BY p.created_at DESC";
    
        // Sử dụng $this->conn (biến kết nối bạn đã khai báo trong __construct)
        $stmt = $this->conn->prepare($sql);
        
        // Thực thi câu lệnh ngay vì không còn tham số :limit
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getProductById($id) {
        $sql = "SELECT p.*, b.name as brand_name, i.quantity 
                FROM products p
                LEFT JOIN brands b ON p.brand_id = b.brand_id
                LEFT JOIN inventory i ON p.product_id = i.product_id
                WHERE p.product_id = :id";
    
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Lấy danh sách các loại sản phẩm (Chuột, Bàn phím...)
    public function getAllCategories() {
        $sql = "SELECT * FROM categories";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy sản phẩm theo loại
    public function getProductsByCategory($cat_id) {
        $sql = "SELECT p.*, i.quantity FROM products p 
                LEFT JOIN inventory i ON p.product_id = i.product_id
                WHERE p.category_id = :cat_id AND p.status = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':cat_id', $cat_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
