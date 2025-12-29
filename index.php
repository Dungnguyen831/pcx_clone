<?php
session_start(); // Khởi động Session (cho Giỏ hàng)

// 1. Nhúng file kết nối
require_once 'app/config/database.php';

// 2. Lấy yêu cầu từ URL (Ví dụ: index.php?controller=product&action=detail)
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// 3. Điều hướng (Routing đơn giản)
switch ($controller) {
    case 'home':
        require_once 'app/controllers/client/HomeController.php';
        $obj = new HomeController();
        break;

    case 'product':
        require_once 'app/controllers/client/ProductController.php';
         $obj = new ProductController();
        break;

    case 'admin-product': // Quản lý sản phẩm dành cho Admin
        require_once 'app/controllers/admin/AdminProductController.php';
        $obj = new AdminProductController();
        break;

    case 'admin-order':
        require_once 'app/controllers/admin/AdminOrderController.php';
        $obj = new AdminOrderController();
        break;

    case 'cart':
        require_once 'app/controllers/client/CartController.php';
        // $obj = new CartController();
        break;

    case 'admin':
        require_once 'app/controllers/admin/DashboardController.php';
        $obj = new DashboardController();
        break;

    case 'auth':
        require_once 'app/controllers/client/AuthController.php';
        $obj = new AuthController();
        break;

    case 'user':
        require_once 'app/controllers/admin/AdminUserController.php';
        $obj = new UserController();
        break;

    default:
        echo "404 - Không tìm thấy trang";
        exit();
}

// 4. Gọi hành động
if (method_exists($obj, $action)) {
    $obj->$action();
} else {
    echo "Hành động không tồn tại";
}
