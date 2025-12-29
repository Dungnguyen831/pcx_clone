<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-info">
            <h3><?= isset($stats['new_orders']) ? $stats['new_orders'] : 0 ?></h3>
            <p>Đơn hàng mới</p>
        </div>
        <div class="stat-icon" style="color: #3498db;">
            <i class="fa-solid fa-cart-shopping"></i>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3><?= isset($stats['products']) ? $stats['products'] : 0 ?></h3>
            <p>Sản phẩm</p>
        </div>
        <div class="stat-icon" style="color: #2ecc71;">
            <i class="fa-solid fa-box-open"></i>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3><?= isset($stats['customers']) ? $stats['customers'] : 0 ?></h3>
            <p>Khách hàng</p>
        </div>
        <div class="stat-icon" style="color: #f1c40f;">
            <i class="fa-solid fa-users"></i>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3><?= number_format(isset($stats['revenue']) ? $stats['revenue'] : 0, 0, ',', '.') ?>đ</h3>
            <p>Doanh thu tháng</p>
        </div>
        <div class="stat-icon" style="color: #e74c3c;">
            <i class="fa-solid fa-sack-dollar"></i>
        </div>
    </div>
</div>

<div class="table-container">
    <h3>Đơn hàng cần xử lý (Mới nhất)</h3>
    <table class="table-admin">
        <thead>
            <tr>
                <th>Mã Đơn</th>
                <th>Khách hàng</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($recent_orders)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; color: #999; padding: 20px;">
                        Chưa có đơn hàng nào gần đây.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($recent_orders as $order): ?>
                    <tr>
                        <td style="font-weight: bold;">#<?= $order['order_id'] ?></td>
                        
                        <td>
                            <?= htmlspecialchars($order['customer_name']) ?>
                            <br>
                            <small style="color: #777; font-size: 11px;"><?= date('d/m H:i', strtotime($order['created_at'])) ?></small>
                        </td>
                        
                        <td style="font-weight: bold; color: #e74c3c;">
                            <?= number_format($order['final_money'], 0, ',', '.') ?>đ
                        </td>
                        
                        <td>
                            <?php 
                            switch ($order['status']) {
                                case 0: 
                                    echo '<span class="status-badge st-new">Mới</span>'; break;
                                case 1: 
                                    echo '<span class="status-badge st-confirmed">Đã xác nhận</span>'; break;
                                case 2: 
                                    echo '<span class="status-badge st-shipping">Đang giao</span>'; break;
                                case 3: 
                                    echo '<span class="status-badge st-completed">Hoàn thành</span>'; break;
                                case 4: 
                                    echo '<span class="status-badge st-cancelled">Đã hủy</span>'; break;
                                default:
                                    echo '<span class="status-badge">Không rõ</span>';
                            }
                            ?>
                        </td>
                        
                        <td>
                            <a href="index.php?controller=admin-order&action=detail&id=<?= $order['order_id'] ?>" 
                               class="btn btn-primary btn-sm">
                               Xem
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>