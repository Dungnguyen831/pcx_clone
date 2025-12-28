<?php require_once 'views/client/layouts/header.php'; ?>

<div class="container" style="margin-top: 40px; margin-bottom: 40px;">
    <h2 style="margin-bottom: 30px;">🚚 Thông tin giao hàng</h2>
    <form action="index.php?controller=cart&action=processCheckout" method="POST">
        <div class="row">
            <div class="col-md-7">
                <div class="form-group mb-3">
                    <label>Họ và tên người nhận</label>
                    <input type="text" name="full_name" class="form-control" required placeholder="Nguyễn Văn A">
                </div>
                <div class="form-group mb-3">
                    <label>Số điện thoại</label>
                    <input type="text" name="phone" class="form-control" required placeholder="090xxxxxxx">
                </div>
                <div class="form-group mb-3">
                    <label>Địa chỉ nhận hàng</label>
                    <textarea name="address" class="form-control" rows="3" required placeholder="Số nhà, tên đường, phường/xã..."></textarea>
                </div>
                <div class="form-group mb-3">
                    <label>Ghi chú</label>
                    <textarea name="note" class="form-control" rows="2" placeholder="Ví dụ: Giao giờ hành chính"></textarea>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card p-3 shadow-sm">
                    <h4>Đơn hàng của bạn</h4>
                    <hr>
                    <?php 
                    $total = 0;
                    foreach ($_SESSION['cart'] as $item): 
                        $total += $item['price'] * $item['quantity'];
                    ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span><?php echo $item['name']; ?> (x<?php echo $item['quantity']; ?>)</span>
                        <strong><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>đ</strong>
                    </div>
                    <?php endforeach; ?>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <h5>Tổng cộng:</h5>
                        <h4 class="text-danger"><?php echo number_format($total, 0, ',', '.'); ?>đ</h4>
                    </div>
                    <button type="submit" class="btn btn-success btn-lg w-100 mt-3">XÁC NHẬN ĐẶT HÀNG</button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php require_once 'views/client/layouts/footer.php'; ?>