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

    public function detail() {
    $id = $_GET['id'] ?? 0;
    
    // 1. Lấy thông tin đơn hàng
    $order = $this->orderModel->getOrderById($id);
    
    // 2. Lấy danh sách sản phẩm (BẮT BUỘC PHẢI CÓ DÒNG NÀY)
    $items = $this->orderModel->getOrderItems($id);
    
    if (!$order) {
        die("Đơn hàng không tồn tại!");
    }

    require_once 'views/client/orders/detail.php'; // Đường dẫn tới file view của bạn
}

    // Hủy đơn
    public function cancel() {
        $order_id = $_GET['id'];

        $this->orderModel->cancelOrder($order_id);
        $_SESSION['success_msg'] = "Đã hủy đơn hàng!";
        header("Location: index.php?controller=order&action=index");
    }
}
