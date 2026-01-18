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
            require_once 'app/models/client/CartModel.php'; 

            $orderModel = new OrderModel();
            $cartModel = new CartModel();

            $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

            $cart = [];
            if ($user_id > 0) {
                $cart = $cartModel->getCartByUser($user_id);
            } else if (isset($_SESSION['cart'])) {
                $cart = $_SESSION['cart'];
            }

            if (empty($cart)) {
                echo "<script>alert('Giỏ hàng trống!'); window.location.href='index.php';</script>";
                return;
            }

            $total_money = 0;
            foreach ($cart as $item) {
                $price_clean = preg_replace('/[^0-9]/', '', $item['price']);
                $total_money += (float)$price_clean * (int)$item['quantity'];
            }

            $discount_amount = 0;
            $coupon_code = null;
            $final_money = $total_money;

            if (isset($_SESSION['coupon'])) {
                $c = $_SESSION['coupon'];
                $coupon_code = $c['code'];

                if ($c['discount_type'] == 'percent') {
                    $discount_amount = $total_money * ($c['discount_value'] / 100);
                } else {
                    $discount_amount = $c['discount_value'];
                }
                
                $final_money = $total_money - $discount_amount;
                if ($final_money < 0) $final_money = 0;
            }

            $data = [
                'customer_name'    => $_POST['customer_name'],
                'customer_phone'   => $_POST['customer_phone'],
                'shipping_address' => $_POST['shipping_address'],
                'note'             => $_POST['note'],
                'total_money'      => $total_money,      
                'discount_amount'  => $discount_amount,  
                'final_money'      => $final_money,      
                'coupon_code'      => $coupon_code,      
                'payment_method'   => 'COD'              
            ];

            
            if ($orderModel->createOrder($user_id, $data, $cart)) {
                
                unset($_SESSION['cart']);   // Xóa giỏ hàng session
                unset($_SESSION['coupon']); // Xóa mã giảm giá đã dùng
                
                echo "<script>alert('Đặt hàng thành công!'); window.location.href='index.php?controller=order&action=index';</script>";
            } else {
                echo "Lỗi khi xử lý đơn hàng.";
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

    public function applyCoupon() {
        if (isset($_GET['code'])) {
            $code = trim($_GET['code']);
            
            $cart = [];

            if (isset($_SESSION['user_id'])) {
                $cartModel = new CartModel();
                $cart = $cartModel->getCartByUser($_SESSION['user_id']); 
            } 

            else if (isset($_SESSION['cart'])) {
                $cart = $_SESSION['cart'];
            }

            $total = 0;
            if (!empty($cart)) {
                foreach ($cart as $item) {
                    $price_clean = preg_replace('/[^0-9]/', '', $item['price']);
                    $total += (float)$price_clean * (int)$item['quantity'];
                }
            }
            
            $cartModel = new CartModel();
            
            $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

            $result = $cartModel->checkCoupon($code, $total, $user_id);

            if ($result['valid']) {
                $_SESSION['coupon'] = $result['data'];
                $msg = "Áp dụng mã " . $result['data']['code'] . " thành công!";
                $type = "success";
            } else {
                unset($_SESSION['coupon']);
                $msg = $result['msg'];
                $type = "error";
            }
        }
        
        // Quay lại trang thanh toán
        header("Location: index.php?controller=cart&action=checkout&msg=" . urlencode($msg) . "&type=$type");
    }

    public function removeCoupon() {
        unset($_SESSION['coupon']);
        header("Location: index.php?controller=cart&action=checkout");
    }
}
