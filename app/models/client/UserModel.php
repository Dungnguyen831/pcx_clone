<?php
require_once 'app/config/database.php';

class UserModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

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

    public function isEmailExists($email)
    {
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetchColumn() > 0;
    }

    public function getUserById($id) {
        $sql = "SELECT * FROM users WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    

    public function register($full_name, $email, $password, $phone)
    {
        try {
            $this->conn->beginTransaction();
            $sqlUser = "INSERT INTO users (full_name, email, password, phone, role, created_at) 
                        VALUES (:name, :email, :pass, :phone, 0, NOW())";
            
            $stmtUser = $this->conn->prepare($sqlUser);
            $stmtUser->execute([
                ':name'  => $full_name,
                ':email' => $email,
                ':pass'  => $password, 
                ':phone' => $phone
            ]);

            $userId = $this->conn->lastInsertId();

            $sqlCustomer = "INSERT INTO customers (user_id, reward_points) VALUES (:uid, 0)";
            $stmtCustomer = $this->conn->prepare($sqlCustomer);
            $stmtCustomer->execute([':uid' => $userId]);

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
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

   
    // --- 1. LẤY DANH SÁCH USER THEO ROLE (Thay thế hàm getAllCustomers cũ) ---
    public function getUsersByRole($role) {
        if ($role == 0) {
            // Nếu là Khách hàng (0): JOIN bảng customers để lấy điểm thưởng
            $sql = "SELECT u.*, c.reward_points 
                    FROM users u
                    LEFT JOIN customers c ON u.user_id = c.user_id 
                    WHERE u.role = 0 
                    ORDER BY u.user_id DESC";
        } else {
            // Nếu là Nhân viên (2) hoặc Admin (1): Chỉ lấy bảng users
            $sql = "SELECT * FROM users WHERE role = ? ORDER BY user_id DESC";
        }

        $stmt = $this->conn->prepare($sql);
        
        if ($role == 0) {
            $stmt->execute();
        } else {
            $stmt->execute([$role]);
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thêm tài khoản cho nhân viên thủ kho
    public function createUser($data) {
        $sql = "INSERT INTO users (full_name, email, password, phone, role, created_at) 
                VALUES (:name, :email, :pass, :phone, :role, NOW())";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':name'  => $data['full_name'],
            ':email' => $data['email'],
            
            ':pass'  => $data['password'], 
            
            ':phone' => $data['phone'],
            ':role'  => $data['role']
        ]);
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

        if ($activeOrders > 0) {
            return 'has_active_orders'; 
        }

        $sqlCheckImport = "SELECT COUNT(*) FROM imports WHERE user_id = ?";
        $stmtCheckImport = $this->conn->prepare($sqlCheckImport);
        $stmtCheckImport->execute([$id]);
        
        if ($stmtCheckImport->fetchColumn() > 0) {
            return 'has_imports'; 
        }

        $sql = "DELETE FROM users WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt->execute([$id])) {
            return true; 
        } else {
            return false; // Lỗi SQL
        }
    }
}
