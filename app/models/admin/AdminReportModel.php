<?php
// 1. Gọi file cấu hình Database (giống các model khác của bạn)
require_once 'app/config/database.php';

class AdminReportModel
{
    private $conn;

    // 2. Tự kết nối Database trong hàm khởi tạo
    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // 3. Các hàm xử lý báo cáo

    // Lấy tổng doanh thu (đơn hàng thành công)
    public function getRevenue($fromDate, $toDate)
    {
        $sql = "SELECT SUM(final_money) as total 
                FROM orders 
                WHERE status = 3 
                AND created_at BETWEEN :from AND :to";
        $stmt = $this->conn->prepare($sql);
        // Lưu ý: format ngày tháng để thêm giờ phút giây cho chính xác
        $stmt->execute([
            ':from' => $fromDate . ' 00:00:00', 
            ':to' => $toDate . ' 23:59:59'
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }

    // Lấy tổng tiền nhập hàng (dựa trên bảng imports)
    public function getImportCost($fromDate, $toDate)
    {
        try {
            // Thay vì SELECT từ bảng imports (đang bị bằng 0), ta JOIN sang bảng import_details
            // i: đại diện bảng imports (để lấy ngày tháng)
            // d: đại diện bảng import_details (để lấy tiền)
            $sql = "SELECT SUM(d.total_price) as total
                    FROM imports i
                    JOIN import_details d ON i.import_id = d.import_id
                    WHERE i.created_at BETWEEN :from AND :to";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':from' => $fromDate . ' 00:00:00', 
                ':to'   => $toDate . ' 23:59:59'
            ]);
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Nếu không có dữ liệu thì trả về 0
            return $row['total'] ?? 0;

        } catch (Exception $e) {
            return 0; 
        }
    }

    // Lấy giá vốn hàng bán (COGS)
    public function getCOGS($fromDate, $toDate)
    {
        // Cần đảm bảo bảng products đã có cột import_price
        try {
            $sql = "SELECT SUM(od.quantity * p.import_price) as total_cogs
                    FROM order_details od
                    JOIN orders o ON od.order_id = o.order_id
                    JOIN products p ON od.product_id = p.product_id
                    
                    WHERE o.status = 3 
                    AND o.created_at BETWEEN :from AND :to";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':from' => $fromDate . ' 00:00:00', 
                ':to' => $toDate . ' 23:59:59'
            ]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total_cogs'] ?? 0;
        } catch (Exception $e) {
            return 0; // Trả về 0 nếu chưa có cột giá vốn
        }
    }

    // Lấy dữ liệu biểu đồ
    public function getChartData($fromDate, $toDate) {
        // Dùng BETWEEN để lấy dữ liệu trong khoảng user chọn
        $sql = "SELECT DATE(created_at) as date, SUM(final_money) as revenue
                FROM orders
                WHERE status = 3
                AND created_at >= :from AND created_at <= :to 
                GROUP BY DATE(created_at)
                ORDER BY date ASC";
                
        $stmt = $this->conn->prepare($sql);
        
        // Thêm giờ phút giây để bao trọn cả ngày cuối cùng
        $stmt->execute([
            ':from' => $fromDate . ' 00:00:00',
            ':to'   => $toDate . ' 23:59:59'
        ]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}