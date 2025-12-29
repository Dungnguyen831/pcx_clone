/* assets/js/slider.js */

document.addEventListener('DOMContentLoaded', function() {
    // 1. Chọn các phần tử cần thiết
    const sliderWrapper = document.querySelector('.slider-wrapper');
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');
    const btnNext = document.querySelector('.btn-next');
    const btnPrev = document.querySelector('.btn-prev');

    // Nếu trang này không có slider thì dừng lại (tránh lỗi ở các trang khác)
    if (!sliderWrapper || slides.length === 0) return;

    let slideIndex = 0;
    let autoSlideInterval;
    const totalSlides = slides.length;

    // 2. Hàm hiển thị slide
    function showSlides(n) {
        // Xử lý vòng lặp vô tận
        if (n >= totalSlides) {
            slideIndex = 0;
        } else if (n < 0) {
            slideIndex = totalSlides - 1;
        } else {
            slideIndex = n;
        }

        // Di chuyển khung chứa ảnh
        sliderWrapper.style.transform = `translateX(-${slideIndex * 100}%)`;

        // Cập nhật trạng thái chấm tròn (active)
        dots.forEach(dot => dot.classList.remove('active'));
        if (dots[slideIndex]) {
            dots[slideIndex].classList.add('active');
        }
    }

    // 3. Hàm xử lý chuyển slide (khi bấm nút hoặc tự chạy)
    function nextSlide() {
        showSlides(slideIndex + 1);
        resetAutoSlide();
    }

    function prevSlide() {
        showSlides(slideIndex - 1);
        resetAutoSlide();
    }

    // 4. Hàm tự động chạy
    function startAutoSlide() {
        autoSlideInterval = setInterval(nextSlide, 5000); // 5 giây đổi 1 lần
    }

    function resetAutoSlide() {
        clearInterval(autoSlideInterval);
        startAutoSlide();
    }

    // 5. Gắn sự kiện (Event Listeners) - Thay thế cho onclick trong HTML
    if (btnNext) {
        btnNext.addEventListener('click', nextSlide);
    }

    if (btnPrev) {
        btnPrev.addEventListener('click', prevSlide);
    }

    // Gắn sự kiện click cho từng dấu chấm
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            showSlides(index);
            resetAutoSlide();
        });
    });

    // 6. Khởi chạy lần đầu
    startAutoSlide();
});