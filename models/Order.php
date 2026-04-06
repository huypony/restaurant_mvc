<?php
require_once 'commons/function.php';

class Order {
    
    public function all() {
        $conn = connectDB();
        $sql = "SELECT o.*, r.customer_name, r.guest_count, r.reservation_time 
                FROM orders o 
                LEFT JOIN reservations r ON o.reservation_id = r.id 
                ORDER BY o.created_at DESC";
        return $conn->query($sql)->fetchAll();
    }
    
    public function findById($id) {
        $conn = connectDB();
        $sql = "SELECT * FROM orders WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        $conn = connectDB();
        $sql = "INSERT INTO orders(reservation_id, user_id, total_price, status) 
                VALUES(:reservation_id, :user_id, :total_price, :status)";
        $stmt = $conn->prepare($sql);
        
        if($stmt->execute($data)) {
            return $conn->lastInsertId();
        }
        return false;
    }
    
    public function getLastInsertId() {
        $conn = connectDB();
        return $conn->lastInsertId();
    }
    
    public function update($id, $data) {
        $conn = connectDB();
        $sql = "UPDATE orders SET status = :status, total_price = :total_price WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $data[':id'] = $id;
        return $stmt->execute($data);
    }
    
    public function getByUser($user_id) {
        $conn = connectDB();
        $sql = "SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchAll();
    }
    
    public function getByReservation($reservation_id) {
        $conn = connectDB();
        $sql = "SELECT * FROM orders WHERE reservation_id = :reservation_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':reservation_id' => $reservation_id]);
        return $stmt->fetch();
    }

    public function getPendingByReservation($reservation_id) {
        $conn = connectDB();
        $sql = "SELECT * FROM orders WHERE reservation_id = :reservation_id AND status = 'pending' LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':reservation_id' => $reservation_id]);
        return $stmt->fetch();
    }

    // Lấy doanh thu toàn bộ (chỉ completed orders)
    public function getTotalRevenue($from_date = null, $to_date = null) {
        $conn = connectDB();
        $sql = "SELECT SUM(total_price) as total_revenue FROM orders WHERE status = 'completed'";
        
        $params = [];
        if($from_date) {
            $sql .= " AND DATE(created_at) >= :from_date";
            $params[':from_date'] = $from_date;
        }
        if($to_date) {
            $sql .= " AND DATE(created_at) <= :to_date";
            $params[':to_date'] = $to_date;
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['total_revenue'] ?? 0;
    }

    // Lấy doanh thu theo ngày
    public function getRevenueByDate($from_date = null, $to_date = null) {
        $conn = connectDB();
        $sql = "SELECT DATE(created_at) as revenue_date, COUNT(*) as order_count, SUM(total_price) as daily_revenue 
                FROM orders WHERE status = 'completed'";
        
        $params = [];
        if($from_date) {
            $sql .= " AND DATE(created_at) >= :from_date";
            $params[':from_date'] = $from_date;
        }
        if($to_date) {
            $sql .= " AND DATE(created_at) <= :to_date";
            $params[':to_date'] = $to_date;
        }

        $sql .= " GROUP BY DATE(created_at) ORDER BY revenue_date DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Lấy doanh thu theo tháng
    public function getRevenueByMonth($year = null) {
        $conn = connectDB();
        $currentYear = $year ?? date('Y');
        $sql = "SELECT MONTH(created_at) as month, YEAR(created_at) as year, 
                COUNT(*) as order_count, SUM(total_price) as monthly_revenue 
                FROM orders WHERE status = 'completed' AND YEAR(created_at) = :year
                GROUP BY YEAR(created_at), MONTH(created_at)
                ORDER BY month DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([':year' => $currentYear]);
        return $stmt->fetchAll();
    }

    // Lấy tất cả completed orders với thông tin liên quan
    public function getCompletedOrders($from_date = null, $to_date = null) {
        $conn = connectDB();
        $sql = "SELECT o.*, r.customer_name, r.guest_count, r.reservation_time 
                FROM orders o 
                LEFT JOIN reservations r ON o.reservation_id = r.id 
                WHERE o.status = 'completed'";
        
        $params = [];
        if($from_date) {
            $sql .= " AND DATE(o.created_at) >= :from_date";
            $params[':from_date'] = $from_date;
        }
        if($to_date) {
            $sql .= " AND DATE(o.created_at) <= :to_date";
            $params[':to_date'] = $to_date;
        }

        $sql .= " ORDER BY o.created_at DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Thống kê doanh thu theo trạng thái
    public function getRevenueStatistics($from_date = null, $to_date = null) {
        $conn = connectDB();
        $sql = "SELECT status, COUNT(*) as order_count, SUM(total_price) as status_revenue 
                FROM orders WHERE 1=1";
        
        $params = [];
        if($from_date) {
            $sql .= " AND DATE(created_at) >= :from_date";
            $params[':from_date'] = $from_date;
        }
        if($to_date) {
            $sql .= " AND DATE(created_at) <= :to_date";
            $params[':to_date'] = $to_date;
        }

        $sql .= " GROUP BY status ORDER BY order_count DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
