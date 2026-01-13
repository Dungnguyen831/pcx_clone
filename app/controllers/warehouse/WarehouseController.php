<?php
require_once 'app/models/admin/AdminProductModel.php';
require_once 'app/models/warehouse/WarehouseModel.php'; 

class WarehouseController {
    private $db;

    public function __construct() {
        $this->db = new Database(); 
    }

    public function index() {
        $warehouseModel = new WarehouseModel();
        $total_items = $warehouseModel->getTotalProducts();
        $total_qty = $warehouseModel->getTotalStockQuantity();
        $low_count = $warehouseModel->getLowStockCount(); 
        $recent_activities = $warehouseModel->getRecentActivities();

        $page_title = "Bảng quản lí kho";
        $content_view = 'views/warehouse/index.php';
        require_once 'views/warehouse/layout/page.php';
    }

    // 1. MỤC: HIỂN THỊ GIAO DIỆN NHẬP KHO
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
        $search = isset($_GET['search']) ? $_GET['search'] : null;
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
        $data = $model->getHistory($query);
        $names = array_values(array_unique(array_column($data, 'product_name')));
        
        header('Content-Type: application/json');
        echo json_encode($names);
        exit;
    }

    // 3. MỤC: QUẢN LÝ TỒN KHO
    public function inventory() {
        $sql = "SELECT p.product_id, p.name, p.image, i.quantity, i.last_updated 
                FROM products p 
                JOIN inventory i ON p.product_id = i.product_id 
                ORDER BY i.last_updated DESC";
        
        $products = $this->db->fetchAll($sql);
        $page_title = "Quản lý tồn kho";
        $controller = "warehouse";
        $action = "Inventory";
        $content_view = 'views/warehouse/Inventory/inventory.php';
        
        require_once 'views/warehouse/layout/page.php';
    }

    // 4. XỬ LÝ NHẬP LẺ THỦ CÔNG (Quan trọng: Phải có hàm này)
    public function processImport() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $product_id = $_POST['product_id'];
            $quantity   = (int)$_POST['quantity'];
            $price      = (float)$_POST['price'];
            $note       = $_POST['note'] ?? 'Nhập hàng mới';
            $user_id    = $_SESSION['user_id'] ?? 1; 

            try {
                // Tạo phiếu nhập
                $sql_import = "INSERT INTO imports (user_id, note, created_at) VALUES ($user_id, '$note', NOW())";
                $this->db->execute($sql_import);

                $res = $this->db->fetchAll("SELECT import_id FROM imports ORDER BY import_id DESC LIMIT 1");
                $import_id = $res[0]['import_id'];

                // Lưu chi tiết & Cập nhật kho
                $this->db->execute("INSERT INTO import_details (import_id, product_id, quantity, import_price) VALUES ($import_id, $product_id, $quantity, $price)");
                $this->db->execute("UPDATE inventory SET quantity = quantity + $quantity WHERE product_id = $product_id");

                header("Location: index.php?controller=warehouse&action=history&message=success");
                exit();
            } catch (Exception $e) {
                die("Lỗi: " . $e->getMessage());
            }
        }
    }

    public function processExcelImport() {
        $libPath = $_SERVER['DOCUMENT_ROOT'] . '/web/pcx_clone/app/libs/SimpleXLSX.php';
        if (!file_exists($libPath)) {
            die("Không tìm thấy thư viện SimpleXLSX");
        }
        require_once $libPath;
    
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['excel_file'])) {
            if ($xlsx = \Shuchkin\SimpleXLSX::parse($_FILES['excel_file']['tmp_name'])) {
                $rows = $xlsx->rows();
                $user_id = $_SESSION['user_id'] ?? 1;
    
                $valid_items = [];
                $grand_total_cost = 0;
    
                // BƯỚC 1: Duyệt Excel để tính Total Cost và lấy giá nhập mới
                foreach ($rows as $index => $row) {
                    if ($index == 0 || empty($row[0])) continue; 
                    
                    $product_name = trim(addslashes($row[0])); 
                    $quantity     = (int)($row[1] ?? 0);
                    $price_input  = (float)($row[2] ?? 0); // Đây là giá nhập trong Excel
    
                    // Tìm sản phẩm trong DB
                    $sql_check = "SELECT product_id FROM products WHERE name = '$product_name' LIMIT 1";
                    $res_check = $this->db->fetchAll($sql_check);
    
                    if (!empty($res_check)) {
                        $p_id = $res_check[0]['product_id'];
                        $grand_total_cost += ($quantity * $price_input); // Tổng tiền hóa đơn
                        
                        $valid_items[] = [
                            'product_id' => $p_id,
                            'quantity'   => $quantity,
                            'price'      => $price_input
                        ];
                    }
                }
    
                // BƯỚC 2: Lưu vào Database
                if (!empty($valid_items)) {
                    // 2.1 Tạo phiếu nhập với total_cost (Dựa trên cấu trúc ảnh image_71f2a5.png)
                    $sql_import = "INSERT INTO imports (user_id, total_cost, note, created_at) 
                                   VALUES ($user_id, $grand_total_cost, 'Nhập kho từ Excel', NOW())";
                    $this->db->execute($sql_import);
                    
                    $res_import = $this->db->fetchAll("SELECT import_id FROM imports ORDER BY import_id DESC LIMIT 1");
                    $import_id = $res_import[0]['import_id'];
    
                    // 2.2 Lưu chi tiết và cập nhật bảng Product + Inventory
                    foreach ($valid_items as $item) {
                        $p_id = $item['product_id'];
                        $qty  = $item['quantity'];
                        $prc  = $item['price'];
    
                        // A. Lưu vào chi tiết phiếu nhập
                        $this->db->execute("INSERT INTO import_details (import_id, product_id, quantity, import_price) 
                                           VALUES ($import_id, $p_id, $qty, $prc)");
    
                        // B. CẬP NHẬT GIÁ NHẬP VÀO BẢNG PRODUCTS (Theo ý bạn muốn)
    // Cột import_price trong bảng products (ảnh image_72e5a8.png) sẽ lấy giá từ Excel
                        $this->db->execute("UPDATE products SET import_price = $prc WHERE product_id = $p_id");
    
                        // C. Cập nhật tồn kho (Inventory)
                        $check_inv = $this->db->fetchAll("SELECT * FROM inventory WHERE product_id = $p_id");
                        if (!empty($check_inv)) {
                            $this->db->execute("UPDATE inventory SET quantity = quantity + $qty WHERE product_id = $p_id");
                        } else {
                            $this->db->execute("INSERT INTO inventory (product_id, quantity, last_updated) VALUES ($p_id, $qty, NOW())");
                        }
                    }
                }
    
                header("Location: index.php?controller=warehouse&action=history&message=excel_success");
                exit();
            }
        }
    }

    // 6. XUẤT FILE EXCEL LỊCH SỬ
    public function exportHistoryExcel() {
        $searchTerm = $_GET['search'] ?? null;
        $model = new WarehouseModel();
        $history = $model->getHistory($searchTerm);

        $filename = "LichSu_NhapKho_" . date('d-m-Y') . ".xls";
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");

        $style = "<style>.excel-table { font-family: Arial; border-collapse: collapse; width: 100%; } .excel-table th { background: #27ae60; color: white; border: 1px solid black; } .excel-table td { border: 1px solid black; }</style>";
        
        echo "<html><head><meta charset='UTF-8'>$style</head><body>";
        echo "<h2>NHẬT KÝ NHẬP KHO</h2><table class='excel-table'><thead><tr><th>Ngày</th><th>Sản phẩm</th><th>SL</th><th>Giá</th><th>Thành tiền</th></tr></thead><tbody>";
        
        foreach ($history as $h) {
            $total = ($h['import_price'] ?? 0) * $h['quantity'];
            echo "<tr><td>{$h['created_at']}</td><td>{$h['product_name']}</td><td>{$h['quantity']}</td><td>{$h['import_price']}</td><td>$total</td></tr>";
        }
        echo "</tbody></table></body></html>";
        exit();
    }
} // Kết thúc Class