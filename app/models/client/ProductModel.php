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

    // Hàm lấy danh sách sản phẩm cho trang chủ 
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

    public function getAllProducts($cat_id = null, $keyword = null) {
        // Câu lệnh SQL cơ bản (Luôn nối các bảng để lấy đủ thông tin)
        $sql = "SELECT p.*, b.name as brand_name, i.quantity 
                FROM products p
                LEFT JOIN brands b ON p.brand_id = b.brand_id
                LEFT JOIN inventory i ON p.product_id = i.product_id
                INNER JOIN categories c ON p.category_id = c.category_id
                WHERE p.status = 1 AND c.status = 1"; 

        // Nếu có lọc theo danh mục -> Nối thêm điều kiện AND
        if ($cat_id) {
            $sql .= " AND p.category_id = :cat_id";
        }

        // Nếu có tìm kiếm -> Nối thêm điều kiện AND LIKE
        if ($keyword) {
            $sql .= " AND p.name LIKE :keyword";
        }

        // Sắp xếp giảm dần theo ngày tạo
        $sql .= " ORDER BY p.created_at DESC";

        $stmt = $this->conn->prepare($sql);

        // Bind giá trị vào tham số (nếu có)
        if ($cat_id) {
            $stmt->bindValue(':cat_id', $cat_id, PDO::PARAM_INT);
        }
        if ($keyword) {
            // Thêm dấu % để tìm kiếm tương đối
            $stmt->bindValue(':keyword', "%$keyword%");
        }

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
        $sql = "SELECT * FROM categories Where status = 1" ;
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy sản phẩm theo loại
    public function getProductsByCategory($cat_id) {
        // Thêm b.name as brand_name và LEFT JOIN brands
        $sql = "SELECT p.*, b.name as brand_name, i.quantity 
                FROM products p 
                LEFT JOIN brands b ON p.brand_id = b.brand_id 
                LEFT JOIN inventory i ON p.product_id = i.product_id
                WHERE p.category_id = :cat_id AND p.status = 1";
                
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':cat_id', $cat_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
