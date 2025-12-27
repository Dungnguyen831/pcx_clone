<?php require_once 'views/client/layouts/header.php'; ?>

<div class="auth-wrapper">
    <div class="auth-box">
        <h2 class="auth-title">Đăng Nhập</h2>

        <?php if (isset($error)): ?>
            <div class="error-msg">
                <i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
            <div style="padding: 15px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 4px; margin-bottom: 20px;">
                <strong>Chúc mừng!</strong> Bạn đã đăng ký tài khoản thành công. Vui lòng đăng nhập.
            </div>
        <?php endif; ?>

        <form action="index.php?controller=auth&action=processLogin" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="Nhập email của bạn" required>
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Nhập mật khẩu" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">
                Đăng nhập
            </button>
        </form>

        <div class="auth-links">
            <a href="#">Quên mật khẩu?</a>
            <span>Chưa có tài khoản? <a href="index.php?controller=auth&action=register">Đăng ký ngay</a></span>
        </div>
    </div>
</div>

<?php require_once 'views/client/layouts/footer.php'; ?>