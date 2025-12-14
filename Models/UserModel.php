<?php
require_once __DIR__ . '/BaseModel.php';

class UserModel extends BaseModel {
    protected $table = "customer";
    public function getAll() {
        $sql = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
