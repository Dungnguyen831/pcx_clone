<?php require_once 'views/client/layouts/header.php'; ?>

<style>
    .checkout-container {
        display: flex;
        max-width: 1100px;
        margin: 40px auto;
        gap: 50px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    }
    .checkout-left { flex: 6; }
    .checkout-right { 
        flex: 4; 
        background-color: #fafafa; 
        padding: 30px; 
        border-left: 1px solid #e1e1e1;
        height: fit-content;
    }
    .section-title { font-size: 1.2rem; margin-bottom: 20px; font-weight: 500; }
    .form-group { margin-bottom: 15px; }
    .form-control {
        width: 100%;
        padding: 12px;
        border: 1px solid #d9d9d9;
        border-radius: 5px;
        font-size: 14px;
        box-sizing: border-box;
    }
    .order-item {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }
    .order-item img {
        width: 60px;
        height: 60px;
        border: 1px solid #e1e1e1;
        border-radius: 8px;
        margin-right: 15px;
    }
    .item-info { flex-grow: 1; }
    .item-name { font-size: 14px; color: #333; }
    .item-price { font-size: 14px; font-weight: 500; }
    .summary-line {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        color: #717171;
        font-size: 14px;
    }
    .total-line {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #e1e1e1;
        font-size: 1.5rem;
        font-weight: 600;
    }
    .btn-complete {
        width: 100%;
        background-color: #197bbd;
        color: white;
        padding: 18px;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        margin-top: 20px;
    }
    .btn-complete:hover { background-color: #1568a0; }
    .cart-link { color: #197bbd; text-decoration: none; font-size: 14px; }
</style>

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
                <input type="text" name="shipping_address" class="form-control" placeholder="Địa chỉ (Số nhà, tên đường, phường/xã...)" required>
            </div>
            <div class="form-group">
                <textarea name="note" class="form-control" placeholder="Ghi chú (ví dụ: Giao giờ hành chính)" rows="3"></textarea>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
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
                <img src="assets/uploads/<?php echo $item['image']; ?>" alt="">
                <span style="position: absolute; top: -10px; right: 5px; background: #808080cc; color: white; border-radius: 50%; padding: 2px 8px; font-size: 12px;">
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
            <button type="button" style="padding: 10px 20px; background: #ebebeb; border: none; border-radius: 5px; color: #999;">Áp dụng</button>
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