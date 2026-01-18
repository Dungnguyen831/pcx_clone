<div style="margin-bottom: 20px; display: flex; justify-content: flex-end;">
    <form action="index.php" method="GET" style="display: flex; gap: 10px; align-items: center;">
        <input type="hidden" name="controller" value="warehouse">
        <input type="hidden" name="action" value="history">
        
        <div style="position: relative; width: 250px;">
            <input type="text" name="search" id="searchInput" placeholder="Tìm tên sản phẩm..." 
                   autocomplete="off"
                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                   style="padding: 8px 15px; border: 1px solid #ddd; border-radius: 20px; width: 100%; box-sizing: border-box;">
        </div>
               
        <button type="submit" style="padding: 8px 20px; background: #3498db; color: white; border: none; border-radius: 20px; cursor: pointer;">
            <i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm
        </button>
        
        <?php if(isset($_GET['search'])): ?>
            <a href="index.php?controller=warehouse&action=history" style="padding: 8px 10px; color: #e74c3c; text-decoration: none;">Xóa bộ lọc</a>
        <?php endif; ?>
    </form>
</div>

<div class="card" style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin: 0;"><i class="fa-solid fa-clock-rotate-left"></i> Nhật Ký Nhập Kho</h3>
        <div style="display: flex; gap: 10px;">
            <a href="index.php?controller=warehouse&action=exportHistoryExcel<?= isset($_GET['search']) ? '&search='.$_GET['search'] : '' ?>" 
            style="padding: 8px 15px; background: #27ae60; color: white; border-radius: 4px; text-decoration: none; font-size: 14px;">
                <i class="fa-solid fa-file-excel"></i> Xuất Excel
            </a>
        <button onclick="window.print()" style="padding: 8px 15px; background: #eee; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">
            <i class="fa-solid fa-print"></i> In báo cáo
        </button>
</div>
    </div>

    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                <th style="padding: 15px; text-align: left;">Ngày thực hiện</th>
                <th style="padding: 15px; text-align: left;">Tên sản phẩm</th>
                <th style="padding: 15px; text-align: center;">Số lượng</th>
                <th style="padding: 15px; text-align: right;">Giá nhập</th>
                <th style="padding: 15px; text-align: right;">Thành tiền</th>
                <th style="padding: 15px; text-align: left;">Ghi chú</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($history)): ?>
                <tr><td colspan="4" style="text-align:center; padding:30px; color: #999;">Chưa có dữ liệu nhập hàng.</td></tr>
            <?php else: foreach($history as $h):
                $total = $h['quantity'] * ($h['import_price'] ?? 0); // Tính tổng tiền
                ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 15px;"><?= date('H:i d/m/Y', strtotime($h['created_at'])) ?></td>
                    <td style="padding: 15px; font-weight: bold;"><?= htmlspecialchars($h['product_name']) ?></td>
                    <td style="padding: 15px; text-align: center;">
                        <span style="background: #d4edda; color: #155724; padding: 5px 10px; border-radius: 15px; font-weight: bold;">
                            + <?= $h['quantity'] ?>
                        </span>
                    </td>

                    <td style="padding: 15px; text-align: right;">
                        <?= number_format($h['import_price'] ?? 0, 0, ',', '.') ?> đ
                    </td>
                    <td style="padding: 15px; text-align: right; font-weight: bold; color: #2c3e50;">
                        <?= number_format($total, 0, ',', '.') ?> đ
                    </td>

                    <td style="padding: 15px; font-style: italic; color: #666;">
                        <?= htmlspecialchars($h['note'] ?? 'Nhập kho định kỳ') ?>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<script>
const input = document.getElementById('searchInput');
const suggestionBox = document.createElement('div');

// CSS cho bảng gợi ý nằm dưới ô input
suggestionBox.style.cssText = `
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 9999;
    margin-top: 5px;
    display: none;
    max-height: 200px;
    overflow-y: auto;
`;
input.parentNode.appendChild(suggestionBox);

input.addEventListener('input', function() {
    let val = this.value.trim();
    if (!val) { suggestionBox.style.display = 'none'; return; }

    fetch(`index.php?controller=warehouse&action=getSuggestions&query=${encodeURIComponent(val)}`)
        .then(res => res.json())
        .then(data => {
            if (data.length > 0) {
                suggestionBox.innerHTML = data.map(name => 
                    `<div class="sug-item" style="padding:10px 15px; cursor:pointer; border-bottom:1px solid #f1f1f1;">${name}</div>`
                ).join('');
                suggestionBox.style.display = 'block';
                
                document.querySelectorAll('.sug-item').forEach(el => {
                    el.onmouseover = () => el.style.backgroundColor = '#f8f9fa';
                    el.onmouseout = () => el.style.backgroundColor = 'white';
                    el.onclick = () => {
                        input.value = el.innerText;
                        suggestionBox.style.display = 'none';
                        input.closest('form').submit();
                    };
                });
            } else { suggestionBox.style.display = 'none'; }
        });
});

document.addEventListener('click', (e) => {
    if (e.target !== input) suggestionBox.style.display = 'none';
});
</script>