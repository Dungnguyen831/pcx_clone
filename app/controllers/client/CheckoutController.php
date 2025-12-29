<?php
require_once 'app/models/OrderModel.php';
require_once 'app/models/CartModel.php'; // NHÚNG THÊM CartModel

class CheckoutController {
    
    public function index() {
        // 1. Lấy dữ liệu từ Database thay vì Session
        $cartModel = new CartModel();
        $user_id = $_SESSION['user_id'] ?? null;

        if (!$user_id) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        $cart = $cartModel->getCartByUser($user_id);

        // 2. Nếu Database trống thì mới văng về trang chủ
        if (empty($cart)) {
            header("Location: index.php");
            exit();
        }

        require_once 'views/client/payment/detail.php';
    }

    public function processCheckout() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderModel = new OrderModel();
            $cartModel = new CartModel();
            $user_id = $_SESSION['user_id'];

            // 1. Lấy giỏ hàng từ Database để tính tiền
            $cartItems = $cartModel->getCartByUser($user_id);
            
            $totalMoney = 0;
            foreach ($cartItems as $item) {
                $totalMoney += $item['price'] * $item['quantity'];
            }

            $data = [
                'user_id'          => $user_id,
                'customer_name'    => $_POST['full_name'],
                'customer_phone'   => $_POST['phone'],
                'shipping_address' => $_POST['address'],
                'note'             => $_POST['note'],
                'total_money'      => $totalMoney
            ];

            // 2. Lưu đơn hàng
            $result = $orderModel->createOrder($user_id, $data, $cart_items);

            if ($result) {
                // 3. XÓA GIỎ HÀNG TRONG DATABASE (Không phải Session)
                // Bạn cần viết thêm hàm này trong CartModel hoặc OrderModel
                $this->clearCart($user_id); 

                echo "<script>alert('Đặt hàng thành công!'); window.location.href='index.php';</script>";
            } else {
                echo "Lỗi khi đặt hàng!";
            }
        }
    }

    // Hàm phụ để xóa giỏ hàng sau khi mua
    private function clearCart($user_id) {
        $db = new Database();
        $conn = $db->getConnection();
        $stmt = $conn->prepare("DELETE FROM carts WHERE user_id = ?");
        $stmt->execute([$user_id]);
    }
}