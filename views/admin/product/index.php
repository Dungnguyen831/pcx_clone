<div class="table-container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #2c3e50; margin: 0; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-boxes-stacked" style="color: var(--primary-color);"></i>
            Quản lý sản phẩm
        </h2>

        <div style="display: flex; gap: 10px;">
            <!-- Xuất Excel -->
            <a href="index.php?controller=admin-product&action=exportExcel"
            class="btn"
            style="background:#ecfeff; color:#0891b2; border:1px solid #bae6fd;
                    display:flex; align-items:center; gap:8px; padding:10px 18px;
                    border-radius:6px; font-weight:600;">
                <i class="fa-solid fa-file-export"></i> Xuất Excel
            </a>

            <!-- Nhập Excel -->
            <button type="button"
                onclick="openExcel()"
                class="btn"
                style="background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0;
                        display:flex; align-items:center; gap:8px; padding:10px 18px;
                        border-radius:6px; font-weight:600;">
                <i class="fa-solid fa-file-import"></i> Nhập Excel
            </button>


            <!-- Thêm sản phẩm -->
            <a href="index.php?controller=admin-product&action=create"
            class="btn btn-primary"
            style="display:flex; align-items:center; gap:8px; padding:10px 20px;">
                <i class="fa-solid fa-plus"></i> Thêm sản phẩm mới
            </a>
        </div>
    </div>

    <form id="importForm"
      action="index.php?controller=admin-product&action=importExcel"
      method="POST"
      enctype="multipart/form-data"
      style="display:none">

    <input type="file"
           id="excelInput"
           name="excel_file"
           accept=".xls, .xlsx">
    </form>

    <div style="background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 25px; border: 1px solid #edf2f7; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
        <form action="index.php" method="GET" style="display: grid; grid-template-columns: 1fr 3fr auto; gap: 15px; align-items: end;">
            <input type="hidden" name="controller" value="admin-product">
            <input type="hidden" name="action" value="index">

            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #4a5568; font-size: 13px;">Mã sản phẩm (ID)</label>
                <input type="number" name="search_id" value="<?= htmlspecialchars($_GET['search_id'] ?? '') ?>"
                    placeholder="Ví dụ: 8"
                    style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; outline: none;">
            </div>

            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #4a5568; font-size: 13px;">Tên sản phẩm</label>
                <div style="position: relative;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #a0aec0;"></i>
                    <input type="text" name="search_name" value="<?= htmlspecialchars($_GET['search_name'] ?? '') ?>"
                        placeholder="Nhập tên sản phẩm cần tìm..."
                        style="width: 100%; padding: 10px 10px 10px 35px; border: 1px solid #e2e8f0; border-radius: 6px; outline: none;">
                </div>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 20px; border-radius: 6px; cursor: pointer;">
                    Tìm kiếm
                </button>
                <a href="index.php?controller=admin-product&action=index" class="btn"
                    style="background: #edf2f7; color: #4a5568; padding: 10px 15px; border-radius: 6px; text-decoration: none; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-rotate-right"></i>
                </a>
            </div>
        </form>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <?php
        $msg = '';
        $bg = '';
        if ($_GET['msg'] == 'success') {
            $msg = 'Thêm sản phẩm thành công!';
            $bg = 'var(--success)';
        }
        if ($_GET['msg'] == 'updated') {
            $msg = 'Cập nhật thành công!';
            $bg = 'var(--primary-color)';
        }
        if ($_GET['msg'] == 'deleted') {
            $msg = 'Đã xóa sản phẩm!';
            $bg = 'var(--danger)';
        }
        ?>
        <?php if ($msg): ?>
            <div style="padding: 12px 20px; background: <?= $bg ?>; color: #fff; border-radius: 6px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-weight: 500;">
                <i class="fa-solid fa-circle-check"></i> <?= $msg ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <table class="table-admin" style="width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden;">
        <thead>
            <tr style="background: #f8fafc; border-bottom: 2px solid #edf2f7;">
                <th style="padding: 15px; text-align: left;">ID</th>
                <th width="80px" style="padding: 15px;">Ảnh</th>
                <th style="padding: 15px; text-align: left;">Tên sản phẩm</th>
                <th style="padding: 15px; text-align: left;">Danh mục</th>
                <th style="padding: 15px; text-align: left;">Thương hiệu</th>
                <th style="padding: 15px; text-align: left;">Giá bán</th>
                <th style="padding: 15px; text-align: left;">Kho</th>
                <th width="120px" style="padding: 15px; text-align: center;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $p): ?>
                    <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;">
                        <td style="padding: 12px 15px; color: #64748b; font-family: monospace;">#<?= $p['product_id'] ?></td>
                        <td style="padding: 12px 15px;">
                            <img src="assets/uploads/products/<?= !empty($p['image']) ? $p['image'] : 'default.png' ?>"
                                alt="Product"
                                style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px; border: 1px solid #f1f5f9; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                        </td>
                        <td style="padding: 12px 15px;">
                            <div style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($p['name']) ?></div>
                        </td>
                        <td style="padding: 12px 15px; color: #475569;"><?= htmlspecialchars($p['category_name'] ?? 'N/A') ?></td>
                        <td style="padding: 12px 15px;">
                            <span style="background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; border: 1px solid #e2e8f0;">
                                <?= htmlspecialchars($p['brand_name'] ?? 'Chưa rõ') ?>
                            </span>
                        </td>
                        <td style="padding: 12px 15px; color: var(--primary-color); font-weight: 700;">
                            <?= number_format($p['price'], 0, ',', '.') ?>đ
                        </td>
                        <td style="padding: 12px 15px;">
                            <span style="display: inline-flex; align-items: center; gap: 5px; font-weight: 600; color: <?= ($p['quantity'] <= 0) ? 'var(--danger)' : '#10b981' ?>;">
                                <i class="fa-solid <?= ($p['quantity'] <= 0) ? 'fa-triangle-exclamation' : 'fa-circle-check' ?>" style="font-size: 10px;"></i>
                                <?= $p['quantity'] ?? 0 ?>
                            </span>
                        </td>
                        <td style="padding: 12px 15px;">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="index.php?controller=admin-product&action=edit&id=<?= $p['product_id'] ?>"
                                    class="btn-sm btn-edit" title="Sửa" style="background: #f1f5f9; color: var(--primary-color); border: 1px solid #e2e8f0; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 6px;">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <a href="index.php?controller=admin-product&action=delete&id=<?= $p['product_id'] ?>"
                                    class="btn-sm btn-delete"
                                    onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')" title="Xóa" style="background: #fff5f5; color: var(--danger); border: 1px solid #fed7d7; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 6px;">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 60px 0; color: #94a3b8; background: #fafafa;">
                        <i class="fa-solid fa-magnifying-glass" style="font-size: 48px; display: block; margin-bottom: 15px; color: #e2e8f0;"></i>
                        <span style="font-size: 16px; font-weight: 500;">Không tìm thấy sản phẩm nào phù hợp</span><br>
                        <small>Vui lòng thử lại với từ khóa khác hoặc xóa bộ lọc</small>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- JS IMPORT EXCEL -->
<script>
function openExcel() {
    document.getElementById('excelInput').click();
}

document.getElementById('excelInput').addEventListener('change', function () {
    if (this.files.length > 0) {
        document.getElementById('importForm').submit();
    }
});
</script>
