<?php
// app/controllers/client/HomeController.php
require_once 'app/models/ProductModel.php';

class HomeController {
    
    public function index() {
        $productModel = new ProductModel();

        $products = $productModel->getHomeProducts(8);

        require_once 'views/client/home/index.php';
    }
}
?>