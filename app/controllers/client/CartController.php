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

    // public function processCheckout() {
    //     if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //         require_once 'app/models/client/OrderModel.php';
    //         $orderModel = new OrderModel();

    //         $user_id = $_SESSION['user_id'];
    //         $data = [
    //             'customer_name'    => $_POST['customer_name'],
    //             'customer_phone'   => $_POST['customer_phone'],
    //             'shipping_address' => $_POST['shipping_address'],
    //             'note'             => $_POST['note'],
    //             'total_money'      => $_POST['total_money']
    //         ];

    //         $cart = $this->cartModel->getCartByUser($user_id);

    //         if ($orderModel->createOrder($user_id, $data, $cart)) {
    //             echo "<script>alert('Đặt hàng thành công!'); window.location.href='index.php?controller=order&action=index';</script>";
    //         } else {
    //             echo "Lỗi khi xử lý đơn hàng.";
    //         }
    //     }
    // }

    public function processCheckout() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            require_once 'app/models/client/OrderModel.php';
            require_once 'app/models/client/CartModel.php'; // Cần cái này để lấy giỏ hàng nếu user đã login

            $orderModel = new OrderModel();
            $cartModel = new CartModel();

            // 1. Xác định User ID (Nếu chưa đăng nhập thì là 0 hoặc khách vãng lai)
            $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

            // 2. Lấy lại Giỏ hàng (Để tính tiền cho chính xác, không dùng dữ liệu từ View gửi lên)
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

            // 3. Tính toán Tổng tiền gốc (Server-side calculation)
            $total_money = 0;
            foreach ($cart as $item) {
                // Xóa ký tự lạ, chỉ giữ số để tính toán
                $price_clean = preg_replace('/[^0-9]/', '', $item['price']);
                $total_money += (float)$price_clean * (int)$item['quantity'];
            }

            // 4. Tính toán Giảm giá (Lấy từ Session Coupon)
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
                
                // Tính tiền cuối cùng
                $final_money = $total_money - $discount_amount;
                if ($final_money < 0) $final_money = 0;
            }

            // 5. Đóng gói dữ liệu để gửi sang Model
            $data = [
                'customer_name'    => $_POST['customer_name'],
                'customer_phone'   => $_POST['customer_phone'],
                'shipping_address' => $_POST['shipping_address'],
                'note'             => $_POST['note'],
                'total_money'      => $total_money,      // Tổng gốc
                'discount_amount'  => $discount_amount,  // Tiền giảm
                'final_money'      => $final_money,      // Tiền khách phải trả
                'coupon_code'      => $coupon_code,      // Mã đã dùng
                'payment_method'   => 'COD'              // Mặc định hoặc lấy từ POST
            ];

            // 6. Gọi Model tạo đơn hàng
            // Lưu ý: Bạn cần chắc chắn hàm createOrder trong OrderModel đã được sửa để nhận array $data này
            if ($orderModel->createOrder($user_id, $data, $cart)) {
                
                // 7. Dọn dẹp sau khi thành công
                unset($_SESSION['cart']);   // Xóa giỏ hàng session
                unset($_SESSION['coupon']); // Xóa mã giảm giá đã dùng
                
                // Nếu user đã login, cần xóa cả giỏ hàng trong DB (nếu bảng cart của bạn không tự xóa trigger)
                if ($user_id > 0) {
                    // $cartModel->clearCart($user_id); // Gọi hàm xóa giỏ hàng DB nếu có
                }

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

    // app/controllers/CartController.php

    // app/controllers/CartController.php

    // app/controllers/CartController.php

    public function applyCoupon() {
        if (isset($_GET['code'])) {
            $code = trim($_GET['code']);
            
            // --- KHỞI TẠO GIỎ HÀNG ĐỂ TÍNH TOÁN ---
            $cart = [];

            // TRƯỜNG HỢP 1: Đã đăng nhập -> Lấy từ Database
            if (isset($_SESSION['user_id'])) {
                $cartModel = new CartModel();
                // Giả sử bạn có hàm getCartByUser trong Model (hãy kiểm tra lại tên hàm trong CartModel của bạn)
                // Nếu chưa có, hãy xem Bước 2 bên dưới
                $cart = $cartModel->getCartByUser($_SESSION['user_id']); 
            } 
            // TRƯỜNG HỢP 2: Khách vãng lai -> Lấy từ Session
            else if (isset($_SESSION['cart'])) {
                $cart = $_SESSION['cart'];
            }

            // --- BẮT ĐẦU TÍNH TỔNG TIỀN ---
            $total = 0;
            if (!empty($cart)) {
                foreach ($cart as $item) {
                    // Xử lý giá tiền (xóa dấu chấm, phẩy, chữ đ) để thành số nguyên
                    // Ví dụ: "3.200.000 đ" -> 3200000
                    $price_clean = preg_replace('/[^0-9]/', '', $item['price']);
                    
                    // Ép kiểu sang số float và int để nhân
                    $total += (float)$price_clean * (int)$item['quantity'];
                }
            }
            
            // --- DEBUG (Nếu vẫn lỗi 0đ, bỏ comment dòng dưới để xem nó in ra gì) ---
            // echo "<pre>"; print_r($cart); echo "Total: " . $total; die();

            // --- GỌI MODEL CHECK MÃ ---
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

    // Hàm xóa mã nếu khách muốn đổi ý
    public function removeCoupon() {
        unset($_SESSION['coupon']);
        header("Location: index.php?controller=cart&action=checkout");
    }
}
