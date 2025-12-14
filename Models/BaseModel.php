<?php
require_once __DIR__ . '/Database.php';

class BaseModel {
    protected $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }
}
