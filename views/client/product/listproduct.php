<?php require_once 'views/client/layouts/header.php'; ?>

<div class="container" style="display: flex; gap: 30px; margin-top: 30px;">
    
    <aside class="sidebar" style="flex: 1; max-width: 250px;">
        <h3 style="border-bottom: 2px solid #333; padding-bottom: 10px;">Danh mục</h3>
        <ul style="list-style: none; padding: 0; margin-top: 15px;">
            <li style="margin-bottom: 10px;">
                <a href="index.php?controller=product&action=index" style="text-decoration: none; color: #333; font-weight: bold;">Tất cả sản phẩm</a>
            </li>
            <?php foreach ($categories as $cat): ?>
                <li style="margin-bottom: 10px; padding: 8px; border-bottom: 1px solid #eee;">
                    <a href="index.php?controller=product&action=index&cat_id=<?php echo $cat['category_id']; ?>" 
                       style="text-decoration: none; color: #555; display: block;">
                        <?php echo $cat['name']; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </aside>

    <main class="product-content" style="flex: 3;">
        <h2 style="margin-bottom: 20px;">Sản phẩm</h2>
        <div class="product-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
            <?php if (empty($products)): ?>
                <p>Không có sản phẩm nào trong mục này.</p>
            <?php else: ?>
                <!-- điều hướng sang chi tiết sản phẩm -->
                <?php foreach ($products as $product): ?>
                    <div class="product-card" style="border: 1px solid #ddd; padding: 15px; border-radius: 8px; text-align: center;">
                        
                        <a href="index.php?controller=product&action=detail&id=<?php echo $product['product_id']; ?>">
                            <img src="assets/uploads/<?php echo $product['image']; ?>" style="width: 100%; height: 200px; object-fit: contain;">
                        </a>

                        <h4 style="margin: 10px 0;">
                            <a href="index.php?controller=product&action=detail&id=<?php echo $product['product_id']; ?>" style="text-decoration: none; color: #333;">
                                <?php echo $product['name']; ?>
                            </a>
                        </h4>

                        <div style="color: #e74c3c; font-weight: bold;">
                            <?php echo number_format($product['price'], 0, ',', '.'); ?>đ
                        </div>

                        <a href="index.php?controller=product&action=detail&id=<?php echo $product['product_id']; ?>" 
                        style="display: block; margin-top: 10px; background: #2563eb; color: #fff; text-decoration: none; padding: 8px; border-radius: 5px;">
                        Xem chi tiết
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php require_once 'views/client/layouts/footer.php'; ?>