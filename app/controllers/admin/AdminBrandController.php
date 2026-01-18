<?php
require_once 'app/models/admin/AdminBrandModel.php';

class AdminBrandController
{
    private $model;

    public function __construct()
    {
        $this->model = new AdminBrandModel();
    }

    public function index()
    {
        $search = $_GET['search'] ?? null;
        $brands = $this->model->getAll($search);

        $controller = 'brand';
        $page_title = "Quản lý hãng sản xuất";
        $content_view = 'views/admin/brand/index.php';
        require_once 'views/admin/layouts/page.php';
    }

    public function edit()
    {
        $id = $_GET['id'];
        $brand_edit = $this->model->getById($id);

        $search = $_GET['search'] ?? null;
        $brands = $this->model->getAll($search);

        $controller = 'brand';
        $page_title = "Sửa thông tin hãng";
        $content_view = 'views/admin/brand/index.php';
        require_once 'views/admin/layouts/page.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = $_POST;
            $data['logo_url'] = ''; // Mặc định rỗng

            // Xử lý Upload Ảnh
            if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] == 0) {
                $fileName = $this->handleUpload($_FILES['logo_file']);
                if ($fileName) {
                    $data['logo_url'] = $fileName;
                }
            }

            $this->model->save($data);
            header("Location: index.php?controller=admin-brand&msg=success");
        }
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = $_POST;
            // Mặc định lấy lại tên ảnh cũ từ hidden input
            $data['logo_url'] = $_POST['old_logo'] ?? '';

            // Nếu người dùng chọn ảnh mới
            if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] == 0) {
                $fileName = $this->handleUpload($_FILES['logo_file']);
                if ($fileName) {
                    $data['logo_url'] = $fileName;
                    // Tùy chọn: Xóa file cũ trong thư mục để tiết kiệm dung lượng
                    if (!empty($_POST['old_logo'])) {
                        $oldPath = 'assets/uploads/brands/' . $_POST['old_logo'];
                        if (file_exists($oldPath)) unlink($oldPath);
                    }
                }
            }

            $this->model->save($data);
            header("Location: index.php?controller=admin-brand&msg=updated");
        }
    }

    // Hàm phụ trợ xử lý di chuyển file vào thư mục dự án
    private function handleUpload($file)
    {
        $targetDir = "assets/uploads/brands/";

        // Tạo thư mục nếu chưa tồn tại
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // Tạo tên file duy nhất để không bị trùng
        $fileName = time() . '_' . basename($file["name"]);
        $targetFilePath = $targetDir . $fileName;

        // Di chuyển file từ bộ nhớ tạm vào thư mục dự án
        if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
            return $fileName;
        }
        return false;
    }

    public function delete()
    {
        $id = $_GET['id'];

        // Lấy thông tin để xóa file ảnh vật lý trước khi xóa record trong DB
        $brand = $this->model->getById($id);

        if ($this->model->delete($id)) {
            if ($brand && !empty($brand['logo_url'])) {
                $filePath = 'assets/uploads/brands/' . $brand['logo_url'];
                if (file_exists($filePath)) unlink($filePath);
            }
            header("Location: index.php?controller=admin-brand&msg=deleted");
        } else {
            header("Location: index.php?controller=admin-brand&msg=error");
        }
    }
}
