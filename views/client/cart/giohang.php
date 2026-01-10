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
                // Đảm bảo Controller đã JOIN bảng inventory để có stock_quantity
                $stock = isset($item['stock_quantity']) ? $item['stock_quantity'] : 0; 
                $is_out_of_stock = ($item['quantity'] > $stock);
            ?>
            <tr class="cart-item" style="border-bottom: 1px solid #eee; <?php echo $is_out_of_stock ? 'background: #fff5f5;' : ''; ?>">
                <td style="padding: 15px; display: flex; align-items: center; gap: 15px;">
                    <img src="assets/uploads/products/<?php echo $item['image']; ?>" width="60">
                    <span class="product-name" style="font-weight: bold;"><?php echo $item['name']; ?></span>
                </td>
                <td class="product-price" data-price="<?php echo $item['price']; ?>">
                    <?php echo number_format($item['price'], 0, ',', '.'); ?>đ
                </td>
                <td>
                    <input type="number" 
                           class="quantity-input"
                           data-id="<?php echo $item['product_id']; ?>" 
                           data-stock="<?php echo $stock; ?>" 
                           value="<?php echo $item['quantity']; ?>" 
                           min="1" style="width: 60px; padding: 5px; text-align: center; border: 1px solid <?php echo $is_out_of_stock ? 'red' : '#ccc'; ?>;">
                    <br>
                    <small style="color: <?php echo $is_out_of_stock ? 'red' : '#666'; ?>;">
                        Kho còn: <strong><?php echo $stock; ?></strong>
                    </small>
                </td>
                <td class="subtotal-display" style="color: #e74c3c; font-weight: bold;">
                    <?php echo number_format($subtotal, 0, ',', '.'); ?>đ
                </td>
                <td>
                    <a href="index.php?controller=cart&action=remove&id=<?php echo $item['product_id']; ?>" 
                       style="color: #e74c3c; text-decoration: none;"
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
                       style="padding: 10px 20px; border: 1px solid #333; text-decoration: none; color: #333; margin-right: 10px; display: inline-block; border-radius: 5px;">
                        Tiếp tục mua hàng
                    </a>              
                    <a href="index.php?controller=cart&action=checkout" id="btn-checkout" 
                       style="padding: 10px 25px; background: #27ae60; color: #fff; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
                        Tiến hành đặt hàng
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<script src="assets/js/cart.js"></script>

<?php require_once 'views/client/layouts/footer.php'; ?>