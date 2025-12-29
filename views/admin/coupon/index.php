<?php
$isEdit = isset($coupon_edit);
$searchKey = $_GET['search'] ?? '';
?>

<div class="coupon-container">
    <div class="coupon-header">
        <h2><i class="fa-solid fa-ticket" style="color: #3498db;"></i> <?= $isEdit ? 'Chỉnh sửa mã giảm giá' : 'Quản lý mã giảm giá' ?></h2>
        <?php if ($isEdit): ?>
            <a href="index.php?controller=admin-coupon&search=<?= urlencode($searchKey) ?>" class="btn btn-secondary">Hủy sửa / Thêm mới</a>
        <?php endif; ?>
    </div>

    <div class="coupon-card <?= $isEdit ? 'edit-mode' : '' ?>">
        <form action="index.php?controller=admin-coupon&action=store" method="POST" class="coupon-form">
            <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= $coupon_edit['coupon_id'] ?>"><?php endif; ?>
            <input type="hidden" name="current_search" value="<?= htmlspecialchars($searchKey) ?>">

            <div class="form-group">
                <label>Mã Code</label>
                <input type="text" name="code" class="form-control" value="<?= $isEdit ? $coupon_edit['code'] : '' ?>" required placeholder="VD: GIAM50" style="text-transform: uppercase;">
            </div>

            <div class="form-group">
                <label>Loại giảm</label>
                <select name="discount_type" class="form-control">
                    <option value="fixed" <?= ($isEdit && $coupon_edit['discount_type'] == 'fixed') ? 'selected' : '' ?>>Số tiền (đ)</option>
                    <option value="percent" <?= ($isEdit && $coupon_edit['discount_type'] == 'percent') ? 'selected' : '' ?>>Phần trăm (%)</option>
                </select>
            </div>

            <div class="form-group">
                <label>Giá trị giảm</label>
                <input type="number" name="discount_value" class="form-control" value="<?= $isEdit ? $coupon_edit['discount_value'] : '' ?>" required>
            </div>

            <div class="form-group">
                <label>Đơn tối thiểu</label>
                <input type="number" name="min_order_value" class="form-control" value="<?= $isEdit ? $coupon_edit['min_order_value'] : '0' ?>">
            </div>

            <div class="form-group">
                <label>Lượt dùng tối đa</label>
                <input type="number" name="usage_limit" class="form-control" value="<?= $isEdit ? $coupon_edit['usage_limit'] : '100' ?>">
            </div>

            <div class="form-group">
                <label>Ngày bắt đầu</label>
                <input type="datetime-local" name="start_date" class="form-control" value="<?= $isEdit ? date('Y-m-d\TH:i', strtotime($coupon_edit['start_date'])) : '' ?>" required>
            </div>

            <div class="form-group">
                <label>Ngày kết thúc</label>
                <input type="datetime-local" name="end_date" class="form-control" value="<?= $isEdit ? date('Y-m-d\TH:i', strtotime($coupon_edit['end_date'])) : '' ?>" required>
            </div>

            <div class="form-group">
                <label>Trạng thái</label>
                <select name="status" class="form-control">
                    <option value="1" <?= ($isEdit && $coupon_edit['status'] == 1) ? 'selected' : '' ?>>Kích hoạt</option>
                    <option value="0" <?= ($isEdit && $coupon_edit['status'] == 0) ? 'selected' : '' ?>>Tạm dừng</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary" style="padding: 10px 40px; font-weight: bold;">
                    <?= $isEdit ? 'CẬP NHẬT MÃ' : 'TẠO MÃ KHUYẾN MÃI' ?>
                </button>
            </div>
        </form>
    </div>

    <div class="search-bar">
        <div style="font-weight: 600; color: #475569;"><i class="fa-solid fa-list"></i> Danh sách mã</div>
        <form action="index.php" method="GET" style="display: flex; gap: 8px;">
            <input type="hidden" name="controller" value="admin-coupon">
            <input type="text" name="search" class="form-control" value="<?= htmlspecialchars($searchKey) ?>" placeholder="Tìm mã khuyến mãi..." style="min-width: 250px; padding: 8px 15px;">
            <button type="submit" class="btn btn-primary" style="padding: 0 15px;">Tìm</button>
            <?php if ($searchKey): ?><a href="index.php?controller=admin-coupon" class="btn btn-secondary">X</a><?php endif; ?>
        </form>
    </div>

    <div class="coupon-table-wrapper">
        <table class="coupon-table">
            <thead>
                <tr>
                    <th>Mã Code</th>
                    <th>Giá trị giảm</th>
                    <th>Lượt dùng</th>
                    <th>Thời hạn</th>
                    <th>Trạng thái</th>
                    <th style="text-align:center;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($coupons)): foreach ($coupons as $c): ?>
                        <tr>
                            <td><span class="coupon-code-badge"><?= $c['code'] ?></span></td>
                            <td style="font-weight: 600;"><?= number_format($c['discount_value']) ?><?= $c['discount_type'] == 'fixed' ? 'đ' : '%' ?></td>
                            <td>
                                <div style="font-size: 11px;">Đã dùng <?= $c['used_count'] ?>/<?= $c['usage_limit'] ?></div>
                                <div class="progress-container">
                                    <?php $p = ($c['usage_limit'] > 0) ? min(100, ($c['used_count'] / $c['usage_limit']) * 100) : 0; ?>
                                    <div class="progress-bar" style="width: <?= $p ?>%;"></div>
                                </div>
                            </td>
                            <td style="font-size: 11px; color: #7f8c8d;">Hết hạn: <span style="color: #e74c3c;"><?= date('d/m/Y', strtotime($c['end_date'])) ?></span></td>
                            <td>
                                <span class="status-badge <?= $c['status'] == 1 ? 'status-active' : 'status-locked' ?>">
                                    <?= $c['status'] == 1 ? 'Đang chạy' : 'Đã khóa' ?>
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 10px; justify-content: center;">
                                    <a href="index.php?controller=admin-coupon&action=edit&id=<?= $c['coupon_id'] ?>&search=<?= urlencode($searchKey) ?>" style="color: #3498db;"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <a href="index.php?controller=admin-coupon&action=delete&id=<?= $c['coupon_id'] ?>&search=<?= urlencode($searchKey) ?>" onclick="return confirm('Xóa mã này?')" style="color: #e74c3c;"><i class="fa-solid fa-trash-can"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="6" style="padding: 30px; text-align: center; color: #94a3b8;">Không tìm thấy mã giảm giá nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>