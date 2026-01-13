<?php
class WarehouseModel {
    private $db;

    public function __construct() {
        // Kết nối cơ sở dữ liệu
        $this->db = new Database(); 
    }

    // 1. Lấy TỔNG MẶT HÀNG (Hiển thị ở ô đầu tiên)
    public function getTotalProducts() {
        $sql = "SELECT COUNT(*) as total FROM products";
        $result = $this->db->fetchAll($sql);
        return $result[0]['total'] ?? 0;
    }

    // 2. Lấy TỔNG TỒN KHO (Hiển thị ở ô thứ hai)
    public function getTotalStockQuantity() {
        $sql = "SELECT SUM(quantity) as total FROM inventory";
        $result = $this->db->fetchAll($sql);
        return $result[0]['total'] ?? 0;
    }

    // 3. Lấy số sản phẩm CẦN NHẬP THÊM (Sửa lỗi biến $low_count)
    public function getLowStockCount($threshold = 10) {
        $sql = "SELECT COUNT(*) as total FROM inventory WHERE quantity < $threshold";
        $result = $this->db->fetchAll($sql);
        return $result[0]['total'] ?? 0;
    }

    // Bổ sung: Lấy hoạt động gần đây để bảng không bị trống
    public function getRecentActivities($limit = 5) {
        $sql = "SELECT i.created_at, p.name, id.quantity 
                FROM imports i
                JOIN import_details id ON i.import_id = id.import_id
                JOIN products p ON id.product_id = p.product_id
                ORDER BY i.created_at DESC LIMIT $limit";
        return $this->db->fetchAll($sql);
    }
  public function getHistory($searchTerm = null) {
    $sql = "SELECT i.created_at, p.name as product_name, id.quantity, id.import_price, id.total_price, i.note 
            FROM imports i
            JOIN import_details id ON i.import_id = id.import_id
            JOIN products p ON id.product_id = p.product_id";
    
    if ($searchTerm) {
        // Dùng trim() để xóa dấu cách thừa ở 2 đầu từ khóa người dùng gõ
        $safeSearch = addslashes(trim($searchTerm)); 
        // Đảm bảo KHÔNG có dấu cách sau dấu % đầu tiên
        $sql .= " WHERE p.name LIKE '%$safeSearch%'"; 
    }
    

    $sql .= " ORDER BY i.created_at DESC";
    return $this->db->fetchAll($sql);
}
}