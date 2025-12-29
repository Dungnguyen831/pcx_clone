<?php
require_once 'app/models/CartModel.php';

class CartController {
    
    // Hàm kiểm tra đăng nhập (Dùng chung cho cả class)
    private function checkLogin() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }
    }

    // 1. Xem giỏ hàng
    public function index() {
        $this->checkLogin(); // Bắt buộc đăng nhập

        $user_id = $_SESSION['user_id'];
        $cartModel = new CartModel();
        $cartItems = $cartModel->getCartItems($user_id);

        // Tính tổng tiền
        $totalPrice = 0;
        foreach ($cartItems as $item) {
            $totalPrice += $item['price'] * $item['quantity'];
        }

        require_once 'views/client/cart/index.php';
    }

    // 2. Thêm vào giỏ (Xử lý khi bấm nút "Thêm vào giỏ")
    public function add() {
        $this->checkLogin();

        if (isset($_GET['id'])) {
            $product_id = $_GET['id'];
            $user_id = $_SESSION['user_id'];
            $quantity = 1; // Mặc định là 1

            $cartModel = new CartModel();
            $cartModel->addToCart($user_id, $product_id, $quantity);

            // Thêm xong chuyển hướng về trang giỏ hàng để khách xem
            header("Location: index.php?controller=cart&action=index");
        }
    }

    // 3. Xóa sản phẩm
    public function delete() {
        $this->checkLogin();
        
        if (isset($_GET['cart_id'])) {
            $cart_id = $_GET['cart_id'];
            $user_id = $_SESSION['user_id']; // Lấy ID user để bảo mật

            $cartModel = new CartModel();
            $cartModel->removeFromCart($cart_id, $user_id);
        }
        header("Location: index.php?controller=cart&action=index");
    }
    
    // 4. Cập nhật số lượng
    public function update() {
        $this->checkLogin();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
             $cartModel = new CartModel();
             $user_id = $_SESSION['user_id'];
             
             // Lặp qua danh sách input số lượng gửi lên
             foreach ($_POST['quantity'] as $cart_id => $qty) {
                 $cartModel->updateQuantity($cart_id, $qty, $user_id);
             }
        }
        header("Location: index.php?controller=cart&action=index");
    }
}
?>