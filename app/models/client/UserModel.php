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

    // --- ĐĂNG NHẬP ---
    public function checkLogin($email, $password)
    {
        // Sử dụng đúng tên cột email
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Kiểm tra mật khẩu (đang so sánh chuỗi thuần theo logic của bạn)
        if ($user && $password === $user['password']) {
            return $user;
        }
        return false;
    }

    // --- KIỂM TRA TỒN TẠI EMAIL ---
    public function isEmailExists($email)
    {
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetchColumn() > 0;
    }

    // --- LẤY THÔNG TIN THEO ID ---
    public function getUserById($id) {
        $sql = "SELECT * FROM users WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    

    // --- ĐĂNG KÝ ---
    public function register($full_name, $email, $password, $phone)
    {
        // Khớp chính xác với cấu trúc: full_name, email, password, phone, role
        $sql = "INSERT INTO users (full_name, email, password, phone, role) 
                VALUES (:full_name, :email, :password, :phone, 0)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':full_name' => $full_name,
            ':email'     => $email,
            ':password'  => $password, 
            ':phone'     => $phone
        ]);
    }

    // --- CẬP NHẬT HỒ SƠ ---
    // Loại bỏ username và address vì database không có các cột này
    public function updateProfile($id, $full_name, $phone) {
        $sql = "UPDATE users SET full_name = ?, phone = ? WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$full_name, $phone, $id]);
    }

    // --- ĐỔI MẬT KHẨU ---
    public function updatePassword($id, $new_password) {
        $sql = "UPDATE users SET password = ? WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$new_password, $id]);
    }

   
    // --- LẤY DANH SÁCH KHÁCH HÀNG ---
    public function getAllCustomers() {
        $sql = "SELECT * FROM users WHERE role = 0 ORDER BY user_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function isPhoneExists($phone)
    {
        $sql = "SELECT COUNT(*) FROM users WHERE phone = :phone";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':phone' => $phone]);
        return $stmt->fetchColumn() > 0;
    }
    public function deleteUser($id) {
    
        $sqlCheck = "SELECT COUNT(*) FROM orders WHERE user_id = ? AND status NOT IN (3, 4)";
        $stmtCheck = $this->conn->prepare($sqlCheck);
        $stmtCheck->execute([$id]);
        $activeOrders = $stmtCheck->fetchColumn();

        // Nếu tìm thấy đơn hàng đang xử lý (> 0) thì trả về thông báo chặn
        if ($activeOrders > 0) {
            return 'has_active_orders'; 
        }

        // BƯỚC 2: Nếu không có đơn hàng vướng bận, tiến hành xóa
        $sql = "DELETE FROM users WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt->execute([$id])) {
            return true; // Xóa thành công
        } else {
            return false; // Lỗi SQL
        }
    }

}
