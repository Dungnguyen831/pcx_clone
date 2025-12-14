<?php
    require_once __DIR__ . '/../../Models/UserModel.php';
    class UserController {
        private $userModel;

        public function __construct() {
            $this->userModel = new UserModel();
        }

        public function index() {
            $user = $this->userModel->getAll();  
            require_once __DIR__ . '/../../views/User/index.php';
        }
    }

    if (isset($_GET['action'])) {
    $controller = new UserController();
    $action = $_GET['action'];

    if (method_exists($controller, $action)) {
        $controller->$action();  // gọi hàm index(), show(), etc.
    } else {
        echo "Action không tồn tại!";
    }
}

    // $userConn = new UserController();
    // $userConn->index();
?>
