<?php
// app/controllers/HomeController.php

class HomeController {
    public function index() {
        // 1. Chuẩn bị dữ liệu giả (để test giao diện trước khi có Database)
        $products = [
            ['Name' => 'Chuột Lamzu Atlantis', 'Price' => 2450000, 'Brand' => 'LAMZU'],
            ['Name' => 'Phím Wooting 60HE', 'Price' => 4500000, 'Brand' => 'WOOTING'],
            ['Name' => 'Lót chuột Ninjutso', 'Price' => 890000, 'Brand' => 'NINJUTSO'],
            ['Name' => 'Pulsar X2V2 Mini', 'Price' => 2100000, 'Brand' => 'PULSAR']
        ];
        // day là huucmt
        // 2. Gọi giao diện ra hiển thị
        // Lưu ý: Đường dẫn tính từ file index.php ở ngoài cùng
        require_once 'views/home/index.php';
    }
}
?>