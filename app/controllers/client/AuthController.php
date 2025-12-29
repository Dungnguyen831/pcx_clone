<?php
// app/controllers/client/AuthController.php
require_once 'app/models/client/UserModel.php';

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
            $email = trim($_POST['email']);
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

    // 3. Hiển thị form đăng ký
    public function register()
    {
        require_once 'views/client/auth/register.php';
    }

    // 4. Xử lý khi bấm nút "Đăng ký"
    public function processRegister()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $full_name = trim($_POST['full_name']);
            $email     = trim($_POST['email']);
            $phone     = trim($_POST['phone']);
            $password  = $_POST['password'];

            $userModel = new UserModel();

            // Kiểm tra trùng Email
            if ($userModel->isEmailExists($email)) {
                $error = "Email này đã được sử dụng!";
                $old_data = $_POST;
                require_once 'views/client/auth/register.php';
                return;
            }

            // Kiểm tra trùng Số điện thoại
            if ($userModel->isPhoneExists($phone)) {
                $error = "Số điện thoại này đã được sử dụng!";
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
    public function profile() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }
    
        $userModel = new UserModel();
        $user_id = $_SESSION['user_id'];
    
        // Xử lý khi nhấn nút Cập nhật (Chỉ nhận full_name và phone khớp với DB)
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $full_name = trim($_POST['full_name']);
            $phone = trim($_POST['phone']);
    
            if ($userModel->updateProfile($user_id, $full_name, $phone)) {
                // Cập nhật lại tên hiển thị trong Session để header đổi ngay lập tức
                $_SESSION['full_name'] = $full_name; 
                $message = "Cập nhật thông tin thành công!";
            } else {
                $error = "Có lỗi xảy ra, vui lòng thử lại.";
            }
        }
    
        $user = $userModel->getUserById($user_id);
        require_once 'views/client/profile/profile.php';
    }

    // 7. Đổi mật khẩu
    public function changePassword() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user_id = $_SESSION['user_id'];
            $old_pass = $_POST['old_password'];
            $new_pass = $_POST['new_password'];
            $confirm_pass = $_POST['confirm_password'];

            $userModel = new UserModel();
            $user = $userModel->getUserById($user_id);

            // Kiểm tra mật khẩu cũ (So sánh trực tiếp theo Model của bạn)
            if ($old_pass !== $user['password']) {
                $error = "Mật khẩu cũ không chính xác!";
            } elseif ($new_pass !== $confirm_pass) {
                $error = "Xác nhận mật khẩu mới không khớp!";
            } else {
                if ($userModel->updatePassword($user_id, $new_pass)) {
                    $success = "Đổi mật khẩu thành công!";
                } else {
                    $error = "Lỗi hệ thống, vui lòng thử lại.";
                }
            }
        }
        require_once 'views/client/profile/change_password.php';
    }
}
