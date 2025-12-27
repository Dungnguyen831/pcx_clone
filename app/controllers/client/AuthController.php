<?php
// app/controllers/client/AuthController.php
require_once 'app/models/UserModel.php';

class AuthController {
    
    // 1. Hiển thị form đăng nhập
    public function login() {
        require_once 'views/client/auth/login.php';
    }

    // 2. Xử lý khi bấm nút "Đăng nhập"
    public function processLogin() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $userModel = new UserModel();
            $user = $userModel->checkLogin($email, $password);

            if ($user) {
                // A. Đăng nhập thành công -> Lưu Session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role']; // Quan trọng: 1 là Admin, 0 là Khách

                // B. Phân quyền chuyển hướng
                if ($user['role'] == 1) {
                    // Là Admin -> Vào trang quản trị
                    header("Location: index.php?controller=admin");
                } else {
                    // Là Khách -> Về trang chủ
                    header("Location: index.php");
                }
                exit();
            } else {
                // C. Đăng nhập thất bại
                $error = "Email hoặc mật khẩu không đúng!";
                require_once 'views/client/auth/login.php';
            }
        }
    }

    // 3. Đăng xuất
    public function logout() {
        session_destroy(); // Xóa sạch Session
        header("Location: index.php"); // Về trang chủ
        exit();
    }
    
    // 4. Trang cá nhân (Profile) - Làm thêm cho đủ bộ
    public function profile() {
        // Nếu chưa đăng nhập thì đá về login
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }
        echo "Đây là trang cá nhân của: " . $_SESSION['full_name'];
        echo '<br><a href="index.php?controller=auth&action=logout">Đăng xuất</a>';
    }
}
?>