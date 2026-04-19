<?php
require_once 'commons/function.php';

class Reservation {

    private $conn;

    public function __construct() {
        $this->conn = connectDB();
    }

    public function create($data) {
        // ✅ Validate trước khi query
        $required = [':user_id', ':customer_name', ':customer_phone', ':reservation_time', ':guest_count', ':status'];
        foreach ($required as $key) {
            if (!isset($data[$key])) return false;
        }

        // ✅ Bọc transaction để tránh race condition
        $this->conn->beginTransaction();
        try {
            // ✅ Thêm table_id vào INSERT (trường bị thiếu)
            $sql = "INSERT INTO reservations(user_id, table_id, customer_name, customer_phone, reservation_time, guest_count, status)
                    VALUES(:user_id, :table_id, :customer_name, :customer_phone, :reservation_time, :guest_count, :status)";
            $stmt = $this->conn->prepare($sql);

            if ($stmt->execute($data)) {
                // ✅ Dùng cùng $this->conn → lastInsertId() chính xác
                $newId = $this->conn->lastInsertId();
                $this->conn->commit();
                return $newId;
            }

            $this->conn->rollBack();
            return false;

        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function getByUser($user_id) {
        $sql = "SELECT * FROM reservations WHERE user_id = :id ORDER BY reservation_time DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $user_id]);
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $sql = "SELECT * FROM reservations WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function all() {
        $sql = "SELECT r.*, u.name, u.email FROM reservations r 
                LEFT JOIN users u ON r.user_id = u.id 
                ORDER BY r.reservation_time DESC";
        return $this->conn->query($sql)->fetchAll();
    }

    public function update($id, $data) {
        // ✅ Whitelist mở rộng đầy đủ
        $allowed = ['status', 'table_id', 'reservation_time', 'guest_count', 'customer_name', 'customer_phone'];

        $fields = [];
        $params = [':id' => $id];

        foreach ($data as $key => $value) {
            $col = ltrim($key, ':');
            if (!in_array($col, $allowed)) continue;
            $fields[] = "$col = :$col";
            $params[":$col"] = $value;
        }

        if (empty($fields)) return false;

        $sql = "UPDATE reservations SET " . implode(", ", $fields) . " WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id) {
        $sql = "DELETE FROM reservations WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    // ✅ Đã sửa: dùng $this->conn thay vì tạo connection mới
    public function getLastInsertId() {
        return $this->conn->lastInsertId();
    }

    // ✅ Đã sửa: dùng prepared statement, tránh SQL Injection
    public function getCurrentByStatus($status = null) {
        $sql = "SELECT * FROM reservations 
                WHERE reservation_time >= DATE_SUB(NOW(), INTERVAL 2 HOUR)
                AND reservation_time <= DATE_ADD(NOW(), INTERVAL 24 HOUR)";

        $params = [];
        if ($status) {
            $sql .= " AND status = :status";
            $params[':status'] = $status;
        }

        $sql .= " ORDER BY reservation_time ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getReservationsByTable($table_id) {
        $sql = "SELECT * FROM reservations 
                WHERE table_id = :table_id
                AND DATE(reservation_time) >= CURDATE()
                AND status IN ('pending', 'confirmed')
                ORDER BY reservation_time ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':table_id' => $table_id]);
        return $stmt->fetchAll();
    }

    public function getUpcomingReservations($limit = 10) {
        $sql = "SELECT r.*, u.name FROM reservations r 
                LEFT JOIN users u ON r.user_id = u.id 
                WHERE r.reservation_time >= NOW() 
                AND r.status IN ('pending', 'confirmed')
                ORDER BY r.reservation_time ASC 
                LIMIT " . intval($limit);
        return $this->conn->query($sql)->fetchAll();
    }

    public function searchByPhone($phone) {
        $sql = "SELECT r.*, t.table_number, t.status as table_status FROM reservations r
                LEFT JOIN tables t ON r.table_id = t.id
                WHERE r.customer_phone LIKE :phone
                ORDER BY r.reservation_time DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':phone' => '%' . $phone . '%']);
        return $stmt->fetchAll();
    }

    public function searchByCustomerName($name) {
        $sql = "SELECT r.*, t.table_number, t.status as table_status FROM reservations r
                LEFT JOIN tables t ON r.table_id = t.id
                WHERE r.customer_name LIKE :name
                ORDER BY r.reservation_time DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':name' => '%' . $name . '%']);
        return $stmt->fetchAll();
    }

    public function allWithTable() {
        $sql = "SELECT r.*, u.name, t.table_number, t.status as table_status FROM reservations r 
                LEFT JOIN users u ON r.user_id = u.id 
                LEFT JOIN tables t ON r.table_id = t.id
                ORDER BY r.reservation_time DESC";
        return $this->conn->query($sql)->fetchAll();
    }

    public function getByIdWithTable($id) {
        $sql = "SELECT r.*, u.name, t.table_number, t.status as table_status FROM reservations r 
                LEFT JOIN users u ON r.user_id = u.id 
                LEFT JOIN tables t ON r.table_id = t.id
                WHERE r.id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function getTablesByReservation($reservation_id) {
        $sql = "SELECT rt.*, t.table_number, t.capacity, t.status 
                FROM reservation_tables rt 
                JOIN tables t ON rt.table_id = t.id 
                WHERE rt.reservation_id = :reservation_id
                ORDER BY t.table_number";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':reservation_id' => $reservation_id]);
        return $stmt->fetchAll();
    }

    // ✅ Đã sửa: thêm transaction + FOR UPDATE để tránh race condition gán trùng bàn
    public function assignTable($reservation_id, $table_id) {
        $this->conn->beginTransaction();
        try {
            $sql = "SELECT id FROM reservation_tables 
                    WHERE reservation_id = :reservation_id AND table_id = :table_id
                    FOR UPDATE";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':reservation_id' => $reservation_id, ':table_id' => $table_id]);

            if ($stmt->fetch()) {
                $this->conn->commit();
                return true;
            }

            $sql = "INSERT INTO reservation_tables(reservation_id, table_id) VALUES(:reservation_id, :table_id)";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([':reservation_id' => $reservation_id, ':table_id' => $table_id]);
            $this->conn->commit();
            return $result;

        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function removeTable($reservation_id, $table_id) {
        $sql = "DELETE FROM reservation_tables WHERE reservation_id = :reservation_id AND table_id = :table_id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':reservation_id' => $reservation_id, ':table_id' => $table_id]);
    }

    public function clearTables($reservation_id) {
        $sql = "DELETE FROM reservation_tables WHERE reservation_id = :reservation_id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':reservation_id' => $reservation_id]);
    }

    public function getReservationCapacity($reservation_id) {
        $sql = "SELECT SUM(t.capacity) as total_capacity FROM reservation_tables rt 
                JOIN tables t ON rt.table_id = t.id 
                WHERE rt.reservation_id = :reservation_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':reservation_id' => $reservation_id]);
        $result = $stmt->fetch();
        return $result['total_capacity'] ?? 0;
    }

    // ✅ Đã sửa: GROUP_CONCAT sai cú pháp MySQL
    public function getTableNumbers($reservation_id) {
        $sql = "SELECT GROUP_CONCAT(t.table_number ORDER BY t.table_number SEPARATOR ', ') as table_list
                FROM reservation_tables rt 
                JOIN tables t ON rt.table_id = t.id 
                WHERE rt.reservation_id = :reservation_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':reservation_id' => $reservation_id]);
        $result = $stmt->fetch();
        return $result['table_list'] ?? 'Chưa có bàn';
    }
}