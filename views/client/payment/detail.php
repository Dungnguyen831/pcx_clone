<?php require_once 'views/client/layouts/header.php'; ?>

<link rel="stylesheet" href="assets/css/checkout.css">

<div class="container checkout-container">
    <form action="index.php?controller=checkout&action=processCheckout" method="POST">
        <div class="row">
            <div class="col-md-7">
                <div class="p-3">
                    <h3 class="mb-4" style="color: #2c3e50;">Phong Cách Xanh</h3>
                    
                    <div class="section-title">Thông tin liên hệ</div>
                    <div class="mb-3">
                        <input type="email" class="form-control" placeholder="Email (không bắt buộc)">
                    </div>
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="news" checked>
                        <label class="form-check-label" for="news" style="font-size: 0.9rem;">Gửi cho tôi tin tức và ưu đãi qua email</label>
                    </div>

                    <div class="section-title">Giao hàng</div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="small text-muted">Họ và tên</label>
                            <input type="text" name="full_name" class="form-control" required placeholder="Nguyễn Văn A">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="small text-muted">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" required placeholder="090xxxxxxx">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="small text-muted">Địa chỉ nhận hàng</label>
                            <textarea name="address" class="form-control" rows="3" required placeholder="Số nhà, tên đường, phường/xã..."></textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="small text-muted">Ghi chú (tùy chọn)</label>
                            <textarea name="note" class="form-control" rows="2" placeholder="Ví dụ: Giao giờ hành chính"></textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-complete w-100 mt-4">HOÀN TẤT ĐẶT HÀNG</button>
                </div>
            </div>

            <div class="col-md-5 order-summary p-4">
                <div class="order-items-list">
                    <?php 
                    $total = 0;
                    foreach ($cart as $item): 
                        $subtotal = $item['price'] * $item['quantity'];
                        $total += $subtotal;
                    ?>
                    <div class="product-item">
                        <div class="product-img-wrapper">
                            <img src="assets/uploads/<?php echo $item['image']; ?>" class="product-img">
                            <span class="product-qty-badge"><?php echo $item['quantity']; ?></span>
                        </div>
                        <div class="flex-grow-1">
                            <div class="small fw-bold"><?php echo $item['name']; ?></div>
                        </div>
                        <div class="text-end fw-bold">
                            <?php echo number_format($subtotal, 0, ',', '.'); ?>đ
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <hr class="my-4">
                
                <div class="d-flex gap-2 mb-4">
                    <input type="text" class="form-control" placeholder="Mã giảm giá">
                    <button type="button" class="btn btn-light border px-3">Áp dụng</button>
                </div>

                <div class="total-line">
                    <span>Tạm tính</span>
                    <span><?php echo number_format($total, 0, ',', '.'); ?>đ</span>
                </div>
                <div class="total-line">
                    <span>Phí vận chuyển</span>
                    <span class="text-success">Miễn phí</span>
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-between align-items-center">
                    <span class="h5 mb-0">Tổng cộng</span>
                    <div class="text-end">
                        <small class="text-muted">VND</small>
                        <span class="total-final ms-2"><?php echo number_format($total, 0, ',', '.'); ?>đ</span>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php require_once 'views/client/layouts/footer.php'; ?>