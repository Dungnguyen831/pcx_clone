<?php
require_once 'app/models/admin/AdminCouponModel.php';

class AdminCouponController
{
    private $model;

    public function __construct()
    {
        $this->model = new AdminCouponModel();
    }

    public function index()
    {
        $search = $_GET['search'] ?? null;
        $coupons = $this->model->getAll($search);
        $this->renderView($coupons);
    }

    public function edit()
    {
        $id = $_GET['id'];
        $search = $_GET['search'] ?? null;
        $coupon_edit = $this->model->getById($id);
        $coupons = $this->model->getAll($search);
        $this->renderView($coupons, $coupon_edit);
    }

    private function renderView($coupons, $coupon_edit = null)
    {
        $controller = 'coupon';
        $page_title = "Quản lý mã giảm giá";
        $content_view = 'views/admin/coupon/index.php';
        require_once 'views/admin/layouts/page.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $search = $_POST['current_search'] ?? '';
            if ($this->model->save($_POST)) {
                header("Location: index.php?controller=admin-coupon&search=$search&msg=success");
            }
        }
    }

    public function delete()
    {
        $id = $_GET['id'];
        $search = $_GET['search'] ?? '';
        if ($this->model->delete($id)) {
            header("Location: index.php?controller=admin-coupon&search=$search&msg=deleted");
        }
    }
}
