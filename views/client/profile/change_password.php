<?php require_once 'views/client/layouts/header.php'; ?>
<Link rel="stylesheet" href="assets/css/change_password.css">
<div class="password-card">
    <h2>🔐 Đổi mật khẩu</h2>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-error">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <form action="index.php?controller=auth&action=changePassword" method="POST">
        <div class="form-group">
            <label>Mật khẩu hiện tại:</label>
            <input type="password" name="old_password" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label>Mật khẩu mới:</label>
            <input type="password" name="new_password" class="form-control" required>
        </div>
        
        <div class="form-group" style="margin-bottom: 20px;">
            <label>Xác nhận mật khẩu mới:</label>
            <input type="password" name="confirm_password" class="form-control" required>
        </div>
        
        <button type="submit" class="btn-update">
            CẬP NHẬT MẬT KHẨU
        </button>
    </form>
    
    <div class="back-link">
        <a href="index.php?controller=auth&action=profile">
            ← Quay lại Trang cá nhân
        </a>
    </div>
</div>

<?php require_once 'views/client/layouts/footer.php'; ?>