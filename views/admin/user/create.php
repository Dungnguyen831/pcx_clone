<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow user-card"> <div class="form-card-header"> <h5 class="mb-0"><i class="fa-solid fa-user-plus"></i> Thêm nhân viên mới</h5>
            </div>
            <div class="card-body p-4">
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form action="index.php?controller=user&action=store" method="POST">
                    
                    <div class="mb-3">
                        <label class="form-label-custom">Họ và Tên <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" class="form-control form-control-custom" required placeholder="Nguyễn Văn A">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label-custom">Email đăng nhập <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control form-control-custom" required>
                        </div>
                        
                    </div>

                    <div class="mb-3">
                        <label class="form-label-custom">Mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control form-control-custom" required>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="index.php?controller=user&action=index&role=2" class="btn btn-light">Quay lại</a>
                        <button type="submit" class="btn btn-primary px-4">Lưu nhân viên</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>