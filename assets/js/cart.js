
document.addEventListener('DOMContentLoaded', function() {
    const quantityInputs = document.querySelectorAll('.quantity-input');
    const totalBillDisplay = document.getElementById('total-bill-display');
    const btnCheckout = document.getElementById('btn-checkout');

    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
    }

    // 1. SO SÁNH TỪNG SẢN PHẨM VỚI TỒN KHO KHI BẤM ĐẶT HÀNG
    if (btnCheckout) {
        btnCheckout.addEventListener('click', function(e) {
            let errorMessages = [];
            
            document.querySelectorAll('.cart-item').forEach(row => {
                const input = row.querySelector('.quantity-input');
                const name = row.querySelector('.product-name').innerText;
                const stock = parseInt(input.getAttribute('data-stock')); // Số lượng trong inventory
                const qty = parseInt(input.value); // Số lượng trong carts

                if (qty > stock) {
                    errorMessages.push(``);
                }
            });

            if (errorMessages.length > 0) {
                e.preventDefault(); // Ngăn chặn đặt hàng
                alert("KHÔNG ĐỦ SỐ LƯỢNG SẢN PHẨM TRONG KHO" + errorMessages.join("\n"));
            }
        });
    }

    // 2. CẬP NHẬT TỔNG TIỀN KHI THAY ĐỔI SỐ LƯỢNG
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

    // 3. AJAX CẬP NHẬT GIỎ HÀNG
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            const productId = this.getAttribute('data-id');
            const newQty = parseInt(this.value);

            if (newQty < 1) {
                this.value = 1;
                return;
            }

            updateTotals();

            fetch('index.php?controller=cart&action=updateAjax', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${productId}&qty=${newQty}`
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    alert('Lỗi cập nhật giỏ hàng!');
                    location.reload();
                }
            });
        });
    });
});
