<?php require_once 'views/client/layouts/header.php'; ?>
<link rel="stylesheet" href="assets/css/profile.css">

<div class="profile-wrapper">
    <div class="profile-card">
        
        <div class="profile-header">
            <div class="profile-icon-circle">
                <i class="fa-solid fa-user fa-2xl"></i>
            </div>
            <h3 style="font-weight: normal; margin: 0;">Hồ sơ của tôi</h3>
            <p style="color: #64748b; font-size: 13px; margin-top: 5px;">Quản lý thông tin tài khoản</p>
        </div>

        <?php if (isset($message)): ?>
            <div style="background: #dcfce7; color: #166534; padding: 10px; border-radius: 8px; text-align: center; margin-bottom: 20px; font-size: 14px;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div id="view-section">
            <div class="info-group">
                <i class="fa-solid fa-envelope"></i>
                <div>
                    <span class="info-label">Email</span>
                    <span class="info-value"><?php echo $user['email']; ?></span>
                </div>
            </div>

            <div class="info-group">
                <i class="fa-solid fa-signature"></i>
                <div>
                    <span class="info-label">Họ và Tên</span>
                    <span class="info-value"><?php echo $user['full_name']; ?></span>
                </div>
            </div>

            <div class="info-group">
                <i class="fa-solid fa-phone"></i>
                <div>
                    <span class="info-label">Số điện thoại</span>
                    <span class="info-value"><?php echo $user['phone'] ?? 'Chưa cập nhật'; ?></span>
                </div>
            </div>

            <button onclick="toggleEdit()" class="btn-edit-toggle">
                Thay đổi thông tin
            </button>
        </div>

        <div id="edit-section" class="hidden">
            <form action="index.php?controller=auth&action=profile" method="POST">
                <div style="margin-bottom: 15px;">
                    <label class="info-label">Họ và tên mới</label>
                    <input type="text" name="full_name" class="form-control-custom" value="<?php echo $user['full_name']; ?>" required>
                </div>

                <div style="margin-bottom: 20px;">
                    <label class="info-label">Số điện thoại mới</label>
                    <input type="text" name="phone" class="form-control-custom" value="<?php echo $user['phone']; ?>">
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="submit" style="flex: 2; padding: 12px; background: #2563EB; color: white; border: none; border-radius: 8px; cursor: pointer;">Lưu cập nhật</button>
                    <button type="button" onclick="toggleEdit()" style="flex: 1; padding: 12px; background: #f1f5f9; color: #64748b; border: none; border-radius: 8px; cursor: pointer;">Hủy</button>
                </div>
            </form>
        </div>

        <div class="profile-footer-links">
            <a href="index.php?controller=auth&action=changePassword" class="link-password">
                <i class="fa-solid fa-key"></i> Đổi mật khẩu
            </a>
            <a href="index.php?controller=auth&action=logout" class="link-logout">
                <i class="fa-solid fa-power-off"></i> Đăng xuất
            </a>
        </div>
    </div>
</div>

<script>
function toggleEdit() {
    const view = document.getElementById('view-section');
    const edit = document.getElementById('edit-section');
    view.classList.toggle('hidden');
    edit.classList.toggle('hidden');
}
</script>

<?php require_once 'views/client/layouts/footer.php'; ?>