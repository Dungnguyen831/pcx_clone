<?php
// app/controllers/client/HomeController.php
require_once 'app/models/ProductModel.php';

class HomeController {
    
    public function index() {
        $productModel = new ProductModel();

        $products = $productModel->getHomeProducts(8);

        require_once 'views/client/home/index.php';
    }


    public function listproduct() {
        require_once 'app/models/ProductModel.php';
        $productModel = new ProductModel();
        
        // 1. Phải lấy categories để sidebar bên trái không bị lỗi
        $categories = $productModel->getAllCategories(); 
        
        // 2. Lấy danh sách sản phẩm
        $products = $productModel->getAllProducts();
        
        // 3. Truyền cả 2 biến này vào view
        require_once 'views/client/product/listproduct.php'; 
    }
 
}


    
?>