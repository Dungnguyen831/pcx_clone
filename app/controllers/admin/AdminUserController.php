<?php
require_once 'app/models/client/UserModel.php';

class UserController {
    
    // Hiển thị danh sách
    public function index() {
        // Kiểm tra admin (Có thể viết hàm check chung để tái sử dụng)
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        $userModel = new UserModel();
        $customers = $userModel->getAllCustomers();

        $page_title = "Quản lý khách hàng";
        $controller = 'user'; // Để active menu sidebar
        
        $content_view = 'views/admin/user/index.php';
        require_once 'views/admin/layouts/page.php';
    }

    /** XUẤT EXCEL DANH SÁCH KHÁCH HÀNG */
    public function exportExcel() 
    {
        // Check admin
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
            exit();
        }

        $userModel = new UserModel();
        $customers = $userModel->getAllCustomers();

        $filename = "DanhSachKhachHang_" . date('d-m-Y') . ".xls";

        // CSS cho Excel
        $style = "
        <style>
            .excel-table {
                font-family: Arial, sans-serif;
                border-collapse: collapse;
                width: 100%;
            }
            .excel-table th {
                background-color: #3498db;
                color: #fff;
                border: 0.5pt solid #000;
                text-align: center;
                font-weight: bold;
                height: 35px;
            }
            .excel-table td {
                border: 0.5pt solid #000;
                padding: 8px;
                font-size: 11pt;
            }
            .text-center { text-align: center; }
            .phone-format { mso-number-format:'\@'; }
            .title-doc {
                font-size: 16pt;
                font-weight: bold;
                text-align: center;
                margin-bottom: 10px;
            }
        </style>";

        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");

        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office"
                xmlns:x="urn:schemas-microsoft-com:office:excel"
                xmlns="http://www.w3.org/TR/REC-html40">';
        echo '<head><meta charset="UTF-8">' . $style . '</head><body>';

        echo '<div class="title-doc">DANH SÁCH KHÁCH HÀNG</div>';
        echo '<div style="text-align:center;margin-bottom:15px;">
                Ngày xuất: ' . date('d/m/Y H:i') . '
            </div>';

        echo '<table class="excel-table">
                <tr>
                    <th width="50">ID</th>
                    <th width="200">Tên khách hàng</th>
                    <th width="150">Email</th>
                    <th width="120">Số điện thoại</th>
                    <th width="150">Ngày tạo</th>
                </tr>';

        if (!empty($customers)) {
            foreach ($customers as $c) {
                echo '<tr>
                    <td class="text-center">' . $c['user_id'] . '</td>
                    <td>' . htmlspecialchars($c['full_name']) . '</td>
                    <td>' . htmlspecialchars($c['email']) . '</td>
                    <td class="text-center phone-format">' . $c['phone'] . '</td>
                    <td class="text-center">' . date('d/m/Y H:i', strtotime($c['created_at'])) . '</td>
                </tr>';
            }
        } else {
            echo '<tr><td colspan="5" style="text-align:center;">Không có dữ liệu</td></tr>';
        }

        echo '</table></body></html>';
        exit;
    }


    // Xóa khách hàng
    public function delete() {
        $id = $_GET['id'] ?? null;

        if ($id) {
            $userModel = new UserModel();
            $result = $userModel->deleteUser($id);

            if ($result === 'has_active_orders') {
                // Trường hợp bị chặn do còn đơn hàng
                $msg = 'Không thể xóa! Người dùng này đang có đơn hàng chưa hoàn thành.';
                echo $msg;
            } elseif ($result === true) {
                echo "Xóa thành công";
            } else {
                echo "Xóa thất bại";
            }
        } else {
            header("Location: index.php?controller=admin-user&action=index");
        }
        exit();
    }
}
?>