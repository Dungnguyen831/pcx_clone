document.addEventListener("DOMContentLoaded", function () {
  const registerForm = document.getElementById("registerForm");

  if (registerForm) {
    registerForm.addEventListener("submit", function (e) {
      let isValid = true;

      // 1. Lấy giá trị các trường và loại bỏ khoảng trắng thừa
      const fullName = document.getElementById("full_name").value.trim();
      const email = document.getElementById("email").value.trim();
      const phone = document.getElementById("phone").value.trim();
      const password = document.getElementById("password").value;
      const confirmPassword = document.getElementById("confirm_password").value;

      // 2. Hàm hỗ trợ hiển thị lỗi để code sạch hơn
      const showError = (id, message) => {
        const errorElement = document.getElementById(id);
        if (errorElement) {
          errorElement.innerText = message;
        }
        isValid = false;
      };

      // 3. Reset toàn bộ thông báo lỗi cũ trước khi kiểm tra mới
      document
        .querySelectorAll(".error-text")
        .forEach((el) => (el.innerText = ""));

      // 4. Validate Họ tên
      if (fullName === "") {
        showError("nameError", "Họ tên không được để trống");
      } else if (fullName.length < 2) {
        showError("nameError", "Họ tên quá ngắn,vui lòng nhập 2 kí tự trở lên");
      }

      // 5. Validate Email
      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (email === "") {
        showError("emailError", "Vui lòng nhập Email");
      } else if (!emailPattern.test(email)) {
        showError(
          "emailError",
          "Email không đúng định dạng (VD: abc@gmail.com)"
        );
      }

      // 6. Validate Số điện thoại (Định dạng Việt Nam)
      const phonePattern = /^(0[35789][0-9]{8}|1[89]00[0-9]{4})$/;
      if (phone === "") {
        showError("phoneError", "Vui lòng nhập số điện thoại");
      } else if (!phonePattern.test(phone)) {
        showError(
          "phoneError",
          "Số điện thoại không hợp lệ (di động Việt Nam hoặc tổng đài 1800, 1900)"
        );
      }

      // 7. Validate Mật khẩu
      if (password === "") {
        showError("passwordError", "Vui lòng nhập mật khẩu");
      } else if (password.length < 6) {
        showError("passwordError", "Mật khẩu phải có ít nhất 6 ký tự");
      }

      // 8. Validate Xác nhận mật khẩu (QUAN TRỌNG)
      if (confirmPassword === "") {
        showError("confirmPasswordError", "Vui lòng nhập lại mật khẩu");
      } else if (confirmPassword !== password) {
        showError("confirmPasswordError", "Mật khẩu xác nhận không khớp");
      }

      // 9. Nếu có bất kỳ lỗi nào, dừng việc gửi form
      if (!isValid) {
        e.preventDefault();
        // Tự động cuộn lên lỗi đầu tiên để người dùng dễ thấy
        const firstError = document.querySelector(".error-text:not(:empty)");
        if (firstError) {
          firstError.scrollIntoView({ behavior: "smooth", block: "center" });
        }
      }
    });
  }
});

// Xử lý ẩn/hiện mật khẩu
document.querySelectorAll(".toggle-password").forEach((icon) => {
  icon.addEventListener("click", function () {
    // Lấy ID của ô input tương ứng từ thuộc tính data-target
    const targetId = this.getAttribute("data-target");
    const input = document.getElementById(targetId);

    if (input.type === "password") {
      // Hiện mật khẩu
      input.type = "text";
      this.classList.remove("fa-eye");
      this.classList.add("fa-eye-slash");
    } else {
      // Ẩn mật khẩu
      input.type = "password";
      this.classList.remove("fa-eye-slash");
      this.classList.add("fa-eye");
    }
  });
});
