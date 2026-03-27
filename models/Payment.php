<?php
require_once 'commons/function.php';

class Payment {
    
    public function create($data) {
        $conn = connectDB();
        $sql = "INSERT INTO payments(order_id, amount, payment_method, payment_status, paid_at) 
                VALUES(:order_id, :amount, :payment_method, :payment_status, :paid_at)";
        $stmt = $conn->prepare($sql);
        return $stmt->execute($data);
    }
    
    public function getByOrder($order_id) {
        $conn = connectDB();
        $sql = "SELECT * FROM payments WHERE order_id = :order_id ORDER BY paid_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':order_id' => $order_id]);
        return $stmt->fetch();
    }
    
    public function update($id, $data) {
        $conn = connectDB();
        $sql = "UPDATE payments SET payment_status = :payment_status, paid_at = :paid_at WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $data[':id'] = $id;
        return $stmt->execute($data);
    }
    
    public function findById($id) {
        $conn = connectDB();
        $sql = "SELECT * FROM payments WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    public function all() {
        $conn = connectDB();
        $sql = "SELECT * FROM payments ORDER BY paid_at DESC";
        return $conn->query($sql)->fetchAll();
    }
}
