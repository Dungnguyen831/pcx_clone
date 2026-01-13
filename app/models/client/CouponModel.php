<?php
class CouponModel
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Tạo mã mới (cho tính năng đổi điểm)
    public function createRedeemCoupon($code, $value)
    {
        // Tạo mã dùng 1 lần, hạn 30 ngày
        $sql = "INSERT INTO coupons (code, discount_type, discount_value, min_order_value, usage_limit, used_count, start_date, end_date, status) 
                VALUES (:code, 'fixed', :val, 0, 1, 0, NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), 1)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':code' => $code, ':val'  => $value]);
    }

    // Kiểm tra mã hợp lệ (cho tính năng Checkout)
    public function checkCoupon($code, $order_total)
    {
        $sql = "SELECT * FROM coupons WHERE code = ? AND status = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$code]);
        $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$coupon) return false;

        // Check hạn dùng
        $now = date('Y-m-d H:i:s');
        if ($coupon['start_date'] > $now || $coupon['end_date'] < $now) return false;

        // Check số lượng dùng
        if ($coupon['used_count'] >= $coupon['usage_limit']) return false;

        // Check đơn tối thiểu
        if ($order_total < $coupon['min_order_value']) return false;

        return $coupon;
    }
}
