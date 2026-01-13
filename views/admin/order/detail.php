<?php
// 1. Logic xác định nhánh con để quay lại đúng danh sách
$back_action = "index"; 
switch ($order['status']) {
    case 0: $back_action = "pending"; break;   // Chờ xác nhận
    case 1: $back_action = "pickup"; break;    // Chờ lấy hàng
    case 2: $back_action = "shipping"; break;  // Đang giao
    case 3: $back_action = "completed"; break; // Đã giao
    case 4: $back_action = "cancelled"; break; // Đã hủy
}

// 2. Mảng tên trạng thái để hiển thị Badge
$status_map = [
    0 => ['text' => 'CHỜ XÁC NHẬN', 'color' => '#2196f3', 'bg' => '#e3f2fd'],
    1 => ['text' => 'CHỜ LẤY HÀNG', 'color' => '#856404', 'bg' => '#fff3cd'],
    2 => ['text' => 'ĐANG GIAO', 'color' => '#0c5460', 'bg' => '#d1ecf1'],
    3 => ['text' => 'HOÀN THÀNH', 'color' => '#155724', 'bg' => '#d4edda'],
    4 => ['text' => 'ĐÃ HỦY', 'color' => '#721c24', 'bg' => '#f8d7da'],
];
$curr_status = $status_map[$order['status']];
?>

<div class="table-container" style="padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 1px solid #eee; padding-bottom: 15px;">
        <div>
            <h2 style="color: #2c3e50; margin: 0; display: flex; align-items: center; gap: 10px;">
                Chi tiết đơn hàng #<?= $order['order_id'] ?>
                <span style="font-size: 12px; padding: 4px 12px; border-radius: 20px; background: <?= $curr_status['bg'] ?>; color: <?= $curr_status['color'] ?>;">
                    <?= $curr_status['text'] ?>
                </span>
            </h2>
            <p style="margin: 5px 0 0 0; color: #64748b; font-size: 13px;">Ngày đặt: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
        </div>
        <a href="index.php?controller=admin-order&action=<?= $back_action ?>" class="btn" style="background: #6c757d; color: #fff; text-decoration: none; display: flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 6px; font-weight: 600; font-size: 14px;">
            <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 25px;">
        <div style="background: #f8fafc; padding: 25px; border-radius: 10px; border: 1px solid #e2e8f0; height: fit-content;">
            <h3 style="margin-top: 0; color: #334155; border-bottom: 2px solid #fff; padding-bottom: 10px; font-size: 18px;">
                <i class="fa-solid fa-user-tag" style="color: #3498db; margin-right: 8px;"></i> Thông tin giao hàng
            </h3>
            <div style="line-height: 2; margin-top: 15px; font-size: 14px; color: #475569;">
                <p style="margin: 8px 0;"><b>Họ tên:</b> <?= htmlspecialchars($order['customer_name']) ?></p>
                <p style="margin: 8px 0;"><b>Điện thoại:</b> <?= htmlspecialchars($order['customer_phone']) ?></p>
                <p style="margin: 8px 0;"><b>Địa chỉ:</b> <?= htmlspecialchars($order['shipping_address']) ?></p>
                <p style="margin: 8px 0;"><b>Phương thức:</b> <span style="background:#e9ecef; color:#495057; padding: 2px 8px; border-radius: 4px; font-size: 12px;"><?= htmlspecialchars($order['payment_method']) ?></span></p>
                <p style="margin: 8px 0;"><b>Ghi chú:</b> <i style="color: #94a3b8;"><?= htmlspecialchars($order['note'] ?: 'Không có ghi chú') ?></i></p>
                
                <hr style="border: none; border-top: 1px dashed #cbd5e1; margin: 20px 0;">
                
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                    <span>Tiền hàng:</span>
                    <span><?= number_format($order['total_money'], 0, ',', '.') ?>đ</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                    <span>Giảm giá:</span>
                    <span style="color: #e74c3c;">-<?= number_format($order['discount_amount'], 0, ',', '.') ?>đ</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #e2e8f0; padding-top: 15px;">
                    <span style="font-weight: 700; color: #1e293b;">THÀNH TIỀN:</span>
                    <span style="font-size: 22px; font-weight: 800; color: #2ecc71;"><?= number_format($order['final_money'], 0, ',', '.') ?>đ</span>
                </div>
            </div>
        </div>

        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <h3 style="margin-top: 0; margin-bottom: 20px; color: #2c3e50; font-size: 18px;">
                    <i class="fa-solid fa-box-open" style="color: #3498db; margin-right: 8px;"></i> Sản phẩm đã đặt
                </h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; background: #f8fafc; border-bottom: 2px solid #eee;">
                            <th style="padding: 12px; font-size: 13px; color: #64748b;">Sản phẩm</th>
                            <th style="padding: 12px; font-size: 13px; color: #64748b;">Giá</th>
                            <th style="padding: 12px; text-align: center; font-size: 13px; color: #64748b;">SL</th>
                            <th style="padding: 12px; text-align: right; font-size: 13px; color: #64748b;">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px; display: flex; align-items: center; gap: 15px;">
                                    <img src="assets/uploads/products/<?= $item['image'] ?>" width="50" height="50" style="object-fit: cover; border-radius: 6px; border: 1px solid #eee;">
                                    <span style="font-weight: 600; color: #334155; font-size: 14px;"><?= htmlspecialchars($item['name']) ?></span>
                                </td>
                                <td style="padding: 12px; color: #64748b; font-size: 14px;"><?= number_format($item['price'], 0, ',', '.') ?>đ</td>
                                <td style="padding: 12px; text-align: center; color: #334155; font-weight: 600;">x<?= $item['quantity'] ?></td>
                                <td style="padding: 12px; text-align: right; font-weight: 700; color: #1e293b;">
                                    <?= number_format($item['total_price'], 0, ',', '.') ?>đ
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($order['status'] == 0): ?>
                <div style="margin-top: 40px; display: flex; gap: 12px; justify-content: flex-end; padding-top: 20px; border-top: 1px solid #f1f5f9;">
                    <a href="index.php?controller=admin-order&action=updateStatus&id=<?= $order['order_id'] ?>&status=4" 
                       class="btn" 
                       style="background: #fff1f0; color: #f5222d; border: 1px solid #ffa39e; padding: 12px 25px; border-radius: 6px; text-decoration: none; font-weight: 700; font-size: 14px;" 
                       onclick="return confirm('Bạn chắc chắn muốn HỦY đơn này?')">
                       <i class="fa-solid fa-xmark"></i> HỦY ĐƠN
                    </a>
                    
                    <a href="index.php?controller=admin-order&action=updateStatus&id=<?= $order['order_id'] ?>&status=1" 
                       class="btn" 
                       style="background: #2ecc71; color: #fff; padding: 12px 25px; border-radius: 6px; text-decoration: none; font-weight: 700; font-size: 14px; box-shadow: 0 4px 12px rgba(46, 204, 113, 0.2);">
                       <i class="fa-solid fa-check"></i> XÁC NHẬN ĐƠN HÀNG
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>