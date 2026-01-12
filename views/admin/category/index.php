<div class="table-container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0;"><i class="fa-solid fa-tags"></i> Quản lý danh mục</h2>
        <form action="index.php" method="GET" style="display: flex; gap: 5px;">
            <input type="hidden" name="controller" value="admin-category">
            <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                placeholder="Tìm danh mục..." style="padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
            <button type="submit" class="btn btn-primary" style="padding: 8px 15px;"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
    </div>

    <?php
    $editMode = isset($category_edit);
    $action_url = $editMode ? "update" : "store";
    ?>
    <div style="background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 25px; border: 1px solid #e1e8ed; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
        <h4 style="margin-top: 0; color: #2c3e50;"><?= $editMode ? 'Chỉnh sửa danh mục' : 'Thêm danh mục mới' ?></h4>
        <form action="index.php?controller=admin-category&action=<?= $action_url ?>" method="POST" style="display: grid; grid-template-columns: 1fr 2fr 150px auto; gap: 15px; align-items: end;">
            <?php if ($editMode): ?>
                <input type="hidden" name="id" value="<?= $category_edit['category_id'] ?>">
            <?php endif; ?>

            <div>
                <label style="display:block; font-size:12px; font-weight:600; margin-bottom:5px;">Tên danh mục</label>
                <input type="text" name="name" value="<?= $editMode ? htmlspecialchars($category_edit['name']) : '' ?>" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;">
            </div>
            <div>
                <label style="display:block; font-size:12px; font-weight:600; margin-bottom:5px;">Mô tả</label>
                <input type="text" name="description" value="<?= $editMode ? htmlspecialchars($category_edit['description']) : '' ?>" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;">
            </div>
            <div>
                <label style="display:block; font-size:12px; font-weight:600; margin-bottom:5px;">Trạng thái</label>
                <select name="status" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;">
                    <option value="1" <?= ($editMode && $category_edit['status'] == 1) ? 'selected' : '' ?>>Hiển thị</option>
                    <option value="0" <?= ($editMode && $category_edit['status'] == 0) ? 'selected' : '' ?>>Ẩn</option>
                </select>
            </div>
            <div style="display:flex; gap:5px;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 20px;"><?= $editMode ? 'Cập nhật' : 'Thêm mới' ?></button>
                <?php if ($editMode): ?>
                    <a href="index.php?controller=admin-category" class="btn" style="background:#95a5a6; color:#fff; text-decoration:none; padding:10px 15px; border-radius:4px;">Hủy</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <table class="table-admin">
        <thead>
            <tr style="background: #f8f9fa;">
                <th width="50">ID</th>
                <th>Tên danh mục</th>
                <th>Mô tả</th>
                <th width="120">Trạng thái</th>
                <th width="150" style="text-align: center;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $item): ?>
                <tr>
                    <td>#<?= $item['category_id'] ?></td>
                    <td style="font-weight: 600; color: #2c3e50;"><?= htmlspecialchars($item['name']) ?></td>
                    <td style="color: #7f8c8d; font-size: 13px;"><?= htmlspecialchars($item['description'] ?? '') ?></td>
                    <td>
                        <span style="background: <?= $item['status'] == 1 ? '#2ecc71' : '#95a5a6' ?>; color: #fff; padding: 3px 10px; border-radius: 20px; font-size: 11px;">
                            <?= $item['status'] == 1 ? 'Hiển thị' : 'Ẩn' ?>
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <a href="index.php?controller=admin-category&action=edit&id=<?= $item['category_id'] ?>" class="btn-sm btn-edit" style="text-decoration:none; margin-right:5px;">
                            <i class="fa-solid fa-pen"></i> Sửa
                        </a>
                        <a href="index.php?controller=admin-category&action=delete&id=<?= $item['category_id'] ?>" class="btn-sm btn-delete" style="text-decoration:none;" onclick="return confirm('Bạn có chắc chắn muốn xóa?')">
                            <i class="fa-solid fa-trash"></i> Xóa
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>