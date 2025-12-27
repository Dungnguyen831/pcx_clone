<div class="table-container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #2c3e50; margin: 0;">Chỉnh sửa: <?= htmlspecialchars($product['name']) ?></h2>

        <a href="index.php?controller=admin-product&action=index"
            class="btn btn-back"
            onmouseover="this.style.backgroundColor='var(--primary-color)'"
            onmouseout="this.style.backgroundColor='#6c757d'"
            style="background: #6c757d; color: #fff; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 6px; font-weight: 600; transition: all 0.3s; box-shadow: 0 2px 4px rgba(0,0,0,0.1); cursor: pointer;">
            <i class="fa-solid fa-arrow-left-long"></i>
            <span>Quay lại danh sách</span>
        </a>
    </div>

    <form action="index.php?controller=admin-product&action=update" method="POST" enctype="multipart/form-data" style="background: #fff;">
        <input type="hidden" name="id" value="<?= $product['product_id'] ?>">
        <input type="hidden" name="old_image" value="<?= $product['image'] ?>">

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px;">Tên sản phẩm</label>
            <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px;">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Danh mục</label>
                <select name="category_id" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px;">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['category_id'] ?>" <?= ($cat['category_id'] == $product['category_id']) ? 'selected' : '' ?>>
                            <?= $cat['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Thương hiệu</label>
                <select name="brand_id" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px;">
                    <?php foreach ($brands as $brand): ?>
                        <option value="<?= $brand['brand_id'] ?>" <?= ($brand['brand_id'] == $product['brand_id']) ? 'selected' : '' ?>>
                            <?= $brand['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Giá bán</label>
                <input type="number" name="price" value="<?= $product['price'] ?>" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Số lượng kho</label>
                <input type="number" name="quantity" value="<?= $product['quantity'] ?>" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px;">Ảnh sản phẩm</label>
            <img src="assets/uploads/<?= $product['image'] ?>" width="120" style="display: block; margin-bottom: 10px; border-radius: 4px; border: 1px solid #eee;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px;">Chọn ảnh mới</label>
            <input type="file" name="image" accept="image/*">
        </div>

        <div style="margin-bottom: 25px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px;">Mô tả</label>
            <textarea name="description" rows="5" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px;"><?= htmlspecialchars($product['description']) ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary" style="padding: 12px 30px; font-weight: 600;">Cập nhật sản phẩm</button>
    </form>
</div>
