<?php require_once 'views/client/layouts/header.php'; ?>

<div class="container" style="margin-top: 50px; margin-bottom: 50px;">
    <h2 style="margin-bottom: 30px;">🛒 Giỏ hàng của bạn</h2>

    <?php if (empty($cart)): ?>
        <div style="text-align: center; padding: 50px; border: 1px dashed #ccc;">
            <p>Giỏ hàng trống. <a href="index.php?controller=product&action=index">Tiếp tục mua sắm</a></p>
        </div>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
            <thead>
                <tr style="background: #f4f4f4; text-align: left;">
                    <th style="padding: 15px;">Sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody id="cart-body">
            <?php 
            $total_bill = 0;
            foreach ($cart as $item): 
                $subtotal = $item['price'] * $item['quantity'];
                $total_bill += $subtotal;
            ?>
            <tr class="cart-item" style="border-bottom: 1px solid #eee;">
                <td style="padding: 15px; display: flex; align-items: center; gap: 15px;">
                    <img src="assets/uploads/<?php echo $item['image']; ?>" width="60">
                    <span><?php echo $item['name']; ?></span>
                </td>
                <td class="product-price" data-price="<?php echo $item['price']; ?>">
                    <?php echo number_format($item['price'], 0, ',', '.'); ?>đ
                </td>
                <td>
                    <input type="number" 
                           class="quantity-input"
                           data-id="<?php echo $item['product_id']; ?>" 
                           value="<?php echo $item['quantity']; ?>" 
                           min="1" style="width: 60px; padding: 5px; text-align: center;">
                </td>
                <td class="subtotal-display" style="color: #e74c3c; font-weight: bold;">
                    <?php echo number_format($subtotal, 0, ',', '.'); ?>đ
                </td>
                <td>
                    <a href="index.php?controller=cart&action=remove&id=<?php echo $item['product_id']; ?>" 
                       onclick="return confirm('Xóa sản phẩm này?')">Xóa</a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <div style="display: flex; justify-content: flex-end; align-items: flex-start;">
            <div style="text-align: right;">
                <h3>Tổng cộng: <span id="total-bill-display" style="color: #e74c3c;"><?php echo number_format($total_bill, 0, ',', '.'); ?>đ</span></h3>
                <div style="margin-top: 20px;">
                <a href="index.php?controller=product&action=index" 
                    style="padding: 10px 20px; border: 1px solid #333; text-decoration: none; color: #333; margin-right: 10px; display: inline-block;">
                    Tiếp tục mua hàng
                    </a>              
                  <a href="index.php?controller=cart&action=checkout" style="padding: 10px 25px; background: #27ae60; color: #fff; text-decoration: none; border-radius: 5px; font-weight: bold;">Tiến hành đặt hàng</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const quantityInputs = document.querySelectorAll('.quantity-input');
    const totalBillDisplay = document.getElementById('total-bill-display');
    const cartCountHeader = document.getElementById('cart-count'); // ID này phải có trong header.php

    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
    }

    function updateTotals() {
        let totalBill = 0;
        document.querySelectorAll('.cart-item').forEach(row => {
            const price = parseFloat(row.querySelector('.product-price').getAttribute('data-price'));
            const quantity = parseInt(row.querySelector('.quantity-input').value) || 0;
            const subtotal = price * quantity;
            row.querySelector('.subtotal-display').innerText = formatCurrency(subtotal);
            totalBill += subtotal;
        });
        totalBillDisplay.innerText = formatCurrency(totalBill);
    }

    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            const productId = this.getAttribute('data-id');
            const newQty = this.value;

            if (newQty < 1) return;

            // Cập nhật giao diện tiền ngay lập tức để tạo cảm giác mượt mà
            updateTotals();

            // Gửi dữ liệu lưu ngầm vào Database pcx_db
            fetch('index.php?controller=cart&action=updateAjax', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${productId}&qty=${newQty}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Cập nhật số lượng trên icon giỏ hàng ở Header
                    if (cartCountHeader) {
                        cartCountHeader.innerText = `(${data.newCount})`;
                    }
                } else {
                    alert('Lỗi cập nhật giỏ hàng!');
                    location.reload(); // Reset lại nếu có lỗi từ server
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });
});
</script>

<?php require_once 'views/client/layouts/footer.php'; ?>