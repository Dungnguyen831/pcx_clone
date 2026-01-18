<div class="table-container">
    <div style="display: grid; grid-template-columns: repeat(<?php echo ($action == 'index' || !isset($_GET['action'])) ? '3' : '1'; ?>, 1fr); gap: 20px; margin-bottom: 25px;">
        <div style="background: #fff; padding: 20px; border-radius: 10px; border-left: 5px solid #3498db; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid #e1e8ed;">
            <div style="color: #7f8c8d; font-size: 13px; font-weight: 600; margin-bottom: 5px;">TỔNG ĐƠN HÀNG</div>
            <div style="font-size: 24px; font-weight: 700; color: #2c3e50;"><?= $stats['total_orders'] ?? 0 ?></div>
        </div>
        <?php if ($action == 'index' || !isset($_GET['action'])): ?>
            <div style="background: #fff; padding: 20px; border-radius: 10px; border-left: 5px solid #f1c40f; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid #e1e8ed;">
                <div style="color: #7f8c8d; font-size: 13px; font-weight: 600; margin-bottom: 5px;">ĐƠN CHỜ DUYỆT</div>
                <div style="font-size: 24px; font-weight: 700; color: #f39c12;"><?= $stats['pending'] ?? 0 ?></div>
            </div>
        <?php endif; ?>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div style="padding: 12px 20px; background: <?= $_GET['msg'] == 'updated' ? '#2ecc71' : '#e74c3c' ?>; color: #fff; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid <?= $_GET['msg'] == 'updated' ? 'fa-circle-check' : 'fa-triangle-exclamation' ?>"></i>
            <?= $_GET['msg'] == 'updated' ? 'Cập nhật trạng thái thành công!' : 'Lỗi: ' . htmlspecialchars($_GET['detail'] ?? 'Thao tác thất bại!') ?>
        </div>
    <?php endif; ?>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #2c3e50; margin: 0; font-weight: 700;">
            <i class="fa-solid fa-file-invoice-dollar" style="margin-right: 10px; color: #3498db;"></i>
            Quản lý đơn hàng: 
            <span style="color: #7f8c8d; font-size: 18px;">
                <?php 
                    $act = $_GET['action'] ?? 'index';
                    echo ($act == 'pending') ? 'Chờ xác nhận' : (($act == 'pickup') ? 'Chờ lấy hàng' : (($act == 'shipping') ? 'Đang giao' : (($act == 'completed') ? 'Đã giao' : (($act == 'cancelled') ? 'Đã hủy' : 'Tất cả'))));
                ?>
            </span>
        </h2>
        <div style="display:flex; gap:12px;">
            <a href="index.php?controller=admin-order&action=exportExcel&action_type=<?= $_GET['action'] ?? 'index' ?>" class="btn" style="background:#2ecc71; color:#fff; padding:10px 15px; border-radius:6px; font-weight:600; text-decoration:none;">
                <i class="fa-solid fa-file-excel"></i> Xuất Excel 
            </a>
        </div>  
    </div>

    <div style="background: #fff; padding: 20px; border-radius: 10px; margin-bottom: 25px; border: 1px solid #e1e8ed;">
        <form action="index.php" method="GET" style="display: grid; grid-template-columns: 150px 1fr auto; gap: 20px; align-items: flex-end;">
            <input type="hidden" name="controller" value="admin-order">
            <input type="hidden" name="action" value="<?= htmlspecialchars($_GET['action'] ?? 'index') ?>">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 13px;">Mã đơn</label>
                <input type="number" name="search_id" value="<?= htmlspecialchars($_GET['search_id'] ?? '') ?>" placeholder="ID..." style="width: 100%; padding: 10px; border: 1px solid #dfe5ef; border-radius: 6px;">
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 13px;">Tên khách hàng</label>
                <input type="text" name="search_name" value="<?= htmlspecialchars($_GET['search_name'] ?? '') ?>" placeholder="Tìm tên khách..." style="width: 100%; padding: 10px; border: 1px solid #dfe5ef; border-radius: 6px;">
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="submit" style="background:#3498db; color:#fff; border:none; padding: 10px 20px; border-radius:6px; font-weight:600; cursor:pointer;">
                    <i class="fa-solid fa-magnifying-glass"></i> Tìm
                </button>
                <a href="index.php?controller=admin-order&action=<?= $_GET['action'] ?? 'index' ?>" style="background: #f1f3f5; color: #495057; padding: 10px 15px; border-radius: 6px; text-decoration: none; border: 1px solid #dee2e6;">
                    <i class="fa-solid fa-rotate"></i>
                </a>
            </div>
        </form>
    </div>

    <table style="width: 100%; border-collapse: collapse; background: #fff;">
        <thead>
            <tr style="background: #f8f9fa; border-bottom: 2px solid #edf2f7;">
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
                        <td style="padding: 15px; color: #e74c3c; font-weight: 700;"><?= number_format($o['final_money'], 0, ',', '.') ?>đ</td>
                        <td style="padding: 15px; font-size: 13px; color: #5a6a85;"><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></td>
                        <td style="padding: 15px; text-align: center;">
                            <?php if ($o['status'] == 3): ?>
                                <span style="padding: 6px 15px; border-radius: 20px; font-size: 11px; font-weight: 700; background:#d4edda; color:#155724;">
                                    <i class="fa-solid fa-check-double"></i> ĐÃ HOÀN THÀNH
                                </span>
                            <?php elseif ($o['status'] == 4): ?>
                                <span style="padding: 6px 15px; border-radius: 20px; font-size: 11px; font-weight: 700; background:#f8d7da; color:#721c24;">
                                    <i class="fa-solid fa-ban"></i> ĐÃ HỦY
                                </span>
                            <?php elseif ($o['status'] == 2): ?>
                                <span style="padding: 6px 15px; border-radius: 20px; font-size: 11px; font-weight: 700; background:#fff3cd; color:#856404;">
                                    <i class="fa-solid fa-truck-fast"></i> ĐANG GIAO HÀNG
                                </span>
                            <?php elseif ($o['status'] == 1): ?>
                                <span style="padding: 6px 15px; border-radius: 20px; font-size: 11px; font-weight: 700; background:#e3f2fd; color:#0d47a1;">
                                    <i class="fa-solid fa-box"></i> ĐÃ XÁC NHẬN
                                </span>
                            <?php else: ?>
                                <span style="padding: 6px 15px; border-radius: 20px; font-size: 11px; font-weight: 700; background:#ecf0f1; color:#7f8c8d;">
                                    <i class="fa-solid fa-clock"></i> CHỜ DUYỆT
                                </span>
                            <?php endif; ?>
                        </td>

                        <td style="padding: 15px; text-align: center;">
                            <div style="display: flex; gap: 8px; justify-content: center; align-items: center;">
                                <?php if ($o['status'] == 0 || $o['status'] == 1): ?>
                                    <form action="index.php" method="GET" style="margin: 0;">
                                        <input type="hidden" name="controller" value="admin-order">
                                        <input type="hidden" name="action" value="updateStatus">
                                        <input type="hidden" name="id" value="<?= $o['order_id'] ?>">
                                        <?php 
                                            $btn_text = ""; $next_status = 0; $btn_css = ""; $tab_target = "index";
                                            if ($o['status'] == 0) {
                                                $btn_text = "Xác nhận đơn"; $next_status = 1; $tab_target = "pickup";
                                                $btn_css = "background: #e3f2fd; color: #2196f3; border: 1px solid #2196f3;";
                                            } elseif ($o['status'] == 1) {
                                                $btn_text = "Giao hàng"; $next_status = 2; $tab_target = "shipping";
                                                $btn_css = "background: #fff3cd; color: #856404; border: 1px solid #856404;";
                                            }
                                        ?>
                                        <input type="hidden" name="status" value="<?= $next_status ?>">
                                        <input type="hidden" name="redirect_to" value="<?= $tab_target ?>">
                                        <button type="submit" onclick="return confirm('Chuyển sang: <?= $btn_text ?>?')"
                                                style="padding: 7px 12px; border-radius: 20px; font-size: 10px; font-weight: 700; cursor: pointer; text-transform: uppercase; outline: none; <?= $btn_css ?>">
                                            <?= $btn_text ?> <i class="fa-solid fa-chevron-right" style="margin-left: 4px; font-size: 9px;"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <?php if ($o['status'] == 2): ?>
                                    <span style="font-size: 12px; font-weight: 600; color: #d35400;">
                                        <i class="fa-solid fa-truck"></i> Đang giao hàng...
                                    </span>
                                <?php endif; ?>

                                <?php if ($o['status'] == 0): ?>
                                    <form action="index.php" method="GET" style="margin: 0;">
                                        <input type="hidden" name="controller" value="admin-order">
                                        <input type="hidden" name="action" value="updateStatus">
                                        <input type="hidden" name="id" value="<?= $o['order_id'] ?>">
                                        <input type="hidden" name="status" value="4"> 
                                        <input type="hidden" name="redirect_to" value="cancelled">
                                        <button type="submit" onclick="return confirm('Bạn có chắc chắn muốn HỦY đơn hàng này?')"
                                                style="padding: 7px 12px; border-radius: 20px; font-size: 10px; font-weight: 700; cursor: pointer; background: #fff1f0; color: #f5222d; border: 1px solid #ffa39e; text-transform: uppercase; outline: none;">
                                            Hủy <i class="fa-solid fa-xmark" style="margin-left: 4px;"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <a href="index.php?controller=admin-order&action=detail&id=<?= $o['order_id'] ?>" style="color:#3498db; font-size: 18px;" title="Xem chi tiết">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7" style="text-align: center; padding: 50px; color: #95a5a6;">Không có đơn hàng nào.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>