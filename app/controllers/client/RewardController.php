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

        $current_points = $this->model->getCurrentPoints($user_id);
        $coupons = $this->model->getAllCoupons($user_id);
        $history = $this->model->getUsedHistory($user_id);

        require_once 'views/client/reward/index.php';
    }

    public function redeem()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user_id = $_SESSION['user_id'];
            $coupon_id = $_POST['coupon_id'];

            $result = $this->model->redeem($user_id, $coupon_id);

            if ($result['status']) {
                // Flash Session: Lưu mã để hiện Popup
                $_SESSION['flash_code'] = $result['code'];
                $_SESSION['success'] = ($result['type'] == 'free') ? "Lấy mã thành công!" : "Đổi điểm thành công!";
            } else {
                $_SESSION['error'] = $result['msg'];
            }
        }
        header("Location: index.php?controller=reward&action=index");
        exit;
    }
}
