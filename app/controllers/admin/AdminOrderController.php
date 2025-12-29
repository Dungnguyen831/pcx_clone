<?php
require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

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

        // 2. Lấy danh sách đơn hàng theo điều kiện lọc
        $orders = $this->orderModel->getAllOrders($search_id, $search_name);

        // 3. Chuẩn bị dữ liệu thống kê (Bước này giúp trang của bạn chuyên nghiệp hơn)
        // Lưu ý: Bạn có thể viết thêm các hàm này trong Model sau
        $stats = [
            'total_orders' => count($orders),
            'pending'      => 0,
            'revenue'      => 0
        ];

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

    /* ================== XUẤT EXCEL ================== */
    public function exportExcel()
    {
        $orders = $this->orderModel->getAllOrders();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->fromArray([
            ['ID', 'Khách hàng', 'Điện thoại', 'Tổng tiền', 'Ngày đặt', 'Trạng thái']
        ]);

        $row = 2;
        foreach ($orders as $o) {
            $sheet->fromArray([
                $o['order_id'],
                $o['customer_name'],
                $o['customer_phone'],
                $o['final_money'],
                $o['created_at'],
                (int)$o['status']   
            ], null, 'A' . $row);
            $row++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="don_hang.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    /* ================== NHẬP EXCEL ================== */
    public function importExcel()
    {
        if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] != 0) {
            header("Location: index.php?controller=admin-order&msg=error");
            exit;
        }

        $spreadsheet = IOFactory::load($_FILES['excel_file']['tmp_name']);
        $rows = $spreadsheet->getActiveSheet()->toArray();

        unset($rows[0]); // bỏ header

        foreach ($rows as $row) {
            if (empty($row[0])) continue;

            $data = [
                'customer_name'  => $row[1],
                'customer_phone' => $row[2],
                'final_money'    => $row[3],
                'created_at'     => $row[4],
                'status'         => $row[5]
            ];

            $this->orderModel->insertOrderFromExcel($data);
        }

        header("Location: index.php?controller=admin-order&msg=imported");
        exit;
    }

}
