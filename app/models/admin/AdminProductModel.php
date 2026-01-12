<?php
require_once 'app/config/database.php';

class AdminProductModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Fix hàm lấy sản phẩm Admin: Sử dụng LEFT JOIN để luôn hiện sản phẩm dù kho lỗi
    public function getAllProductsAdmin($search_id = null, $search_name = null) {
        $sql = "SELECT p.*, c.name as category_name, i.quantity, b.name as brand_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.category_id
                LEFT JOIN inventory i ON p.product_id = i.product_id
                LEFT JOIN brands b ON p.brand_id = b.brand_id
                WHERE 1=1";
        $params = [];
        if (!empty($search_id)) {
            $sql .= " AND p.product_id = :search_id";
            $params[':search_id'] = $search_id;
        }
        if (!empty($search_name)) {
            $sql .= " AND p.name LIKE :search_name";
            $params[':search_name'] = "%$search_name%";
        }
        $sql .= " ORDER BY p.product_id DESC"; // Mới nhất lên đầu
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addProduct($data) {
        try {
            $this->conn->beginTransaction();

            // Câu lệnh SQL: Bỏ import_price, thêm created_at
            $sql = "INSERT INTO products (category_id, brand_id, name, price, image, description, technical_specs, status, created_at) 
                    VALUES (:cat_id, :brand_id, :name, :price, :image, :desc, :specs, :status, NOW())";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':cat_id'   => $data['category_id'],
                ':brand_id' => $data['brand_id'],
                ':name'     => $data['name'],
                ':price'    => $data['price'],
                ':image'    => $data['image'],
                ':desc'     => $data['description'],
                ':specs'    => $data['technical_specs'],
                ':status'   => 1 // Đảm bảo luôn bằng 1 để hiển thị ngay
            ]);

            $productId = $this->conn->lastInsertId();

            // Thêm vào kho (Inventory) - Bắt buộc phải thành công
            $stmtInv = $this->conn->prepare("INSERT INTO inventory (product_id, quantity) VALUES (?, ?)");
            $stmtInv->execute([$productId, $data['quantity']]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
    public function updateProduct($data) {
        try {
            $this->conn->beginTransaction();
            // Bổ sung import_price nếu bảng yêu cầu, nếu không dùng hãy để mặc định là 0
            $sql = "UPDATE products SET 
                    category_id = :cat_id, 
                    brand_id = :brand_id, 
                    name = :name, 
                    price = :price, 
                    import_price = :import_price,
                    image = :image, 
                    description = :desc, 
                    technical_specs = :specs 
                    WHERE product_id = :id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':cat_id'       => $data['category_id'],
                ':brand_id'     => $data['brand_id'],
                ':name'         => $data['name'],
                ':price'        => $data['price'],
                ':import_price' => $data['import_price'] ?? 0, // Sửa lỗi cột thiếu
                ':image'        => $data['image'],
                ':desc'         => $data['description'],
                ':specs'        => $data['technical_specs'],
                ':id'           => $data['id']
            ]);
    
            // Cập nhật kho
            $stmtInv = $this->conn->prepare("UPDATE inventory SET quantity = ? WHERE product_id = ?");
            $stmtInv->execute([$data['quantity'], $data['id']]);
    
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
    public function deleteProduct($id) {
        try {
            $this->conn->beginTransaction();
            // Xóa bảng con trước (inventory)
            $this->conn->prepare("DELETE FROM inventory WHERE product_id = ?")->execute([$id]);
            // Xóa bảng chính (products)
            $this->conn->prepare("DELETE FROM products WHERE product_id = ?")->execute([$id]);
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function getProductById($id) {
        $sql = "SELECT p.*, i.quantity FROM products p 
                LEFT JOIN inventory i ON p.product_id = i.product_id WHERE p.product_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getCategories() { return $this->conn->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC); }
    public function getBrands() { return $this->conn->query("SELECT * FROM brands")->fetchAll(PDO::FETCH_ASSOC); }
    
    public function isProductInProcessingOrders($id) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM order_details WHERE product_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn() > 0;
    }
}