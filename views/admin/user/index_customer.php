<div class="user-tab-container">
    <div class="user-tab-menu">
        <a href="index.php?controller=user&action=index&role=0" class="user-tab-link active">
           <i class="fa-solid fa-users"></i> Khách hàng
        </a>
        <a href="index.php?controller=user&action=index&role=2" class="user-tab-link">
           <i class="fa-solid fa-user-tie"></i> Nhân viên
        </a>
    </div>
    
    <a href="index.php?controller=user&action=exportExcel" class="btn-custom btn-excel text-decoration-none">
        <i class="fa-solid fa-file-excel"></i> Xuất Excel
    </a>
</div>

<div class="user-card">
    <table class="user-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">ID</th>
                <th width="25%">Thông tin khách hàng</th>
                <th width="30%">Liên hệ</th>
                <th width="15%" class="text-center">Điểm thưởng</th>
                <th width="15%">Ngày đăng ký</th>
                <th width="10%" class="text-center">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($users)): ?>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td class="text-center fw-bold text-secondary">#<?= $u['user_id'] ?></td>
                        <td>
                            <div class="user-info">
                                <span class="name"><?= htmlspecialchars($u['full_name']) ?></span>
                                <span class="sub-text text-muted">Khách thành viên</span>
                            </div>
                        </td>
                        <td>
                            <div class="user-info">
                                <div class="sub-text"><i class="fa-regular fa-envelope"></i> <?= htmlspecialchars($u['email']) ?></div>
                                <div class="sub-text"><i class="fa-solid fa-phone"></i> <?= htmlspecialchars($u['phone']) ?></div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="point-badge">
                                <?= number_format($u['reward_points'] ?? 0) ?> điểm
                            </span>
                        </td>
                        <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                        <td class="text-center">
                            <a href="index.php?controller=user&action=delete&id=<?= $u['user_id'] ?>&role=0" 
                               onclick="return confirm('Xóa khách hàng này?')" 
                               class="btn-delete-icon" title="Xóa">
                               <i class="fa-solid fa-trash-can"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center py-5">Chưa có khách hàng nào.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>