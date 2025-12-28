<div class="table-container" style="max-width: 800px; margin: 0 auto;">
    <h2 style="margin-bottom: 20px;"><i class="fa-solid fa-address-card"></i> Thông tin cá nhân</h2>

    <div style="background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <div style="display: flex; gap: 30px; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 20px;">
            <div style="width: 100px; height: 100px; background: #3498db; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 40px;">
                <i class="fa-solid fa-user-gear"></i>
            </div>
            <div>
                <h3 style="margin: 0; color: #2c3e50;"><?= htmlspecialchars($user['full_name']) ?></h3>
                <p style="margin: 5px 0; color: #7f8c8d;">
                    Chức vụ: <strong><?= ($user['role'] == 1) ? 'Quản trị viên' : 'Khách hàng' ?></strong>
                </p>
                <p style="margin: 0; font-size: 13px; color: #bdc3c7;">Ngày tham gia: <?= date('d/m/Y', strtotime($user['created_at'])) ?></p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 5px; color: #34495e;">Họ và tên</label>
                <div style="padding: 10px; background: #f8f9fa; border-radius: 4px; border: 1px solid #eee;">
                    <?= htmlspecialchars($user['full_name']) ?>
                </div>
            </div>
            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 5px; color: #34495e;">Email liên hệ</label>
                <div style="padding: 10px; background: #f8f9fa; border-radius: 4px; border: 1px solid #eee;">
                    <?= htmlspecialchars($user['email']) ?>
                </div>
            </div>
            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 5px; color: #34495e;">Số điện thoại</label>
                <div style="padding: 10px; background: #f8f9fa; border-radius: 4px; border: 1px solid #eee;">
                    <?= htmlspecialchars($user['phone'] ?? 'Chưa cập nhật') ?>
                </div>
            </div>
            <div>
                <label style="display: block; font-weight: bold; margin-bottom: 5px; color: #34495e;">Mật khẩu</label>
                <div style="padding: 10px; background: #f8f9fa; border-radius: 4px; border: 1px solid #eee;">
                    ******** (Bảo mật)
                </div>
            </div>
        </div>
    </div>
</div>