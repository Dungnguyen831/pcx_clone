<?php
// require_once 'vendor/autoload.php';

// use PhpOffice\PhpSpreadsheet\Spreadsheet;
// use PhpOffice\PhpSpreadsheet\IOFactory;

class AdminOrderController
{
    private $orderModel;

    public function __construct()
    {
        require_once 'app/models/admin/AdminOrderModel.php';
        $this->orderModel = new AdminOrderModel();
    }

    public function index()
    {
        $search_id = $_GET['search_id'] ?? null;
        $search_name = $_GET['search_name'] ?? null;


        $action = $_GET['action'] ?? 'index';
        $status = null;
        
        switch ($action) {
            case 'pending':   $status = 0; break; 
            case 'pickup':    $status = 1; break; 
            case 'shipping':  $status = 2; break; 
            case 'completed': $status = 3; break; 
            case 'cancelled': $status = 4; break; 
            default:          $status = null; break; 
        }

        $orders = $this->orderModel->getAllOrders($search_id, $search_name, $status);

        $stats = ['total_orders' => count($orders), 'pending' => 0, 'revenue' => 0];
        $allOrdersForStats = $this->orderModel->getAllOrders();

        foreach ($allOrdersForStats as $o) {
            if ($o['status'] == 0) $stats['pending']++;
            if ($o['status'] == 3) $stats['revenue'] += $o['final_money'];
        }

        $page_title = "Quản lý đơn hàng";
        $controller = 'order';
        $content_view = 'views/admin/order/index.php';
        if (!isset($_GET['action'])) {
            $_GET['action'] = 'created_at_desc';
        }

        require_once 'views/admin/layouts/page.php';
    }

    public function pending() { $_GET['action'] = 'pending'; $this->index(); }
    public function pickup() { $_GET['action'] = 'pickup'; $this->index(); }
    public function shipping() { $_GET['action'] = 'shipping'; $this->index(); }
    public function completed() { $_GET['action'] = 'completed'; $this->index(); }
    public function cancelled() { $_GET['action'] = 'cancelled'; $this->index(); }

    public function detail()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: index.php?controller=admin-order");
            exit();
        }
        $order = $this->orderModel->getOrderById($id);
        if (!$order) {
            header("Location: index.php?controller=admin-order");
            exit();
        }
        $items = $this->orderModel->getOrderDetails($id);
        $page_title = "Chi tiết đơn hàng #" . $id;
        $controller = 'order';
        $content_view = 'views/admin/order/detail.php';
        require_once 'views/admin/layouts/page.php';
    }

    public function updateStatus()
    {
        $id = $_GET['id'] ?? null;
        $status = $_GET['status'] ?? null;
        // Lấy thông tin tab cần quay về sau khi xong
        $redirect_to = $_GET['redirect_to'] ?? 'index';

        if ($id !== null && $status !== null) {
            $result = $this->orderModel->updateStatusAndInventory($id, $status);

            if ($result === true) {
                // Chuyển hướng về tab tương ứng (ví dụ: pickup, shipping, cancelled)
                header("Location: index.php?controller=admin-order&action=" . $redirect_to . "&msg=updated");
            } else {
                header("Location: index.php?controller=admin-order&msg=error&detail=" . urlencode($result));
            }
            exit();
        }
    }

    public function exportExcel() 
    {
        $action_type = $_GET['action_type'] ?? 'index';
        $status = null;
        $typeName = "Tất cả";

        switch ($action_type) {
            case 'pending':   $status = 0; $typeName = "Chờ xác nhận"; break;
            case 'pickup':    $status = 1; $typeName = "Chờ lấy hàng"; break;
            case 'shipping':  $status = 2; $typeName = "Đang giao"; break;
            case 'completed': $status = 3; $typeName = "Đã giao"; break;
            case 'cancelled': $status = 4; $typeName = "Đã hủy"; break;
        }

        $orders = $this->orderModel->getAllOrders(null, null, $status);
        $filename = "DonHang_" . str_replace(' ', '_', $typeName) . "_" . date('d-m-Y') . ".xls";

        $style = "<style>
            .excel-table { font-family: 'Arial', sans-serif; border-collapse: collapse; width: 100%; }
            .excel-table th { background-color: #2ecc71; color: #ffffff; font-weight: bold; border: 0.5pt solid #000000; text-align: center; height: 35px; font-size: 12pt; }
            .excel-table td { border: 0.5pt solid #000000; padding: 8px; vertical-align: middle; font-size: 11pt; }
            .text-center { text-align: center; } .text-right { text-align: right; } .phone-format { mso-number-format:'\@'; }
            .title-doc { font-family: 'Arial'; font-size: 16pt; font-weight: bold; text-align: center; color: #2c3e50; }
        </style>";

        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");

        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        echo '<head><meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">' . $style . '</head><body>';
        echo '<div class="title-doc">DANH SÁCH ĐƠN HÀNG ' . mb_strtoupper($typeName, 'UTF-8') . '</div>';
        echo '<div style="text-align: center; margin-bottom: 20px;">Ngày xuất: ' . date('d/m/Y H:i') . '</div><br>';
        echo '<table class="excel-table" border="1"><thead><tr><th>ID</th><th>Khách hàng</th><th>Điện thoại</th><th>Tổng tiền</th><th>Ngày đặt</th><th>Trạng thái</th></tr></thead><tbody>';

        if (!empty($orders)) {
            foreach ($orders as $o) {
                echo '<tr><td class="text-center">' . $o['order_id'] . '</td><td>' . htmlspecialchars($o['customer_name']) . '</td><td class="text-center phone-format">' . $o['customer_phone'] . '</td><td class="text-right">' . number_format($o['final_money'], 0, ',', '.') . ' VNĐ</td><td class="text-center">' . date('d/m/Y H:i', strtotime($o['created_at'])) . '</td><td class="text-center">' . $this->getStatusName($o['status']) . '</td></tr>';
            }
        }
        echo '</tbody></table></body></html>';
        exit;
    }

    private function getStatusName($status) {
        switch ($status) {
            case 0: return 'Chờ xác nhận';
            case 1: return 'Đã xác nhận';
            case 2: return 'Đang giao hàng';
            case 3: return 'Hoàn thành';
            case 4: return 'Đã hủy';
            default: return 'Không xác định';
        }
    }
}