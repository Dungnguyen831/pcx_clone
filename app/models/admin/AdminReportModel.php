<?php
require_once 'app/config/database.php';

class AdminReportModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getRevenue($fromDate, $toDate)
    {
        $sql = "SELECT SUM(final_money) as total 
                FROM orders 
                WHERE status = 3 
                AND created_at BETWEEN :from AND :to";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':from' => $fromDate . ' 00:00:00', 
            ':to' => $toDate . ' 23:59:59'
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }

    public function getImportCost($fromDate, $toDate)
    {
        try {
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
            
            return $row['total'] ?? 0;

        } catch (Exception $e) {
            return 0; 
        }
    }

    // Lấy giá vốn hàng bán (COGS)
    public function getCOGS($fromDate, $toDate)
    {
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

    public function getChartData($fromDate, $toDate) {
        // Dùng BETWEEN để lấy dữ liệu trong khoảng user chọn
        $sql = "SELECT DATE(created_at) as date, SUM(final_money) as revenue
                FROM orders
                WHERE status = 3
                AND created_at >= :from AND created_at <= :to 
                GROUP BY DATE(created_at)
                ORDER BY date ASC";
                
        $stmt = $this->conn->prepare($sql);
        
        $stmt->execute([
            ':from' => $fromDate . ' 00:00:00',
            ':to'   => $toDate . ' 23:59:59'
        ]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}