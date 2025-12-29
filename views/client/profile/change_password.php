<?php require_once 'views/client/layouts/header.php'; ?>

<div class="container" style="max-width: 500px; margin: 50px auto; padding: 30px; border: 1px solid #eee; border-radius: 10px; font-family: Arial, sans-serif; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
    <h2 style="text-align: center; color: #333;">🔐 Đổi mật khẩu</h2>
    
    <?php if (isset($error)): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
        <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <form action="index.php?controller=auth&action=changePassword" method="POST">
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px;">Mật khẩu hiện tại:</label>
            <input type="password" name="old_password" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box;">
        </div>
        
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px;">Mật khẩu mới:</label>
            <input type="password" name="new_password" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box;">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px;">Xác nhận mật khẩu mới:</label>
            <input type="password" name="confirm_password" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box;">
        </div>
        
        <button type="submit" style="width: 100%; padding: 14px; background: #2563eb; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 16px;">
            CẬP NHẬT MẬT KHẨU
        </button>
    </form>
    
    <div style="margin-top: 20px; text-align: center;">
        <a href="index.php?controller=auth&action=profile" style="color: #666; text-decoration: none; font-size: 14px;">
            ← Quay lại Trang cá nhân
        </a>
    </div>
</div>

<?php require_once 'views/client/layouts/footer.php'; ?>