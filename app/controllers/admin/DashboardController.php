<?php
class DashboardController
{
    public function index()
    {
        // Logic kiểm tra: Nếu chưa đăng nhập hoặc không phải Admin thì đuổi về
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        // Sau này sẽ gọi Model để lấy số liệu thống kê thật
        // $stats = $dashboardModel->getStats();

        $page_title = "Tổng quan hệ thống";
        $controller = 'dashboard'; // Để active menu sidebar

        // 2. Định nghĩa file nội dung con sẽ nằm ở giữa

        $content_view = 'views/admin/dashboard/index.php';

        // 3. Gọi Master Layout
        require_once 'views/admin/layouts/page.php';
    }
}
