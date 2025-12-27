<?php
// 1. Khởi tạo số lượng bằng 0
$display_count = 0;

// 2. Kiểm tra nếu người dùng đã đăng nhập, ưu tiên lấy từ Database
if (isset($_SESSION['user_id'])) {
    require_once 'app/models/CartModel.php';
    $cartModel = new CartModel();
    $display_count = $cartModel->getCartCount($_SESSION['user_id']);
} 
// 3. Nếu chưa đăng nhập, có thể đếm tạm từ Session (nếu bạn vẫn dùng cho khách vãng lai)
else if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $display_count += $item['quantity'];
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Phong Cách Xanh Clone'; ?></title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css" rel="stylesheet">
</head>
<body>

<header>
    <div class="container header-flex">
        <a href="index.php" class="logo">
            <i class="fa-solid fa-layer-group"></i> PCX Clone
        </a>

        <nav>
            <ul class="main-menu">
                <li><a href="index.php">Trang chủ</a></li>
                <li><a href="index.php?controller=home&action=listproduct">Sản phẩm</a></li>
                <li><a href="#">Đơn hàng</a></li>
            </ul>
        </nav>

        <div class="header-icons">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="index.php?controller=auth&action=profile"><i class="fa-solid fa-user-check"></i></a>
            <?php else: ?>
                <a href="index.php?controller=auth&action=login"><i class="fa-regular fa-user"></i></a>
            <?php endif; ?>

            <a href="index.php?controller=cart&action=index" class="cart-icon">
                <i class="fa-solid fa-cart-shopping"></i> 
                <span class="cart-count">(<?php echo $display_count; ?>)</span>
            </a>
        </div>
    </div>
</header>