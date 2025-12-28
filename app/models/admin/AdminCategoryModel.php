<?php
require_once 'app/config/database.php';

class AdminCategoryModel
{
    private $conn;
    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getAll($search = null)
    {
        $sql = "SELECT * FROM categories WHERE 1=1";
        $params = [];
        if ($search) {
            $sql .= " AND name LIKE :search";
            $params[':search'] = "%$search%";
        }
        $sql .= " ORDER BY category_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM categories WHERE category_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function save($data)
    {
        if (isset($data['id'])) {
            // Cập nhật
            $sql = "UPDATE categories SET name = :name, description = :desc, status = :status WHERE category_id = :id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':name' => $data['name'],
                ':desc' => $data['description'],
                ':status' => $data['status'],
                ':id' => $data['id']
            ]);
        } else {
            // Thêm mới
            $sql = "INSERT INTO categories (name, description, status) VALUES (:name, :desc, :status)";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':name' => $data['name'],
                ':desc' => $data['description'],
                ':status' => $data['status']
            ]);
        }
    }

    public function delete($id)
    {
        $check = $this->conn->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
        $check->execute([$id]);
        if ($check->fetchColumn() > 0) return false;

        $stmt = $this->conn->prepare("DELETE FROM categories WHERE category_id = ?");
        return $stmt->execute([$id]);
    }
}
