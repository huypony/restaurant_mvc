<?php
/**
 * Script Reset Password Admin
 * Truy cập: http://localhost/restaurant_mvc/reset-admin-password.php
 */

require_once './commons/env.php';
require_once './commons/function.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($email) || empty($new_password) || empty($confirm_password)) {
        $message = '<div class="alert alert-danger">Vui lòng điền đầy đủ thông tin!</div>';
    } elseif ($new_password !== $confirm_password) {
        $message = '<div class="alert alert-danger">Password không trùng khớp!</div>';
    } elseif (strlen($new_password) < 6) {
        $message = '<div class="alert alert-danger">Password phải từ 6 ký tự trở lên!</div>';
    } else {
        try {
            $conn = connectDB();
            
            // Kiểm tra email tồn tại
            $sql = "SELECT * FROM users WHERE email = :email";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if (!$user) {
                $message = '<div class="alert alert-danger">Email không tồn tại!</div>';
            } else {
                // Update password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET password = :password WHERE email = :email";
                $stmt = $conn->prepare($sql);
                $result = $stmt->execute([
                    ':password' => $hashed_password,
                    ':email' => $email
                ]);

                if ($result) {
                    $message = '<div class="alert alert-success">✅ Cập nhật password thành công! Vui lòng <a href="index.php?act=login">đăng nhập tại đây</a></div>';
                } else {
                    $message = '<div class="alert alert-danger">Cập nhật password thất bại!</div>';
                }
            }
        } catch (Exception $e) {
            $message = '<div class="alert alert-danger">Lỗi: ' . $e->getMessage() . '</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card">
                    <div class="card-body p-5">
                        <h2 class="card-title text-center mb-4">🔑 Reset Password Admin</h2>

                        <?php if (!empty($message)) echo $message; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Email Admin</label>
                                <input type="email" class="form-control" name="email" required>
                                <small class="text-muted">Nhập email của tài khoản admin cần reset password</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password Mới</label>
                                <input type="password" class="form-control" name="new_password" required>
                                <small class="text-muted">Tối thiểu 6 ký tự</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Xác Nhận Password</label>
                                <input type="password" class="form-control" name="confirm_password" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mb-3">Reset Password</button>
                        </form>

                        <hr>

                        <div class="alert alert-info" role="alert">
                            <strong>💡 Hướng dẫn:</strong>
                            <ol class="mb-0 mt-2">
                                <li>Nhập email của tài khoản admin bạn vừa thêm vào database</li>
                                <li>Nhập password mới (sẽ tự động được mã hóa)</li>
                                <li>Nhấn "Reset Password"</li>
                                <li>Đăng nhập với email và password mới</li>
                            </ol>
                        </div>

                        <p class="text-center mt-3">
                            <a href="index.php?act=login">Quay lại trang đăng nhập</a> | 
                            <a href="index.php">Trang chủ</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
