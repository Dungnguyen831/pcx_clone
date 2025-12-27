<?php
class ProductController {
    public function detail() {
        // 1. Lấy ID từ URL
        $id = isset($_GET['id']) ? $_GET['id'] : 0;

        // 2. Gọi Model để lấy dữ liệu
        require_once 'app/models/ProductModel.php';
        $productModel = new ProductModel();
        $product = $productModel->getProductById($id);

        if (!$product) {
            die("Sản phẩm không tồn tại!");
        }

        // 3. Hiển thị View chitiet.php
        require_once 'views/client/productdetail/chitiet.php';
    }
    public function index() {
        require_once 'app/models/ProductModel.php';
        $productModel = new ProductModel();
        
        // Lấy ID loại sản phẩm từ URL (nếu có)
        $cat_id = isset($_GET['cat_id']) ? $_GET['cat_id'] : null;
    
        // Lấy tất cả danh mục để hiện sidebar
        $categories = $productModel->getAllCategories(); 
    
        // Nếu có cat_id thì lọc, không thì hiện tất cả
        if ($cat_id) {
            $products = $productModel->getProductsByCategory($cat_id);
        } else {
            $products = $productModel->getAllProducts();
        }
    
        require_once 'views/client/product/listproduct.php';
    }
   
}