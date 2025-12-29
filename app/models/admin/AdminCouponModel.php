<?php
require_once 'app/config/database.php';

class AdminCouponModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getAll($search = null)
    {
        $sql = "SELECT * FROM coupons WHERE 1=1";
        $params = [];
        if ($search) {
            $sql .= " AND code LIKE :search";
            $params[':search'] = "%$search%";
        }
        $sql .= " ORDER BY coupon_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM coupons WHERE coupon_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function save($data)
    {
        if (isset($data['id']) && !empty($data['id'])) {
            $sql = "UPDATE coupons SET code = :code, discount_type = :type, discount_value = :value, 
                    min_order_value = :min_val, usage_limit = :limit, start_date = :start, 
                    end_date = :end, status = :status WHERE coupon_id = :id";
            $params = [
                ':code' => strtoupper(trim($data['code'])),
                ':type' => $data['discount_type'],
                ':value' => $data['discount_value'],
                ':min_val' => $data['min_order_value'],
                ':limit' => $data['usage_limit'],
                ':start' => $data['start_date'],
                ':end' => $data['end_date'],
                ':status' => $data['status'],
                ':id' => $data['id']
            ];
        } else {
            $sql = "INSERT INTO coupons (code, discount_type, discount_value, min_order_value, usage_limit, start_date, end_date, status) 
                    VALUES (:code, :type, :value, :min_val, :limit, :start, :end, :status)";
            $params = [
                ':code' => strtoupper(trim($data['code'])),
                ':type' => $data['discount_type'],
                ':value' => $data['discount_value'],
                ':min_val' => $data['min_order_value'],
                ':limit' => $data['usage_limit'],
                ':start' => $data['start_date'],
                ':end' => $data['end_date'],
                ':status' => $data['status']
            ];
        }
        return $this->conn->prepare($sql)->execute($params);
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM coupons WHERE coupon_id = ?");
        return $stmt->execute([$id]);
    }

    public function autoExpireCoupons()
    {
        $sql = "
        UPDATE coupons
        SET status = 0
        WHERE end_date IS NOT NULL
          AND end_date < NOW()
          AND status = 1
    ";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute();
    }
}
