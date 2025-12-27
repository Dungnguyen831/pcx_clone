<?php
// app/models/UserModel.php
require_once 'app/config/database.php';

class UserModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // --- Hàm checkLogin cũ của bạn giữ nguyên ---
    public function checkLogin($email, $password)
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $password === $user['password']) {
            return $user;
        }
        return false;
    }

    // --- THÊM MỚI: Kiểm tra email đã tồn tại chưa ---
    public function isEmailExists($email)
    {
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetchColumn() > 0;
    }

    public function isPhoneExists($phone)
    {
        $sql = "SELECT COUNT(*) FROM users WHERE phone = :phone";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':phone' => $phone]);
        return $stmt->fetchColumn() > 0;
    }

    // --- THÊM MỚI: Đăng ký user mới ---
    public function register($full_name, $email, $password, $phone)
    {
        $sql = "INSERT INTO users (full_name, email, password, phone, role) 
                VALUES (:full_name, :email, :password, :phone, 0)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':full_name' => $full_name,
            ':email'     => $email,
            ':password'  => $password, // Đang lưu text thuần để khớp với login của bạn
            ':phone'     => $phone
        ]);
    }
}
