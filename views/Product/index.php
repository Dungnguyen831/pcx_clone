<?php include __DIR__ . '/../layouts/header.php';
 ?>

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
                    <img src="<?php echo $p['Image']; ?>" alt="Product" class="p-img">
                    
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

<?php include __DIR__ . '/../layouts/footer.php'; ?>
