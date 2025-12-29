<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Admin Panel'; ?></title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="assets/css/coupon.css">
</head>

<body>
    <div class="admin-wrapper">
        <aside class="sidebar">
            <div class="sidebar-header">
                <i class="fa-solid fa-shield-halved"></i> PCX ADMIN
            </div>
            <ul class="sidebar-menu">
                <li>
                    <a href="index.php?controller=admin" class="<?php echo ($controller == 'dashboard') ? 'active' : ''; ?>">
                        <i class="fa-solid fa-gauge"></i> Tổng quan
                    </a>
                </li>
                <li class="has-submenu <?php echo ($controller == 'product' || $controller == 'category' || $controller == 'brand') ? 'active' : ''; ?>">
                    <a href="javascript:void(0)" onclick="toggleSubmenu(this)" class="menu-item">
                        <i class="fa-solid fa-box"></i>
                        <span>Sản phẩm</span>
                        <i class="fa-solid fa-chevron-down arrow-icon" style="margin-left: auto; font-size: 12px;"></i>
                    </a>

                    <ul class="submenu" id="productSubmenu" style="<?php echo ($controller == 'product' || $controller == 'category' || $controller == 'brand') ? 'display: block;' : 'display: none;'; ?>">
                        <li>
                            <a href="index.php?controller=admin-product&action=index" class="<?php echo ($controller == 'product') ? 'active' : ''; ?>">
                                <i class="fa-solid fa-list-ul"></i> Danh sách sản phẩm
                            </a>
                        </li>
                        <li>
                            <a href="index.php?controller=admin-category" class="<?php echo ($controller == 'category') ? 'active' : ''; ?>">
                                <i class="fa-solid fa-tags"></i> Danh mục
                            </a>
                        </li>
                        <li>
                            <a href="index.php?controller=admin-brand" class="<?php echo ($controller == 'brand') ? 'active' : ''; ?>">
                                <i class="fa-solid fa-copyright"></i> Hãng sản xuất
                            </a>
                        </li>
                    </ul>
                </li>

                <li>
                    <a href="index.php?controller=admin-coupon" class="<?php echo ($controller == 'coupon') ? 'active' : ''; ?>">
                        <i class="fa-solid fa-ticket"></i> Mã giảm giá
                    </a>
                </li>

                <li>
                    <a href="index.php?controller=admin-order" class="<?php echo ($controller == 'order') ? 'active' : ''; ?>">
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
                <div class="user-nav">
                    <div class="user-info-toggle" onclick="toggleUserDropdown(event)">
                        <div class="user-avatar">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                        <div class="user-text">
                            <span class="user-welcome">Xin chào,</span>
                            <span class="user-name">Admin <i class="fa-solid fa-caret-down"></i></span>
                        </div>
                    </div>

                    <ul class="user-dropdown-menu" id="userDropdown">
                        <li>
                            <a href="index.php?controller=admin-profile&action=profileAdmin">
                                <i class="fa-solid fa-circle-user"></i> Hồ sơ cá nhân
                            </a>
                        </li>
                        <li class="dropdown-divider"></li>
                        <li>
                            <a href="index.php?controller=auth&action=logout" class="logout-link">
                                <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
                            </a>
                        </li>
                    </ul>
                </div>
            </header>

            <div class="content-body">
                <?php
                if (isset($content_view) && file_exists($content_view)) {
                    require_once $content_view;
                } else {
                    echo "<p>Không tìm thấy nội dung.</p>";
                }
                ?>
            </div>
        </div>
    </div>
    <script>
        function toggleSubmenu(element) {
            const parentLi = element.parentElement;
            const submenu = parentLi.querySelector('.submenu');

            if (submenu.style.display === "none" || submenu.style.display === "") {
                submenu.style.display = "block";
                parentLi.classList.add("open");
            } else {
                submenu.style.display = "none";
                parentLi.classList.remove("open");
            }
        }

        function toggleUserDropdown(event) {
            event.stopPropagation();
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('active');
        }

        window.addEventListener('click', function() {
            const dropdown = document.getElementById('userDropdown');
            if (dropdown && dropdown.classList.contains('active')) {
                dropdown.classList.remove('active');
            }
        });
    </script>
</body>

</html>