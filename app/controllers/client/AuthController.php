<?php
// app/controllers/client/AuthController.php
require_once 'app/models/UserModel.php';

class AuthController
{

    // 1. Hiển thị form đăng nhập
    public function login()
    {
        require_once 'views/client/auth/login.php';
    }

    // 2. Xử lý khi bấm nút "Đăng nhập"
    public function processLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $userModel = new UserModel();
            $user = $userModel->checkLogin($email, $password);

            if ($user) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] == 1) {
                    header("Location: index.php?controller=admin");
                } else {
                    header("Location: index.php");
                }
                exit();
            } else {
                $error = "Email hoặc mật khẩu không đúng!";
                require_once 'views/client/auth/login.php';
            }
        }
    }

    // ========================================================
    // 3. THÊM MỚI: Hiển thị form đăng ký
    // ========================================================
    public function register()
    {
        require_once 'views/client/auth/register.php';
    }

    // ========================================================
    // 4. THÊM MỚI: Xử lý khi bấm nút "Đăng ký"
    // ========================================================
    public function processRegister()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $full_name = trim($_POST['full_name']);
            $email     = trim($_POST['email']);
            $phone     = trim($_POST['phone']);
            $password  = $_POST['password'];

            $userModel = new UserModel();

            // Kiểm tra trùng Email hoặc Số điện thoại
            $isEmailUsed = $userModel->isEmailExists($email);
            // Giả sử bạn có thêm hàm kiểm tra số điện thoại trong UserModel
            // $isPhoneUsed = $userModel->isPhoneExists($phone); 

            if ($isEmailUsed) {
                $error = "Email này đã được sử dụng!";
                // Lưu dữ liệu đã nhập vào một mảng để truyền lại View
                $old_data = $_POST;
                require_once 'views/client/auth/register.php';
                return;
            }

            $isPhoneUsed = $userModel->isPhoneExists($phone);
            if ($isPhoneUsed) {
                $error = "Số điện thoại này đã được sử dụng!";
                // Lưu dữ liệu đã nhập vào một mảng để truyền lại View
                $old_data = $_POST;
                require_once 'views/client/auth/register.php';
                return;
            }

            $isSuccess = $userModel->register($full_name, $email, $password, $phone);

            if ($isSuccess) {
                header("Location: index.php?controller=auth&action=login&status=success");
                exit();
            } else {
                $error = "Có lỗi xảy ra, vui lòng thử lại sau!";
                $old_data = $_POST;
                require_once 'views/client/auth/register.php';
            }
        }
    }

    // 5. Đăng xuất
    public function logout()
    {
        session_destroy();
        header("Location: index.php");
        exit();
    }

    // 6. Trang cá nhân (Profile)
    public function profile()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }
        echo "Đây là trang cá nhân của: " . $_SESSION['full_name'];
        echo '<br><a href="index.php?controller=auth&action=logout">Đăng xuất</a>';
    }
}
