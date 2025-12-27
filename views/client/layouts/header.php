<?php
// Logic đếm số lượng trong giỏ hàng (Session)
$total_items = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total_items += $item['quantity'];
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
                <li><a href="#">Thương hiệu</a></li>
                <li><a href="#">Chuột</a></li>
                <li><a href="#">Bàn phím</a></li>
                <li><a href="#">Tai nghe</a></li>
            </ul>
        </nav>

        <div class="header-icons">
            <a href="#"><i class="fa-solid fa-magnifying-glass"></i></a>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="index.php?controller=auth&action=profile"><i class="fa-solid fa-user-check"></i></a>
            <?php else: ?>
                <a href="index.php?controller=auth&action=login"><i class="fa-regular fa-user"></i></a>
            <?php endif; ?>

            <a href="index.php?controller=cart&action=index" class="cart-icon">
                <i class="fa-solid fa-cart-shopping"></i> 
                <span class="cart-count">(<?php echo $total_items; ?>)</span>
            </a>
        </div>
    </div>
</header>