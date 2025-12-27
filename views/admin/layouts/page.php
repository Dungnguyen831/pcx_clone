<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Admin Panel'; ?></title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>

<body>
    <div class="admin-wrapper">
        <aside class="sidebar">
            <div class="sidebar-header">
                <i class="fa-solid fa-shield-halved"></i> PCX ADMIN
            </div>
            <ul class="sidebar-menu">
                <li>
                    <a href="index.php?controller=dashboard" class="<?php echo ($controller == 'dashboard') ? 'active' : ''; ?>">
                        <i class="fa-solid fa-gauge"></i> Tổng quan
                    </a>
                </li>
                <li>
                    <a href="index.php?controller=product" class="<?php echo ($controller == 'product') ? 'active' : ''; ?>">
                        <i class="fa-solid fa-box"></i> Sản phẩm
                    </a>
                </li>
                <li>
                    <a href="index.php?controller=order" class="<?php echo ($controller == 'order') ? 'active' : ''; ?>">
                        <i class="fa-solid fa-file-invoice-dollar"></i> Đơn hàng
                    </a>
                </li>
            </ul>
        </aside>

        <div class="main-content">
            <header class="admin-header">
                <div class="admin-title">
                    <?php echo isset($page_title) ? $page_title : 'Dashboard'; ?>
                </div>
                <div class="user-info">
                    Xin chào, <strong>Admin</strong> <i class="fa-solid fa-user-tie"></i>
                </div>
            </header>

            <div class="content-body">
                <?php
                // Kiểm tra và nhúng file view con vào đây
                if (isset($content_view) && file_exists($content_view)) {
                    require_once $content_view;
                } else {
                    echo "<p>Không tìm thấy nội dung.</p>";
                }
                ?>
            </div>
        </div>
    </div>

</body>

</html>