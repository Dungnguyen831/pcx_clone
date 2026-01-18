/* assets/js/checkout.js */

document.addEventListener('DOMContentLoaded', function() {
    // 1. Tìm nút bấm và ô nhập liệu
    const btnApply = document.getElementById('btnApplyCoupon');
    const inputCoupon = document.getElementById('couponInput');

    // Chỉ chạy logic nếu nút bấm tồn tại (tránh lỗi khi đã áp mã rồi thì nút biến mất)
    if (btnApply && inputCoupon) {
        
        // 2. Lắng nghe sự kiện Click
        btnApply.addEventListener('click', function() {
            const code = inputCoupon.value.trim();

            if (code === "") {
                alert("Vui lòng nhập mã giảm giá!");
                inputCoupon.focus(); // Đưa con trỏ chuột về lại ô nhập
                return;
            }

            // 3. Chuyển hướng
            // Lưu ý: Đường dẫn này phải đúng với router của bạn
            window.location.href = "index.php?controller=cart&action=applyCoupon&code=" + encodeURIComponent(code);
        });

        // (Bonus) Cho phép bấm Enter để áp dụng luôn
        inputCoupon.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // Chặn việc submit form chính
                btnApply.click();   // Kích hoạt sự kiện click của nút áp dụng
            }
        });
    }
});