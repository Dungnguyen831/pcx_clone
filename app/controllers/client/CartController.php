<?php
require_once 'app/models/CartModel.php';

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
}