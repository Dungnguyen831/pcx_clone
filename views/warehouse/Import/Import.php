<div class="card" style="background: white; padding: 30px; border-radius: 10px; border-top: 5px solid #27ae60; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto;">
    <h3 style="color: #27ae60; margin-bottom: 20px;">
        <i class="fa-solid fa-cart-plus"></i> Tạo Phiếu Nhập Hàng Mới
    </h3>
    
    <form action="index.php?controller=warehouse&action=processImport" method="POST">
        <div class="form-group" style="margin-bottom: 20px;">
            <label style="display:block; font-weight:bold; margin-bottom: 8px;">Chọn sản phẩm nhập về:</label>
            <select name="product_id" required style="width:100%; padding:12px; border-radius:5px; border:1px solid #ddd; font-size: 16px;">
                <option value="">-- Chọn sản phẩm --</option>
                <?php foreach($products as $p): ?>
                    <option value="<?= $p['product_id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label style="display:block; font-weight:bold; margin-bottom: 8px;">Số lượng nhập:</label>
            <input type="number" name="quantity" min="1" required style="width:100%; padding:12px; border-radius:5px; border:1px solid #ddd;" placeholder="Ví dụ: 50">
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label style="display:block; font-weight:bold; margin-bottom: 8px;">Giá Nhập(VNĐ/Sản Phẩm):</label>
            <input type="number" name="price" min="0" required 
           style="width:100%; padding:12px; border-radius:5px; border:1px solid #ddd; box-sizing: border-box;">
        </div>

        <button type="submit" style="width:100%; padding:15px; background:#27ae60; color:white; border:none; border-radius:5px; cursor:pointer; font-weight:bold; font-size: 16px;">
            <i class="fa-solid fa-check-double"></i> XÁC NHẬN NHẬP KHO
        </button>

    </form>
</div>