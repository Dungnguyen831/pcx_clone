<?php include 'views/client/layouts/header.php'; ?>

<link rel="stylesheet" href="assets/css/reward.css?v=<?php echo time(); ?>">

<div class="container py-5" style="background: #fdfdfd; min-height: 800px;">

    <?php if (isset($_SESSION['flash_code'])): ?>
        <div class="flash-box">
            <h4 class="text-success"><i class="fa fa-gift"></i> THÀNH CÔNG!</h4>
            <p class="mb-0">Mã giảm giá của bạn là (Hãy sao chép ngay):</p>

            <div class="flash-code-text" id="codeArea"><?= $_SESSION['flash_code'] ?></div>

            <br>
            <button class="btn btn-success btn-sm" onclick="copyCode()">
                <i class="fa fa-copy"></i> Sao chép
            </button>
            <div class="text-danger small mt-2">⚠ Lưu ý: Mã sẽ ẩn đi khi bạn tải lại trang. Hãy lưu lại ngay!</div>
        </div>
        <script>
            function copyCode() {
                var code = document.getElementById("codeArea").innerText;
                navigator.clipboard.writeText(code);
                alert("Đã sao chép: " + code);
            }
        </script>
        <?php unset($_SESSION['flash_code']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger text-center shadow-sm"><?= $_SESSION['error'];
                                                                unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="point-banner">
        <div>
            <h5 class="text-muted mb-1">Điểm thưởng hiện có</h5>
            <h2 class="point-value"><?= number_format($current_points) ?></h2>
        </div>
        <div class="text-right d-none d-md-block">
            <p class="mb-0 text-muted small">Tích lũy khi mua hàng</p>
            <small>100.000đ = 1 Điểm</small>
        </div>
    </div>

    <h4 class="mb-3 font-weight-bold text-dark border-bottom pb-2">
        <i class="fa fa-ticket"></i> Kho Mã Giảm Giá
    </h4>

    <div class="coupon-grid">
        <?php if (empty($coupons)): ?>
            <div class="col-12 text-center text-muted py-5">Hiện chưa có mã giảm giá nào.</div>
        <?php else: ?>
            <?php foreach ($coupons as $c): ?>
                <div class="coupon-card">
                    <div class="c-header">
                        <div class="c-value">
                            Giảm <?= number_format($c['discount_value']) ?><?= $c['discount_type'] == 'percent' ? '%' : 'đ' ?>
                        </div>
                        <div class="c-min">Đơn từ: <?= number_format($c['min_order_value']) ?>đ</div>
                    </div>

                    <div class="c-body">
                        <div>
                            <?php if ($c['points_cost'] == 0): ?>
                                <span class="c-badge bg-free">Miễn phí</span>
                            <?php else: ?>
                                <span class="c-badge bg-cost">Đổi <?= number_format($c['points_cost']) ?> điểm</span>
                            <?php endif; ?>
                        </div>
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
                                <button type="button" class="btn btn-secondary btn-block" disabled style="width:100%; opacity:0.7;">Đã sử dụng</button>

                            <?php elseif ($c['points_cost'] > 0 && $current_points < $c['points_cost']): ?>
                                <button type="button" class="btn btn-light btn-block border" disabled style="width:100%;">Thiếu điểm</button>

                            <?php else: ?>
                                <?php
                                $btnClass = ($c['points_cost'] == 0) ? 'btn-success' : 'btn-warning text-white';
                                $btnText = ($c['points_cost'] == 0) ? 'Lấy Mã Ngay' : 'Đổi Điểm Ngay';
                                $confirm = ($c['points_cost'] == 0) ? 'Lấy mã này?' : 'Trừ ' . $c['points_cost'] . ' điểm để lấy mã?';
                                ?>
                                <button type="submit" class="btn <?= $btnClass ?> btn-block" style="width:100%; font-weight:600;" onclick="return confirm('<?= $confirm ?>');">
                                    <?= $btnText ?>
                                </button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <h4 class="mb-3 font-weight-bold text-dark border-bottom pb-2 mt-5">
        <i class="fa fa-history"></i> Ví Của Tôi (Đã sử dụng)
    </h4>

    <?php if (empty($history)): ?>
        <div class="text-center p-4 bg-white border rounded">
            <p class="text-muted mb-0">Bạn chưa sử dụng mã giảm giá nào.</p>
        </div>
    <?php else: ?>
        <div class="history-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th width="25%">Mã Code</th>
                        <th width="30%">Giá trị giảm</th>
                        <th width="25%">Ngày sử dụng</th>
                        <th width="20%">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $h): ?>
                        <tr>
                            <td><span class="code-hl"><?= $h['code'] ?></span></td>
                            <td>
                                <strong style="color:#e74c3c;">
                                    Giảm <?= number_format($h['discount_value']) ?><?= $h['discount_type'] == 'percent' ? '%' : 'đ' ?>
                                </strong>
                            </td>
                            <td><?= date('H:i d/m/Y', strtotime($h['used_at'])) ?></td>
                            <td><span class="badge-used">Đã dùng</span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="text-muted small mt-2">
            * Danh sách này hiển thị các mã bạn đã áp dụng thành công vào đơn hàng.
        </p>
    <?php endif; ?>

</div>

<?php include 'views/client/layouts/footer.php'; ?>