<?php
require_once 'app/models/client/UserModel.php'; 

class UserController {
    private $userModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        $this->userModel = new UserModel();
    }
    
    public function index() {
        $role = isset($_GET['role']) ? (int)$_GET['role'] : 0;

        $users = $this->userModel->getUsersByRole($role);
        
        $controller = 'user'; 

        if ($role == 2) {
            $page_title = "Quản lý nhân viên";
            $content_view = 'views/admin/user/index_employee.php';
        } else {
            $page_title = "Quản lý khách hàng";
            $content_view = 'views/admin/user/index_customer.php';
        }

        require_once 'views/admin/layouts/page.php';
    }

    public function create() {
        $page_title = "Thêm nhân viên mới";
        $controller = 'user';
        
        $content_view = 'views/admin/user/create.php';
        require_once 'views/admin/layouts/page.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'full_name' => $_POST['full_name'] ?? '',
                'email'     => $_POST['email'] ?? '',
                'password'  => $_POST['password'] ?? '', 
                'phone'     => $_POST['phone'] ?? '',
                'role'      => 2
            ];

            if ($this->userModel->isEmailExists($data['email'])) {
                echo "<script>
                    alert('Lỗi: Email này đã tồn tại!');
                    window.history.back();
                </script>";
                return;
            }

            if ($this->userModel->createUser($data)) {
                // Thành công -> Về danh sách nhân viên
                header("Location: index.php?controller=user&action=index&role=2&msg=created");
                exit();
            } else {
                echo "<script>alert('Lỗi hệ thống! Vui lòng thử lại.'); window.history.back();</script>";
            }
        }
    }

    // Xuất Excel
    public function exportExcel() 
    {
        $customers = $this->userModel->getUsersByRole(0);

        $filename = "DanhSachKhachHang_" . date('d-m-Y') . ".xls";

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Transfer-Encoding: binary");

        echo "\xEF\xBB\xBF"; 

        $style = "
        <style>
            body { font-family: 'Times New Roman', serif; font-size: 12pt; }
            .excel-table { width: 100%; border-collapse: collapse; border: 1px solid #000; }
            .excel-table th { background-color: #2c3e50; color: #ffffff; padding: 10px; border: 1px solid #000; text-align: center; font-weight: bold; }
            .excel-table td { border: 1px solid #000; padding: 8px 5px; vertical-align: middle; }
            .odd-row { background-color: #ffffff; }
            .even-row { background-color: #f2f2f2; }
            .text-center { text-align: center; }
            .text-bold { font-weight: bold; }
            .format-text { mso-number-format:'\@'; } 
            .format-num { mso-number-format:'0'; }
            .title-doc { font-size: 18pt; font-weight: bold; text-align: center; margin: 20px 0; color: #2980b9; text-transform: uppercase; }
        </style>";

        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $style . '</head><body>';

        echo '<div class="title-doc">DANH SÁCH KHÁCH HÀNG THÀNH VIÊN</div>';
        echo '<div style="text-align:center; margin-bottom:20px; font-style:italic;">Ngày xuất báo cáo: ' . date('d/m/Y H:i') . '</div>';

        echo '<table class="excel-table">
                <thead>
                    <tr>
                        <th width="50">ID</th>
                        <th width="250">Họ và Tên</th>
                        <th width="250">Email</th>
                        <th width="120">Số điện thoại</th>
                        <th width="100">Điểm thưởng</th>
                        <th width="150">Ngày đăng ký</th>
                    </tr>
                </thead>
                <tbody>';

        if (!empty($customers)) {
            $i = 0;
            foreach ($customers as $c) {
                $rowClass = ($i % 2 == 0) ? 'odd-row' : 'even-row';
                $i++;
                $points = isset($c['reward_points']) ? $c['reward_points'] : 0;
                $phone  = !empty($c['phone']) ? $c['phone'] : '-';

                echo '<tr class="' . $rowClass . '">
                    <td class="text-center">' . $c['user_id'] . '</td>
                    <td class="text-bold">' . htmlspecialchars($c['full_name']) . '</td>
                    <td>' . htmlspecialchars($c['email']) . '</td>
                    <td class="text-center format-text">' . $phone . '</td>
                    <td class="text-center format-num" style="color:#d35400; font-weight:bold;">' . number_format($points) . '</td>
                    <td class="text-center">' . date('d/m/Y', strtotime($c['created_at'])) . '</td>
                </tr>';
            }
        } else {
            echo '<tr><td colspan="6" class="text-center">Không có dữ liệu.</td></tr>';
        }

        echo '</tbody></table></body></html>';
        exit;
    }

    public function import()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['excel_file'])) {
            
            require_once 'app/libs/SimpleXLSX.php';

            if ($_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
                echo "<script>
                    alert('Lỗi upload: Vui lòng chọn file Excel hợp lệ hoặc kiểm tra lại file!');
                    window.location.href = 'index.php?controller=user&action=index&role=2';
                </script>";
                exit();                         
            }

            $xlsx = Shuchkin\SimpleXLSX::parse($_FILES['excel_file']['tmp_name']);

            if ($xlsx) {
                $rows = $xlsx->rows();
                array_shift($rows); 

                $count = 0;
                $fail  = 0;

                foreach ($rows as $row) {
                    if (empty($row[0]) || empty($row[1])) continue;

                    $email = trim($row[1]);

                    if ($this->userModel->isEmailExists($email)) {
                        $fail++;
                        continue;
                    }

                    $data = [
                        'full_name' => trim($row[0]),
                        'email'     => $email,
                        'phone'     => isset($row[2]) ? trim($row[2]) : '',
                        
                        'password'  => !empty($row[3]) ? trim($row[3]) : '123456',
                        
                        'role'      => 2 // Cố định là Nhân viên
                    ];

                    // Gọi Model thêm vào DB
                    if ($this->userModel->createUser($data)) {
                        $count++;
                    }
                }
                
                header("Location: index.php?controller=user&action=index&role=2&msg=imported&count=$count&fail=$fail");
            } else {
                echo "Lỗi đọc file: " . SimpleXLSX::parseError();
            }
            exit();
        }
        
        header("Location: index.php?controller=user&action=index&role=2");
        exit();
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        $currentRole = isset($_GET['role']) ? $_GET['role'] : 0; 

        if ($id) {
            $result = $this->userModel->deleteUser($id);

            $redirectUrl = "index.php?controller=user&action=index&role=$currentRole";

            if ($result === 'has_active_orders') {
                echo "<script>
                    alert('CẢNH BÁO: Không thể xóa tài khoản này!\\nLý do: Đang có đơn hàng liên quan chưa hoàn thành.');
                    window.location.href = '$redirectUrl';
                </script>";
            } elseif ($result === true) {
                echo "<script>
                    alert('Đã xóa tài khoản thành công!');
                    window.location.href = '$redirectUrl';
                </script>";
            }
            else if ($result === 'has_imports') {
                echo "<script>
                    alert('CẢNH BÁO: Không thể xóa tài khoản này!\\nLý do: Tài khoản đã từng nhập hàng vào kho.');
                    window.location.href = '$redirectUrl';
                </script>";
            }
            else {
                echo "<script>
                    alert('Lỗi hệ thống: Không thể xóa vào lúc này.');
                    window.location.href = '$redirectUrl';
                </script>";
            }
        } else {
            header("Location: index.php?controller=user&action=index");
        }
        exit();
    }
}
?>