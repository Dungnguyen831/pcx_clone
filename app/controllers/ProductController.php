<?php
    require_once __DIR__ . '/../../Models/ProductModel.php';
    class ProductController {
        private $productModel;

        public function __construct() {
            $this->productModel = new ProductModel();
        }
        public function index() {
            $products = $this->productModel->getAll();  
            require_once __DIR__ . '/../../views/Product/index.php';
        }
    }

    $product = new ProductController();
    $product->index();
?>
