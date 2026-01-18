<div class="table-container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0;"><i class="fa-solid fa-copyright"></i> Quản lý hãng sản xuất</h2>
        <form action="index.php" method="GET" style="display: flex; gap: 5px;">
            <input type="hidden" name="controller" value="admin-brand">
            <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                placeholder="Tìm tên hãng..." style="padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
            <button type="submit" class="btn btn-primary" style="padding: 8px 15px;"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
    </div>

    <?php
    $isEdit = isset($brand_edit);
    $target_action = $isEdit ? "update" : "store";
    ?>
    <div style="background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 25px; border: 1px solid #e1e8ed; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
        <h4 style="margin-top: 0; color: #2c3e50;"><?= $isEdit ? 'Cập nhật hãng: ' . htmlspecialchars($brand_edit['name']) : 'Thêm hãng mới' ?></h4>

        <form action="index.php?controller=admin-brand&action=<?= $target_action ?>" method="POST" enctype="multipart/form-data" style="display: grid; grid-template-columns: 1fr 2fr auto; gap: 15px; align-items: end;">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= $brand_edit['brand_id'] ?>">
                <input type="hidden" name="old_logo" value="<?= htmlspecialchars($brand_edit['logo_url'] ?? '') ?>">
            <?php endif; ?>

            <div>
                <label style="display:block; font-size:12px; font-weight:600; margin-bottom:5px;">Tên hãng</label>
                <input type="text" name="name" value="<?= $isEdit ? htmlspecialchars($brand_edit['name']) : '' ?>" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;">
            </div>

            <div>
                <label style="display:block; font-size:12px; font-weight:600; margin-bottom:5px;">Logo Hãng (Tải lên ảnh)</label>
                <input type="file" name="logo_file" accept="image/*" style="width:100%; padding:5px; border: 1px dashed #cbd5e0; border-radius: 4px;">
                <?php if ($isEdit && !empty($brand_edit['logo_url'])): ?>
                    <div style="margin-top: 5px; font-size: 11px; color: #718096;">Ảnh hiện tại: <?= htmlspecialchars($brand_edit['logo_url']) ?></div>
                <?php endif; ?>
            </div>

            <div style="display:flex; gap:5px;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 20px;"><?= $isEdit ? 'Lưu thay đổi' : 'Thêm hãng' ?></button>
                <?php if ($isEdit): ?>
                    <a href="index.php?controller=admin-brand" class="btn" style="background:#95a5a6; color:#fff; text-decoration:none; padding:10px 15px; border-radius:4px;">Hủy</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <table class="table-admin">
        <thead>
            <tr style="background: #f8f9fa;">
                <th width="50">ID</th>
                <th width="100">Logo</th>
                <th>Tên hãng sản xuất</th>
                <th>Tên file / URL</th>
                <th width="150" style="text-align: center;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($brands)): ?>
                <?php foreach ($brands as $item): ?>
                    <tr>
                        <td>#<?= $item['brand_id'] ?></td>
                        <td>
                            <?php if (!empty($item['logo_url'])): ?>
                                <?php
                                // Kiểm tra nếu là link ngoài thì dùng trực tiếp, nếu là file thì trỏ vào thư mục upload
                                $imagePath = (strpos($item['logo_url'], 'http') === 0)
                                    ? $item['logo_url']
                                    : 'assets/uploads/brands/' . $item['logo_url'];
                                ?>
                                <img src="<?= $imagePath ?>" alt="logo" style="height: 40px; max-width: 80px; object-fit: contain; border-radius: 4px; border: 1px solid #edf2f7;">
                            <?php else: ?>
                                <span style="color: #ccc; font-size: 11px;">No logo</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-weight: 600; color: #2c3e50;"><?= htmlspecialchars($item['name']) ?></td>
                        <td style="color: #7f8c8d; font-size: 12px; font-family: monospace;">
                            <?= htmlspecialchars($item['logo_url'] ?? 'N/A') ?>
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="index.php?controller=admin-brand&action=edit&id=<?= $item['brand_id'] ?>" class="btn-sm btn-edit" title="Sửa">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <a href="index.php?controller=admin-brand&action=delete&id=<?= $item['brand_id'] ?>" class="btn-sm btn-delete" onclick="return confirm('Xóa hãng sẽ ảnh hưởng đến các sản phẩm liên quan?')" title="Xóa">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px; color: #94a3b8;">Không có dữ liệu hãng.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>