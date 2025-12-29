<?php
class AdminProductController
{
    private $productModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Kiểm tra quyền Admin
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }
        require_once 'app/models/admin/AdminProductModel.php';
        $this->productModel = new AdminProductModel();
    }

    public function index()
    {
        $search_id = $_GET['search_id'] ?? null;
        $search_name = $_GET['search_name'] ?? null;
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

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $image = "default.png";

            // Xử lý Upload Ảnh
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $image = $this->handleUpload($_FILES['image']);
                if (!$image) {
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
                'image'       => $image,
                'description' => $_POST['description'] ?? '',
                'status'      => 1
            ];

            if ($this->productModel->addProduct($data)) {
                header("Location: index.php?controller=admin-product&action=index&msg=success");
            }
        }
    }

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
        $content_view = 'views/admin/product/edit.php';
        require_once 'views/admin/layouts/page.php';
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            $old_image = $_POST['old_image'];
            $image = $old_image;

            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $new_image = $this->handleUpload($_FILES['image']);
                if ($new_image) {
                    $image = $new_image;
                    // Xóa ảnh cũ cho sạch máy (tránh xóa ảnh mặc định)
                    if ($old_image != 'default.png') {
                        $oldPath = "assets/uploads/products/" . $old_image;
                        if (file_exists($oldPath)) unlink($oldPath);
                    }
                }
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
            $product = $this->productModel->getProductById($id);
            if ($product) {
                if ($this->productModel->deleteProduct($id)) {
                    if ($product['image'] != 'default.png') {
                        $imagePath = "assets/uploads/products/" . $product['image'];
                        if (file_exists($imagePath)) unlink($imagePath);
                    }
                    header("Location: index.php?controller=admin-product&action=index&msg=deleted");
                    exit();
                }
            }
        }
    }

    // Hàm phụ trợ xử lý upload dùng chung
    private function handleUpload($file)
    {
        $targetDir = "assets/uploads/products/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $fileName = time() . '_' . basename($file["name"]);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            return $fileName;
        }
        return false;
    }
}
