<?php require_once 'views/client/layouts/header.php'; ?>

<?php
// Xác định trạng thái hiện tại
$currentStatus = $_GET['status'] ?? 'all';

// Các tab trạng thái
$tabs = [
    'all' => 'Tất cả',
    0 => 'Chờ xác nhận',
    1 => 'Chờ lấy hàng',
    2 => 'Đang giao',
    3 => 'Đã giao',
    4 => 'Đã hủy'
];
?>

<div class="container" style="margin-top: 30px; min-height: 500px;">
    <h2 class="section-title" style="margin-bottom: 25px;">
        <i class="fa-solid fa-clock-rotate-left"></i> Lịch sử đơn hàng
    </h2>

<!-- ===== ORDER STATUS TABS ===== -->
    <div class="order-tab-wrapper">
        <div class="order-tab-bar">
            <?php foreach ($tabs as $key => $label): 
                $active = ((string)$currentStatus === (string)$key) ? 'active' : '';
                $url = ($key === 'all')
                    ? 'index.php?controller=order&action=index'
                    : 'index.php?controller=order&action=index&status=' . $key;
            ?>
                <a href="<?= $url ?>" class="order-tab-item <?= $active ?>">
                    <?= $label ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if (empty($orders)): ?>
        <div style="text-align: center; padding: 50px; background: #fff; border-radius: 8px;">
            <img src="https://cdn-icons-png.flaticon.com/512/2038/2038854.png" width="100" style="opacity: 0.5; margin-bottom: 20px;">
            <p style="color: #777; margin-bottom: 20px;">Bạn chưa có đơn hàng nào!</p>
            <a href="index.php?controller=product&action=index" class="btn btn-primary">
                Mua sắm ngay
            </a>
        </div>
    
    <?php else: ?>
        <div class="order-list">
            <?php foreach ($orders as $o): ?>
                
                <?php
                    $s_badge = 'pending'; $s_text = 'Chờ xử lý';
                    switch ($o['status']) {
                        case 0: $s_badge = 'pending'; $s_text =  'Chờ xác nhận'; break;
                        case 1: $s_badge = 'comfirmed'; $s_text = 'Đã xác nhận'; break;
                        case 2: $s_badge = 'shipping'; $s_text = 'Đang vận chuyển'; break;
                        case 3: $s_badge = 'completed'; $s_text = 'Giao thành công'; break;
                        case 4: $s_badge = 'cancelled'; $s_text = 'Đã hủy'; break;
                        default : $s_text = 'Không xác định'; 
                    }
                ?>

                <div class="order-history-card">
                    <div class="order-card-header">
                        <div class="order-id">
                            <i class="fa-solid fa-receipt"></i> Đơn hàng #<?= $o['order_id'] ?>
                        </div>
                        <span class="status-badge <?= $s_badge ?>"><?= $s_text ?></span>
                    </div>

                    <div class="order-card-body">
                        <div class="order-date">
                            <i class="fa-regular fa-calendar"></i> 
                            Ngày đặt: <?= date('d/m/Y H:i', strtotime($o['created_at'])) ?>
                        </div>
                        <div class="order-total">
                            <span>Tổng tiền:</span>
                            <strong><?= number_format($o['final_money'], 0, ',', '.') ?>đ</strong>
                        </div>
                    </div>

                    <div class="order-card-footer">
                        <a href="index.php?controller=order&action=detail&id=<?= $o['order_id'] ?>" class="btn-sm btn-outline">
                            Xem chi tiết
                        </a>

                        <!-- NÚT ĐÃ NHẬN: chỉ hiện khi đang giao -->
                        <?php if ($o['status'] == 2): ?>
                            <a href="index.php?controller=order&action=received&id=<?= $o['order_id'] ?>"
                            class="btn-sm btn-received"
                            onclick="return confirm('Xác nhận bạn đã nhận được hàng?')">
                                Đã nhận
                            </a>
                        <?php endif; ?>

                        <!-- NÚT HỦY: chỉ khi chờ xác nhận -->
                        <?php if ($o['status'] == 0): ?>
                            <a href="index.php?controller=order&action=cancel&id=<?= $o['order_id'] ?>" 
                               class="btn-sm" 
                               style="background: #fff0f0; color: #e74c3c; border: 1px solid #fadbd8;"
                               onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?')">
                               Hủy đơn
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'views/client/layouts/footer.php'; ?>