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
        // 1. Gọi file thư viện bạn vừa tạo ở Bước 1
        require_once 'app/libs/SimpleXLSX.php';

        // 2. Kiểm tra xem người dùng có gửi file lên không
        if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
            // Nếu lỗi hoặc không có file -> Quay về trang chủ báo lỗi
            header("Location: index.php?controller=admin-product&action=index&msg=error");
            exit();
        }

        // 3. Bắt đầu đọc file Excel
        // ::parse() là hàm của thư viện giúp đọc file từ bộ nhớ tạm
        $xlsx = Shuchkin\SimpleXLSX::parse($_FILES['excel_file']['tmp_name']);

        if ($xlsx) {
            $rows = $xlsx->rows(); // Lấy tất cả các dòng trong file Excel
            
            // Xóa dòng đầu tiên (vì nó là dòng tiêu đề: Tên, Giá, Số lượng...)
            array_shift($rows); 

            $count = 0; // Biến đếm số sản phẩm thêm thành công

            foreach ($rows as $row) {
                // $row[0] là Cột A, $row[1] là Cột B...
                
                // Kiểm tra: Nếu tên sản phẩm (Cột A) bỏ trống thì bỏ qua dòng này
                if (empty($row[0])) continue;

                // 4. Ghép dữ liệu từ Excel vào mảng để lưu Database
                $data = [
                    'name'        => $row[0],       // Cột A: Tên sản phẩm
                    'category_id' => (int)$row[1],  // Cột B: ID Danh mục (phải là số)
                    'brand_id'    => (int)$row[2],  // Cột C: ID Thương hiệu (phải là số)
                    'price'       => (float)$row[3],// Cột D: Giá bán
                    'quantity'    => (int)$row[4],  // Cột E: Số lượng trong kho
                    'description' => $row[5] ?? '', // Cột F: Mô tả (nếu có)
                    'image'       => 'default.png', // Mặc định ảnh là default
                    'status'      => 1              // Mặc định là Hiển thị
                ];

                // Gọi Model để thêm vào CSDL
                $this->productModel->addProduct($data);
                $count++;
            }
            
            // Thành công -> Quay về và thông báo số lượng đã thêm
            header("Location: index.php?controller=admin-product&action=index&msg=imported&count=$count");
        } else {
            // Trường hợp file tải lên không phải Excel chuẩn hoặc bị lỗi
            echo "Lỗi: " . Shuchkin\SimpleXLSX::parseError();
        }
        exit();
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