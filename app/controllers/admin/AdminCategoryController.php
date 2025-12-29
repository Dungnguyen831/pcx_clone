<?php
require_once 'app/models/admin/AdminCategoryModel.php';

class AdminCategoryController
{
    private $model;

    public function __construct()
    {
        $this->model = new AdminCategoryModel();
    }

    public function index()
    {
        $search = $_GET['search'] ?? null;
        $categories = $this->model->getAll($search);

        // Dữ liệu cho Sidebar
        $controller = 'category';
        $page_title = "Quản lý danh mục";
        $content_view = 'views/admin/category/index.php';
        require_once 'views/admin/layouts/page.php';
    }

    public function edit()
    {
        $id = $_GET['id'];
        $category_edit = $this->model->getById($id);

        $search = $_GET['search'] ?? null;
        $categories = $this->model->getAll($search);

        $controller = 'category';
        $page_title = "Sửa danh mục";
        $content_view = 'views/admin/category/index.php';
        require_once 'views/admin/layouts/page.php';
    }

    public function store()
    {
        $this->model->save($_POST);
        header("Location: index.php?controller=admin-category&msg=success");
    }

    public function update()
    {
        $this->model->save($_POST);
        header("Location: index.php?controller=admin-category&msg=updated");
    }

    public function delete()
    {
        $id = $_GET['id'];
        if ($this->model->delete($id)) {
            header("Location: index.php?controller=admin-category&msg=deleted");
        } else {
            header("Location: index.php?controller=admin-category&msg=error");
        }
    }
}
