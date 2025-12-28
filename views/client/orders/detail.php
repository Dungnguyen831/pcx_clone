<?php require_once 'views/client/layouts/header.php'; ?>

<?php
    $status_class = 'pending';
    $status_text = 'Chờ xử lý';
    
    // Giả sử: 0=Chờ, 1=Đang giao, 2=Hoàn thành, 3=Hủy
    // Bạn hãy chỉnh lại số case khớp với Database của bạn
    switch ($order['status']) {
        case 0: $status_class = 'pending'; $status_text =  'Chờ xác nhận'; break;
        case 1: $status_class = 'comfirmed'; $status_text = 'Đã xác nhận'; break;
        case 2: $status_class = 'shipping'; $status_text= 'Đang vận chuyển'; break;
        case 3: $status_class = 'completed'; $status_text = 'Giao thành công'; break;
        case 4: $status_class = 'cancelled'; $status_text = 'Đã hủy'; break;
        default :$status_text = 'Không xác định'; 
    }
?>

<div class="container" style="margin-top: 30px; min-height: 500px;">
    
    <div class="order-header">
        <div>
            <h2 style="margin-bottom: 5px;">Chi tiết đơn hàng #<?= $order['order_id'] ?></h2>
            <span class="status-badge <?= $status_class ?>"><?= $status_text ?></span>
            <span style="color: #777; font-size: 13px; margin-left: 10px;">
                Ngày đặt: <?= date("d/m/Y H:i", strtotime($order['created_at'])) ?>
            </span>
        </div>
        <a href="index.php?controller=order&action=index" class="btn" style="background: #eee; color: #333;">
            <i class="fa-solid fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="order-layout">
        
        <div class="col-left">
            <div class="info-card">
                <h4>Sản phẩm đã mua</h4>
                
                <?php foreach ($items as $i): ?>
                    <div class="order-item">
                        <img src="assets/uploads/products/<?= $i['image'] ?>" class="item-img" alt="<?= $i['name'] ?>">
                        
                        <div class="item-info">
                            <div class="item-name"><?= $i['name'] ?></div>
                            <div class="item-meta">
                                Số lượng: x<?= $i['quantity'] ?>
                            </div>
                        </div>

                        <div class="item-price">
                            <?= number_format($i['total_price'], 0, ',', '.') ?>đ
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="col-right">
            
            <div class="info-card">
                <h4>Địa chỉ nhận hàng</h4>
                <p style="font-weight: 600; margin-bottom: 5px;"><?= $order['customer_name'] ?></p>
                <p style="color: #555; font-size: 14px; margin-bottom: 5px;">
                    <i class="fa-solid fa-phone" style="width: 20px;"></i> <?= $order['customer_phone'] ?>
                </p>
                <p style="color: #555; font-size: 14px;">
<i class="fa-solid fa-location-dot" style="width: 20px;"></i> <?= $order['shipping_address'] ?>
                </p>
            </div>

            <div class="info-card">
                <h4>Tổng thanh toán</h4>
                <div class="summary-row">
                    <span>Tạm tính:</span>
                    <span><?= number_format($order['final_money'], 0, ',', '.') ?>đ</span>
                </div>
                <div class="summary-row">
                    <span>Phí vận chuyển:</span>
                    <span>Miễn phí</span>
                </div>
                
                <div class="summary-row total">
                    <span>Tổng cộng:</span>
                    <span><?= number_format($order['final_money'], 0, ',', '.') ?>đ</span>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once 'views/client/layouts/footer.php'; ?>