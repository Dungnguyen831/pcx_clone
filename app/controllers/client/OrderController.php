<?php
require_once 'app/models/OrderModel.php';

class OrderController {
     private $orderModel;

    public function __construct() {
        global $pdo; // lấy PDO đã tạo ở index.php
        $this->orderModel = new OrderModel($pdo);
    }

    // Danh sách đơn hàng
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        $orders = $this->orderModel->getOrdersByUser($_SESSION['user_id']);
        require_once 'views/client/orders/index.php';
    }

    // Chi tiết đơn hàng
    public function detail() {
        $order_id = $_GET['id'] ?? 0;

        $order = $this->orderModel->getOrderById($order_id);
        $items = $this->orderModel->getOrderItems($order_id);

        require_once 'views/client/orders/detail.php';
    }

    // Hủy đơn
    public function cancel() {
        $order_id = $_GET['id'];

        $this->orderModel->cancelOrder($order_id);
        $_SESSION['success_msg'] = "Đã hủy đơn hàng!";
        header("Location: index.php?controller=order&action=index");
    }
}
