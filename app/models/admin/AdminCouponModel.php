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

    /**
     * CẬP NHẬT: Hàm getAll giờ nhận vào mảng $filters để lọc nhiều tiêu chí
     */
    public function getAll($filters = [])
    {
        $sql = "SELECT * FROM coupons WHERE 1=1";
        $params = [];

        // 1. Lọc theo Từ khóa (Code)
        if (!empty($filters['keyword'])) {
            $sql .= " AND code LIKE :keyword";
            $params[':keyword'] = "%" . $filters['keyword'] . "%";
        }

        // 2. Lọc theo Trạng thái (0 hoặc 1)
        if (isset($filters['status']) && $filters['status'] !== '') {
            $sql .= " AND status = :status";
            $params[':status'] = (int)$filters['status'];
        }

        // 3. Lọc theo Loại giảm giá
        if (!empty($filters['discount_type'])) {
            $sql .= " AND discount_type = :type";
            $params[':type'] = $filters['discount_type'];
        }

        // 4. Lọc theo Ngày bắt đầu (Từ ngày...)
        if (!empty($filters['date_from'])) {
            $sql .= " AND start_date >= :date_from";
            $params[':date_from'] = $filters['date_from'] . " 00:00:00";
        }

        // 5. Lọc theo Ngày kết thúc (Đến ngày...)
        if (!empty($filters['date_to'])) {
            $sql .= " AND end_date <= :date_to";
            $params[':date_to'] = $filters['date_to'] . " 23:59:59";
        }

        $sql .= " ORDER BY coupon_id DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * MỚI: Hàm kiểm tra mã trùng (Dùng cho Validate)
     */
    public function checkCodeExists($code, $id = null)
    {
        $sql = "SELECT COUNT(*) FROM coupons WHERE code = :code";
        $params = [':code' => $code];

        if ($id) {
            // Nếu đang sửa (update), loại trừ chính ID đó ra
            $sql .= " AND coupon_id != :id";
            $params[':id'] = $id;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM coupons WHERE coupon_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // --- LOGIC CŨ GIỮ NGUYÊN 100% ---
    public function save($data)
    {
        // 1. Lấy giá trị points_cost từ form, nếu không nhập thì mặc định là 0
        $points_cost = isset($data['points_cost']) && $data['points_cost'] !== '' ? (int)$data['points_cost'] : 0;

        if (isset($data['id']) && !empty($data['id'])) {
            // UPDATE: Thêm points_cost = :points_cost
            $sql = "UPDATE coupons SET 
                        code = :code, 
                        discount_type = :type, 
                        discount_value = :value, 
                        min_order_value = :min_val, 
                        usage_limit = :limit, 
                        start_date = :start, 
                        end_date = :end, 
                        status = :status,
                        points_cost = :points_cost 
                    WHERE coupon_id = :id";

            $params = [
                ':code' => strtoupper(trim($data['code'])),
                ':type' => $data['discount_type'],
                ':value' => $data['discount_value'],
                ':min_val' => $data['min_order_value'],
                ':limit' => $data['usage_limit'],
                ':start' => $data['start_date'],
                ':end' => $data['end_date'],
                ':status' => $data['status'],
                ':points_cost' => $points_cost, // Thêm tham số
                ':id' => $data['id']
            ];
        } else {
            // INSERT: Thêm cột points_cost
            $sql = "INSERT INTO coupons 
                    (code, discount_type, discount_value, min_order_value, usage_limit, start_date, end_date, status, points_cost) 
                    VALUES 
                    (:code, :type, :value, :min_val, :limit, :start, :end, :status, :points_cost)";

            $params = [
                ':code' => strtoupper(trim($data['code'])),
                ':type' => $data['discount_type'],
                ':value' => $data['discount_value'],
                ':min_val' => $data['min_order_value'],
                ':limit' => $data['usage_limit'],
                ':start' => $data['start_date'],
                ':end' => $data['end_date'],
                ':status' => $data['status'],
                ':points_cost' => $points_cost // Thêm tham số
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
        $sql = "UPDATE coupons SET status = 0 WHERE end_date IS NOT NULL AND end_date < NOW() AND status = 1";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute();
    }
}
