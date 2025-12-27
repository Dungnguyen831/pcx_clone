<?php
// app/models/admin/AdminProductModel.php
require_once 'app/config/database.php';

class AdminProductModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Lấy tất cả sản phẩm cho trang quản trị (kèm lọc và thông tin kho)
     */
    public function getAllProductsAdmin($search_id = null, $search_name = null)
    {
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

        $sql .= " ORDER BY p.product_id ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Thêm sản phẩm mới kèm khởi tạo kho hàng (Transaction)
     */
    public function addProduct($data)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "INSERT INTO products (category_id, brand_id, name, price, image, description, status) 
                    VALUES (:cat_id, :brand_id, :name, :price, :image, :desc, :status)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':cat_id'   => $data['category_id'],
                ':brand_id' => $data['brand_id'],
                ':name'     => $data['name'],
                ':price'    => $data['price'],
                ':image'    => $data['image'],
                ':desc'     => $data['description'],
                ':status'   => $data['status']
            ]);

            $productId = $this->conn->lastInsertId();

            $sqlInv = "INSERT INTO inventory (product_id, quantity) VALUES (?, ?)";
            $this->conn->prepare($sqlInv)->execute([$productId, $data['quantity']]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    /**
     * Cập nhật sản phẩm và số lượng kho (Transaction)
     */
    public function updateProduct($data)
    {
        try {
            $this->conn->beginTransaction();

            $sql = "UPDATE products SET 
                    category_id = :cat_id, brand_id = :brand_id, name = :name, 
                    price = :price, image = :image, description = :desc 
                    WHERE product_id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':cat_id'   => $data['category_id'],
                ':brand_id' => $data['brand_id'],
                ':name'     => $data['name'],
                ':price'    => $data['price'],
                ':image'    => $data['image'],
                ':desc'     => $data['description'],
                ':id'        => $data['id']
            ]);

            $sqlInv = "UPDATE inventory SET quantity = ? WHERE product_id = ?";
            $this->conn->prepare($sqlInv)->execute([$data['quantity'], $data['id']]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    /**
     * Xóa sản phẩm sạch sẽ khỏi cả 2 bảng (Transaction)
     */
    public function deleteProduct($id)
    {
        try {
            $this->conn->beginTransaction();

            $sqlInv = "DELETE FROM inventory WHERE product_id = ?";
            $this->conn->prepare($sqlInv)->execute([$id]);

            $sqlProd = "DELETE FROM products WHERE product_id = ?";
            $this->conn->prepare($sqlProd)->execute([$id]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function getProductById($id)
    {
        $sql = "SELECT p.*, i.quantity 
                FROM products p 
                LEFT JOIN inventory i ON p.product_id = i.product_id 
                WHERE p.product_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getCategories()
    {
        return $this->conn->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBrands()
    {
        return $this->conn->query("SELECT * FROM brands")->fetchAll(PDO::FETCH_ASSOC);
    }
}
