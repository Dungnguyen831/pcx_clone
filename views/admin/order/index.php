<div class="table-container">
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 25px;">
        <div style="background: #fff; padding: 20px; border-radius: 10px; border-left: 5px solid #3498db; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid #e1e8ed;">
            <div style="color: #7f8c8d; font-size: 13px; font-weight: 600; margin-bottom: 5px;">TỔNG ĐƠN HÀNG</div>
            <div style="font-size: 24px; font-weight: 700; color: #2c3e50;"><?= $stats['total_orders'] ?? 0 ?></div>
        </div>
        <div style="background: #fff; padding: 20px; border-radius: 10px; border-left: 5px solid #f1c40f; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid #e1e8ed;">
            <div style="color: #7f8c8d; font-size: 13px; font-weight: 600; margin-bottom: 5px;">ĐƠN CHỜ DUYỆT</div>
            <div style="font-size: 24px; font-weight: 700; color: #f39c12;"><?= $stats['pending'] ?? 0 ?></div>
        </div>
        <div style="background: #fff; padding: 20px; border-radius: 10px; border-left: 5px solid #2ecc71; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid #e1e8ed;">
            <div style="color: #7f8c8d; font-size: 13px; font-weight: 600; margin-bottom: 5px;">DOANH THU THỰC TẾ</div>
            <div style="font-size: 24px; font-weight: 700; color: #2ecc71;"><?= number_format($stats['revenue'] ?? 0, 0, ',', '.') ?>đ</div>
        </div>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <?php if ($_GET['msg'] == 'updated'): ?>
            <div style="padding: 12px 20px; background: #2ecc71; color: #fff; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-circle-check"></i> Cập nhật trạng thái thành công!
            </div>
        <?php elseif ($_GET['msg'] == 'error'): ?>
            <div style="padding: 12px 20px; background: #e74c3c; color: #fff; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-triangle-exclamation"></i>
                Lỗi: <?= htmlspecialchars($_GET['detail'] ?? 'Không thể cập nhật kho hàng!') ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #2c3e50; margin: 0; font-weight: 700;">
            <i class="fa-solid fa-file-invoice-dollar" style="margin-right: 10px; color: var(--primary-color);"></i>
            Quản lý đơn hàng
        </h2>
    </div>

    <div style="background: #fff; padding: 20px; border-radius: 10px; margin-bottom: 25px; border: 1px solid #e1e8ed; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
        <form action="index.php" method="GET" style="display: grid; grid-template-columns: 150px 1fr auto; gap: 20px; align-items: flex-end;">
            <input type="hidden" name="controller" value="admin-order">
            <input type="hidden" name="action" value="index">

            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 13px; color: #5a6a85;">Mã đơn hàng</label>
                <input type="number" name="search_id" value="<?= htmlspecialchars($_GET['search_id'] ?? '') ?>"
                    placeholder="ID..." style="width: 100%; padding: 12px; border: 1px solid #dfe5ef; border-radius: 6px; outline: none;">
            </div>

            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 13px; color: #5a6a85;">Tên khách hàng</label>
                <input type="text" name="search_name" value="<?= htmlspecialchars($_GET['search_name'] ?? '') ?>"
                    placeholder="Tìm theo tên khách hàng..." style="width: 100%; padding: 12px; border: 1px solid #dfe5ef; border-radius: 6px; outline: none;">
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary" style="padding: 12px 25px; font-weight: 600;">
                    <i class="fa-solid fa-magnifying-glass"></i> Tìm
                </button>
                <a href="index.php?controller=admin-order" class="btn" style="background: #f1f3f5; color: #495057; padding: 12px 15px; border-radius: 6px; text-decoration: none; border: 1px solid #dee2e6;">
                    <i class="fa-solid fa-rotate"></i>
                </a>
            </div>
        </form>
    </div>

    <table class="table-admin" style="width: 100%; border-collapse: collapse; background: #fff;">
        <thead>
            <tr style="background: #f8f9fa;">
                <th style="padding: 15px; text-align: left;">ID</th>
                <th style="padding: 15px; text-align: left;">Khách hàng</th>
                <th style="padding: 15px; text-align: left;">Điện thoại</th>
                <th style="padding: 15px; text-align: left;">Tổng tiền</th>
                <th style="padding: 15px; text-align: left;">Ngày đặt</th>
                <th style="padding: 15px; text-align: center;">Trạng thái</th>
                <th style="padding: 15px; text-align: center;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $o): ?>
                    <tr style="border-bottom: 1px solid #f1f3f5;">
                        <td style="padding: 15px; font-weight: bold; color: #adb5bd;">#<?= $o['order_id'] ?></td>
                        <td style="padding: 15px; font-weight: 600; color: #2c3e50;"><?= htmlspecialchars($o['customer_name']) ?></td>
                        <td style="padding: 15px; color: #5a6a85;"><?= htmlspecialchars($o['customer_phone']) ?></td>
                        <td style="padding: 15px; color: var(--primary-color); font-weight: 700;">
                            <?= number_format($o['final_money'], 0, ',', '.') ?>đ
                        </td>
                        <td style="padding: 15px; font-size: 13px; color: #5a6a85;">
                            <?= date('d/m/Y H:i', strtotime($o['created_at'])) ?>
                        </td>

                        <td style="padding: 15px; text-align: center;">
                            <form action="index.php" method="GET" style="margin: 0;">
                                <input type="hidden" name="controller" value="admin-order">
                                <input type="hidden" name="action" value="updateStatus">
                                <input type="hidden" name="id" value="<?= $o['order_id'] ?>">

                                <select name="status" onchange="this.form.submit()"
                                    style="padding: 6px 10px; border-radius: 20px; border: 1px solid #dfe5ef; font-size: 11px; font-weight: 600; cursor: pointer; outline: none;
                                    <?php
                                    switch ($o['status']) {
                                        case 0:
                                            echo 'background: #e3f2fd; color: #2196f3;';
                                            break; // Mới
                                        case 1:
                                            echo 'background: #e8f5e9; color: #2e7d32;';
                                            break; // Xác nhận
                                        case 2:
                                            echo 'background: #fff3e0; color: #ef6c00;';
                                            break; // Đang giao
                                        case 3:
                                            echo 'background: #f1f8e9; color: #4caf50;';
                                            break; // Hoàn thành
                                        case 4:
                                            echo 'background: #ffebee; color: #c62828;';
                                            break; // Hủy
                                    }
                                    ?>">
                                    <option value="0" <?= $o['status'] == 0 ? 'selected' : '' ?>>Mới</option>
                                    <option value="1" <?= $o['status'] == 1 ? 'selected' : '' ?>>Xác nhận (Trừ kho)</option>
                                    <option value="2" <?= $o['status'] == 2 ? 'selected' : '' ?>>Đang giao</option>
                                    <option value="3" <?= $o['status'] == 3 ? 'selected' : '' ?>>Hoàn thành</option>
                                    <option value="4" <?= $o['status'] == 4 ? 'selected' : '' ?>>Hủy đơn (Hoàn kho)</option>
                                </select>
                            </form>
                        </td>

                        <td style="padding: 15px;">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="index.php?controller=admin-order&action=detail&id=<?= $o['order_id'] ?>"
                                    class="btn-sm btn-edit" title="Xem chi tiết" style="background: #f1f3f5; color: #5a6a85; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 6px; text-decoration: none; border: 1px solid #dee2e6;">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 50px; color: #95a5a6;">Chưa có đơn hàng nào khớp với tìm kiếm.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>