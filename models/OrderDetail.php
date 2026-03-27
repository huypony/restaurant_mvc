<?php
require_once 'commons/function.php';

class OrderDetail {
    
    public function create($data) {
        $conn = connectDB();
        $sql = "INSERT INTO order_details(order_id, food_id, quantity, price) 
                VALUES(:order_id, :food_id, :quantity, :price)";
        $stmt = $conn->prepare($sql);
        return $stmt->execute($data);
    }
    
    public function getByOrder($order_id) {
        $conn = connectDB();
        $sql = "SELECT od.*, f.name, f.image FROM order_details od 
                JOIN foods f ON od.food_id = f.id 
                WHERE od.order_id = :order_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':order_id' => $order_id]);
        return $stmt->fetchAll();
    }
    
    public function delete($id) {
        $conn = connectDB();
        $sql = "DELETE FROM order_details WHERE id = :id";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
    
    public function findById($id) {
        $conn = connectDB();
        $sql = "SELECT * FROM order_details WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
}
