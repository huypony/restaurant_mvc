<?php
require_once 'commons/function.php';

class Order {
    
    public function all() {
        $conn = connectDB();
        $sql = "SELECT * FROM orders ORDER BY created_at DESC";
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
}
