<?php

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
        // 1. Lấy dữ liệu sản phẩm từ Model
        // (Hàm này bạn đã có trong model rồi)
        $products = $this->productModel->getAllProductsAdmin(null, null);
        
        $filename = "DanhSach_SanPham_" . date('d-m-Y') . ".xls";

        // 2. CSS định dạng bảng (Màu xanh, kẻ khung)
        $style = "
        <style>
            body { font-family: 'Times New Roman', serif; }
            .excel-table { border-collapse: collapse; width: 100%; font-size: 11pt; }
            .excel-table th { 
                background-color: #1cc88a; /* Màu xanh lá cho sản phẩm */
                color: #ffffff; 
                font-weight: bold; 
                border: 1px solid #000000; 
                text-align: center; 
                height: 40px; 
                vertical-align: middle;
            }
            .excel-table td { 
                border: 1px solid #000000; 
                padding: 8px; 
                vertical-align: middle;
            }
            .text-center { text-align: center; }
            .text-right { text-align: right; }
            .money-format { mso-number-format:'\#\,\#\#0'; } /* Định dạng số tiền */
            .title-doc { font-size: 18pt; font-weight: bold; text-align: center; color: #2c3e50; margin-bottom: 20px; }
        </style>";

        // 3. Thiết lập Header tải xuống
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        echo "\xEF\xBB\xBF"; // BOM fix lỗi tiếng Việt

        // 4. Xuất HTML
        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        echo '<head><meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">' . $style . '</head>';
        echo '<body>';
        
        echo '<div class="title-doc">DANH SÁCH SẢN PHẨM</div>';

        echo '<table class="excel-table">';
        echo '<thead>
                <tr>
                    <th style="width: 50px;">ID</th>
                    <th style="width: 300px;">Tên sản phẩm</th>
                    <th style="width: 150px;">Danh mục</th>
                    <th style="width: 150px;">Thương hiệu</th>
                    <th style="width: 120px;">Giá bán</th>
                    <th style="width: 80px;">Kho</th>
                    <th style="width: 100px;">Trạng thái</th>
                </tr>
            </thead><tbody>';

        if (!empty($products)) {
            foreach ($products as $p) {
                // Xử lý trạng thái
                $statusText = ($p['status'] == 1) ? 'Hiển thị' : 'Ẩn';
                
                // Xử lý tên danh mục/thương hiệu (phòng trường hợp null)
                $catName = isset($p['category_name']) ? $p['category_name'] : $p['category_id'];
                $brandName = isset($p['brand_name']) ? $p['brand_name'] : $p['brand_id'];

                echo '<tr>
                    <td class="text-center">' . $p['product_id'] . '</td>
                    <td>' . htmlspecialchars($p['name']) . '</td>
                    <td>' . htmlspecialchars($catName) . '</td>
                    <td>' . htmlspecialchars($brandName) . '</td>
                    <td class="text-right money-format">' . number_format($p['price'], 0, ',', '.') . '</td>
                    <td class="text-center">' . $p['quantity'] . '</td>
                    <td class="text-center">' . $statusText . '</td>
                </tr>';
            }
        }

        echo '</tbody></table></body></html>';
        exit;
    }

    /* ================== NHẬP EXCEL (Dùng SimpleXLSX) ================== */
    public function importExcel()
    {
        // 1. Gọi thư viện
        require_once 'app/libs/SimpleXLSX.php';

        if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
            header("Location: index.php?controller=admin-product&action=index&msg=error");
            exit();
        }

        // 2. Đọc file Excel tải lên
        $xlsx = Shuchkin\SimpleXLSX::parse($_FILES['excel_file']['tmp_name']);

        if ($xlsx) {
            $rows = $xlsx->rows(); // Lấy toàn bộ dòng
            
            // Xóa dòng tiêu đề (Dòng đầu tiên)
            array_shift($rows);

            foreach ($rows as $row) {
                // $row[0] là cột A, $row[1] là cột B...
                
                // Kiểm tra nếu tên sản phẩm rỗng thì bỏ qua
                if (empty($row[0])) continue;

                $data = [
                    'name'        => $row[0],
                    'category_id' => (int)$row[1],
                    'brand_id'    => (int)$row[2],
                    'price'       => (float)$row[3],
                    'quantity'    => (int)$row[4],
                    'image'       => 'default.png',
                    'description' => $row[5] ?? '',
                    'status'      => isset($row[6]) ? (int)$row[6] : 1
                ];

                $this->productModel->addProduct($data);
            }
            
            header("Location: index.php?controller=admin-product&action=index&msg=imported");
        } else {
            // Lỗi đọc file (ví dụ file bị mã hóa hoặc sai định dạng)
            echo SimpleXLSX::parseError();
        }
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
