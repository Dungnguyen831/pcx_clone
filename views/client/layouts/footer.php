<footer>
    <div class="container footer-grid">
        <div class="footer-col">
            <h4>Về Phong Cách Xanh</h4>
            <ul>
                <li><a href="#">Giới thiệu</a></li>
                <li><a href="#">Tuyển dụng</a></li>
                <li><a href="#">Liên hệ</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Hỗ trợ khách hàng</h4>
            <ul>
                <li><a href="#">Hướng dẫn mua hàng</a></li>
                <li><a href="#">Chính sách bảo hành</a></li>
                <li><a href="#">Vận chuyển & Giao nhận</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Đăng ký nhận tin</h4>
            <p>Nhận thông tin sản phẩm mới nhất.</p>
        </div>
        <div class="footer-col">
            <h4>Kết nối</h4>
            <div class="socials">
                <i class="fa-brands fa-facebook"></i>
                <i class="fa-brands fa-youtube"></i>
                <i class="fa-brands fa-tiktok"></i>
            </div>
        </div>
    </div>
    <div class="copyright">
        &copy; 2025 Phong Cách Xanh Clone by Gemini User.
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="assets/js/main.js"></script>

<?php if (isset($_SESSION['success_msg'])): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Thành công!',
            text: '<?php echo $_SESSION['success_msg']; ?>',
            timer: 2000,
            showConfirmButton: false
        });
    </script>
    <?php unset($_SESSION['success_msg']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_msg'])): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Lỗi!',
            text: '<?php echo $_SESSION['error_msg']; ?>',
        });
    </script>
    <?php unset($_SESSION['error_msg']); ?>
<?php endif; ?>

</body>
</html>