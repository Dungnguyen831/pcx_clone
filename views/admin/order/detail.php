<div class="table-container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 1px solid #eee; padding-bottom: 15px;">
        <h2 style="color: #2c3e50; margin: 0;">Chi tiết đơn hàng #<?= $order['order_id'] ?></h2>
        <a href="index.php?controller=admin-order" class="btn" style="background: #6c757d; color: #fff; text-decoration: none; display: flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 6px;">
            <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 25px;">
        <div style="background: #f8fafc; padding: 25px; border-radius: 10px; border: 1px solid #e2e8f0;">
            <h3 style="margin-top: 0; color: var(--primary-color); border-bottom: 2px solid #fff; padding-bottom: 10px;">
                <i class="fa-solid fa-user-tag"></i> Thông tin giao hàng
            </h3>
            <div style="line-height: 1.8; margin-top: 15px;">
                <p><b>Họ tên:</b> <?= htmlspecialchars($order['customer_name']) ?></p>
                <p><b>Điện thoại:</b> <?= htmlspecialchars($order['customer_phone']) ?></p>
                <p><b>Địa chỉ:</b> <?= htmlspecialchars($order['shipping_address']) ?></p>
                <p><b>Phương thức:</b> <span class="badge" style="background:#e9ecef; color:#495057;"><?= htmlspecialchars($order['payment_method']) ?></span></p>
                <p><b>Ghi chú:</b> <i style="color: #64748b;"><?= htmlspecialchars($order['note'] ?: 'Không có ghi chú') ?></i></p>
                <hr style="border: none; border-top: 1px dashed #cbd5e1; margin: 15px 0;">
                <p><b>Tiền hàng:</b> <?= number_format($order['total_money'], 0, ',', '.') ?>đ</p>
                <p><b>Giảm giá:</b> <span style="color: var(--danger);">-<?= number_format($order['discount_amount'], 0, ',', '.') ?>đ</span></p>
                <p style="font-size: 20px; font-weight: 800; color: var(--primary-color);">Thanh toán: <?= number_format($order['final_money'], 0, ',', '.') ?>đ</p>
            </div>
        </div>

        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px;">
            <h3 style="margin-top: 0; margin-bottom: 20px; color: #2c3e50;">Sản phẩm đã đặt</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; background: #f8fafc; border-bottom: 2px solid #eee;">
                        <th style="padding: 12px;">Sản phẩm</th>
                        <th style="padding: 12px;">Giá</th>
                        <th style="padding: 12px; text-align: center;">SL</th>
                        <th style="padding: 12px; text-align: right;">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 12px; display: flex; align-items: center; gap: 15px;">
                                <img src="assets/uploads/<?= $item['image'] ?>" width="50" height="50" style="object-fit: cover; border-radius: 6px; border: 1px solid #eee;">
                                <span style="font-weight: 600; color: #334155;"><?= htmlspecialchars($item['name']) ?></span>
                            </td>
                            <td style="padding: 12px; color: #64748b;"><?= number_format($item['price'], 0, ',', '.') ?>đ</td>
                            <td style="padding: 12px; text-align: center;">x<?= $item['quantity'] ?></td>
                            <td style="padding: 12px; text-align: right; font-weight: 700; color: #1e293b;">
                                <?= number_format($item['total_price'], 0, ',', '.') ?>đ
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if ($order['status'] == 0): ?>
                <div style="margin-top: 30px; display: flex; gap: 10px; justify-content: flex-end;">
                    <a href="index.php?controller=admin-order&action=updateStatus&id=<?= $order['order_id'] ?>&status=1" class="btn btn-primary" style="padding: 12px 25px;">Xác nhận đơn hàng</a>
                    <a href="index.php?controller=admin-order&action=updateStatus&id=<?= $order['order_id'] ?>&status=4" class="btn" style="background: var(--danger); color: #fff; padding: 12px 25px;" onclick="return confirm('Bạn chắc chắn muốn hủy đơn này?')">Hủy đơn</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>