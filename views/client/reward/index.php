<?php include 'views/client/layouts/header.php'; ?>
<link rel="stylesheet" href="assets/css/reward.css?v=<?php echo time(); ?>">

<div class="reward-wrapper">

    <?php if (isset($_SESSION['flash_code'])): ?>
        <div class="flash-box">
            <h4 class="bg-free" style="background:none; border:none; margin-top:0;">
                <i class="fa fa-gift"></i> THÀNH CÔNG!
            </h4>
            <p>Mã giảm giá của bạn là (Hãy sao chép ngay):</p>
            <div class="flash-code-text" id="codeArea"><?= $_SESSION['flash_code'] ?></div>
            <br>
            <button class="btn-copy-wallet" style="background:#059669; color:#fff; padding:10px 20px;" onclick="copyCode()">
                <i class="fa fa-copy"></i> Sao chép mã
            </button>
        </div>
        <script>
            function copyCode() {
                var code = document.getElementById("codeArea").innerText;
                navigator.clipboard.writeText(code);
                alert("Đã sao chép mã: " + code);
            }
        </script>
        <?php unset($_SESSION['flash_code']);
        unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="point-banner">
        <div>
            <h5 class="c-min" style="font-size:16px; margin-bottom:5px;">Điểm thưởng hiện có</h5>
            <h2 class="point-value"><?= number_format($current_points) ?></h2>
        </div>
        <div class="d-none d-md-block">
            <p class="c-min" style="margin:0;">Tích lũy khi mua hàng</p>
            <small class="c-min">100.000đ = 1 Điểm</small>
        </div>
    </div>

    <h4 class="section-title">
        <i class="fa fa-ticket"></i> Kho Mã Giảm Giá
    </h4>

    <div class="coupon-grid">
        <?php foreach ($coupons as $c): ?>
            <div class="coupon-card">
                <div class="c-header">
                    <div class="c-value">Giảm <?= number_format($c['discount_value']) ?><?= $c['discount_type'] == 'percent' ? '%' : 'đ' ?></div>
                    <div class="c-min">Đơn từ: <?= number_format($c['min_order_value']) ?>đ</div>
                </div>

                <div class="c-body">
                    <span class="c-badge <?= ($c['points_cost'] == 0) ? 'bg-free' : 'bg-cost' ?>">
                        <?= ($c['points_cost'] == 0) ? 'Miễn phí' : 'Đổi ' . number_format($c['points_cost']) . ' điểm' ?>
                    </span>
                    <div class="c-row">
                        <span><i class="fa fa-clock"></i> Hạn dùng:</span>
                        <span><?= date('d/m/Y', strtotime($c['end_date'])) ?></span>
                    </div>
                    <div class="c-row">
                        <span><i class="fa fa-layer-group"></i> Còn lại:</span>
                        <span><?= $c['usage_limit'] - $c['used_count'] ?> lượt</span>
                    </div>
                </div>

                <div class="c-footer">
                    <form action="index.php?controller=reward&action=redeem" method="POST">
                        <input type="hidden" name="coupon_id" value="<?= $c['coupon_id'] ?>">

                        <?php if ($c['is_used_by_me']): ?>
                            <button type="button" class="btn-full btn-owned" disabled>Đã sở hữu</button>
                        <?php elseif ($c['points_cost'] > $current_points): ?>
                            <button type="button" class="btn-full btn-disabled" disabled>Thiếu điểm</button>
                        <?php else: ?>
                            <?php $btnBg = ($c['points_cost'] == 0) ? '#10b981' : '#f59e0b'; ?>
                            <button type="submit" class="btn-full btn-redeem" style="background: <?= $btnBg ?>;" onclick="return confirm('Xác nhận đổi mã này?');">
                                <?= ($c['points_cost'] == 0) ? 'Lấy Mã Ngay' : 'Đổi Điểm Ngay' ?>
                            </button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <h4 class="section-title section-title-wallet">
        <i class="fa fa-wallet"></i> Ví Voucher Của Tôi (Chưa sử dụng)
    </h4>

    <?php if (empty($owned_coupons)): ?>
        <div class="empty-state">
            <i class="fa fa-ticket-alt empty-icon"></i>
            <p class="c-min" style="font-size:16px;">Ví voucher của bạn đang trống.</p>
            <small class="c-min">Hãy tích lũy điểm để đổi thêm mã giảm giá!</small>
        </div>
    <?php else: ?>
        <div class="history-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Mã Code</th>
                        <th>Giá trị giảm</th>
                        <th>Ngày nhận</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($owned_coupons as $h): ?>
                        <tr>
                            <td><span class="code-hl"><?= $h['code'] ?></span></td>
                            <td><strong class="text-danger-custom">Giảm <?= number_format($h['discount_value']) ?><?= $h['discount_type'] == 'percent' ? '%' : 'đ' ?></strong></td>
                            <td><?= date('d/m/Y', strtotime($h['owned_at'])) ?></td>
                            <td>
                                <button onclick="copyWalletCode('<?= $h['code'] ?>')" class="btn-copy-wallet">Copy</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>

<script>
    function copyWalletCode(code) {
        navigator.clipboard.writeText(code);
        alert("Đã sao chép mã: " + code);
    }
</script>

<?php include 'views/client/layouts/footer.php'; ?>