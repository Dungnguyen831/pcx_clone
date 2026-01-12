<?php
// Nhúng file CSS (Đường dẫn phải đúng với project của bạn)
echo '<link rel="stylesheet" href="assets/css/admin-coupons.css">';

// Chuẩn bị dữ liệu
$isEdit = isset($coupon_edit) && !empty($coupon_edit['coupon_id']);
$formData = $coupon_edit ?? [];
$keyword      = $filters['keyword'] ?? '';
$statusFilter = $filters['status'] ?? '';
$typeFilter   = $filters['discount_type'] ?? '';
if (!isset($errors)) $errors = [];
?>

<div class="coupon-container">
    <div class="coupon-header">
        <h2><i class="fa-solid fa-ticket" style="color: #3498db;"></i> <?= $isEdit ? 'Chỉnh sửa mã giảm giá' : 'Quản lý mã giảm giá' ?></h2>
        <?php if ($isEdit || (!empty($formData) && empty($formData['coupon_id']))): ?>
            <a href="index.php?controller=admin-coupon" class="btn btn-secondary"><i class="fa-solid fa-xmark"></i> Hủy bỏ / Thêm mới</a>
        <?php endif; ?>
    </div>

    <div class="coupon-card <?= $isEdit ? 'edit-mode' : '' ?>">
        <form action="index.php?controller=admin-coupon&action=store" method="POST" class="coupon-form">
            <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= $formData['coupon_id'] ?>"><?php endif; ?>
            <input type="hidden" name="current_search" value="<?= htmlspecialchars($keyword) ?>">

            <div class="form-group">
                <label>Mã Code <span style="color:red">*</span></label>
                <input type="text" name="code" class="form-control <?= isset($errors['code']) ? 'is-invalid' : '' ?> <?= $isEdit ? 'readonly-field' : '' ?>"
                    value="<?= $formData['code'] ?? '' ?>" <?= $isEdit ? 'readonly' : '' ?> style="text-transform: uppercase;" placeholder="VD: SALE2024">
                <?php if (isset($errors['code'])): ?><div class="invalid-feedback"><?= $errors['code'] ?></div><?php endif; ?>
            </div>

            <div class="form-group">
                <label>Loại giảm</label>
                <?php if ($isEdit): ?>
                    <input type="hidden" name="discount_type" value="<?= $formData['discount_type'] ?>">
                    <select class="form-control readonly-field" disabled>
                        <option value="fixed" <?= ($formData['discount_type'] ?? '') == 'fixed' ? 'selected' : '' ?>>Số tiền (đ)</option>
                        <option value="percent" <?= ($formData['discount_type'] ?? '') == 'percent' ? 'selected' : '' ?>>Phần trăm (%)</option>
                    </select>
                <?php else: ?>
                    <select name="discount_type" class="form-control">
                        <option value="fixed" <?= ($formData['discount_type'] ?? '') == 'fixed' ? 'selected' : '' ?>>Số tiền (đ)</option>
                        <option value="percent" <?= ($formData['discount_type'] ?? '') == 'percent' ? 'selected' : '' ?>>Phần trăm (%)</option>
                    </select>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Giá trị giảm <span style="color:red">*</span></label>
                <input type="number" name="discount_value" class="form-control <?= isset($errors['discount_value']) ? 'is-invalid' : '' ?> <?= $isEdit ? 'readonly-field' : '' ?>"
                    value="<?= $formData['discount_value'] ?? '' ?>" <?= $isEdit ? 'readonly' : '' ?> placeholder="Nhập số">
                <?php if (isset($errors['discount_value'])): ?><div class="invalid-feedback"><?= $errors['discount_value'] ?></div><?php endif; ?>
            </div>

            <div class="form-group">
                <label>Đơn tối thiểu</label>
                <input type="number" name="min_order_value" class="form-control <?= isset($errors['min_order_value']) ? 'is-invalid' : '' ?> <?= $isEdit ? 'readonly-field' : '' ?>"
                    value="<?= $formData['min_order_value'] ?? '0' ?>" <?= $isEdit ? 'readonly' : '' ?>>
                <?php if (isset($errors['min_order_value'])): ?><div class="invalid-feedback"><?= $errors['min_order_value'] ?></div><?php endif; ?>
            </div>

            <div class="form-group">
                <label>Điểm đổi thưởng <span style="font-weight:normal; font-size:11px; color:#e74c3c;">(0 = Miễn phí)</span></label>
                <input type="number" name="points_cost" class="form-control" value="<?= $formData['points_cost'] ?? '0' ?>" min="0">
            </div>

            <div class="form-group">
                <label>Lượt dùng tối đa <span class="editable-label">(Có thể sửa)</span></label>
                <input type="number" name="usage_limit" class="form-control <?= isset($errors['usage_limit']) ? 'is-invalid' : '' ?>"
                    value="<?= $formData['usage_limit'] ?? '100' ?>">
                <?php if (isset($errors['usage_limit'])): ?>
                    <div class="invalid-feedback"><?= $errors['usage_limit'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Ngày bắt đầu <span style="color:red">*</span></label>
                <?php $startVal = isset($formData['start_date']) ? date('Y-m-d\TH:i', strtotime($formData['start_date'])) : ''; ?>
                <input type="datetime-local" name="start_date" class="form-control <?= isset($errors['start_date']) ? 'is-invalid' : '' ?> <?= $isEdit ? 'readonly-field' : '' ?>"
                    value="<?= $startVal ?>" <?= $isEdit ? 'readonly' : '' ?>>
                <?php if (isset($errors['start_date'])): ?><div class="invalid-feedback"><?= $errors['start_date'] ?></div><?php endif; ?>
            </div>

            <div class="form-group">
                <label>Ngày kết thúc <span class="editable-label">(Có thể sửa)</span></label>
                <?php $endVal = isset($formData['end_date']) ? date('Y-m-d\TH:i', strtotime($formData['end_date'])) : ''; ?>
                <input type="datetime-local" name="end_date" class="form-control <?= isset($errors['end_date']) ? 'is-invalid' : '' ?>"
                    value="<?= $endVal ?>">
                <?php if (isset($errors['end_date'])): ?><div class="invalid-feedback"><?= $errors['end_date'] ?></div><?php endif; ?>
            </div>

            <div class="form-group">
                <label>Trạng thái <span class="editable-label">(Có thể sửa)</span></label>
                <select name="status" class="form-control">
                    <option value="1" <?= (isset($formData['status']) && $formData['status'] == 1) ? 'selected' : '' ?>>Kích hoạt</option>
                    <option value="0" <?= (isset($formData['status']) && $formData['status'] == 0) ? 'selected' : '' ?>>Tạm dừng</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> <?= $isEdit ? 'CẬP NHẬT MÃ' : 'TẠO MÃ MỚI' ?></button>
            </div>
        </form>
    </div>

    <div class="filter-bar">
        <span class="filter-header-text"><i class="fa-solid fa-filter" style="color:#3498db"></i> Bộ lọc & Tìm kiếm</span>
        <form action="index.php" method="GET" class="filter-form">
            <input type="hidden" name="controller" value="admin-coupon">
            <div class="filter-item-search">
                <input type="text" name="keyword" class="form-control" value="<?= htmlspecialchars($keyword) ?>" placeholder="Nhập mã coupon cần tìm...">
            </div>
            <div class="filter-item-select">
                <select name="discount_type" class="form-control">
                    <option value="">-- Tất cả loại --</option>
                    <option value="percent" <?= $typeFilter == 'percent' ? 'selected' : '' ?>>Phần trăm (%)</option>
                    <option value="fixed" <?= $typeFilter == 'fixed' ? 'selected' : '' ?>>Số tiền (đ)</option>
                </select>
            </div>
            <div class="filter-item-select">
                <select name="status" class="form-control">
                    <option value="">-- Trạng thái --</option>
                    <option value="1" <?= $statusFilter === '1' ? 'selected' : '' ?>>Đang Kích hoạt</option>
                    <option value="0" <?= $statusFilter === '0' ? 'selected' : '' ?>>Đang Tạm dừng</option>
                </select>
            </div>

            <div class="filter-item-btn">
                <button type="submit" class="btn btn-primary" title="Tìm kiếm"><i class="fa-solid fa-search"></i> Tìm</button>
                <?php if ($keyword || $statusFilter !== '' || $typeFilter): ?>
                    <a href="index.php?controller=admin-coupon" class="btn btn-secondary" title="Reset bộ lọc"><i class="fa-solid fa-rotate-right"></i></a>
                <?php endif; ?>
            </div>
            <a href="index.php?controller=admin-coupon&action=exportExcel&keyword=<?= $filters['keyword'] ?>&status=<?= $filters['status'] ?>"
                class="btn btn-success">
                <i class="fas fa-file-excel"></i> Xuất Excel
            </a>
            <a href="index.php?controller=admin-coupon&action=import" class="btn btn-warning">
                <i class="fa-solid fa-file-import"></i> Nhập Excel
            </a>
        </form>
    </div>

    <div class="coupon-table-wrapper">
        <table class="coupon-table">
            <thead>
                <tr>
                    <th>Mã Code</th>
                    <th>Giá trị giảm</th>
                    <th style="text-align: center;">Điểm đổi</th>
                    <th>Lượt dùng</th>
                    <th>Hạn sử dụng</th>
                    <th>Trạng thái</th>
                    <th style="text-align:center;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($coupons)): foreach ($coupons as $c): ?>
                        <tr>
                            <td><span class="coupon-code-badge"><?= $c['code'] ?></span></td>
                            <td style="font-weight: 600;"><?= number_format($c['discount_value']) ?><?= $c['discount_type'] == 'fixed' ? 'đ' : '%' ?></td>
                            <td style="text-align: center;">
                                <?php if ($c['points_cost'] > 0): ?>
                                    <span style="background: #fff3cd; color: #856404; padding: 2px 8px; border-radius: 4px; font-weight: bold; font-size: 11px;"><?= number_format($c['points_cost']) ?> điểm</span>
                                <?php else: ?>
                                    <span style="background: #f1f5f9; color: #64748b; padding: 2px 8px; border-radius: 4px; font-size: 11px;">Miễn phí</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="font-size: 11px; margin-bottom: 2px;">Đã dùng <b><?= $c['used_count'] ?></b> / <?= $c['usage_limit'] ?></div>
                                <div class="progress-container">
                                    <?php $p = ($c['usage_limit'] > 0) ? min(100, ($c['used_count'] / $c['usage_limit']) * 100) : 0; ?>
                                    <div class="progress-bar" style="width: <?= $p ?>%;"></div>
                                </div>
                            </td>
                            <td style="font-size: 13px;">
                                <span style="color: #64748b;">Hết hạn:</span> <br>
                                <span style="color: #ef4444; font-weight:500;"><?= date('d/m/Y', strtotime($c['end_date'])) ?></span>
                            </td>
                            <td>
                                <?php
                                if ($c['status'] == 0) echo '<span class="status-badge status-locked">Tạm dừng</span>';
                                else if (($c['status'] == 1)) echo '<span class="status-badge status-active">Đang chạy</span>';
                                ?>
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    <a href="index.php?controller=admin-coupon&action=edit&id=<?= $c['coupon_id'] ?>&keyword=<?= urlencode($keyword) ?>" class="btn btn-secondary" style="padding: 5px 10px; height: 30px; background:#e0f2fe; color:#0369a1;" title="Sửa"><i class="fa-solid fa-pen-to-square" style="margin:0"></i></a>
                                    <a href="index.php?controller=admin-coupon&action=delete&id=<?= $c['coupon_id'] ?>&keyword=<?= urlencode($keyword) ?>" onclick="return confirm('Xóa mã này?')" class="btn btn-secondary" style="padding: 5px 10px; height: 30px; background:#fee2e2; color:#b91c1c;" title="Xóa"><i class="fa-solid fa-trash-can" style="margin:0"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="7" style="padding: 40px; text-align: center; color: #94a3b8;">Không tìm thấy mã giảm giá nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('.coupon-form');

        function showError(inputName, message) {
            const input = form.querySelector(`[name="${inputName}"]`);
            if (input) {
                input.classList.add('is-invalid');
                let errorDiv = input.nextElementSibling;
                if (!errorDiv || !errorDiv.classList.contains('invalid-feedback')) {
                    errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback';
                    input.parentNode.appendChild(errorDiv);
                }
                errorDiv.textContent = message;
                errorDiv.style.display = 'block';
            }
        }

        function clearErrors() {
            const inputs = form.querySelectorAll('.is-invalid');
            inputs.forEach(input => {
                input.classList.remove('is-invalid');
                const errorDiv = input.nextElementSibling;
                if (errorDiv && errorDiv.classList.contains('invalid-feedback')) errorDiv.style.display = 'none';
            });
        }

        form.addEventListener('submit', function(e) {
            let isValid = true;
            clearErrors();

            // Validate Code
            const codeInput = form.querySelector('[name="code"]');
            if (!codeInput.value.trim()) {
                showError('code', 'Nhập mã coupon');
                isValid = false;
            } else if (!/^[A-Z0-9]+$/.test(codeInput.value.toUpperCase())) {
                showError('code', 'Mã sai định dạng');
                isValid = false;
            }

            // Validate Giá trị giảm
            const valInput = form.querySelector('[name="discount_value"]');
            const typeInput = form.querySelector('[name="discount_type"]');
            const typeVal = typeInput.value;
            if (valInput.value === '' || parseFloat(valInput.value) < 0) {
                showError('discount_value', 'Giá trị không âm');
                isValid = false;
            } else if (typeVal === 'percent' && parseFloat(valInput.value) > 100) {
                showError('discount_value', 'Tối đa 100%');
                isValid = false;
            }

            // Validate Đơn tối thiểu
            const minOrder = form.querySelector('[name="min_order_value"]');
            if (minOrder.value !== '' && parseFloat(minOrder.value) < 0) {
                showError('min_order_value', 'Không được âm');
                isValid = false;
            }

            // Validate Lượt dùng (ĐÃ THÊM)
            const limit = form.querySelector('[name="usage_limit"]');
            if (limit.value !== '' && parseFloat(limit.value) < 0) {
                showError('usage_limit', 'Không được âm');
                isValid = false;
            }

            // Validate Ngày
            const start = form.querySelector('[name="start_date"]');
            const end = form.querySelector('[name="end_date"]');
            if (!start.value) {
                showError('start_date', 'Chọn ngày');
                isValid = false;
            }
            if (!end.value) {
                showError('end_date', 'Chọn ngày');
                isValid = false;
            }
            if (start.value && end.value && new Date(start.value) > new Date(end.value)) {
                showError('start_date', 'Ngày bắt đầu > kết thúc');
                isValid = false;
            }

            if (!isValid) e.preventDefault();
        });
    });
</script>