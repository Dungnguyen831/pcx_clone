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

    // --- THÊM MỚI: Lấy thông tin user theo ID ---
    public function getAllCustomers() {
        // Có thể Join thêm bảng orders để đếm xem khách này mua bao nhiêu đơn
        // Nhưng tạm thời SELECT đơn giản trước
        $sql = "SELECT * FROM users WHERE role = 0 ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById($id) {
        $sql = "SELECT * FROM users WHERE user_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteUser($id) {
        // Lưu ý: Nếu Database có khóa ngoại (Foreign Key) chặt chẽ, 
        // bạn sẽ không xóa được nếu khách này đã có đơn hàng.
        try {
            $sql = "DELETE FROM users WHERE user_id = :id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            // Trả về false nếu lỗi (ví dụ dính khóa ngoại)
            return false;
        }
    }
}
