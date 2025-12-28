<?php require_once 'views/client/layouts/header.php'; ?>

<div class="container" style="margin-top: 50px; margin-bottom: 50px;">
    <h2 style="margin-bottom: 30px;">🛒 Giỏ hàng của bạn</h2>

    <?php if (empty($cart)): ?>
        <div style="text-align: center; padding: 50px; border: 1px dashed #ccc;">
            <p>Giỏ hàng trống. <a href="index.php?controller=product&action=index">Tiếp tục mua sắm</a></p>
        </div>
    <?php else: ?>
        <form action="index.php?controller=cart&action=update" method="POST">
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
                <tbody>
                <?php 
$total_bill = 0;
foreach ($cart as $item): 
    $subtotal = $item['price'] * $item['quantity'];
    $total_bill += $subtotal;
?>
<tr style="border-bottom: 1px solid #eee;">
    <td style="padding: 15px; display: flex; align-items: center; gap: 15px;">
        <img src="assets/uploads/<?php echo $item['image']; ?>" width="60">
        <span><?php echo $item['name']; ?></span>
    </td>
    <td><?php echo number_format($item['price'], 0, ',', '.'); ?>đ</td>
    <td>
        <input type="number" name="qty[<?php echo $item['product_id']; ?>]" 
               value="<?php echo $item['quantity']; ?>" 
               min="1" style="width: 60px; padding: 5px; text-align: center;">
    </td>
    <td style="color: #e74c3c; font-weight: bold;">
        <?php echo number_format($subtotal, 0, ',', '.'); ?>đ
    </td>
    <td>
        <a href="index.php?controller=cart&action=remove&id=<?php echo $item['product_id']; ?>" 
           onclick="return confirm('Xóa?')">Xóa</a>
    </td>
</tr>
<?php endforeach; ?>
                </tbody>
            </table>

            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <button type="submit" style="padding: 10px 20px; background: #34495e; color: #fff; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">
                    <i class="fa-solid fa-arrows-rotate"></i> Cập nhật giỏ hàng
                </button>

                <div style="text-align: right;">
                    <h3>Tổng cộng: <span style="color: #e74c3c;"><?php echo number_format($total_bill, 0, ',', '.'); ?>đ</span></h3>
                    <div style="margin-top: 20px;">
                        <a href="index.php?controller=product&action=index" style="padding: 10px 20px; border: 1px solid #333; text-decoration: none; color: #333; margin-right: 10px;">Tiếp tục mua hàng</a>
                        <a href="index.php?controller=cart&action=checkout" style="padding: 10px 25px; background: #27ae60; color: #fff; text-decoration: none; border-radius: 5px; font-weight: bold;">Tiến hành đặt hàng</a>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php require_once 'views/client/layouts/footer.php'; ?>