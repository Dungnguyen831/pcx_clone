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
}
