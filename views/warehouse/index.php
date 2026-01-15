<?php
// Hàm bổ trợ tính thời gian thực (X phút trước, X giờ trước)
if (!function_exists('time_ago')) {
    function time_ago($timestamp) {
        // 1. Ép PHP chạy đúng múi giờ Việt Nam
        date_default_timezone_set('Asia/Ho_Chi_Minh'); 
        
        $time_ago = strtotime($timestamp);
        $current_time = time();
        $time_diff = $current_time - $time_ago;

        // Nếu thời gian bị âm (do lệch múi giờ server), ép về 1 giây để hiện "Vừa xong"
        if ($time_diff < 0) $time_diff = 1; 

        if ($time_diff <= 60) return "Vừa xong";
        
        $mins = round($time_diff / 60);
        if ($mins < 60) return $mins . " phút trước";
        
        $hours = round($time_diff / 3600);
        if ($hours <= 24) return $hours . " giờ trước";
        
        return date('d/m/Y', $time_ago);
    }
}
?>

<div class="dashboard-grid">
    <div class="stat-card">
        <div class="stat-info">
            <h3>Tổng mặt hàng</h3>
            <p><?php echo $total_items; ?> loại</p>
        </div>
        <i class="fa-solid fa-layer-group stat-icon" style="color: #3498db;"></i>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3>Tổng tồn kho</h3>
            <p><?php echo number_format($total_qty); ?> cái</p>
        </div>
        <i class="fa-solid fa-boxes-stacked stat-icon" style="color: #27ae60;"></i>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3>Cần nhập thêm</h3>
            <p style="color: #e74c3c;"><?php echo $low_count; ?> sản phẩm</p>
        </div>
        <i class="fa-solid fa-triangle-exclamation stat-icon" style="color: #f1c40f;"></i>
    </div>
</div>

<div class="welcome-message" style="text-align: center; margin: 40px 0; color: #7f8c8d;">
    <i class="fa-solid fa-warehouse" style="font-size: 3rem; opacity: 0.1; margin-bottom: 15px;"></i>
    <p style="font-size: 1rem; margin-bottom: 5px;">Hệ thống quản lý kho PCX đã sẵn sàng.</p>
    <p style="font-size: 0.85rem;">Vui lòng chọn chức năng từ thanh menu để bắt đầu.</p>
</div>

<div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
    <h3 style="margin-top: 0; font-size: 1.1rem; color: #2c3e50; border-bottom: 1px solid #f4f7f6; padding-bottom: 15px;">
        <i class="fa-solid fa-clock-rotate-left" style="color: #3498db; margin-right: 8px;"></i> Hoạt động nhập kho gần đây
    </h3>

    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="text-align: left; color: #95a5a6; font-size: 0.85rem; text-transform: uppercase;">
                <th style="padding: 10px;">Thời gian</th>
                <th style="padding: 10px;">Sản phẩm</th>
                <th style="padding: 10px;">Số lượng</th>
                <th style="padding: 10px;">Trạng thái</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($recent_activities)): ?>
                <?php foreach ($recent_activities as $activity): ?>
                <tr style="border-bottom: 1px solid #f8f9fa;">
                    <td style="padding: 12px; font-size: 0.9rem; color: #7f8c8d;">
                        <?php echo time_ago($activity['created_at']); ?>
                    </td>
                    <td style="padding: 12px; font-size: 0.9rem; font-weight: 500;">
                        <?php echo $activity['name']; ?>
                    </td>
                    <td style="padding: 12px;">
                        <span style="background: #e1f5fe; color: #039be5; padding: 3px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: bold;">
                            +<?php echo $activity['quantity']; ?>
                        </span>
                    </td>
                    <td style="padding: 12px; color: #27ae60; font-size: 0.9rem;">
                        <i class="fa-solid fa-circle-check"></i> Thành công
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align: center; padding: 30px; color: #bdc3c7;">Chưa có dữ liệu nhập kho.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>