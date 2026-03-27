<?php
require_once 'commons/env.php';
require_once 'commons/function.php';

try {
    $conn = connectDB();
    
    // 1. Kiểm tra và thêm cột table_id vào reservations nếu chưa có
    $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_NAME = 'reservations' AND COLUMN_NAME = 'table_id'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $column_exists = $stmt->fetch();
    
    if(!$column_exists) {
        $sql = "ALTER TABLE reservations ADD COLUMN table_id INT DEFAULT NULL AFTER user_id";
        $conn->exec($sql);
        echo "✅ Thêm cột table_id vào bảng reservations<br>";
    }
    
    // 2. Kiểm tra xem bảng reservation_tables có tồn tại chưa
    $sql = "SHOW TABLES LIKE 'reservation_tables'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $table_exists = $stmt->fetch();
    
    if(!$table_exists) {
        // Tạo bảng reservation_tables (junction table)
        $sql = "CREATE TABLE reservation_tables(
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    reservation_id INT NOT NULL,
                    table_id INT NOT NULL,
                    FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE CASCADE,
                    FOREIGN KEY (table_id) REFERENCES tables(id) ON DELETE CASCADE,
                    UNIQUE KEY unique_reservation_table (reservation_id, table_id)
                )";
        $conn->exec($sql);
        echo "✅ Tạo bảng reservation_tables (junction table)<br>";
    } else {
        echo "ℹ️ Bảng reservation_tables đã tồn tại<br>";
    }
    
    // 3. Kiểm tra và thêm created_at vào categories
    $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_NAME = 'categories' AND COLUMN_NAME = 'created_at'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $column_exists = $stmt->fetch();
    
    if(!$column_exists) {
        $sql = "ALTER TABLE categories ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
        $conn->exec($sql);
        echo "✅ Thêm cột created_at vào bảng categories<br>";
    }
    
    // 4. Kiểm tra và thêm created_at vào tables
    $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_NAME = 'tables' AND COLUMN_NAME = 'created_at'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $column_exists = $stmt->fetch();
    
    if(!$column_exists) {
        $sql = "ALTER TABLE tables ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
        $conn->exec($sql);
        echo "✅ Thêm cột created_at vào bảng tables<br>";
    }
    
    echo "<br>✅ Migration hoàn tất!";
    
} catch(Exception $e) {
    echo "❌ Lỗi: " . $e->getMessage();
}

