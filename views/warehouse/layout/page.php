<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Quản lý kho'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --sidebar-bg: #2c3e50; --main-bg: #f4f7f6; --primary-blue: #3498db; --success-green: #27ae60; --danger-red: #e74c3c; }
        body { margin: 0; font-family: 'Segoe UI', sans-serif; display: flex; background: var(--main-bg); }
        .sidebar { width: 260px; background: var(--sidebar-bg); min-height: 100vh; color: white; }
        .sidebar-header { padding: 20px; font-weight: bold; border-bottom: 1px solid #34495e; display: flex; align-items: center; gap: 10px; }
        .menu-list { list-style: none; padding: 0; margin: 20px 0; }
        .menu-item { padding: 15px 25px; display: flex; align-items: center; gap: 15px; color: #bdc3c7; text-decoration: none; transition: 0.3s; }
        .menu-item:hover, .menu-item.active { background: #34495e; color: white; border-left: 4px solid var(--primary-blue); }
        .main-content { flex: 1; }
        .header { background: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .content-body { padding: 30px; }
        
        /* Grid cho menu chính */
        .dashboard-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .stat-link { text-decoration: none; color: inherit; display: block; }
        .stat-link:hover .stat-card { transform: translateY(-5px); box-shadow: 0 8px 15px rgba(0,0,0,0.1); }
        .stat-card { background: white; padding: 25px; border-radius: 8px; display: flex; align-items: center; justify-content: space-between; transition: 0.3s; pointer-events: none; }
        .stat-info h3 { margin: 0; font-size: 0.9rem; color: #7f8c8d; text-transform: uppercase; }
        .stat-info p { margin: 5px 0 0; font-size: 1.1rem; font-weight: bold; }
        .stat-icon { font-size: 2rem; opacity: 0.4; }
        /* Container chính của user info */
    .user-info.dropdown {
        position: relative;
        display: inline-block;
    }

    /* Phần chữ "Xin chào" */
    .dropdown-trigger {
        padding: 10px;
        cursor: pointer;
    }

    /* Menu đăng xuất mặc định sẽ bị ẩn */
    .dropdown-content {
        display: none; 
        position: absolute;
        right: 0;
        background-color: white;
        min-width: 150px;
        box-shadow: 0px 8px 16px rgba(0,0,0,0.1);
        border-radius: 8px;
        z-index: 1000;
        overflow: hidden;
        border: 1px solid #eee;
    }

        /* Style cho nút đăng xuất bên trong menu */
        .dropdown-content a {
            color: #e74c3c; /* Màu đỏ cho nút đăng xuất */
            padding: 12px 16px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: 0.2s;
        }

        .dropdown-content a:hover {
            background-color: #f8f9fa;
        }

        /* HIỆU ỨNG: Khi di chuột vào khu vực Xin chào, menu sẽ hiện ra */
        .user-info.dropdown:hover .dropdown-content {
            display: block;
    }
    </style>
</head>
<body>
    <nav class="sidebar">
    <div class="sidebar-header"><i class="fa-solid fa-shield-halved"></i> PCX ADMIN</div>
    <ul class="menu-list">
        <li>
            <a href="index.php?controller=warehouse&action=index" class="menu-item <?php echo ($action == 'index') ? 'active' : ''; ?>">
                <i class="fa-solid fa-warehouse"></i> Quản lý kho
            </a>
        </li>
        
        <li>
            <a href="index.php?controller=warehouse&action=Import" class="menu-item <?php echo ($action == 'Import') ? 'active' : ''; ?>">
                <i class="fa-solid fa-file-import"></i> Tiến hành nhập kho
            </a>
        </li>
        <li>
            <a href="index.php?controller=warehouse&action=history" class="menu-item <?php echo ($action == 'History') ? 'active' : ''; ?>">
                <i class="fa-solid fa-clock-rotate-left"></i> Lịch sử nhập kho
            </a>
        </li>
        <li>
            <a href="index.php?controller=warehouse&action=inventory" class="menu-item <?php echo ($action == 'Inventory') ? 'active' : ''; ?>">
                <i class="fa-solid fa-boxes-stacked"></i> Quản lý tồn kho
            </a>
        </li>
    </ul>
    </nav>

    <div class="main-content">
        <header class="header">
            <div class="page-title"><strong><?php echo $page_title; ?></strong></div>
                <div class="user-info dropdown">
            <div class="dropdown-trigger">
                Xin chào, <strong><?= $_SESSION['full_name'] ?? 'Thủ kho' ?></strong> 
                <i class="fa-solid fa-caret-down"></i>
            </div>
            
            <div class="dropdown-content">
                <a href="index.php?controller=auth&action=logout">
                    <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
                </a>
            </div>
</div>
        </header>

        <div class="content-body">
            <?php 
                // NƠI HIỂN THỊ NỘI DUNG ĐỘNG
                if (isset($content_view) && file_exists($content_view)) {
                    include $content_view;
                } else {
                    echo "<h3>Chào mừng bạn đến với hệ thống kho!</h3>";
                }
            ?>
        </div>
    </div>
    
</body>
</html>