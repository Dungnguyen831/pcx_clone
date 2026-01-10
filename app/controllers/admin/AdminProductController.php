<?php
// require_once 'vendor/autoload.php';

// use PhpOffice\PhpSpreadsheet\Spreadsheet;
// use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
// use PhpOffice\PhpSpreadsheet\IOFactory;

class AdminProductController
{
    private $productModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Check quyền admin
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        require_once 'app/models/admin/AdminProductModel.php';
        $this->productModel = new AdminProductModel();
    }

    /* ================== DANH SÁCH ================== */
    public function index()
    {
        $search_id   = $_GET['search_id'] ?? null;
        $search_name = $_GET['search_name'] ?? null;

        $products = $this->productModel->getAllProductsAdmin($search_id, $search_name);

        $page_title  = "Quản lý sản phẩm";
        $controller  = 'product';
        $content_view = 'views/admin/product/index.php';
        require_once 'views/admin/layouts/page.php';
    }

    /* ================== THÊM ================== */
    public function create()
    {
        $categories = $this->productModel->getCategories();
        $brands     = $this->productModel->getBrands();

        $page_title   = "Thêm mới sản phẩm";
        $controller   = 'product';
        $content_view = 'views/admin/product/add.php';
        require_once 'views/admin/layouts/page.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $image = 'default.png';
            if (!empty($_FILES['image']['name'])) {
                $image = $this->handleUpload($_FILES['image']) ?? 'default.png';
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

            $this->productModel->addProduct($data);
            header("Location: index.php?controller=admin-product&action=index&msg=success");
            exit();
        }
    }

    /* ================== SỬA ================== */
    public function edit()
    {
        $id = $_GET['id'] ?? null;
        $product = $this->productModel->getProductById($id);

        if (!$product) {
            header("Location: index.php?controller=admin-product");
            exit();
        }

        $categories = $this->productModel->getCategories();
        $brands     = $this->productModel->getBrands();

        $page_title   = "Chỉnh sửa sản phẩm";
        $controller   = 'product';
        $content_view = 'views/admin/product/edit.php';
        require_once 'views/admin/layouts/page.php';
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $image = $_POST['old_image'];

            if (!empty($_FILES['image']['name'])) {
                $newImage = $this->handleUpload($_FILES['image']);
                if ($newImage) {
                    if ($image !== 'default.png') {
                        $oldPath = "assets/uploads/products/$image";
                        if (file_exists($oldPath)) unlink($oldPath);
                    }
                    $image = $newImage;
                }
            }

            $data = [
                'id'          => $_POST['id'],
                'name'        => $_POST['name'],
                'category_id' => $_POST['category_id'],
                'brand_id'    => $_POST['brand_id'],
                'price'       => $_POST['price'],
                'quantity'    => $_POST['quantity'],
                'image'       => $image,
                'description' => $_POST['description']
            ];

            $this->productModel->updateProduct($data);
            header("Location: index.php?controller=admin-product&action=index&msg=updated");
            exit();
        }
    }

    /* ================== XOÁ ================== */
    public function delete()
    {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $product = $this->productModel->getProductById($id);
            if ($product) {
                $this->productModel->deleteProduct($id);
                if ($product['image'] !== 'default.png') {
                    $path = "assets/uploads/products/" . $product['image'];
                    if (file_exists($path)) unlink($path);
                }
            }
        }
        header("Location: index.php?controller=admin-product&action=index&msg=deleted");
        exit();
    }

    /* ================== XUẤT EXCEL ================== */
    public function exportExcel()
    {
        $products = $this->productModel->getAllProductsAdmin(null, null);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([
            ['Tên', 'Danh mục ID', 'Thương hiệu ID', 'Giá', 'Số lượng', 'Mô tả', 'Trạng thái']
        ], null, 'A1');

        $row = 2;
        foreach ($products as $p) {
            $sheet->fromArray([
                $p['name'],
                $p['category_id'],
                $p['brand_id'],
                $p['price'],
                $p['quantity'],
                $p['description'],
                $p['status']
            ], null, 'A' . $row);
            $row++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="products.xlsx"');
        header('Cache-Control: max-age=0');

        (new Xlsx($spreadsheet))->save('php://output');
        exit();
    }

    /* ================== NHẬP EXCEL ================== */
    public function importExcel()
    {
        if (!isset($_FILES['excel_file'])) {
            header("Location: index.php?controller=admin-product&action=index");
            exit();
        }

        $spreadsheet = IOFactory::load($_FILES['excel_file']['tmp_name']);
        $rows = $spreadsheet->getActiveSheet()->toArray();

        unset($rows[0]); // bỏ header

        foreach ($rows as $row) {
            if (empty($row[0])) continue;

            $data = [
                'name'        => $row[0],
                'category_id' => $row[1],
                'brand_id'    => $row[2],
                'price'       => $row[3],
                'quantity'    => $row[4],
                'image'       => 'default.png',
                'description' => $row[5] ?? '',
                'status'      => $row[6] ?? 1
            ];

            $this->productModel->addProduct($data);
        }

        header("Location: index.php?controller=admin-product&action=index&msg=imported");
        exit();
    }

    /* ================== UPLOAD ẢNH ================== */
    private function handleUpload($file)
    {
        $dir = "assets/uploads/products/";
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        $fileName = time() . '_' . basename($file['name']);
        $target   = $dir . $fileName;

        return move_uploaded_file($file['tmp_name'], $target) ? $fileName : null;
    }
}
