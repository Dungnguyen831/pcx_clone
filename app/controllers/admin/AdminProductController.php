<?php
class AdminProductController {
    private $productModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }
        require_once 'app/models/admin/AdminProductModel.php';
        $this->productModel = new AdminProductModel();
    }

    public function index() {
        $products = $this->productModel->getAllProductsAdmin($_GET['search_id'] ?? null, $_GET['search_name'] ?? null);
        $page_title = "Quản lý sản phẩm";
        $content_view = 'views/admin/product/index.php';
        require_once 'views/admin/layouts/page.php';
    }

    // --- BỔ SUNG PHƯƠNG THỨC CREATE ---
    public function create() {
        $categories = $this->productModel->getCategories();
        $brands = $this->productModel->getBrands();
        $page_title = "Thêm sản phẩm mới";
        $content_view = 'views/admin/product/add.php'; // Đảm bảo file này tồn tại
        require_once 'views/admin/layouts/page.php';
    }

    // --- BỔ SUNG PHƯƠNG THỨC EDIT ---
    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: index.php?controller=admin-product&action=index");
            exit();
        }
        $product = $this->productModel->getProductById($id);
        $categories = $this->productModel->getCategories();
        $brands = $this->productModel->getBrands();
        $page_title = "Chỉnh sửa sản phẩm";
        $content_view = 'views/admin/product/edit.php'; // Đảm bảo file này tồn tại
        require_once 'views/admin/layouts/page.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Lưu dữ liệu vào Session tạm thời để nếu lỗi thì lấy lại được
            $_SESSION['old_post'] = $_POST; 
    
            $data = [
                'name'            => $_POST['name'],
                'category_id'     => $_POST['category_id'],
                'brand_id'        => $_POST['brand_id'],
                'price'           => $_POST['price'],
                'quantity'        => $_POST['quantity'] ?? 0,
                'image'           => $this->handleUpload($_FILES['image']) ?? 'default.png',
                'description'     => $_POST['description'] ?? '',
                'technical_specs' => $_POST['technical_specs'] ?? '',
                'status'          => 1
            ];
    
            if ($this->productModel->addProduct($data)) {
                unset($_SESSION['old_post']); // Xóa dữ liệu tạm khi thành công
                header("Location: index.php?controller=admin-product&action=index&msg=success");
            } else {
                // Nếu lỗi, quay lại trang thêm kèm thông báo lỗi
                header("Location: index.php?controller=admin-product&action=create&msg=error");
            }
            exit();
        }
    }

  // app/controllers/admin/AdminProductController.php

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'id'              => $_POST['id'],
                'name'            => $_POST['name'],
                'category_id'     => $_POST['category_id'],
                'brand_id'        => $_POST['brand_id'],
                'price'           => $_POST['price'],
                'quantity'        => $_POST['quantity'],
                'image'           => $_POST['old_image'], // Mặc định dùng ảnh cũ
                'description'     => $_POST['description'] ?? '',
                'technical_specs' => $_POST['technical_specs'] ?? '', // Thêm ?? '' để sửa lỗi dòng 95
                'import_price'    => $_POST['import_price'] ?? 0
            ];

            // Xử lý upload ảnh mới nếu có
            if (!empty($_FILES['image']['name'])) {
                $data['image'] = $this->handleUpload($_FILES['image']);
            }

            if ($this->productModel->updateProduct($data)) {
                header("Location: index.php?controller=admin-product&action=index&msg=updated");
            } else {
                // Nếu lỗi, lưu lại thông tin vào SESSION để trang edit không bị trống
                $_SESSION['error_data'] = $_POST;
                header("Location: index.php?controller=admin-product&action=edit&id=".$data['id']."&msg=error");
            }
            exit();
        }
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            // Kiểm tra ràng buộc đơn hàng trước khi xóa
            if ($this->productModel->isProductInProcessingOrders($id)) {
                $msg = "error_processing";
            } else {
                $product = $this->productModel->getProductById($id);
                if ($product && $this->productModel->deleteProduct($id)) {
                    if ($product['image'] !== 'default.png') {
                        @unlink("assets/uploads/products/".$product['image']);
                    }
                    $msg = "deleted";
                }
            }
        }
        header("Location: index.php?controller=admin-product&action=index&msg=" . ($msg ?? 'error'));
        exit();
    }

    private function handleUpload($file) {
        $dir = "assets/uploads/products/";
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        $fileName = time() . '_' . basename($file['name']);
        return move_uploaded_file($file['tmp_name'], $dir . $fileName) ? $fileName : null;
    }
}