
<?php
require_once 'commons/function.php';

class Food {
    
    public function all() {
        $conn = connectDB();
        $sql = "SELECT f.*, c.name as category_name FROM foods f 
                LEFT JOIN categories c ON f.category_id = c.id 
                WHERE f.status = 'active' ORDER BY f.created_at DESC";
        return $conn->query($sql)->fetchAll();
    }

    public function findById($id) {
        $conn = connectDB();
        $sql = "SELECT f.*, c.name as category_name FROM foods f 
                LEFT JOIN categories c ON f.category_id = c.id 
                WHERE f.id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $conn = connectDB();
        $sql = "INSERT INTO foods(name, price, image, category_id, status) 
                VALUES(:name, :price, :image, :category_id, :status)";
        $stmt = $conn->prepare($sql);
        return $stmt->execute($data);
    }

    public function update($id, $data) {
        $conn = connectDB();
        $sql = "UPDATE foods SET name = :name, price = :price, image = :image, 
                category_id = :category_id, status = :status WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $data[':id'] = $id;
        return $stmt->execute($data);
    }

    public function delete($id) {
        $conn = connectDB();
        $sql = "DELETE FROM foods WHERE id = :id";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function getByCategory($category_id) {
        $conn = connectDB();
        $sql = "SELECT * FROM foods WHERE category_id = :category_id AND status = 'active' 
                ORDER BY created_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':category_id' => $category_id]);
        return $stmt->fetchAll();
    }

    public function getLastInsertId() {
        $conn = connectDB();
        return $conn->lastInsertId();
    }
}
