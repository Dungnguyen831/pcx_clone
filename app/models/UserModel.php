<?php
// app/models/UserModel.php
require_once 'app/config/database.php';

class UserModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Hàm kiểm tra đăng nhập
    public function checkLogin($email, $password) {
        // 1. Tìm user theo email
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. Kiểm tra mật khẩu
        if ($user) {
            // Lưu ý: Vì dữ liệu mẫu đang là '123456' (chưa mã hóa) nên ta so sánh trực tiếp.
            // Trong thực tế, bạn nên dùng: if (password_verify($password, $user['password']))
            if ($password === $user['password']) {
                return $user; // Trả về thông tin user nếu đúng
            }
        }
        return false; // Sai email hoặc password
    }
}
?>