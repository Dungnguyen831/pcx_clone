<?php require_once 'views/client/layouts/header.php'; ?>

<link rel="stylesheet" href="assets/css/checkout.css">

<div class="checkout-container">
    <div class="checkout-left">
        <form action="index.php?controller=cart&action=processCheckout" method="POST">
            <h3 class="section-title">Thông tin liên hệ</h3>
            <div class="form-group">
                <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>

            <h3 class="section-title" style="margin-top: 30px;">Giao hàng</h3>
            <div class="form-group">
                <input type="text" name="customer_name" class="form-control" placeholder="Tên" required>
            </div>
            <div class="form-group">
                <input type="text" name="customer_phone" class="form-control" placeholder="Số điện thoại" required>
            </div>
            <div class="form-group">
                <input type="text" name="shipping_address" class="form-control" placeholder="Địa chỉ (Số nhà, tên đường...)" required>
            </div>
            <div class="form-group">
                <textarea name="note" class="form-control" placeholder="Ghi chú (ví dụ: Giao giờ hành chính)" rows="3"></textarea>
            </div>

            <div style="margin-top: 20px;">
                <a href="index.php?controller=cart&action=index" class="cart-link">
                    <i class="fa fa-chevron-left"></i> Quay lại giỏ hàng
                </a>
            </div>
    </div>

    <div class="checkout-right">
        <?php 
        $total = 0;
        foreach ($cart as $item): 
            $subtotal = $item['price'] * $item['quantity'];
            $total += $subtotal;
        ?>
        <div class="order-item">
            <div style="position: relative;">
                <img src="assets/uploads/products/<?php echo $item['image']; ?>" alt="">
                <span class="quantity-badge">
                    <?php echo $item['quantity']; ?>
                </span>
            </div>
            <div class="item-info">
                <div class="item-name"><?php echo $item['name']; ?></div>
            </div>
            <div class="item-price"><?php echo number_format($subtotal, 0, ',', '.'); ?> ₫</div>
        </div>
        <?php endforeach; ?>

        <div style="margin: 20px 0; display: flex; gap: 10px;">
            <input type="text" class="form-control" placeholder="Mã giảm giá">
            <button type="button" style="padding: 10px 20px; background: #ebebeb; border: none; border-radius: 5px; color: #999; cursor: pointer;">Áp dụng</button>
        </div>

        <div class="summary-line">
            <span>Tạm tính</span>
            <span><?php echo number_format($total, 0, ',', '.'); ?> ₫</span>
        </div>
        <div class="summary-line">
            <span>Phí vận chuyển</span>
            <span>Miễn phí</span>
        </div>

        <div class="total-line">
            <span style="font-size: 16px; color: #4b4b4b;">Tổng cộng</span>
            <div style="text-align: right;">
                <span style="font-size: 12px; color: #717171; font-weight: normal;">VND</span> 
                <?php echo number_format($total, 0, ',', '.'); ?> ₫
            </div>
        </div>

        <input type="hidden" name="total_money" value="<?php echo $total; ?>">
        
        <button type="submit" class="btn-complete">ĐẶT HÀNG</button>
        </form>
    </div>
</div>

<?php require_once 'views/client/layouts/footer.php'; ?>