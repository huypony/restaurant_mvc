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
        $sql = "INSERT INTO tables(table_number, capacity, status) VALUES(:table_number, :capacity, :status)";
        $stmt = $conn->prepare($sql);
        return $stmt->execute($data) ? $conn->lastInsertId() : false;
    }
    
    public function update($id, $data) {
        $conn = connectDB();
        
        // Build dynamic update SQL
        $setClause = [];
        $params = [':id' => $id];
        
        if(isset($data[':table_number']) && !empty($data[':table_number'])) {
            $setClause[] = "table_number = :table_number";
            $params[':table_number'] = $data[':table_number'];
        }
        if(isset($data[':capacity'])) {
            $setClause[] = "capacity = :capacity";
            $params[':capacity'] = $data[':capacity'];
        }
        if(isset($data[':status'])) {
            $setClause[] = "status = :status";
            $params[':status'] = $data[':status'];
        }
        
        if(empty($setClause)) return false;
        
        $sql = "UPDATE tables SET " . implode(', ', $setClause) . " WHERE id = :id";
        $stmt = $conn->prepare($sql);
        return $stmt->execute($params);
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

    public function getAvailableByCapacity($min_capacity) {
        $conn = connectDB();
        $sql = "SELECT * FROM tables WHERE status = 'available' AND capacity >= :capacity ORDER BY capacity ASC";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':capacity' => $min_capacity]);
        return $stmt->fetchAll();
    }

    public function getByStatus($status) {
        $conn = connectDB();
        $sql = "SELECT * FROM tables WHERE status = :status ORDER BY table_number";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':status' => $status]);
        return $stmt->fetchAll();
    }

    public function getLastInsertId() {
        $conn = connectDB();
        return $conn->lastInsertId();
    }
}

