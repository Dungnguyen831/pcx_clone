<?php include 'views/layouts/header.php'; ?>

<section class="hero-banner">
    <div class="hero-content">
        <h2>Gamesense x Asuka Radar<br>Kunai Limited Edition</h2>
        <a href="#" class="btn-primary">XEM NGAY</a>
    </div>
</section>

<section class="category-strip">
    <div class="container">
        <div class="cat-grid">
            <div class="cat-item">
                <div class="cat-icon"><i class="fa-solid fa-computer-mouse"></i></div>
                <span class="cat-title">Chuột Gaming</span>
            </div>
            <div class="cat-item">
                <div class="cat-icon"><i class="fa-regular fa-keyboard"></i></div>
                <span class="cat-title">Bàn phím</span>
            </div>
            <div class="cat-item">
                <div class="cat-icon"><i class="fa-solid fa-square"></i></div>
                <span class="cat-title">Lót chuột</span>
            </div>
            <div class="cat-item">
                <div class="cat-icon"><i class="fa-solid fa-headphones"></i></div>
                <span class="cat-title">Tai nghe</span>
            </div>
             <div class="cat-item">
                <div class="cat-icon"><i class="fa-solid fa-chair"></i></div>
                <span class="cat-title">Ghế</span>
            </div>
        </div>
    </div>
</section>

<section class="products-section">
    <div class="container">
        <div class="section-header">
            <h3 class="section-title">Gear mới về - Cẩn thận dính ví</h3>
            <a href="#" class="view-all">Xem toàn bộ <i class="fa-solid fa-arrow-right"></i></a>
        </div>

        <div class="product-grid">
            <?php 
            // Kiểm tra xem biến $products có dữ liệu từ Controller gửi qua không
            // Nếu không có thì dùng dữ liệu giả để test giao diện
            if (!isset($products) || empty($products)) {
                $products = [
                    ['Name' => 'Chuột Lamzu Atlantis Og V2', 'Price' => 2450000, 'Image' => 'mouse1.jpg', 'Brand' => 'LAMZU'],
                    ['Name' => 'Bàn phím Wooting 60HE', 'Price' => 4500000, 'Image' => 'kb1.jpg', 'Brand' => 'WOOTING'],
                    ['Name' => 'Lót chuột Ninjutso NPC', 'Price' => 890000, 'Image' => 'pad1.jpg', 'Brand' => 'NINJUTSO'],
                    ['Name' => 'Pulsar X2V2 Mini', 'Price' => 2100000, 'Image' => 'mouse2.jpg', 'Brand' => 'PULSAR'],
                ];
            }

            foreach ($products as $p): 
            ?>
                <div class="product-card">
                    <span class="p-tag">Mới</span>
                    <img src="https://via.placeholder.com/300" alt="Product" class="p-img">
                    
                    <div class="p-info">
                        <div class="p-brand"><?php echo isset($p['Brand']) ? $p['Brand'] : 'GEAR'; ?></div>
                        <h4 class="p-name"><?php echo $p['Name']; ?></h4>
                        <div class="p-actions">
                            <span class="p-price"><?php echo number_format($p['Price']); ?>đ</span>
                            <div class="btn-cart"><i class="fa-solid fa-cart-plus"></i></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<div style="height: 50px;"></div>

<?php include 'views/layouts/footer.php'; ?>