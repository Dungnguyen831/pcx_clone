<?php
class AdminProductController
{
    private $productModel;

    public function __construct()
    {
        // Kiểm tra quyền Admin
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }
        require_once 'app/models/admin/AdminProductModel.php';
        $this->productModel = new AdminProductModel();
    }

    // app/controllers/admin/AdminProductController.php

    public function index()
    {
        // Lấy từ khóa tìm kiếm từ URL (nếu có)
        $search_id = $_GET['search_id'] ?? null;
        $search_name = $_GET['search_name'] ?? null;

        // Truyền tham số tìm kiếm vào Model
        $products = $this->productModel->getAllProductsAdmin($search_id, $search_name);

        $page_title = "Quản lý sản phẩm";
        $controller = 'product';
        $content_view = 'views/admin/product/index.php';
        require_once 'views/admin/layouts/page.php';
    }

    public function create()
    {
        $categories = $this->productModel->getCategories();
        $brands = $this->productModel->getBrands();
        $page_title = "Thêm mới sản phẩm";
        $controller = 'product';
        $content_view = 'views/admin/product/add.php';
        require_once 'views/admin/layouts/page.php';
    }

    // --- ĐÃ SỬA: GIỮ NGUYÊN TÊN FILE GỐC ---
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $image = "default.png";

            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $targetDir = "assets/uploads/";

                // Lấy đúng tên file gốc từ máy tính của bạn
                $image = basename($_FILES["image"]["name"]);
                $targetFile = $targetDir . $image;

                // Thực hiện di chuyển file vào thư mục upload
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                    // Di chuyển thành công
                } else {
                    echo "Lỗi: Không thể tải ảnh lên thư mục.";
                    exit();
                }
            }

            $data = [
                'name'        => $_POST['name'],
                'category_id' => $_POST['category_id'],
                'brand_id'    => $_POST['brand_id'],
                'price'       => $_POST['price'],
                'quantity'    => $_POST['quantity'],
                'image'       => $image, // Lưu đúng tên file gốc vào DB
                'description' => $_POST['description'] ?? '',
                'status'      => 1
            ];

            if ($this->productModel->addProduct($data)) {
                header("Location: index.php?controller=admin-product&action=index&msg=success");
            } else {
                echo "Lỗi: Không thể lưu sản phẩm!";
            }
        }
    }

    // app/controllers/admin/AdminProductController.php

    // Hàm hiển thị Form sửa
    public function edit()
    {
        $id = $_GET['id'] ?? null;
        $product = $this->productModel->getProductById($id);

        if (!$product) {
            header("Location: index.php?controller=admin-product");
            exit();
        }

        $categories = $this->productModel->getCategories();
        $brands = $this->productModel->getBrands();

        $page_title = "Chỉnh sửa sản phẩm";
        $controller = 'product';
        $content_view = 'views/admin/product/edit.php'; // File view này ta sẽ tạo ở bước 3
        require_once 'views/admin/layouts/page.php';
    }

    // Hàm xử lý khi nhấn nút Lưu (Update)
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            $old_image = $_POST['old_image']; // Tên ảnh cũ lưu trong input hidden

            // Logic xử lý ảnh: Nếu không chọn ảnh mới, lấy lại tên ảnh cũ
            $image = $old_image;
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $image = basename($_FILES["image"]["name"]);
                move_uploaded_file($_FILES["image"]["tmp_name"], "assets/uploads/products/" . $image);
            }

            $data = [
                'id'          => $id,
                'name'        => $_POST['name'],
                'category_id' => $_POST['category_id'],
                'brand_id'    => $_POST['brand_id'],
                'price'       => $_POST['price'],
                'quantity'    => $_POST['quantity'],
                'image'       => $image,
                'description' => $_POST['description']
            ];

            if ($this->productModel->updateProduct($data)) {
                header("Location: index.php?controller=admin-product&action=index&msg=updated");
            }
        }
    }
    public function delete()
    {
        $id = $_GET['id'] ?? null;

        if ($id) {
            // 1. Lấy thông tin sản phẩm để biết tên file ảnh
            $product = $this->productModel->getProductById($id);

            if ($product) {
                // 2. Thực hiện xóa trong Database
                if ($this->productModel->deleteProduct($id)) {

                    // 3. Xóa file ảnh vật lý trong thư mục assets/uploads/ cho sạch máy
                    $imagePath = "assets/uploads/" . $product['image'];
                    if (file_exists($imagePath) && $product['image'] != '') {
                        unlink($imagePath);
                    }

                    header("Location: index.php?controller=admin-product&action=index&msg=deleted");
                    exit();
                }
            }
        }
    }
}
