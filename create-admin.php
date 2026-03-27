<?php
require_once 'commons/env.php';
require_once 'commons/function.php';

try {
    $conn = connectDB();
    
    // Lấy thông tin từ form hoặc từ query parameter
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';
        
        // Validation
        if(empty($name) || empty($email) || empty($password)) {
            $error = 'Vui lòng điền đầy đủ thông tin!';
        } elseif($password !== $password_confirm) {
            $error = 'Mật khẩu không trùng khớp!';
        } elseif(strlen($password) < 6) {
            $error = 'Mật khẩu phải ít nhất 6 ký tự!';
        } else {
            // Kiểm tra email đã tồn tại chưa
            $sql = "SELECT id FROM users WHERE email = :email";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':email' => $email]);
            
            if($stmt->fetch()) {
                $error = 'Email này đã được sử dụng!';
            } else {
                // Tạo admin account
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $sql = "INSERT INTO users(name, email, password, role) 
                        VALUES(:name, :email, :password, 'admin')";
                $stmt = $conn->prepare($sql);
                
                if($stmt->execute([
                    ':name' => $name,
                    ':email' => $email,
                    ':password' => $hashed_password
                ])) {
                    $success = '✅ Tạo tài khoản admin thành công!<br>';
                    $success .= 'Email: ' . htmlspecialchars($email) . '<br>';
                    $success .= 'Bạn có thể đăng nhập ngay bây giờ.';
                    
                    // Clear form
                    $name = $email = '';
                } else {
                    $error = 'Lỗi khi tạo tài khoản!';
                }
            }
        }
    }
    
} catch(Exception $e) {
    $error = '❌ Lỗi: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo Tài Khoản Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            border: none;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 30px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">👨‍💼 Tạo Tài Khoản Admin</h3>
                    </div>
                    <div class="card-body p-4">
                        <?php if(isset($success)) { ?>
                            <div class="alert alert-success" role="alert">
                                <?= $success ?>
                            </div>
                        <?php } ?>
                        
                        <?php if(isset($error)) { ?>
                            <div class="alert alert-danger" role="alert">
                                <?= $error ?>
                            </div>
                        <?php } ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="name" class="form-label">Tên Quản Trị Viên</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?= isset($name) ? htmlspecialchars($name) : '' ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Mật Khẩu</label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Ít nhất 6 ký tự" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password_confirm" class="form-label">Xác Nhận Mật Khẩu</label>
                                <input type="password" class="form-control" id="password_confirm" 
                                       name="password_confirm" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                🔐 Tạo Admin
                            </button>
                            
                            <a href="<?= BASE_URL ?>" class="btn btn-secondary w-100">
                                ← Quay Về Trang Chủ
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
