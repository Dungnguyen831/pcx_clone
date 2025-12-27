<?php require_once 'views/client/layouts/header.php'; ?>

<div class="auth-wrapper">
    <div class="auth-box">
        <h2 class="auth-title">Đăng Ký Tài Khoản</h2>

        <?php if (isset($error)): ?>
            <div class="error-msg">
                <i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form id="registerForm" action="index.php?controller=auth&action=processRegister" method="POST">
            <div class="form-group">
                <label for="full_name">Họ và tên</label>
                <input type="text" id="full_name" name="full_name" class="form-control"
                    value="<?php echo isset($old_data['full_name']) ? htmlspecialchars($old_data['full_name']) : ''; ?>"
                    placeholder="Nhập họ và tên">
                <small class="error-text" id="nameError" style="color: red;"></small>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control"
                    value="<?php echo isset($old_data['email']) ? htmlspecialchars($old_data['email']) : ''; ?>"
                    placeholder="Nhập email của bạn">
                <small class="error-text" id="emailError" style="color: red;"></small>
            </div>

            <div class="form-group">
                <label for="phone">Số điện thoại</label>
                <input type="text" id="phone" name="phone" class="form-control"
                    value="<?php echo isset($old_data['phone']) ? htmlspecialchars($old_data['phone']) : ''; ?>"
                    placeholder="Nhập số điện thoại">
                <small class="error-text" id="phoneError" style="color: red;"></small>
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <div class="password-wrapper" style="position: relative;">
                    <input type="password" id="password" name="password" class="form-control" placeholder="Nhập mật khẩu">
                    <i class="fa-solid fa-eye toggle-password" data-target="password"
                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #666;"></i>
                </div>
                <small class="error-text" id="passwordError" style="color: red;"></small>
            </div>

            <div class="form-group">
                <label for="confirm_password">Xác nhận mật khẩu</label>
                <div class="password-wrapper" style="position: relative;">
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Nhập lại mật khẩu">
                    <i class="fa-solid fa-eye toggle-password" data-target="confirm_password"
                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #666;"></i>
                </div>
                <small class="error-text" id="confirmPasswordError" style="color: red;"></small>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Đăng ký</button>
        </form>

        <div class="auth-links">
            <span>Đã có tài khoản? <a href="index.php?controller=auth&action=login">Đăng nhập ngay</a></span>
        </div>
    </div>
</div>
<script src="assets/js/RegisterVal.js"></script>

<?php require_once 'views/client/layouts/footer.php'; ?>