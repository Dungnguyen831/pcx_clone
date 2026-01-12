<?php
class Database
{
    private $conn;
    private $host = "localhost";
    private $db_name = "pcx_db";
    private $username = "root";
    private $password = "";

    public function getConnection()
    {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Lỗi kết nối: " . $exception->getMessage();
        }
        return $this->conn;
    }
    public function fetchAll($sql) {
        $db = $this->getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function execute($sql) {
        $db = $this->getConnection();
        $stmt = $db->prepare($sql);
        return $stmt->execute();
    }
    public function insert($sql) {
        $db = $this->getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        return $db->lastInsertId();
    }
}
