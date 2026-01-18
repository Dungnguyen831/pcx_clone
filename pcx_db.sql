-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th1 15, 2026 lúc 09:16 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `pcx_db`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `brands`
--

CREATE TABLE `brands` (
  `brand_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `logo_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `brands`
--

INSERT INTO `brands` (`brand_id`, `name`, `logo_url`) VALUES
(1, 'Logitech', NULL),
(2, 'Razer', NULL),
(3, 'Lamzu', NULL),
(4, 'Wooting', NULL),
(5, 'HyperX', ''),
(6, 'ATK', '1766992546_360_F_98344523_jdknXEgm09lVYAAgH4Tj9ar5MIaDWuc6.jpg'),
(7, 'Artisan', '');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `carts`
--

CREATE TABLE `carts` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `description`, `status`) VALUES
(1, 'Chuột Gaming', NULL, 1),
(2, 'Bàn Phím Cơ', NULL, 1),
(3, 'Tai Nghe', '', 1),
(4, 'Pad chuột', '', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `coupons`
--

CREATE TABLE `coupons` (
  `coupon_id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `discount_type` enum('percent','fixed') NOT NULL DEFAULT 'fixed',
  `discount_value` decimal(15,0) NOT NULL,
  `min_order_value` decimal(15,0) DEFAULT 0,
  `usage_limit` int(11) DEFAULT 100,
  `used_count` int(11) DEFAULT 0,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `points_cost` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `coupons`
--

INSERT INTO `coupons` (`coupon_id`, `code`, `discount_type`, `discount_value`, `min_order_value`, `usage_limit`, `used_count`, `start_date`, `end_date`, `status`, `points_cost`) VALUES
(1, 'GIAM50K', 'fixed', 50000, 200000, 103, 0, NULL, '2025-12-31 00:00:00', 0, 0),
(2, 'SALE10', 'percent', 10, 1000000, 100, 0, NULL, '2025-12-31 00:00:00', 0, 0),
(3, 'GEARUP10', 'percent', 20, 100000, 100, 0, '2025-12-30 01:17:00', '2026-01-02 01:18:00', 0, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reward_points` int(11) DEFAULT 0 COMMENT 'Điểm thưởng',
  `date_of_birth` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `customers`
--

INSERT INTO `customers` (`customer_id`, `user_id`, `reward_points`, `date_of_birth`) VALUES
(1, 3, 0, NULL),
(2, 5, 0, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `imports`
--

CREATE TABLE `imports` (
  `import_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'Nhân viên thực hiện nhập (Role 2)',
  `supplier_name` varchar(255) DEFAULT NULL COMMENT 'Nhà cung cấp',
  `total_cost` decimal(15,0) NOT NULL DEFAULT 0 COMMENT 'Tổng tiền nhập',
  `note` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `import_details`
--

CREATE TABLE `import_details` (
  `id` int(11) NOT NULL,
  `import_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL COMMENT 'Số lượng nhập',
  `import_price` decimal(15,0) NOT NULL COMMENT 'Giá vốn nhập vào',
  `total_price` decimal(15,0) GENERATED ALWAYS AS (`quantity` * `import_price`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `inventory`
--

CREATE TABLE `inventory` (
  `inventory_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `last_updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `inventory`
--

INSERT INTO `inventory` (`inventory_id`, `product_id`, `quantity`, `last_updated`) VALUES
(1, 1, 40, '2025-12-30 00:49:47'),
(2, 2, 4, '2025-12-30 00:55:13'),
(3, 3, 99, '2025-12-30 00:28:33'),
(4, 4, 111, '2025-12-30 01:23:58'),
(5, 5, 19, '2026-01-11 21:29:30'),
(6, 6, 0, '2026-01-12 17:52:03'),
(7, 7, 50, '2026-01-12 17:54:18'),
(8, 10, 10, '2026-01-12 17:54:18');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `shipping_address` varchar(255) NOT NULL,
  `note` text DEFAULT NULL,
  `total_money` decimal(15,0) NOT NULL,
  `discount_amount` decimal(15,0) DEFAULT 0,
  `final_money` decimal(15,0) NOT NULL,
  `coupon_code` varchar(50) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT 'COD',
  `status` tinyint(4) DEFAULT 0 COMMENT '0: Mới, 1: Xác nhận, 2: Giao, 3: Hoàn thành, 4: Hủy',
  `created_at` datetime DEFAULT current_timestamp(),
  `is_points_calculated` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `customer_name`, `customer_phone`, `shipping_address`, `note`, `total_money`, `discount_amount`, `final_money`, `coupon_code`, `payment_method`, `status`, `created_at`, `is_points_calculated`) VALUES
(1, 5, 'Shiba PJ', '0904754029', '2b triều khúc thanh xuân hà nội', 'giao hỏa tốc', 10900000, 0, 10900000, NULL, 'COD', 3, '2025-12-29 14:20:01', 0),
(2, 5, 'Shiba PJ', '0904754029', '2b triều khúc thanh xuân hà nội', '', 1500000, 0, 1500000, NULL, 'COD', 4, '2025-12-29 14:26:36', 0),
(3, 5, 'Shiba PJ', '0904754029', '2b triều khúc thanh xuân hà nội', '', 4900000, 0, 4900000, NULL, 'COD', 3, '2026-01-08 14:29:14', 0),
(4, 5, 'Shiba PJ', '0904754029', '2b triều khúc thanh xuân hà nội', '', 2450000, 0, 2450000, NULL, 'COD', 4, '2025-12-29 14:29:44', 0),
(5, 5, 'Shiba PJ', '0904754029', '2b triều khúc thanh xuân hà nội', '', 4500000, 0, 4500000, NULL, 'COD', 4, '2025-12-29 14:30:23', 0),
(6, 5, 'Shiba PJ', '0904754029', '2b triều khúc thanh xuân hà nội', '', 1500000, 0, 1500000, NULL, 'COD', 3, '2025-12-29 15:04:45', 0),
(7, 5, 'Shiba PJ', '0904754029', '2b triều khúc thanh xuân hà nội', 'giao chậm', 3320000, 0, 3320000, NULL, 'COD', 3, '2025-12-29 17:08:21', 0),
(8, 5, 'Shiba PJ', '0904754029', '2b triều khúc thanh xuân hà nội', '', 13050000, 0, 13050000, NULL, 'COD', 3, '2026-01-09 21:24:59', 0),
(9, 5, 'Shiba PJ', '0904754029', '2b triều khúc thanh xuân hà nội', '', 2450000, 0, 2450000, NULL, 'COD', 4, '2025-12-29 21:51:48', 0),
(10, 5, 'Shiba PJ', '0904754029', '2b triều khúc thanh xuân hà nội', '', 4500000, 0, 4500000, NULL, 'COD', 4, '2025-12-29 22:23:21', 0),
(11, 5, 'Shiba PJ', '0904754029', '2b triều khúc thanh xuân hà nội', '', 2205000, 0, 2205000, NULL, 'COD', 4, '2025-12-30 00:09:54', 0),
(12, 5, 'Shiba PJ', '0904754029', '2b triều khúc thanh xuân hà nội', '', 4500000, 450000, 4050000, 'SALE10', 'COD', 1, '2025-12-30 00:24:16', 0),
(13, 5, 'Shiba PJ', '0904754029', '2b triều khúc thanh xuân hà nội', '', 3200000, 320000, 2880000, 'SALE10', 'COD', 1, '2025-12-30 00:28:33', 0),
(14, 5, 'Shiba PJ', '0904754029', '2b triều khúc thanh xuân hà nội', '', 2450000, 245000, 2205000, 'SALE10', 'COD', 2, '2025-12-30 00:41:22', 0),
(15, 5, 'Shiba PJ', '0904754029', '2b triều khúc thanh xuân hà nội', '', 4500000, 450000, 4050000, 'SALE10', 'COD', 3, '2025-12-30 00:41:47', 0),
(16, 5, 'Shiba PJ', '0904754029', '2b triều khúc thanh xuân hà nội', '', 1500000, 50000, 1450000, 'GIAM50K', 'COD', -1, '2025-12-30 00:48:57', 0),
(17, 5, 'Shiba PJ', '0904754029', '2b triều khúc thanh xuân hà nội', '', 3950000, 50000, 3900000, 'GIAM50K', 'COD', -1, '2025-12-30 00:49:47', 0),
(18, 5, 'Shiba PJ', '0904754029', '2b triều khúc thanh xuân hà nội', '', 1500000, 0, 1500000, NULL, 'COD', -1, '2025-12-30 00:50:33', 0),
(19, 5, 'Shiba PJ', '0904754029', '2b triều khúc thanh xuân hà nội', '', 4500000, 50000, 4450000, 'GIAM50K', 'COD', 4, '2025-12-30 00:55:13', 0),
(20, 5, 'Shiba PJ', '0904754029', '2b triều khúc thanh xuân hà nội', '', 1500000, 300000, 1200000, 'GEARUP10', 'COD', 2, '2025-12-30 01:23:58', 0),
(21, 5, 'Dũng', '0904754029', '2b triều khúc', 'giao hỏa tốc', 870000, 0, 870000, NULL, 'COD', 3, '2026-01-11 21:29:30', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_details`
--

CREATE TABLE `order_details` (
  `detail_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `price` decimal(15,0) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total_price` decimal(15,0) GENERATED ALWAYS AS (`price` * `quantity`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_details`
--

INSERT INTO `order_details` (`detail_id`, `order_id`, `product_id`, `price`, `quantity`) VALUES
(1, 1, 1, 2450000, 2),
(2, 1, 2, 4500000, 1),
(3, 1, 4, 1500000, 1),
(4, 2, 4, 1500000, 1),
(5, 3, 1, 2450000, 2),
(6, 4, 1, 2450000, 1),
(7, 5, 2, 4500000, 1),
(8, 6, 4, 1500000, 1),
(9, 7, 1, 2450000, 1),
(10, 7, 5, 870000, 1),
(11, 8, 5, 870000, 15),
(12, 9, 1, 2450000, 1),
(13, 10, 2, 4500000, 1),
(14, 11, 1, 2450000, 1),
(15, 12, 2, 4500000, 1),
(16, 13, 3, 3200000, 1),
(17, 14, 1, 2450000, 1),
(18, 15, 2, 4500000, 1),
(19, 16, 4, 1500000, 1),
(20, 17, 4, 1500000, 1),
(21, 17, 1, 2450000, 1),
(22, 18, 4, 1500000, 1),
(23, 19, 2, 4500000, 1),
(24, 20, 4, 1500000, 1),
(25, 21, 5, 870000, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(15,0) NOT NULL DEFAULT 0,
  `import_price` decimal(15,0) DEFAULT 0 COMMENT 'Giá vốn nhập vào trung bình',
  `image` varchar(255) DEFAULT NULL COMMENT 'Ảnh đại diện',
  `description` text DEFAULT NULL,
  `technical_specs` text DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`product_id`, `category_id`, `brand_id`, `name`, `price`, `import_price`, `image`, `description`, `technical_specs`, `status`, `created_at`) VALUES
(1, 1, 3, 'Chuột Lamzu Atlantis OG V2', 2450000, 0, 'lamzu-og.jpg', 'Chuột siêu nhẹ.', NULL, 1, '2025-12-13 17:46:35'),
(2, 2, 4, 'Bàn phím Wooting 60HE', 4500000, 0, 'wooting.jpg', 'Bàn phím Rapid Trigger.', NULL, 1, '2025-12-13 17:46:35'),
(3, 1, 1, 'Chuột Logitech G Pro X 2', 3200000, 0, 'gpx2.jpg', 'Huyền thoại trở lại.', NULL, 1, '2025-12-13 17:46:35'),
(4, 3, 5, 'Tai nghe Hyper x Cloud III', 1500000, 0, '1766992670_hyperx-cloud-iii-1201660528.jpg', '- Sự thoải mái và độ bền đặc trưng của HyperX\r\n- Driver nghiêng 53mm, tinh chỉnh cho chất âm chuẩn xác tuyệt đối\r\n- Micro 10mm khử ồn với chất giọng rõ ràng và đèn báo tắt mic LED\r\n- Tương thích đa nền tảng qua cổng 3.5mm, USB-C và USB-A\r\n- Tương thích với: PC, PS5, PS4, Xbox Series X|S, Xbox One, Nintendo Switch, Mac và thiết bị di động.', NULL, 1, '2025-12-29 14:17:50'),
(5, 4, 7, 'Pad Artisan Hien', 870000, 0, '1767002874_d-t-tr-c-lot-chu-t-kinh-c-ng-l-c-yuki-aim-x-demon1-limited-edition-1175101791.jpg', '- Kích thước: 500 x 400 x 2.8 mm\r\n- Kính: 1.5mm\r\n- Đế silicone: 1.3mm\r\n- Chất liệu: kính cường lực\r\n- Bề mặt: được xử lý nhiệt siêu mượt\r\n- Đáy: silicone chống trượt được thiết kế custom.\r\nArtwork nguyên bản được thiết kế bởi Yuki Aim và Demon1', NULL, 1, '2025-12-29 17:07:54'),
(6, 4, 7, 'Pad kính Aula', 1230000, 0, 'default.png', 'Pad kính Aula Phiên bản control', '', 1, '2026-01-12 17:52:03'),
(7, 1, 5, 'Chuột Logitech G102', 450000, 0, 'default.png', 'Chuột gaming quốc dân, led RGB', NULL, 1, '2026-01-12 17:54:18'),
(10, 4, 4, 'Màn hình LG 24inch', 3500000, 0, 'default.png', 'Tần số quét 144Hz, tấm nền IPS', NULL, 1, '2026-01-12 17:54:18');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_images`
--

CREATE TABLE `product_images` (
  `image_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` tinyint(1) DEFAULT 0 COMMENT '1: Admin, 0: Khách hàng',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `password`, `phone`, `role`, `created_at`) VALUES
(1, 'Quản Trị Viên', 'admin@pcx.com', '123456', NULL, 1, '2025-12-13 17:46:34'),
(3, 'Dũng Nguyễn', 'nvhoa200373@gmail.com', 'admin1234', '0904754029', 0, '2025-12-27 22:55:44'),
(5, 'Shiba PJ', 'nguyenanhdung831@gmail.com', 'admin1234', '0904754028', 0, '2025-12-29 13:54:49');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_coupons`
--

CREATE TABLE `user_coupons` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `coupon_id` int(11) NOT NULL,
  `is_used` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`brand_id`);

--
-- Chỉ mục cho bảng `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`cart_id`),
  ADD UNIQUE KEY `unique_user_product` (`user_id`,`product_id`),
  ADD KEY `fk_cart_product` (`product_id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Chỉ mục cho bảng `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`coupon_id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Chỉ mục cho bảng `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `idx_user_link` (`user_id`);

--
-- Chỉ mục cho bảng `imports`
--
ALTER TABLE `imports`
  ADD PRIMARY KEY (`import_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `import_details`
--
ALTER TABLE `import_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `import_id` (`import_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`inventory_id`),
  ADD UNIQUE KEY `idx_product_inventory` (`product_id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `idx_user` (`user_id`);

--
-- Chỉ mục cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`detail_id`),
  ADD KEY `idx_order` (`order_id`),
  ADD KEY `idx_product_detail` (`product_id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_brand` (`brand_id`);

--
-- Chỉ mục cho bảng `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `idx_product` (`product_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Chỉ mục cho bảng `user_coupons`
--
ALTER TABLE `user_coupons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `coupon_id` (`coupon_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `brands`
--
ALTER TABLE `brands`
  MODIFY `brand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `carts`
--
ALTER TABLE `carts`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `coupons`
--
ALTER TABLE `coupons`
  MODIFY `coupon_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `imports`
--
ALTER TABLE `imports`
  MODIFY `import_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `import_details`
--
ALTER TABLE `import_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `inventory`
--
ALTER TABLE `inventory`
  MODIFY `inventory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT cho bảng `order_details`
--
ALTER TABLE `order_details`
  MODIFY `detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `product_images`
--
ALTER TABLE `product_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `user_coupons`
--
ALTER TABLE `user_coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `imports`
--
ALTER TABLE `imports`
  ADD CONSTRAINT `imports_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE NO ACTION;

--
-- Các ràng buộc cho bảng `import_details`
--
ALTER TABLE `import_details`
  ADD CONSTRAINT `import_details_ibfk_1` FOREIGN KEY (`import_id`) REFERENCES `imports` (`import_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `import_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE NO ACTION;

--
-- Các ràng buộc cho bảng `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `fk_inventory_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_order_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `fk_detail_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_detail_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE NO ACTION;

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_product_brand` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`brand_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `fk_gallery_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `user_coupons`
--
ALTER TABLE `user_coupons`
  ADD CONSTRAINT `user_coupons_ibfk_1` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`coupon_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
