<div class="table-container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #2c3e50; margin: 0;">Thêm sản phẩm mới</h2>
        <a href="index.php?controller=admin-product&action=index" style="color: var(--primary-color); text-decoration: none;">
            <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <form action="index.php?controller=admin-product&action=store" method="POST" enctype="multipart/form-data" style="background: #fff;">

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px;">Tên sản phẩm <span style="color: var(--danger);">*</span></label>
            <input type="text" name="name" placeholder="Ví dụ: Chuột Logitech G Pro X 2" required
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Danh mục</label>
                <select name="category_id" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; background: #fff;">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Thương hiệu</label>
                <select name="brand_id" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; background: #fff;">
                    <?php foreach ($brands as $brand): ?>
                        <option value="<?= $brand['brand_id'] ?>"><?= htmlspecialchars($brand['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Giá bán (VNĐ) <span style="color: var(--danger);">*</span></label>
                <input type="number" name="price" placeholder="Nhập giá tiền" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Số lượng nhập kho <span style="color: var(--danger);">*</span></label>
                <input type="number" name="quantity" value="1" min="1" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px;">Ảnh sản phẩm</label>
            <div style="border: 2px dashed #ddd; padding: 20px; text-align: center; border-radius: 8px; background: #f9f9f9;">
                <input type="file" name="image" accept="image/*" style="cursor: pointer;">
                <p style="margin: 10px 0 0; font-size: 12px; color: #7f8c8d;">Định dạng: JPG, PNG, WEBP (Dưới 2MB)</p>
            </div>
        </div>

        <div style="margin-bottom: 25px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px;">Mô tả chi tiết</label>
            <textarea name="description" rows="5" placeholder="Nhập thông tin chi tiết về sản phẩm..."
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; resize: vertical;"></textarea>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary" style="padding: 12px 25px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-save"></i> Lưu sản phẩm
            </button>
            <button type="reset" class="btn" style="padding: 12px 25px; background: #95a5a6; color: #fff;">
                Nhập lại
            </button>
        </div>
    </form>
</div>