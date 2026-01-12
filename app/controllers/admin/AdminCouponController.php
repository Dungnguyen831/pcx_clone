<?php
require_once 'app/models/admin/AdminCouponModel.php';

class AdminCouponController
{
    private $model;
    public function __construct()
    {
        $this->model = new AdminCouponModel();
    }

    // --- 1. HIỂN THỊ DANH SÁCH ---
    public function index()
    {
        $this->model->autoExpireCoupons();
        $filters = [
            'keyword'       => $_GET['keyword'] ?? ($_GET['search'] ?? ''),
            'status'        => $_GET['status'] ?? '',
            'discount_type' => $_GET['discount_type'] ?? ''
        ];
        $coupons = $this->model->getAll($filters);
        $this->renderView($coupons, null, ['filters' => $filters]);
    }

    // --- 2. HIỂN THỊ FORM SỬA ---
    public function edit()
    {
        $this->model->autoExpireCoupons();
        $id = $_GET['id'];
        $filters = [
            'keyword'       => $_GET['keyword'] ?? '',
            'status'        => $_GET['status'] ?? '',
            'discount_type' => $_GET['discount_type'] ?? ''
        ];
        $coupon_edit = $this->model->getById($id);
        $coupons = $this->model->getAll($filters);
        $this->renderView($coupons, $coupon_edit, ['filters' => $filters]);
    }

    // --- 3. XỬ LÝ LƯU & VALIDATE (Đã chặn số âm Lượt dùng) ---
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $postData = $_POST;
            $search = $postData['current_search'] ?? '';
            $errors = [];

            // 1. Validate Code
            $code = trim($postData['code']);
            if (empty($code)) {
                $errors['code'] = "Vui lòng nhập mã coupon.";
            } elseif (!preg_match('/^[A-Z0-9]+$/', strtoupper($code))) {
                $errors['code'] = "Mã chỉ được chứa chữ cái không dấu và số.";
            } else {
                $idCheck = !empty($postData['id']) ? $postData['id'] : null;
                if ($this->model->checkCodeExists($code, $idCheck)) {
                    $errors['code'] = "Mã '$code' đã tồn tại.";
                }
            }

            // 2. Validate Giá trị giảm
            if ($postData['discount_value'] < 0) {
                $errors['discount_value'] = "Giá trị giảm không được âm.";
            }
            if ($postData['discount_type'] == 'percent' && $postData['discount_value'] > 100) {
                $errors['discount_value'] = "Giảm giá phần trăm không được quá 100%.";
            }

            // 3. Validate Đơn tối thiểu
            if ($postData['min_order_value'] < 0) {
                $errors['min_order_value'] = "Đơn tối thiểu không được âm.";
            }

            // 4. Validate Lượt dùng tối đa (ĐÃ THÊM)
            if ((int)$postData['usage_limit'] < 0) {
                $errors['usage_limit'] = "Lượt dùng tối đa không được âm.";
            }

            // 5. Validate Ngày tháng
            $start = $postData['start_date'];
            $end = $postData['end_date'];

            if (empty($start)) $errors['start_date'] = "Chưa chọn ngày bắt đầu.";
            if (empty($end))   $errors['end_date'] = "Chưa chọn ngày kết thúc.";

            if (!empty($start) && !empty($end)) {
                if (strtotime($start) > strtotime($end)) {
                    $errors['start_date'] = "Ngày bắt đầu không được lớn hơn ngày kết thúc.";
                }
            }

            // Xử lý kết quả
            if (!empty($errors)) {
                $this->model->autoExpireCoupons();
                $filters = ['keyword' => $search];
                $coupons = $this->model->getAll($filters);

                // Render lại view kèm lỗi
                $this->renderView($coupons, $postData, [
                    'errors' => $errors,
                    'filters' => $filters
                ]);
            } else {
                if ($this->model->save($postData)) {
                    header("Location: index.php?controller=admin-coupon&keyword=$search&msg=success");
                    exit;
                }
            }
        }
    }

    public function delete()
    {
        $id = $_GET['id'];
        $search = $_GET['keyword'] ?? '';
        if ($this->model->delete($id)) {
            header("Location: index.php?controller=admin-coupon&keyword=$search&msg=deleted");
        }
    }

    // --- 4. HÀM RENDER VIEW (Đã trỏ đúng layout page.php để hiện Menu) ---
    private function renderView($coupons, $coupon_edit = null, $extraData = [])
    {
        $controller = 'coupon';
        $page_title = "Quản lý mã giảm giá";

        if (!empty($extraData)) extract($extraData);
        if (!isset($filters)) $filters = [];
        if (!isset($errors)) $errors = [];

        $content_view = 'views/admin/coupon/index.php';
        require_once 'views/admin/layouts/page.php';
    }

    /** XUẤT EXCEL VOUCHER */
    /** XUẤT EXCEL VOUCHER - FIX TRIỆT ĐỂ LỖI ĐỊNH DẠNG NGÀY THÁNG */
    public function exportExcel()
    {
        // 1. Lấy dữ liệu đã lọc
        $filters = [
            'keyword'       => $_GET['keyword'] ?? '',
            'status'        => $_GET['status'] ?? '',
            'discount_type' => $_GET['discount_type'] ?? ''
        ];
        $coupons = $this->model->getAll($filters);
        $filename = "DanhSach_Voucher_" . date('d-m-Y') . ".xls";

        // 2. CSS định dạng bảng và cưỡng ép kiểu Text
        $style = "
    <style>
        .excel-table { font-family: 'Arial', sans-serif; border-collapse: collapse; width: 100%; }
        .excel-table th { 
            background-color: #3498db; color: #ffffff; font-weight: bold; 
            border: 0.5pt solid #000000; text-align: center; height: 35px;
        }
        .excel-table td { border: 0.5pt solid #000000; padding: 8px; font-size: 10pt; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        /* Kỹ thuật mso-number-format nâng cao */
        .force-text { mso-number-format:'\@'; white-space: nowrap; } 
    </style>";

        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");

        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        echo '<head><meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">' . $style . '</head>';
        echo '<body>';

        echo '<div style="font-size: 16pt; font-weight: bold; text-align: center;">DANH SÁCH MÃ GIẢM GIÁ (COUPONS)</div>';
        echo '<div style="text-align: center;">Ngày xuất: ' . date('d/m/Y H:i') . '</div><br>';

        echo '<table class="excel-table" border="1">';
        echo '<thead>
            <tr>
                <th style="width: 40pt;">ID</th>
                <th style="width: 100pt;">Mã Code</th>
                <th style="width: 120pt;">Giá trị giảm</th>
                <th style="width: 120pt;">Đơn tối thiểu</th>
                <th style="width: 80pt;">Điểm đổi</th>
                <th style="width: 100pt;">Lượt dùng</th>
                <th style="width: 100pt;">Hạn sử dụng</th>
                <th style="width: 80pt;">Trạng thái</th>
            </tr>
        </thead><tbody>';

        if (!empty($coupons)) {
            foreach ($coupons as $c) {
                $discount = ($c['discount_type'] == 'percent')
                    ? $c['discount_value'] . '%'
                    : number_format($c['discount_value'], 0, ',', '.') . ' VNĐ';

                $points = ($c['points_cost'] > 0) ? $c['points_cost'] . ' điểm' : 'Miễn phí';

                // Kỹ thuật bổ trợ: Thêm dấu nháy đơn ẩn đằng trước để Excel không tự ý format số/ngày
                $usage_display = ($c['usage_limit'] > 0) ? $c['used_count'] . ' / ' . $c['usage_limit'] : '';

                echo '<tr>
                <td class="text-center">' . $c['coupon_id'] . '</td>
                <td class="text-center force-text">' . $c['code'] . '</td>
                <td class="text-right">' . $discount . '</td>
                <td class="text-right">' . number_format($c['min_order_value'], 0, ',', '.') . ' VNĐ</td>
                <td class="text-center">' . $points . '</td>
                
                <td class="text-center force-text">' . $usage_display . '&nbsp;</td>
                
                <td class="text-center force-text">' . date('d/m/Y', strtotime($c['end_date'])) . '</td>
                <td class="text-center">' . ($c['status'] == 1 ? 'Đang chạy' : 'Tạm dừng') . '</td>
            </tr>';
            }
        }
        echo '</tbody></table></body></html>';
        exit;
    }
}
