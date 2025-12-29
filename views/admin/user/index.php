<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
        <h3>Danh sách khách hàng</h3>
        </div>

    <table class="table-admin">
        <thead>
            <tr>
                <th>ID</th>
                <th>Họ tên</th>
                <th>Email</th>
                <th>Số điện thoại</th>
                <th>Ngày đăng ký</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($customers)): ?>
                <tr><td colspan="6" style="text-align:center;">Chưa có khách hàng nào</td></tr>
            <?php else: ?>
                <?php foreach ($customers as $cus): ?>
                    <tr>
                        <td>#<?= $cus['user_id'] ?></td>
                        <td>
                            <strong><?= $cus['full_name'] ?></strong>
                        </td>
                        <td><?= $cus['email'] ?></td>
                        <td><?= $cus['phone'] ? $cus['phone'] : '<span style="color:#ccc">Chưa cập nhật</span>' ?></td>
                        <td><?= date('d/m/Y', strtotime($cus['created_at'])) ?></td>
                        <td>
                            <a href="index.php?controller=user&action=delete&id=<?= $cus['user_id'] ?>" 
                               class="btn btn-sm btn-danger"
                               style="background: #e74c3c; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none;"
                               onclick="return confirm('CẢNH BÁO: Xóa khách hàng có thể gây lỗi dữ liệu nếu họ đã có đơn hàng. Bạn chắc chắn chứ?')">
                               <i class="fa-solid fa-trash"></i> Xóa
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>