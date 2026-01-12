<?php
// require_once 'vendor/autoload.php';

// use PhpOffice\PhpSpreadsheet\Spreadsheet;
// use PhpOffice\PhpSpreadsheet\IOFactory;

class AdminOrderController
{
    private $orderModel;

    public function __construct()
    {
        // Yêu cầu Model phù hợp
        require_once 'app/models/admin/AdminOrderModel.php';
        $this->orderModel = new AdminOrderModel();
    }

    /**
     * Hiển thị danh sách đơn hàng và các thông số thống kê nhanh
     */
    public function index()
    {
        // 1. Lấy dữ liệu tìm kiếm
        $search_id = $_GET['search_id'] ?? null;
        $search_name = $_GET['search_name'] ?? null;

        // Xác định trạng thái dựa trên action (nhánh con)
        $action = $_GET['action'] ?? 'index';
        $status = null;
        
        switch ($action) {
            case 'pending':   $status = 0; break; // Chờ xác nhận
            case 'pickup':    $status = 1; break; // Chờ lấy hàng (Đã xác nhận)
            case 'shipping':  $status = 2; break; // Đang giao
            case 'completed': $status = 3; break; // Đã giao (Hoàn thành)
            case 'cancelled': $status = 4; break; // Đã hủy
            default:          $status = null; break; // Tất cả
        }

        $orders = $this->orderModel->getAllOrders($search_id, $search_name, $status);

        // Thống kê nhanh
        $stats = ['total_orders' => count($orders), 'pending' => 0, 'revenue' => 0];
        // Lưu ý: stats này nên tính trên toàn bộ đơn hàng, không chỉ đơn đang lọc
        $allOrdersForStats = $this->orderModel->getAllOrders();

        foreach ($orders as $o) {
            if ($o['status'] == 0) $stats['pending']++;
            if ($o['status'] == 3) $stats['revenue'] += $o['final_money'];
        }

        // 4. Thiết lập các thông số view
        $page_title = "Quản lý đơn hàng";
        $controller = 'order';
        $content_view = 'views/admin/order/index.php';

        // Truyền biến vào layout chung
        require_once 'views/admin/layouts/page.php';
    }

    public function pending() {
        $_GET['action'] = 'pending';
        $this->index();
    }

    public function pickup() {
        $_GET['action'] = 'pickup';
        $this->index();
    }

    public function shipping() {
        $_GET['action'] = 'shipping';
        $this->index();
    }

    public function completed() {
        $_GET['action'] = 'completed';
        $this->index();
    }

    public function cancelled() {
        $_GET['action'] = 'cancelled';
        $this->index();
    }
    /**
     * Xem chi tiết một đơn hàng cụ thể
     */
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

        // Lấy danh sách sản phẩm trong đơn
        $items = $this->orderModel->getOrderDetails($id);

        $page_title = "Chi tiết đơn hàng #" . $id;
        $controller = 'order';
        $content_view = 'views/admin/order/detail.php';
        require_once 'views/admin/layouts/page.php';
    }

    /**
     * Cập nhật trạng thái kèm theo xử lý Kho hàng (Inventory)
     */
    public function updateStatus()
    {
        $id = $_GET['id'] ?? null;
        $status = $_GET['status'] ?? null;

        if ($id !== null && $status !== null) {
            // Gọi hàm xử lý Transaction trong Model đã nâng cấp
            $result = $this->orderModel->updateStatusAndInventory($id, $status);

            if ($result === true) {
                // Thành công: Chuyển hướng kèm thông báo thành công
                header("Location: index.php?controller=admin-order&msg=updated");
            } else {
                // Thất bại: Chuyển hướng kèm thông báo lỗi (ví dụ: Hết hàng)
                // $result lúc này chứa chuỗi thông báo lỗi từ Exception
                header("Location: index.php?controller=admin-order&msg=error&detail=" . urlencode($result));
            }
            exit();
        }
    }

   /**  XUẤT CSV */
   public function exportExcel() 
    {
        // 1. Lấy trạng thái từ nhánh con hiện tại
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

        // 2. Lấy dữ liệu đã lọc từ Model
        $orders = $this->orderModel->getAllOrders(null, null, $status);
        $filename = "DonHang_" . str_replace(' ', '_', $typeName) . "_" . date('d-m-Y') . ".xls";

        // 3. Định nghĩa CSS 
        $style = "
        <style>
            .excel-table { font-family: 'Arial', sans-serif; border-collapse: collapse; width: 100%; }
            .excel-table th { 
                background-color: #2ecc71; color: #ffffff; font-weight: bold; 
                border: 0.5pt solid #000000; text-align: center; height: 35px; font-size: 12pt;
            }
            .excel-table td { 
                border: 0.5pt solid #000000; padding: 8px; vertical-align: middle; font-size: 11pt; 
            }
            .text-center { text-align: center; }
            .text-right { text-align: right; }
            .phone-format { mso-number-format:'\@'; } /* Giữ số 0 */
            .title-doc { font-family: 'Arial'; font-size: 16pt; font-weight: bold; text-align: center; color: #2c3e50; }
        </style>";

        // 4. Xuất Header và Nội dung HTML
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");

        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        echo '<head><meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">' . $style . '</head>';
        echo '<body>';
        
        // Tiêu đề trong file
        echo '<div class="title-doc">DANH SÁCH ĐƠN HÀNG ' . mb_strtoupper($typeName, 'UTF-8') . '</div>';
        echo '<div style="text-align: center; margin-bottom: 20px;">Ngày xuất: ' . date('d/m/Y H:i') . '</div><br>';

        echo '<table class="excel-table" border="1">';
        echo '<thead>
                <tr>
                    <th style="width: 50pt;">ID</th>
                    <th style="width: 200pt;">Khách hàng</th>
                    <th style="width: 120pt;">Điện thoại</th>
                    <th style="width: 130pt;">Tổng tiền</th>
                    <th style="width: 160pt;">Ngày đặt</th>
                    <th style="width: 130pt;">Trạng thái</th>
                </tr>
            </thead><tbody>';

        if (!empty($orders)) {
            foreach ($orders as $o) {
                echo '<tr>
                    <td class="text-center">' . $o['order_id'] . '</td>
                    <td>' . htmlspecialchars($o['customer_name']) . '</td>
                    <td class="text-center phone-format">' . $o['customer_phone'] . '</td>
                    <td class="text-right">' . number_format($o['final_money'], 0, ',', '.') . ' VNĐ</td>
                    <td class="text-center">' . date('d/m/Y H:i', strtotime($o['created_at'])) . '</td>
                    <td class="text-center">' . $this->getStatusName($o['status']) . '</td>
                </tr>';
            }
        } else {
            echo '<tr><td colspan="6" style="text-align:center; padding: 20px;">Không có dữ liệu đơn hàng nào.</td></tr>';
        }

        echo '</tbody></table></body></html>';
        exit;
    }
    /**
     * Hàm bổ trợ chuyển đổi mã số trạng thái sang tên hiển thị
     */
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
