<div class="coupon-container">
    <div class="coupon-header">
        <h2><i class="fa-solid fa-file-import" style="color: #f39c12;"></i> Nhập Coupon từ Excel</h2>
        <a href="index.php?controller=admin-coupon" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="coupon-card">
        <form action="index.php?controller=admin-coupon&action=importStore" method="POST" enctype="multipart/form-data">

            <div class="alert alert-info" style="background: #e0f2fe; color: #0284c7; padding: 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #bae6fd;">
                <strong><i class="fa-solid fa-circle-info"></i> Quy định file Excel:</strong><br>
                <ul style="margin: 5px 0 0 20px; line-height: 1.6;">
                    <li><strong>Cột A:</strong> Mã Code (Bắt buộc)</li>
                    <li><strong>Cột B:</strong> Loại giảm (nhập 'percent' hoặc 'fixed')</li>
                    <li><strong>Cột C:</strong> Giá trị giảm</li>
                    <li><strong>Cột D:</strong> Đơn tối thiểu</li>
                    <li><strong>Cột E:</strong> Lượt dùng giới hạn</li>
                    <li><strong>Cột F:</strong> Điểm đổi (nhập 0 nếu miễn phí)</li>
                    <li><strong>Cột G:</strong> Ngày bắt đầu (YYYY-MM-DD)</li>
                    <li><strong>Cột H:</strong> Ngày kết thúc (YYYY-MM-DD)</li>
                </ul>
                <div style="margin-top: 10px; font-style: italic;">
                    * Lưu ý: Cần định dạng cột Ngày tháng trong Excel là <b>Text</b> để tránh lỗi định dạng.
                </div>
            </div>

            <div class="form-group mb-3">
                <label style="font-weight: bold; display: block; margin-bottom: 8px;">Chọn file Excel (.xlsx)</label>
                <input type="file" name="file_excel" class="form-control" required accept=".xlsx" style="padding: 10px;">
            </div>

            <div class="form-actions" style="margin-top: 20px;">
                <button type="submit" name="btn_import" class="btn btn-warning" style="color: white; padding: 10px 20px;">
                    <i class="fa-solid fa-upload"></i> Tiến hành nhập dữ liệu
                </button>
            </div>
        </form>
    </div>
</div>