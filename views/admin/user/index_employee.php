<div class="user-tab-container">
    <div class="user-tab-menu">
        <a href="index.php?controller=user&action=index&role=0" class="user-tab-link">
           <i class="fa-solid fa-users"></i> Khách hàng
        </a>
        <a href="index.php?controller=user&action=index&role=2" class="user-tab-link active">
           <i class="fa-solid fa-user-tie"></i> Nhân viên
        </a>
    </div>
    
    <div class="btn-action-group">
        <form id="form-import-excel" action="index.php?controller=user&action=import" method="POST" enctype="multipart/form-data" style="display: none;">
            <input type="file" name="excel_file" id="file-upload-input" accept=".xlsx" onchange="document.getElementById('form-import-excel').submit();">
        </form>

        <button type="button" class="btn-custom btn-import text-decoration-none border-0" 
                onclick="document.getElementById('file-upload-input').click();">
            <i class="fa-solid fa-file-import"></i> Nhập Excel
        </button>
        
        <a href="index.php?controller=user&action=create" class="btn-custom btn-add text-decoration-none">
            <i class="fa-solid fa-user-plus"></i> Thêm nhân viên
        </a>
    </div>
</div>

<div class="user-card">
    <table class="user-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">ID</th>
                <th width="30%">Nhân viên</th>
                <th width="35%">Thông tin đăng nhập</th>
                <th width="20%">Ngày tạo</th>
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
                                <span class="sub-text">
                                    <i class="fa-solid fa-phone"></i> <?= !empty($u['phone']) ? $u['phone'] : '---' ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="user-info">
                                <span class="sub-text" style="font-size: 0.95rem;">
                                    <i class="fa-regular fa-envelope"></i> <?= htmlspecialchars($u['email']) ?>
                                </span>
                                <span class="badge bg-light text-dark mt-1 border">Role: Staff</span>
                            </div>
                        </td>
                        <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                        <td class="text-center">
                            <a href="index.php?controller=user&action=delete&id=<?= $u['user_id'] ?>&role=2" 
                               onclick="return confirm('Bạn sắp xóa nhân viên <?= htmlspecialchars($u['full_name']) ?>. Tiếp tục?')" 
                               class="btn-delete-icon" title="Xóa">
                               <i class="fa-solid fa-trash-can"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" class="text-center py-5">Chưa có nhân viên nào.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>