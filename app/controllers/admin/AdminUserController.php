<?php
require_once 'app/models/client/UserModel.php';

class UserController {
    
    // Hiển thị danh sách
    public function index() {
        // Kiểm tra admin (Có thể viết hàm check chung để tái sử dụng)
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        $userModel = new UserModel();
        $customers = $userModel->getAllCustomers();

        $page_title = "Quản lý khách hàng";
        $controller = 'user'; // Để active menu sidebar
        
        $content_view = 'views/admin/user/index.php';
        require_once 'views/admin/layouts/page.php';
    }

    // Xóa khách hàng
    public function delete() {
        // Check Admin...
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) exit();

        if (isset($_GET['id'])) {
            $userModel = new UserModel();
            $result = $userModel->deleteUser($_GET['id']);

            if ($result) {
                // Xóa thành công
                header("Location: index.php?controller=user&action=index");
            } else {
                // Xóa thất bại (Thường do dính đơn hàng)
                echo "<script>alert('Không thể xóa khách hàng này vì họ đã có dữ liệu đơn hàng!'); window.location.href='index.php?controller=user';</script>";
            }
        }
    }
}
?>