<?php
class OrderModel {
    private $db;

    public function __construct() {
        // Khởi tạo kết nối giống các Model khác của bạn
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Lấy danh sách đơn hàng của một người dùng
    public function getOrdersByUser($user_id, $status = null) {
        $sql = "SELECT * FROM orders WHERE user_id = ?";
        $params = [$user_id];
        if ($status !== null) {
        $sql .= " AND status = ?";
        $params[] = $status;
        }
        $sql .= " ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy thông tin tổng quát của 1 đơn hàng
    public function getOrderById($id) {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE order_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy chi tiết các sản phẩm trong đơn hàng (JOIN với bảng products để lấy ảnh và tên)
    public function getOrderItems($order_id) {
        $sql = "SELECT od.*, p.name, p.image
                FROM order_details od
                JOIN products p ON od.product_id = p.product_id
                WHERE od.order_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$order_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Logic tạo đơn hàng mới (Đã sửa theo cấu trúc bảng của bạn)
    // public function createOrder($user_id, $data, $cart_items) {
    //     try {
    //         $this->db->beginTransaction();

    //         // 1. Lưu vào bảng orders
    //         $sqlOrder = "INSERT INTO orders (user_id, customer_name, customer_phone, shipping_address, note, total_money, final_money, status, created_at) 
    //                      VALUES (:uid, :name, :phone, :address, :note, :total, :final, 0, NOW())";
    //         $stmtOrder = $this->db->prepare($sqlOrder);
    //         $stmtOrder->execute([
    //             ':uid'      => $user_id,
    //             ':name'     => $data['customer_name'],
    //             ':phone'    => $data['customer_phone'],
    //             ':address'  => $data['shipping_address'],
    //             ':note'     => $data['note'],
    //             ':total'    => $data['total_money'],
    //             ':final'    => $data['total_money']
    //         ]);
            
    //         $order_id = $this->db->lastInsertId();

    //         // 2. Lưu vào bảng order_details và TRỪ KHO
    //         foreach ($cart_items as $item) {
    //             $total_price = $item['price'] * $item['quantity'];
                
    //             // Lưu chi tiết
    //             $sqlDetail = "INSERT INTO order_details (order_id, product_id, price, quantity, total_price) 
    //                           VALUES (:oid, :pid, :price, :qty, :tprice)";
    //             $stmtDetail = $this->db->prepare($sqlDetail);
    //             $stmtDetail->execute([
    //                 ':oid'    => $order_id,
    //                 ':pid'    => $item['product_id'],
    //                 ':price'  => $item['price'],
    //                 ':qty'    => $item['quantity'],
    //                 ':tprice' => $total_price
    //             ]);

    //             // Trừ kho (Bảng inventory)
    //             $sqlInv = "UPDATE inventory SET quantity = quantity - ? WHERE product_id = ?";
    //             $this->db->prepare($sqlInv)->execute([$item['quantity'], $item['product_id']]);
    //         }

    //         // 3. Xóa giỏ hàng database
    //         $sqlClear = "DELETE FROM carts WHERE user_id = ?";
    //         $this->db->prepare($sqlClear)->execute([$user_id]);

    //         $this->db->commit();
    //         return $order_id;
    //     } catch (Exception $e) {
    //         $this->db->rollBack();
    //         return false;
    //     }
    // }

    public function createOrder($user_id, $data, $cart_items) {
    try {
        $this->db->beginTransaction();

        // =================================================================
        // 1. SỬA: Cập nhật câu SQL để lưu thêm Discount, Coupon, Final Money
        // =================================================================
        $sqlOrder = "INSERT INTO orders (
                        user_id, 
                        customer_name, 
                        customer_phone, 
                        shipping_address, 
                        note, 
                        total_money, 
                        discount_amount,  /* Mới */
                        final_money,      /* Mới */
                        coupon_code,      /* Mới */
                        payment_method,   /* Mới (Nếu bảng DB có cột này) */
                        status, 
                        created_at
                    ) VALUES (
                        :uid, :name, :phone, :address, :note, 
                        :total, :discount, :final, :code, :method, 
                        0, NOW()
                    )";
        
        $stmtOrder = $this->db->prepare($sqlOrder);
        $stmtOrder->execute([
            ':uid'      => $user_id,
            ':name'     => $data['customer_name'],
            ':phone'    => $data['customer_phone'],
            ':address'  => $data['shipping_address'],
            ':note'     => $data['note'],
            ':total'    => $data['total_money'],     // Tổng tiền gốc
            ':discount' => $data['discount_amount'], // Tiền giảm (Lấy từ Controller gửi sang)
            ':final'    => $data['final_money'],     // Tiền khách phải trả (Controller đã tính)
            ':code'     => $data['coupon_code'],     // Mã voucher
            ':method'   => $data['payment_method'] ?? 'COD' // Mặc định COD nếu không có
        ]);
        
        $order_id = $this->db->lastInsertId();

        // =================================================================
        // 2. GIỮ NGUYÊN: Logic lưu chi tiết và TRỪ KHO
        // =================================================================
        foreach ($cart_items as $item) {
            // An toàn: Làm sạch giá tiền trước khi nhân (tránh lỗi 3.200.000 * số lượng)
            $price_clean = preg_replace('/[^0-9]/', '', $item['price']);
            $total_price = $price_clean * $item['quantity'];
            
            // Lưu chi tiết (Giữ nguyên logic của bạn)
            $sqlDetail = "INSERT INTO order_details (order_id, product_id, price, quantity, total_price) 
                          VALUES (:oid, :pid, :price, :qty, :tprice)";
            $stmtDetail = $this->db->prepare($sqlDetail);
            $stmtDetail->execute([
                ':oid'    => $order_id,
                ':pid'    => $item['product_id'],
                ':price'  => $price_clean, // Lưu giá sạch
                ':qty'    => $item['quantity'],
                ':tprice' => $total_price
            ]);

            // Trừ kho (Giữ nguyên logic của bạn)
            $sqlInv = "UPDATE inventory SET quantity = quantity - ? WHERE product_id = ?";
            $this->db->prepare($sqlInv)->execute([$item['quantity'], $item['product_id']]);
        }

        // =================================================================
        // 3. GIỮ NGUYÊN: Xóa giỏ hàng database
        // =================================================================
        $sqlClear = "DELETE FROM carts WHERE user_id = ?";
        $this->db->prepare($sqlClear)->execute([$user_id]);

        $this->db->commit();
        return $order_id;

    } catch (Exception $e) {
        $this->db->rollBack();
        // Ghi log lỗi để debug nếu cần
        // error_log($e->getMessage()); 
        return false;
    }
}

    // Hủy đơn (Trạng thái 4) + Hoàn lại số lượng vào kho
    public function cancelOrder($order_id) {
        // 1. Lấy thông tin đơn hàng để xem nó dùng mã gì
        $sqlGet = "SELECT coupon_code FROM orders WHERE order_id = :oid";
        $stmtGet = $this->db->prepare($sqlGet);
        $stmtGet->execute([':oid' => $order_id]);
        $order = $stmtGet->fetch(PDO::FETCH_ASSOC);

        // 2. Cập nhật trạng thái đơn hàng thành Hủy (Ví dụ -1)
        $sqlUpdate = "UPDATE orders SET status = 4 WHERE order_id = :oid";
        $this->db->prepare($sqlUpdate)->execute([':oid' => $order_id]);

        // 3. LOGIC HOÀN MÃ: Nếu đơn có dùng mã, hãy cộng lại lượt dùng
        if (!empty($order['coupon_code'])) {
            // Cộng lại 1 lượt dùng vào bảng coupons
            // usage_limit: Số lượng mã được phép dùng
            // used_count: Số lượng đã dùng thực tế (nếu bạn có cột này)
            
            // Cách 1: Nếu bạn trừ trực tiếp vào usage_limit lúc đặt hàng
            $sqlRestore = "UPDATE coupons SET usage_limit = usage_limit + 1 WHERE code = :code";
            
            // Cách 2: Nếu bạn dùng cột used_count để đếm
            // $sqlRestore = "UPDATE coupons SET used_count = used_count - 1 WHERE code = :code";

            $this->db->prepare($sqlRestore)->execute([':code' => $order['coupon_code']]);
        }
    }
}