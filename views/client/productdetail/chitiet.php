<?php require_once 'views/client/layouts/header.php'; ?>

<div class="container" style="margin-top: 50px; margin-bottom: 50px;">
    <div class="product-detail-wrapper" style="display: flex; gap: 40px; background: #fff; padding: 20px; border-radius: 10px;">
        
        <div class="product-image" style="flex: 1;">
            <img src="assets/uploads/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" style="width: 100%; border-radius: 10px; border: 1px solid #eee;">
        </div>

        <div class="product-info" style="flex: 1;">
            <nav style="font-size: 14px; color: #888; margin-bottom: 10px;">
                Trang chủ / <?php echo $product['brand_name']; ?> / <?php echo $product['name']; ?>
            </nav>
            
            <h1 style="font-size: 28px; margin-bottom: 15px;"><?php echo $product['name']; ?></h1>
            
            <div class="price" style="font-size: 24px; color: #e74c3c; font-weight: bold; margin-bottom: 20px;">
                <?php echo number_format($product['price'], 0, ',', '.'); ?>đ
            </div>

            <div class="stock-status" style="margin-bottom: 20px;">
                <?php if ($product['quantity'] > 0): ?>
                    <span style="color: #27ae60;">● Còn hàng (<?php echo $product['quantity']; ?> sản phẩm)</span>
                <?php else: ?>
                    <span style="color: #c0392b;">● Hết hàng</span>
                <?php endif; ?>
            </div>

            <div class="description" style="margin-bottom: 30px; line-height: 1.6; color: #555;">
                <h4 style="margin-bottom: 10px;">Mô tả sản phẩm:</h4>
                <p><?php echo nl2br($product['description']); ?></p>
            </div>

            <div class="action-buttons">
                <?php if ($product['quantity'] > 0): ?>
                    <a href="index.php?controller=cart&action=add&id=<?php echo $product['product_id']; ?>" 
                       style="background: #2563eb; color: #fff; padding: 15px 30px; border-radius: 5px; text-decoration: none; font-weight: bold; display: inline-block;">
                        THÊM VÀO GIỎ HÀNG <i class="fa-solid fa-cart-plus"></i>
                    </a>
                <?php else: ?>
                    <button disabled style="background: #ccc; color: #fff; padding: 15px 30px; border-radius: 5px; border: none; cursor: not-allowed;">
                        HẾT HÀNG
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/client/layouts/footer.php'; ?>