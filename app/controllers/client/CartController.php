<?php
require_once 'app/models/client/CartModel.php';

class CartController {
    private $cartModel;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }
        $this->cartModel = new CartModel();
    }

    public function add() {
        $product_id = $_GET['id'] ?? null;
        $user_id = $_SESSION['user_id'];
        
        if ($product_id) {
            $this->cartModel->addToCart($user_id, $product_id, 1);
        }
        header("Location: index.php?controller=cart&action=index");
    }

    public function index() {
        $user_id = $_SESSION['user_id'];
        $cart = $this->cartModel->getCartByUser($user_id);
        require_once 'views/client/cart/giohang.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user_id = $_SESSION['user_id'];
            foreach ($_POST['qty'] as $product_id => $qty) {
                if ($qty <= 0) {
                    $this->cartModel->removeFromCart($user_id, $product_id);
                } else {
                    $this->cartModel->updateQuantity($user_id, $product_id, $qty);
                }
            }
        }
        header("Location: index.php?controller=cart&action=index");
    }

    public function remove() {
        $product_id = $_GET['id'] ?? null;
        $user_id = $_SESSION['user_id'];
        if ($product_id) {
            $this->cartModel->removeFromCart($user_id, $product_id);
        }
        header("Location: index.php?controller=cart&action=index");
    }
    // Thêm vào trong class CartController
public function checkout() {
    $user_id = $_SESSION['user_id'];
    $cart = $this->cartModel->getCartByUser($user_id);
    
    if (empty($cart)) {
        header("Location: index.php?controller=cart&action=index");
        exit();
    }
    require_once 'views/client/cart/checkout.php';
}
public function processCheckout() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        require_once 'app/models/client/OrderModel.php';
        $orderModel = new OrderModel();
        $user_id = $_SESSION['user_id'];

        $data = [
            'customer_name'    => $_POST['customer_name'],
            'customer_phone'   => $_POST['customer_phone'],
            'shipping_address' => $_POST['shipping_address'],
            'note'             => $_POST['note'],
            'total_money'      => $_POST['total_money']
        ];

        $cart_items = $this->cartModel->getCartByUser($user_id);

        // Gọi hàm và nhận kết quả là một Mảng
        $result = $orderModel->createOrder($user_id, $data, $cart_items);

        // Kiểm tra logic dựa trên cấu trúc mảng mới
        if (isset($result['error']) && $result['error'] === false) {
            // Đặt hàng thành công
            echo "<script>
                alert('Đặt hàng thành công!'); 
                window.location.href='index.php?controller=order&action=index';
            </script>";
        } else {
            // Hiển thị thông báo lỗi cụ thể từ Model (ví dụ: Không đủ hàng)
            $message = isset($result['message']) ? $result['message'] : 'Lỗi hệ thống khi đặt hàng.';
            echo "<script>
                alert('" . $message . "'); 
                window.history.back();
            </script>";
        }
    }
}
public function updateAjax() {
    $productId = $_POST['id'] ?? 0;
    $quantity = $_POST['qty'] ?? 1;
    $userId = $_SESSION['user_id'] ?? null; // Đảm bảo dùng user_id đồng nhất

    if ($productId > 0 && $userId) {
        // Cập nhật số lượng mới vào DB
        $this->cartModel->updateQuantity($userId, $productId, $quantity);
        
        // Lấy lại số lượng sản phẩm khác nhau (ví dụ: 3 loại sản phẩm)
        $uniqueProductCount = $this->cartModel->getCartCount($userId);

        echo json_encode([
            'success' => true,
            'newCount' => $uniqueProductCount 
        ]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit();
}
}