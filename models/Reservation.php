<?php
require_once 'commons/function.php';

class Reservation {
    
    public function create($data) {
        $conn = connectDB();
        
        // ===== TÍNH NĂNG MỚI: CHỐNG TRÙNG BÀN =====
        $time_start = $data[':reservation_time'];
        $time_end = date('Y-m-d H:i:s', strtotime($time_start . ' +2 hours'));
        $sql_check = "SELECT id FROM reservations 
                      WHERE table_id = :table_id 
                      AND status IN ('pending', 'confirmed')
                      AND reservation_time < :time_end 
                      AND DATE_ADD(reservation_time, INTERVAL 2 HOUR) > :time_start";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->execute([':table_id' => $data[':table_id'], ':time_start' => $time_start, ':time_end' => $time_end]);
        if ($stmt_check->rowCount() > 0) {
            throw new Exception("Bàn đã được đặt trong khung giờ này");
        }
        
        $sql = "INSERT INTO reservations(user_id, table_id, customer_name, customer_phone, reservation_time, guest_count, status)
                VALUES(:user_id, :table_id, :customer_name, :customer_phone, :reservation_time, :guest_count, :status)";
        $stmt = $conn->prepare($sql);
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
        $fields = [];
        $params = [':id' => $id];
        
        foreach ($data as $key => $value) {
            // Lấy tên cột bằng cách bỏ dấu ':' ở đầu key (VD: ':status' -> 'status')
            $colName = ltrim($key, ':');
            $fields[] = "$colName = $key";
            $params[$key] = $value;
        }
        
        if(empty($fields)) return false;
        
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
}