<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Báo Cáo Doanh Thu</h1>

    <form method="GET" action="index.php" class="row mb-4">
        <input type="hidden" name="controller" value="report">
        <input type="hidden" name="action" value="index">
        
        <div class="col-md-3">
            <label>Từ ngày:</label>
            <input type="date" name="from" class="form-control" value="<?= $fromDate ?>">
        </div>
        <div class="col-md-3">
            <label>Đến ngày:</label>
            <input type="date" name="to" class="form-control" value="<?= $toDate ?>">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100"><i class="fa fa-filter"></i> Lọc</button>
        </div>
    </form>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Doanh thu</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($revenue) ?> VNĐ</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Tiền Nhập Hàng</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($importTotal) ?> VNĐ</div>
                    <small class="text-muted">(Chi phí mua hàng về kho)</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Giá Vốn Hàng Bán</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($cogs) ?> VNĐ</div>
                    <small class="text-muted">(Vốn của sp đã bán)</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Lợi Nhuận Gộp</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($profit) ?> VNĐ</div>
                    <small class="text-muted">(Doanh thu - Giá vốn)</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Biểu đồ doanh thu 7 ngày qua</h6>
        </div>
        <div class="card-body">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('revenueChart');
    
    // Lấy dữ liệu đã được xử lý "lấp đầy" từ Controller
    // Lưu ý: Biến PHP bây giờ là $chartDataFinal
    const labels = <?= json_encode(array_column($chartDataFinal, 'date')) ?>;
    const data = <?= json_encode(array_column($chartDataFinal, 'revenue')) ?>;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: data,
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)', // Màu nền mờ dưới đường kẻ
                borderWidth: 2,
                pointRadius: 3, // Kích thước chấm tròn
                pointBackgroundColor: '#4e73df',
                tension: 0.3, // Độ cong của đường (0 là thẳng tưng, cao hơn là uốn lượn)
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // Để biểu đồ tự co giãn theo chiều cao div cha
            plugins: {
                legend: {
                    display: false // Ẩn chú thích nếu chỉ có 1 đường cho gọn
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            // Format tiền tệ Việt Nam trong tooltip
                            label += new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(context.raw);
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        // Format trục Y thành tiền tệ cho đẹp (ví dụ: 1M, 500k...)
                        callback: function(value) {
                            return new Intl.NumberFormat('vi-VN', { notation: "compact" }).format(value) + 'đ';
                        }
                    }
                },
                x: {
                    grid: {
                        display: false // Bỏ lưới dọc cho đỡ rối mắt
                    }
                }
            }
        }
    });
</script>