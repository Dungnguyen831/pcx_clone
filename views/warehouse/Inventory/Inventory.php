<div class="card" style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
    <h3 style="margin-bottom: 25px;"><i class="fa-solid fa-boxes-stacked"></i> Kiểm Soát Tồn Kho</h3>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div style="background: #e3f2fd; padding: 20px; border-radius: 8px; border-left: 5px solid #2196f3;">
            <small>Tổng chủng loại</small>
            <h2 style="margin: 5px 0;"><?= count($products) ?></h2>
        </div>
        </div>

    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                <th style="padding: 15px; text-align: left;">Mã SP</th>
                <th style="padding: 15px; text-align: left;">Tên sản phẩm</th>
                <th style="padding: 15px; text-align: center;">Tồn kho thực tế</th>
                <th style="padding: 15px; text-align: center;">Cảnh báo</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($products as $p): ?>
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 15px;">#<?= $p['product_id'] ?></td>
                <td style="padding: 15px; font-weight: bold;"><?= htmlspecialchars($p['name']) ?></td>
                <td style="padding: 15px; text-align: center; font-size: 18px; font-weight: bold;">
                    <?= $p['quantity'] ?? 0 ?>
                </td>
                <td style="padding: 15px; text-align: center;">
                    <?php if(($p['quantity'] ?? 0) <= 5): ?>
                        <span style="color: #e74c3c;"><i class="fa-solid fa-triangle-exclamation"></i> Sắp hết hàng!</span>
                    <?php else: ?>
                        <span style="color: #27ae60;"><i class="fa-solid fa-circle-check"></i> An toàn</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>