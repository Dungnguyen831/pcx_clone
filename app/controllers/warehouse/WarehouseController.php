<?php
require_once 'app/models/admin/AdminProductModel.php';
// Giả sử bạn có WarehouseModel để xử lý database chuyên sâu
require_once 'app/models/warehouse/WarehouseModel.php'; 

class WarehouseController {
    private $db;

    public function __construct() {
        // Khởi tạo database để dùng cho các câu lệnh SQL
        $this->db = new Database(); 
    }
  public function index() {
    // Khởi tạo Model
    $warehouseModel = new WarehouseModel();

    // Gọi các hàm từ Model để lấy dữ liệu cho 3 trường bạn đang dùng
    $total_items = $warehouseModel->getTotalProducts();
    $total_qty = $warehouseModel->getTotalStockQuantity();
    $low_count = $warehouseModel->getLowStockCount(); // Sửa lỗi Undefined variable tại đây
    
    // Lấy hoạt động thực tế cho bảng bên dưới
    $recent_activities = $warehouseModel->getRecentActivities();

    // Thiết lập hiển thị
    $page_title = "Bảng quản lí kho";
    $content_view = 'views/warehouse/index.php';
    require_once 'views/warehouse/layout/page.php';
}

    // 1. MỤC: TIẾN HÀNH NHẬP KHO
    public function Import() {
        $productModel = new AdminProductModel();
        $products = $productModel->getAllProducts();
        
        $page_title = "Tiến hành nhập kho";
        $controller = "warehouse"; 
        $action = "Import";
        $content_view = 'views/warehouse/Import/Import.php'; 
        
        require_once 'views/warehouse/layout/page.php'; 
    }

    // 2. MỤC: LỊCH SỬ NHẬP KHO
    public function history() {
    $warehouseModel = new WarehouseModel();
    
    // Lấy từ khóa tìm kiếm từ URL (nếu có)
    $search = isset($_GET['search']) ? $_GET['search'] : null;
    
    // Gọi hàm lấy lịch sử từ Model (có kèm theo từ khóa tìm kiếm)
    $history = $warehouseModel->getHistory($search);

    $page_title = "Lịch sử nhập kho";
    $controller = "warehouse";
    $action = "History";
    $content_view = 'views/warehouse/History/History.php';
    
    require_once 'views/warehouse/layout/page.php';
    }
    
    public function getSuggestions() {
    $query = $_GET['query'] ?? '';
    $model = new WarehouseModel();
    // Lấy dữ liệu từ hàm getHistory đã sửa ở trên
    $data = $model->getHistory($query);
    
    // Trích xuất tên sản phẩm và xóa trùng lặp
    $names = array_values(array_unique(array_column($data, 'product_name')));
    
    header('Content-Type: application/json');
    echo json_encode($names);
    exit;
    }

    // 3. MỤC: QUẢN LÝ KHO (TỒN KHO)
    public function inventory() {
    // Câu lệnh SQL JOIN để lấy tên từ bảng products và số lượng từ bảng inventory
    $sql = "SELECT p.product_id, p.name, p.image, i.quantity, i.last_updated 
            FROM products p 
            JOIN inventory i ON p.product_id = i.product_id 
            ORDER BY i.last_updated DESC";
    
    $products = $this->db->fetchAll($sql);

    $page_title = "Quản lý tồn kho";
    $controller = "warehouse";
    $action = "Inventory";
    
    // Lưu ý: Sửa đúng đường dẫn file (xóa dấu // và kiểm tra chữ i thường)
    $content_view = 'views/warehouse/Inventory/inventory.php';
    
    require_once 'views/warehouse/layout/page.php';
}

     public function processImport() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // 1. Lấy dữ liệu từ FORM
        $product_id = $_POST['product_id'];
        $quantity   = (int)$_POST['quantity'];
        $price =  (float)$_POST['price'];
        $note       = isset($_POST['note']) ? $_POST['note'] : 'Nhập hàng mới';
        $user_id    = $_SESSION['user_id'] ?? 1; 

        try {
            // 2. Bước 1: Lưu vào bảng 'imports' (Bảng cha)
            $sql_import = "INSERT INTO imports (user_id, note, created_at) 
                           VALUES ($user_id, '$note', NOW())";
            $this->db->execute($sql_import);

            // 3. THAY THẾ CÁCH LẤY ID: Truy vấn ID lớn nhất vừa tạo của user này
            // Cách này khắc phục triệt để lỗi "Không thể tạo ID" dù đã tích A_I
            $sql_get_id = "SELECT import_id FROM imports 
                           WHERE user_id = $user_id 
                           ORDER BY import_id DESC LIMIT 1";
            $result = $this->db->fetchAll($sql_get_id);
            $import_id = $result[0]['import_id'] ?? 0;

            // 4. Kiểm tra ID để tránh lỗi Foreign Key
            if ($import_id == 0) {
                die("Lỗi: Hệ thống không tìm thấy phiếu nhập vừa tạo. Vui lòng kiểm tra lại bảng imports.");
            }

            // 5. Bước 2: Lưu vào bảng 'import_details' (Bảng con)
            $sql_detail = "INSERT INTO import_details (import_id, product_id, quantity, import_price) 
                           VALUES ($import_id, $product_id, $quantity,$price)";
            $this->db->execute($sql_detail);

            // 6. Bước 3: Cập nhật số lượng trong bảng 'products'
            $sql_update = "UPDATE inventory SET quantity = quantity + $quantity 
                           WHERE product_id = $product_id";
            $this->db->execute($sql_update);

            // 7. Thành công: Chuyển hướng về trang lịch sử
            // Đường dẫn action=history sẽ gọi hàm history() trỏ vào views/warehouse/History/History.php
            header("Location: index.php?controller=warehouse&action=history&message=success");
            exit();

        } catch (Exception $e) {
            die("Lỗi xử lý kho: " . $e->getMessage());
        }
    }
}
    /** XUẤT EXCEL LỊCH SỬ NHẬP KHO */
public function exportHistoryExcel() 
{
    // 1. Lấy dữ liệu từ Model (bao gồm cả tìm kiếm nếu có)
    $searchTerm = $_GET['search'] ?? null;
    $model = new WarehouseModel();
    $history = $model->getHistory($searchTerm); // Đảm bảo hàm này lấy đúng import_price

    $filename = "LichSu_NhapKho_" . date('d-m-Y') . ".xls";

    // 2. Định nghĩa CSS cho file Excel
    $style = "
    <style>
        .excel-table { font-family: 'Arial', sans-serif; border-collapse: collapse; width: 100%; }
        .excel-table th { 
            background-color: #27ae60; color: #ffffff; font-weight: bold; 
            border: 0.5pt solid #000000; text-align: center; height: 35px; font-size: 11pt;
        }
        .excel-table td { 
            border: 0.5pt solid #000000; padding: 8px; vertical-align: middle; font-size: 10pt; 
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .title-doc { font-size: 16pt; font-weight: bold; text-align: center; color: #27ae60; }
    </style>";

    // 3. Thiết lập Header để trình duyệt hiểu là file Excel
    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=\"$filename\"");

    echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    echo '<head><meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">' . $style . '</head>';
    echo '<body>';
    
    echo '<div class="title-doc">NHẬT KÝ NHẬP KHO PCX ADMIN</div>';
    echo '<div style="text-align: center; margin-bottom: 20px;">Ngày xuất báo cáo: ' . date('d/m/Y H:i') . '</div><br>';

    echo '<table class="excel-table" border="1">';
    echo '<thead>
            <tr>
                <th style="width: 120pt;">Ngày thực hiện</th>
                <th style="width: 250pt;">Tên sản phẩm</th>
                <th style="width: 80pt;">Số lượng</th>
                <th style="width: 100pt;">Giá nhập</th>
                <th style="width: 120pt;">Thành tiền</th>
                <th style="width: 200pt;">Ghi chú</th>
            </tr>
          </thead><tbody>';

    if (!empty($history)) {
        $grandTotal = 0;
        foreach ($history as $h) {
            // Sử dụng import_price và total_price từ database
            $price = $h['import_price'] ?? 0;
            $total = $h['total_price'] ?? ($price * $h['quantity']);
            $grandTotal += $total;

            echo '<tr>
                <td class="text-center">' . date('d/m/Y H:i', strtotime($h['created_at'])) . '</td>
                <td>' . htmlspecialchars($h['product_name']) . '</td>
                <td class="text-center">' . $h['quantity'] . '</td>
                <td class="text-right">' . number_format($price, 0, ',', '.') . '</td>
                <td class="text-right">' . number_format($total, 0, ',', '.') . '</td>
                <td>' . htmlspecialchars($h['note'] ?? 'Nhập hàng mới') . '</td>
            </tr>';
        }
        // Thêm dòng tổng cộng cuối bảng
        echo '<tr>
            <td colspan="4" style="text-align:right; font-weight:bold; background:#f9f9f9;">TỔNG GIÁ TRỊ NHẬP:</td>
            <td class="text-right" style="font-weight:bold; background:#f9f9f9;">' . number_format($grandTotal, 0, ',', '.') . ' VNĐ</td>
            <td style="background:#f9f9f9;"></td>
        </tr>';
    } else {
        echo '<tr><td colspan="6" style="text-align:center;">Không có dữ liệu nhập kho.</td></tr>';
    }

    echo '</tbody></table></body></html>';
    exit;
}
    
}
