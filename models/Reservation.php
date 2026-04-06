
<?php
require_once 'commons/function.php';

class Reservation {
    
    public function create($data) {
        $conn = connectDB();
        $sql = "INSERT INTO reservations(user_id, customer_name, customer_phone, reservation_time, guest_count, status)
                VALUES(:user_id, :customer_name, :customer_phone, :reservation_time, :guest_count, :status)";
        $stmt = $conn->prepare($sql);
        
        // Ensure all required keys are present
        if(!isset($data[':user_id']) || !isset($data[':customer_name']) || !isset($data[':customer_phone']) ||
           !isset($data[':reservation_time']) || !isset($data[':guest_count']) || !isset($data[':status'])) {
            return false;
        }
        
        return $stmt->execute($data) ? $conn->lastInsertId() : false;
    }

    public function getByUser($user_id) {
        $conn = connectDB();
        $sql = "SELECT * FROM reservations WHERE user_id = :id ORDER BY reservation_time DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $user_id]);
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $conn = connectDB();
        $sql = "SELECT * FROM reservations WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function all() {
        $conn = connectDB();
        $sql = "SELECT r.*, u.name, u.email FROM reservations r 
                LEFT JOIN users u ON r.user_id = u.id 
                ORDER BY r.reservation_time DESC";
        return $conn->query($sql)->fetchAll();
    }

    public function update($id, $data) {
        $conn = connectDB();
        
        // Xây dựng câu SQL động dựa trên các trường được cập nhật
        $fields = [];
        $params = [':id' => $id];
        
        if(isset($data[':status'])) {
            $fields[] = "status = :status";
            $params[':status'] = $data[':status'];
        }
        
        if(isset($data[':table_id'])) {
            $fields[] = "table_id = :table_id";
            $params[':table_id'] = $data[':table_id'];
        }
        
        if(empty($fields)) {
            return false;
        }
        
        $sql = "UPDATE reservations SET " . implode(", ", $fields) . " WHERE id = :id";
        $stmt = $conn->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id) {
        $conn = connectDB();
        $sql = "DELETE FROM reservations WHERE id = :id";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function getLastInsertId() {
        $conn = connectDB();
        return $conn->lastInsertId();
    }

    // Lấy đặt bàn hiện tại/sắp tới
    public function getCurrentByStatus($status = null) {
        $conn = connectDB();
        $sql = "SELECT * FROM reservations 
                WHERE reservation_time >= DATE_SUB(NOW(), INTERVAL 2 HOUR)
                AND reservation_time <= DATE_ADD(NOW(), INTERVAL 24 HOUR)";
        
        if($status) {
            $sql .= " AND status = '" . $status . "'";
        }
        
        $sql .= " ORDER BY reservation_time ASC";
        return $conn->query($sql)->fetchAll();
    }

    // Lấy đặt bàn cho một bàn cụ thể trong thời gian hiện tại
    public function getReservationsByTable($table_id) {
        $conn = connectDB();
        $sql = "SELECT * FROM reservations 
                WHERE table_id = :table_id
                AND DATE(reservation_time) >= CURDATE()
                AND status IN ('pending', 'confirmed')
                ORDER BY reservation_time ASC";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':table_id' => $table_id]);
        return $stmt->fetchAll();
    }

    // Lấy đặt bàn từ hôm nay trở đi
    public function getUpcomingReservations($limit = 10) {
        $conn = connectDB();
        $sql = "SELECT r.*, u.name FROM reservations r 
                LEFT JOIN users u ON r.user_id = u.id 
                WHERE r.reservation_time >= NOW() 
                AND r.status IN ('pending', 'confirmed')
                ORDER BY r.reservation_time ASC 
                LIMIT " . intval($limit);
        return $conn->query($sql)->fetchAll();
    }

    // Tìm kiếm theo số điện thoại
    public function searchByPhone($phone) {
        $conn = connectDB();
        $sql = "SELECT r.*, t.table_number, t.status as table_status FROM reservations r
                LEFT JOIN tables t ON r.table_id = t.id
                WHERE r.customer_phone LIKE :phone
                ORDER BY r.reservation_time DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':phone' => '%' . $phone . '%']);
        return $stmt->fetchAll();
    }

    // Tìm kiếm theo tên khách
    public function searchByCustomerName($name) {
        $conn = connectDB();
        $sql = "SELECT r.*, t.table_number, t.status as table_status FROM reservations r
                LEFT JOIN tables t ON r.table_id = t.id
                WHERE r.customer_name LIKE :name
                ORDER BY r.reservation_time DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':name' => '%' . $name . '%']);
        return $stmt->fetchAll();
    }

    // Lấy tất cả đặt bàn kèm thông tin bàn
    public function allWithTable() {
        $conn = connectDB();
        $sql = "SELECT r.*, u.name, t.table_number, t.status as table_status FROM reservations r 
                LEFT JOIN users u ON r.user_id = u.id 
                LEFT JOIN tables t ON r.table_id = t.id
                ORDER BY r.reservation_time DESC";
        return $conn->query($sql)->fetchAll();
    }

    // Lấy một đặt bàn kèm thông tin bàn
    public function getByIdWithTable($id) {
        $conn = connectDB();
        $sql = "SELECT r.*, u.name, t.table_number, t.status as table_status FROM reservations r 
                LEFT JOIN users u ON r.user_id = u.id 
                LEFT JOIN tables t ON r.table_id = t.id
                WHERE r.id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // Lấy tất cả bàn được gán cho một đặt bàn (ghép bàn)
    public function getTablesByReservation($reservation_id) {
        $conn = connectDB();
        $sql = "SELECT rt.*, t.table_number, t.capacity, t.status 
                FROM reservation_tables rt 
                JOIN tables t ON rt.table_id = t.id 
                WHERE rt.reservation_id = :reservation_id
                ORDER BY t.table_number";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':reservation_id' => $reservation_id]);
        return $stmt->fetchAll();
    }

    // Gán bàn cho đặt bàn (thêm vào table ghép)
    public function assignTable($reservation_id, $table_id) {
        $conn = connectDB();
        // Kiểm tra xem bàn đã được gán chưa
        $sql = "SELECT id FROM reservation_tables WHERE reservation_id = :reservation_id AND table_id = :table_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':reservation_id' => $reservation_id, ':table_id' => $table_id]);
        if($stmt->fetch()) {
            return true; // Đã tồn tại
        }

        // Thêm bàn vào đặt bàn
        $sql = "INSERT INTO reservation_tables(reservation_id, table_id) VALUES(:reservation_id, :table_id)";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([':reservation_id' => $reservation_id, ':table_id' => $table_id]);
    }

    // Xóa bàn từ đặt bàn (bỏ từ table ghép)
    public function removeTable($reservation_id, $table_id) {
        $conn = connectDB();
        $sql = "DELETE FROM reservation_tables WHERE reservation_id = :reservation_id AND table_id = :table_id";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([':reservation_id' => $reservation_id, ':table_id' => $table_id]);
    }

    // Xóa tất cả bàn từ đặt bàn
    public function clearTables($reservation_id) {
        $conn = connectDB();
        $sql = "DELETE FROM reservation_tables WHERE reservation_id = :reservation_id";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([':reservation_id' => $reservation_id]);
    }

    // Lấy tổng sức chứa của all tables gán cho đặt bàn
    public function getReservationCapacity($reservation_id) {
        $conn = connectDB();
        $sql = "SELECT SUM(t.capacity) as total_capacity FROM reservation_tables rt 
                JOIN tables t ON rt.table_id = t.id 
                WHERE rt.reservation_id = :reservation_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':reservation_id' => $reservation_id]);
        $result = $stmt->fetch();
        return $result['total_capacity'] ?? 0;
    }

    // Lấy danh sách bàn dưới dạng string để hiển thị
    public function getTableNumbers($reservation_id) {
        $conn = connectDB();
        $sql = "SELECT GROUP_CONCAT(t.table_number, ', ') as table_list FROM reservation_tables rt 
                JOIN tables t ON rt.table_id = t.id 
                WHERE rt.reservation_id = :reservation_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':reservation_id' => $reservation_id]);
        $result = $stmt->fetch();
        return $result['table_list'] ?? 'Chưa có bàn';
    }
}

