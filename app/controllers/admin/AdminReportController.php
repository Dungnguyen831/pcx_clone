<?php
require_once 'app/models/admin/AdminReportModel.php';

class AdminReportController {
    public function index() {

        $reportModel = new AdminReportModel();

        $fromDate = isset($_GET['from']) ? $_GET['from'] : date('Y-m-d', strtotime('-30 days'));
        $toDate = isset($_GET['to']) ? $_GET['to'] : date('Y-m-d');

        $revenue = $reportModel->getRevenue($fromDate, $toDate);
        $importTotal = $reportModel->getImportCost($fromDate, $toDate);
        $cogs = $reportModel->getCOGS($fromDate, $toDate);
        $profit = $revenue - $cogs;

        $rawData = $reportModel->getChartData($fromDate, $toDate);
        
        $dbData = [];
        foreach ($rawData as $row) {
            $dbData[$row['date']] = (float)$row['revenue'];
        }

        $chartDataFinal = [];
        $currentDate = strtotime($fromDate);
        $endDate = strtotime($toDate);

        while ($currentDate <= $endDate) {
            $dateStr = date('Y-m-d', $currentDate);
            
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