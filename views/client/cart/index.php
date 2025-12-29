<?php require_once 'views/client/layouts/header.php'; ?>

<div class="container" style="margin-top: 30px; min-height: 400px;">
    <h2>Giỏ hàng của bạn</h2>

    <?php if (empty($cartItems)): ?>
        <div style="text-align: center; padding: 50px;">
            <p>Giỏ hàng đang trống!</p>
            <a href="index.php?controller=product&action=index" class="btn btn-primary">Tiếp tục mua sắm</a>
        </div>
    <?php else: ?>
        
        <form action="index.php?controller=cart&action=update" method="POST">
            <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #ddd;">
                        <th style="padding: 10px; text-align: left;">Sản phẩm</th>
                        <th style="padding: 10px;">Đơn giá</th>
                        <th style="padding: 10px;">Số lượng</th>
                        <th style="padding: 10px;">Thành tiền</th>
                        <th style="padding: 10px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 15px; display: flex; align-items: center; gap: 15px;">
                                <img src="assets/uploads/<?php echo $item['image']; ?>" width="60" height="60" style="object-fit: contain; border: 1px solid #ddd;">
                                <strong><?php echo $item['name']; ?></strong>
                            </td>
                            <td style="text-align: center; color: #e74c3c; font-weight: bold;">
                                <?php echo number_format($item['price'], 0, ',', '.'); ?>đ
                            </td>
                            <td style="text-align: center;">
                                <input type="number" name="quantity[<?php echo $item['cart_id']; ?>]" 
                                       value="<?php echo $item['quantity']; ?>" min="1" 
                                       style="width: 50px; text-align: center; padding: 5px;">
                            </td>
                            <td style="text-align: center; font-weight: bold;">
                                <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>đ
                            </td>
                            <td style="text-align: center;">
                                <a href="index.php?controller=cart&action=delete&cart_id=<?php echo $item['cart_id']; ?>" 
                                   style="color: red; text-decoration: none;"
                                   onclick="return confirm('Bạn chắc chắn muốn xóa?')">
                                   <i class="fa-solid fa-trash"></i> Xóa
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div style="margin-top: 30px; display: flex; justify-content: space-between; align-items: center;">
                <button type="submit" class="btn" style="background: #7f8c8d; color: white;">Cập nhật giỏ hàng</button>
                
                <div style="text-align: right;">
                    <h3>Tổng tiền: <span style="color: #e74c3c;"><?php echo number_format($totalPrice, 0, ',', '.'); ?>đ</span></h3>
                    <a href="index.php?controller=order&action=checkout" class="btn btn-primary" style="padding: 12px 30px; margin-top: 10px;">
                        Tiến hành thanh toán <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php require_once 'views/client/layouts/footer.php'; ?>