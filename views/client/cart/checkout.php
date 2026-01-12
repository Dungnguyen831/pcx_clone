<?php require_once 'views/client/layouts/header.php'; ?>

<link rel="stylesheet" href="assets/css/checkout.css">

<form action="index.php?controller=cart&action=processCheckout" method="POST">

    <div class="checkout-container">
        <div class="checkout-left">
            <h3 class="section-title">Thông tin liên hệ</h3>

            <?php if (isset($_GET['msg'])): ?>
                <div style="padding: 10px; margin-bottom: 15px; border-radius: 5px; font-size: 13px;
                    background: <?php echo $_GET['type'] == 'success' ? '#d4edda' : '#f8d7da'; ?>; 
                    color: <?php echo $_GET['type'] == 'success' ? '#155724' : '#721c24'; ?>;">
                    <?php echo htmlspecialchars($_GET['msg']); ?>
                </div>
            <?php endif; ?>

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

            <div style="margin: 20px 0; border-bottom: 1px solid #eee; padding-bottom: 20px;">
                <?php if (isset($_SESSION['coupon'])): ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; background: #e8f5e9; padding: 10px; border-radius: 5px; border: 1px dashed #4caf50;">
                        <div>
                            <span style="color: #2e7d32; font-weight: bold;">
                                <i class="fa-solid fa-ticket"></i> <?php echo $_SESSION['coupon']['code']; ?>
                            </span>
                            <div style="font-size: 12px; color: #666;">Đã áp dụng</div>
                        </div>
                        <a href="index.php?controller=cart&action=removeCoupon" style="color: #c62828; font-size: 13px; text-decoration: underline;">Xóa</a>
                    </div>
                <?php else: ?>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" id="couponInput" class="form-control" placeholder="Mã giảm giá">

                        <button type="button" id="btnApplyCoupon"
                            style="padding: 10px 20px; background: #ebebeb; border: none; border-radius: 5px; color: #555; cursor: pointer; font-weight: bold;">
                            Áp dụng
                        </button>
                    </div>
                <?php endif; ?>
            </div>

            <?php
            $discount_money = 0;
            if (isset($_SESSION['coupon'])) {
                $c = $_SESSION['coupon'];

                // Sửa logic so sánh chuỗi thay vì số 1, 2
                // Kiểm tra xem database lưu 'percent' hay là 'fixed'
                if ($c['discount_type'] == 'percent') { // Giảm theo %
                    $discount_money = $total * ($c['discount_value'] / 100);

                    // (Optional) Giới hạn số tiền giảm tối đa nếu cần
                    // if ($discount_money > 50000) $discount_money = 50000; 

                } elseif ($c['discount_type'] == 'fixed') { // Giảm tiền mặt
                    $discount_money = $c['discount_value'];
                }
            }

            $final_total = $total - $discount_money;
            if ($final_total < 0) $final_total = 0;
            ?>

            <div class="summary-line">
                <span>Tạm tính</span>
                <span><?php echo number_format($total, 0, ',', '.'); ?> ₫</span>
            </div>

            <?php if ($discount_money > 0): ?>
                <div class="summary-line" style="color: #212221;">
                    <span>Giảm giá</span>
                    <span>-<?php echo number_format($discount_money, 0, ',', '.'); ?> ₫</span>
                </div>
            <?php endif; ?>

            <div class="summary-line">
                <span>Phí vận chuyển</span>
                <span>Miễn phí</span>
            </div>

            <div class="total-line">
                <span style="font-size: 16px; color: #4b4b4b;">Tổng cộng</span>
                <div style="text-align: right;">
                    <span style="font-size: 12px; color: #717171; font-weight: normal;">VND</span>
                    <?php echo number_format($final_total, 0, ',', '.'); ?> ₫
                </div>
            </div>

            <input type="hidden" name="total_money" value="<?php echo $final_total; ?>">
            <input type="hidden" name="discount_money" value="<?php echo $discount_money; ?>">

            <button type="submit" class="btn-complete">ĐẶT HÀNG</button>
        </div>
    </div>
</form>
<script src="assets/js/checkout.js"></script>

<?php require_once 'views/client/layouts/footer.php'; ?>