<?php require_once 'views/client/layouts/header.php'; ?>

<section class="banner-section" style="margin-bottom: 30px;">
    <div class="container">
        <img src="assets/images/banner-sample.jpg" alt="Banner" style="width: 100%; border-radius: 10px;">
    </div>
</section> 

<section class="products-section">
    <div class="container">
        <h2 class="section-title">✨ Gear Mới Về</h2>
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <?php 
                
                    // Logic kiểm tra tồn kho ngay tại View
                    $is_out_of_stock = ($product['quantity'] <= 0);
                ?>

                <div class="product-card">
                    <div class="card-image">
                        <a href="index.php?controller=product&action=detail&id=<?php echo $product['product_id']; ?>">
                            <img src="assets/uploads/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                        </a>
                        
                        <?php if ($is_out_of_stock): ?>
                            <span class="badge-stock out">Hết hàng</span>
                        <?php else: ?>
                            <span class="badge-stock in">Còn <?php echo $product['quantity']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="card-info">
                        <div class="brand-name"><?php echo $product['brand_name']; ?></div>
                        <h3 class="product-name">
                            <a href="index.php?controller=product&action=detail&id=<?php echo $product['product_id']; ?>">
                                <?php echo $product['name']; ?>
                            </a>
                        </h3>
                        <div class="price"><?php echo number_format($product['price'], 0, ',', '.'); ?>đ</div>
                        
                        <div class="card-action">
                            <?php if ($is_out_of_stock): ?>
                                <button class="btn-disabled" disabled>Hết hàng</button>
                            <?php else: ?>
                                <a href="index.php?controller=cart&action=add&id=<?php echo $product['product_id']; ?>" class="btn-buy">
                                    Thêm vào giỏ <i class="fa-solid fa-cart-plus"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once 'views/client/layouts/footer.php'; ?>