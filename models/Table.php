<?php
require_once 'commons/function.php';

class Table {
    
    public function all() {
        $conn = connectDB();
        $sql = "SELECT * FROM tables ORDER BY table_number";
        return $conn->query($sql)->fetchAll();
    }
    
    public function findById($id) {
        $conn = connectDB();
        $sql = "SELECT * FROM tables WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $conn = connectDB();
        $sql = "INSERT INTO tables(table_number, status, capacity) VALUES(:table_number, :status, :capacity)";
        $stmt = $conn->prepare($sql);
        return $stmt->execute($data);
    }
    
    public function update($id, $data) {
        $conn = connectDB();
        
        if(isset($data[':table_number']) && empty($data[':table_number']) && isset($data[':status'])) {
            $sql = "UPDATE tables SET status = :status WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $params = [':id' => $id, ':status' => $data[':status']];
            return $stmt->execute($params);
        }
        
        $sql = "UPDATE tables SET table_number = :table_number, status = :status, capacity = :capacity WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $data[':id'] = $id;
        return $stmt->execute($data);
    }

    public function delete($id) {
        $conn = connectDB();
        $sql = "DELETE FROM tables WHERE id = :id";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
    
    public function getAvailable() {
        $conn = connectDB();
        $sql = "SELECT * FROM tables WHERE status = 'available' ORDER BY table_number";
        return $conn->query($sql)->fetchAll();
    }

    public function getByStatus($status) {
        $conn = connectDB();
        $sql = "SELECT * FROM tables WHERE status = :status ORDER BY table_number";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':status' => $status]);
        return $stmt->fetchAll();
    }

    // ===== TÍNH NĂNG MỚI: SƠ ĐỒ BÀN THEO GIỜ =====
    public function getTableMap($datetime) {
        $tables = $this->all();
        $result = [];
        foreach ($tables as $table) {
            $result[] = [
                'id' => $table['id'],
                'table_number' => $table['table_number'],
                'capacity' => $table['capacity'],
                'status' => $this->getStatusByTime($table['id'], $datetime),
                'customer_name' => $this->getCustomerByTable($table['id'], $datetime)
            ];
        }
        return $result;
    }

    public function getStatusByTime($table_id, $datetime) {
        $conn = connectDB();
        $time_end = date('Y-m-d H:i:s', strtotime($datetime . ' +2 hours'));
        $sql = "SELECT o.status as order_status 
                FROM reservations r 
                LEFT JOIN orders o ON r.id = o.reservation_id
                WHERE r.table_id = :table_id 
                AND r.status IN ('pending', 'confirmed')
                AND r.reservation_time < :time_end 
                AND DATE_ADD(r.reservation_time, INTERVAL 2 HOUR) > :time_start";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':table_id' => $table_id, ':time_start' => $datetime, ':time_end' => $time_end]);
        $res = $stmt->fetch();
        if (!$res) return 'trong';
        if ($res['order_status'] == 'serving') return 'dang_an';
        return 'da_dat';
    }

    public function getCustomerByTable($table_id, $datetime) {
        $conn = connectDB();
        $time_end = date('Y-m-d H:i:s', strtotime($datetime . ' +2 hours'));
        $sql = "SELECT COALESCE(r.customer_name, u.name) as name 
                FROM reservations r 
                LEFT JOIN users u ON r.user_id = u.id
                WHERE r.table_id = :table_id 
                AND r.status IN ('pending', 'confirmed')
                AND r.reservation_time < :time_end 
                AND DATE_ADD(r.reservation_time, INTERVAL 2 HOUR) > :time_start
                LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':table_id' => $table_id, ':time_start' => $datetime, ':time_end' => $time_end]);
        return $stmt->fetch()['name'] ?? '';
    }
}