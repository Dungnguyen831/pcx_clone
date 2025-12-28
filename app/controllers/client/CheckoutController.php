<?php
require_once 'app/models/OrderModel.php';

class CheckoutController {
    // Hiển thị trang nhập thông tin
    public function checkout() {
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            header("Location: index.php");
            exit();
        }
        require_once 'views/client/payment/detail.php';
    }

    // Xử lý lưu đơn hàng
    public function processCheckout() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderModel = new OrderModel();
            
            // Tính tổng tiền từ session
            $totalMoney = 0;
            foreach ($_SESSION['cart'] as $item) {
                $totalMoney += $item['price'] * $item['quantity'];
            }

            $data = [
                'user_id'          => $_SESSION['user_id'] ?? null,
                'customer_name'    => $_POST['full_name'],
                'customer_phone'   => $_POST['phone'],
                'shipping_address' => $_POST['address'],
                'note'             => $_POST['note'],
                'total_money'      => $totalMoney
            ];

            $result = $orderModel->createOrder($data, $_SESSION['cart']);

            if ($result) {
                unset($_SESSION['cart']); // Xóa giỏ hàng
                echo "<script>alert('Đặt hàng thành công!'); window.location.href='index.php';</script>";
            } else {
                echo "Lỗi khi đặt hàng!";
            }
        }
    }
}