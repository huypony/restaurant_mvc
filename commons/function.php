<?php

// Kết nối CSDL qua PDO
function connectDB() {
    // Kết nối CSDL
    $host = DB_HOST;
    $port = DB_PORT;
    $dbname = DB_NAME;

    try {
        $conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname", DB_USERNAME, DB_PASSWORD);

        // cài đặt chế độ báo lỗi là xử lý ngoại lệ
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // cài đặt chế độ trả dữ liệu
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
        return $conn;
    } catch (PDOException $e) {
        echo ("Connection failed: " . $e->getMessage());
    }
}

// Kiểm tra người dùng đã đăng nhập
function isLoggedIn() {
    return isset($_SESSION['user']);
}

// Lấy người dùng hiện tại
function getCurrentUser() {
    return $_SESSION['user'] ?? null;
}

// Kiểm tra quyền admin
function isAdmin() {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
}

// Chuyển hướng
function redirect($url) {
    header("Location: " . $url);
    exit;
}

// Hiển thị thông báo lỗi
function showError($message) {
    echo "<div class='alert alert-danger'>$message</div>";
}

// Hiển thị thông báo thành công
function showSuccess($message) {
    echo "<div class='alert alert-success'>$message</div>";
}

// Xóa bỏ dữ liệu đầu vào
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Kiểm tra email hợp lệ
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Định dạng tiền tệ
function formatMoney($amount) {
    return number_format($amount, 2, '.', ',') . ' đ';
}

// Định dạng ngày giờ
function formatDate($dateTime) {
    return date('d/m/Y H:i', strtotime($dateTime));
}

