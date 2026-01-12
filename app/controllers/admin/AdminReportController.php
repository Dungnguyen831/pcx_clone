<?php
require_once 'app/models/admin/AdminReportModel.php';

class AdminReportController {
    public function index() {
        require_once 'app/models/admin/AdminReportModel.php';
        $reportModel = new AdminReportModel();

        // 1. Lấy khoảng thời gian từ URL (hoặc mặc định 30 ngày gần nhất)
        $fromDate = isset($_GET['from']) ? $_GET['from'] : date('Y-m-d', strtotime('-30 days'));
        $toDate = isset($_GET['to']) ? $_GET['to'] : date('Y-m-d');

        // 2. Lấy các chỉ số tổng quan (Giữ nguyên)
        $revenue = $reportModel->getRevenue($fromDate, $toDate);
        $importTotal = $reportModel->getImportCost($fromDate, $toDate);
        $cogs = $reportModel->getCOGS($fromDate, $toDate);
        $profit = $revenue - $cogs;

        // 3. XỬ LÝ DỮ LIỆU BIỂU ĐỒ THÔNG MINH (Lấp đầy ngày trống)
        // 3a. Lấy dữ liệu thô từ DB (chỉ chứa những ngày có đơn hàng)
        $rawData = $reportModel->getChartData($fromDate, $toDate);
        
        // Chuyển đổi dữ liệu DB thành dạng Key-Value cho dễ tra cứu (Key là ngày, Value là tiền)
        $dbData = [];
        foreach ($rawData as $row) {
            $dbData[$row['date']] = (float)$row['revenue'];
        }

        // 3b. Tạo vòng lặp từ ngày bắt đầu đến ngày kết thúc
        $chartDataFinal = [];
        $currentDate = strtotime($fromDate);
        $endDate = strtotime($toDate);

        while ($currentDate <= $endDate) {
            $dateStr = date('Y-m-d', $currentDate);
            
            // Nếu ngày này có trong DB thì lấy doanh thu, không thì bằng 0
            $revenueVal = isset($dbData[$dateStr]) ? $dbData[$dateStr] : 0;
            
            $chartDataFinal[] = [
                'date' => date('d/m', $currentDate), // Format ngày hiển thị (vd: 15/01)
                'revenue' => $revenueVal
            ];

            // Tăng thêm 1 ngày
            $currentDate = strtotime('+1 day', $currentDate);
        }

        // 4. Gọi View
        $content_view = 'views/admin/report/index.php';
        $controller = 'report';
        $page_title = 'Báo cáo doanh thu';
        require_once 'views/admin/layouts/page.php';
    }
}