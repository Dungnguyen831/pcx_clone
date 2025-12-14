<?php
require_once __DIR__ . '/BaseModel.php';

class ProductModel extends BaseModel {
    protected $table = "product";
    public function getAll() {
        $sql = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
