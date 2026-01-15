<?php
require_once 'app/models/CouponModel.php';

class AdminCouponController {
    private $couponModel;

    public function __construct() {
        $this->couponModel = new CouponModel();
        // Kiểm tra quyền Admin ở đây nếu cần
    }

    // Hiển thị danh sách mã
    public function index() {
        $coupons = $this->couponModel->getAll();
        require_once 'views/admin/coupons/index.php';
    }

    // Xử lý thêm mã mới
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'code' => $_POST['code'],
                'discount_type' => $_POST['discount_type'],
                'discount_value' => $_POST['discount_value'],
                'min_order_value' => $_POST['min_order_value'] ?? 0,
                'usage_limit' => $_POST['usage_limit'] ?? 100,
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'status' => 1
            ];
            $this->couponModel->add($data);
            header("Location: index.php?controller=admin-coupon");
        }
    }

    // Xử lý xóa mã
    public function delete() {
        $id = $_GET['id'] ?? 0;
        $this->couponModel->delete($id);
        header("Location: index.php?controller=admin-coupon");
    }
}