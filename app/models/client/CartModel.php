<?php
class CartModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function addToCart($user_id, $product_id, $quantity) {
        // Kiểm tra xem sản phẩm đã có trong giỏ hàng của user này chưa
        $sql = "SELECT cart_id, quantity FROM carts WHERE user_id = :user_id AND product_id = :product_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $user_id, ':product_id' => $product_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Nếu có rồi thì cập nhật số lượng
            $new_qty = $row['quantity'] + $quantity;
            $update_sql = "UPDATE carts SET quantity = :qty WHERE cart_id = :cart_id";
            $update_stmt = $this->conn->prepare($update_sql);
            return $update_stmt->execute([':qty' => $new_qty, ':cart_id' => $row['cart_id']]);
        } else {
            // Nếu chưa có thì chèn mới
            $insert_sql = "INSERT INTO carts (user_id, product_id, quantity) VALUES (:user_id, :product_id, :qty)";
            $insert_stmt = $this->conn->prepare($insert_sql);
            return $insert_stmt->execute([':user_id' => $user_id, ':product_id' => $product_id, ':qty' => $quantity]);
        }
    }

    public function getCartByUser($user_id) {
        if ($this->conn === null) {
            return [];
        }
    
        // Câu lệnh SQL JOIN để lấy stock_quantity từ bảng inventory
        $sql = "SELECT c.*, p.name, p.image, p.price, i.quantity as stock_quantity 
                FROM carts c
                JOIN products p ON c.product_id = p.product_id
                JOIN inventory i ON c.product_id = i.product_id
                WHERE c.user_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        
        // SỬA LỖI: Thay $stmt.execute thành $stmt->execute
        $stmt->execute([$user_id]); 
        
        // Tương tự, thay $stmt.fetchAll thành $stmt->fetchAll
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateQuantity($user_id, $product_id, $quantity) {
        $sql = "UPDATE carts SET quantity = :qty WHERE user_id = :user_id AND product_id = :product_id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':qty' => $quantity, ':user_id' => $user_id, ':product_id' => $product_id]);
    }

    public function removeFromCart($user_id, $product_id) {
        $sql = "DELETE FROM carts WHERE user_id = :user_id AND product_id = :product_id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':user_id' => $user_id, ':product_id' => $product_id]);
    }
   

    public function getCartCount($user_id) {
        // Sử dụng COUNT để đếm số lượng mã sản phẩm khác nhau
        $sql = "SELECT COUNT(product_id) as total FROM carts WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['total'] ?? 0; 
    }
}