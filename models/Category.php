<?php
require_once 'commons/function.php';

class Category {
    
    public function all() {
        $conn = connectDB();
        $sql = "SELECT * FROM categories";
        return $conn->query($sql)->fetchAll();
    }
    
    public function findById($id) {
        $conn = connectDB();
        $sql = "SELECT * FROM categories WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        $conn = connectDB();
        $sql = "INSERT INTO categories(name) VALUES(:name)";
        $stmt = $conn->prepare($sql);
        return $stmt->execute($data);
    }
    
    public function update($id, $data) {
        $conn = connectDB();
        $sql = "UPDATE categories SET name = :name WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $data[':id'] = $id;
        return $stmt->execute($data);
    }
    
    public function delete($id) {
        $conn = connectDB();
        $sql = "DELETE FROM categories WHERE id = :id";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
