
<?php
require_once 'commons/function.php';

class User {
    
    public function findByEmail($email) {
        $conn = connectDB();
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    public function findById($id) {
        $conn = connectDB();
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $conn = connectDB();
        $sql = "INSERT INTO users(name, email, password, role) VALUES(:name, :email, :password, :role)";
        $stmt = $conn->prepare($sql);
        return $stmt->execute($data);
    }

    public function all() {
        $conn = connectDB();
        $sql = "SELECT * FROM users ORDER BY created_at DESC";
        return $conn->query($sql)->fetchAll();
    }

    public function update($id, $data) {
        $conn = connectDB();
        $sql = "UPDATE users SET name = :name, email = :email, role = :role WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $data[':id'] = $id;
        return $stmt->execute($data);
    }

    public function delete($id) {
        $conn = connectDB();
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function getLastInsertId() {
        $conn = connectDB();
        return $conn->lastInsertId();
    }
}
