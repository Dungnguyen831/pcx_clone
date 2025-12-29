<?php
class ProductController {
    public function detail() {
        // 1. Lấy ID từ URL
        $id = isset($_GET['id']) ? $_GET['id'] : 0;

        // 2. Gọi Model để lấy dữ liệu
        require_once 'app/models/client/ProductModel.php';
        $productModel = new ProductModel();
        $product = $productModel->getProductById($id);

        if (!$product) {
            die("Sản phẩm không tồn tại!");
        }

        // 3. Hiển thị View chitiet.php
        require_once 'views/client/productdetail/chitiet.php';
    }
    
    public function index() {
        require_once 'app/models/client/ProductModel.php';
        $productModel = new ProductModel();
        
        // 1. Lấy dữ liệu từ URL
        $cat_id = isset($_GET['cat_id']) ? $_GET['cat_id'] : null;
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : null;
    
        // 2. Lấy danh sách danh mục (để hiện sidebar)
        $categories = $productModel->getAllCategories(); 
    
        // Ta dùng chung 1 hàm getAllProducts và truyền tham số vào
        $products = $productModel->getAllProducts($cat_id, $keyword);
    
        require_once 'views/client/product/listproduct.php';
    }
   // Trong ProductController.php
public function __construct() {
    require_once 'app/models/client/CartModel.php';
    $this->cartModel = new CartModel(); // Khởi tạo để tránh lỗi "Call to a member function... on null"
}
}