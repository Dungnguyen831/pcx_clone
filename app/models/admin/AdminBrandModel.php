<?php
require_once 'app/config/database.php';

class AdminBrandModel
{
    private $conn;
    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getAll($search = null)
    {
        $sql = "SELECT * FROM brands WHERE 1=1";
        $params = [];
        if ($search) {
            $sql .= " AND name LIKE :search";
            $params[':search'] = "%$search%";
        }
        $sql .= " ORDER BY brand_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM brands WHERE brand_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function save($data)
    {
        if (isset($data['id']) && !empty($data['id'])) {
            // Cập nhật: khớp với cột name và logo_url
            $sql = "UPDATE brands SET name = :name, logo_url = :logo WHERE brand_id = :id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':name' => $data['name'],
                ':logo' => $data['logo_url'] ?? '',
                ':id' => $data['id']
            ]);
        } else {
            // Thêm mới
            $sql = "INSERT INTO brands (name, logo_url) VALUES (:name, :logo)";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':name' => $data['name'],
                ':logo' => $data['logo_url'] ?? ''
            ]);
        }
    }

    public function delete($id)
    {
        $check = $this->conn->prepare("SELECT COUNT(*) FROM products WHERE brand_id = ?");
        $check->execute([$id]);
        if ($check->fetchColumn() > 0) return false;

        $stmt = $this->conn->prepare("DELETE FROM brands WHERE brand_id = ?");
        return $stmt->execute([$id]);
    }
}
