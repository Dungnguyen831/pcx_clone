<?php
// app/controllers/admin/DashboardController.php
require_once 'app/models/admin/DashboardModel.php';

class DashboardController {
    public function index() {
        // Kiểm tra đăng nhập Admin (Giữ nguyên logic cũ của bạn)
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        // --- LẤY DỮ LIỆU THỐNG KÊ ---
        $dashboardModel = new DashboardModel();
        
        $stats = [
            'new_orders' => $dashboardModel->countNewOrders(),
            'products'   => $dashboardModel->countProducts(),
            'customers'  => $dashboardModel->countCustomers(),
            'revenue'    => $dashboardModel->getMonthlyRevenue()
        ];

        // Lấy danh sách đơn hàng mới nhất
        $recent_orders = $dashboardModel->getRecentOrders();

        // Gửi dữ liệu sang View
        $page_title = "Tổng quan hệ thống";
        $controller = 'dashboard'; 
        $content_view = 'views/admin/dashboard/index.php';
        require_once 'views/admin/layouts/page.php';
    }
}
?>