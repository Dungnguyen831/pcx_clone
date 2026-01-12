<?php
require_once 'app/models/client/RewardModel.php';
class RewardController
{
    private $model;
    public function __construct()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }
        $this->model = new RewardModel();
    }
    public function index()
    {
        $user_id = $_SESSION['user_id'];
        // Tự động quét và cập nhật điểm từ đơn hàng Hoàn thành
        $this->model->updatePointsFromOrders($user_id);
        $current_points = $this->model->getCurrentPoints($user_id);
        $coupons = $this->model->getAllCoupons($user_id);
        $owned_coupons = $this->model->getOwnedCoupons($user_id);
        require_once 'views/client/reward/index.php';
    }
    public function redeem()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $res = $this->model->redeem($_SESSION['user_id'], $_POST['coupon_id']);
            if ($res['status']) {
                $_SESSION['flash_code'] = $res['code'];
                $_SESSION['success'] = ($res['type'] == 'free') ? "Lấy mã thành công!" : "Đổi điểm thành công!";
            } else {
                $_SESSION['error'] = $res['msg'];
            }
        }
        header("Location: index.php?controller=reward&action=index");
        exit;
    }
}
