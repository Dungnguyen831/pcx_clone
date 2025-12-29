<?php
require_once 'app/config/database.php';

class CartModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // 1. Thêm sản phẩm vào giỏ
    public function addToCart($user_id, $product_id, $quantity = 1) {
        // Kiểm tra xem sản phẩm này đã có trong giỏ của user chưa
        $sqlCheck = "SELECT * FROM carts WHERE user_id = :uid AND product_id = :pid";
        $stmtCheck = $this->conn->prepare($sqlCheck);
        $stmtCheck->execute([':uid' => $user_id, ':pid' => $product_id]);
        $item = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            // A. Nếu có rồi -> Cộng dồn số lượng
            $sqlUpdate = "UPDATE carts SET quantity = quantity + :qty WHERE user_id = :uid AND product_id = :pid";
            $stmtUpdate = $this->conn->prepare($sqlUpdate);
            return $stmtUpdate->execute([':qty' => $quantity, ':uid' => $user_id, ':pid' => $product_id]);
        } else {
            // B. Nếu chưa có -> Thêm mới
            $sqlInsert = "INSERT INTO carts (user_id, product_id, quantity) VALUES (:uid, :pid, :qty)";
            $stmtInsert = $this->conn->prepare($sqlInsert);
            return $stmtInsert->execute([':uid' => $user_id, ':pid' => $product_id, ':qty' => $quantity]);
        }
    }

    // 2. Lấy danh sách giỏ hàng (Kèm thông tin sản phẩm)
    public function getCartItems($user_id) {
        // JOIN bảng carts với products để lấy tên, giá, ảnh
        $sql = "SELECT c.cart_id, c.quantity, p.product_id, p.name, p.price, p.image 
                FROM carts c
                JOIN products p ON c.product_id = p.product_id
                WHERE c.user_id = :uid";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':uid' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. Xóa 1 món khỏi giỏ
    public function  removeFromCart($cart_id, $user_id) {
        // Cần check thêm user_id để đảm bảo không xóa nhầm giỏ của người khác
        $sql = "DELETE FROM carts WHERE cart_id = :cid AND user_id = :uid";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':cid' => $cart_id, ':uid' => $user_id]);
    }
    
    // 4. Cập nhật số lượng (Dùng khi khách sửa số lượng trong giỏ)
    public function updateQuantity($cart_id, $quantity, $user_id) {
        if ($quantity <= 0) {
            return $this->removeFromCart($cart_id, $user_id);
        }
        $sql = "UPDATE carts SET quantity = :qty WHERE cart_id = :cid AND user_id = :uid";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':qty' => $quantity, ':cid' => $cart_id, ':uid' => $user_id]);
    }
}
?>