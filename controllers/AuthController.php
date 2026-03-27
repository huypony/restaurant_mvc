
<?php
require_once 'models/User.php';

class AuthController{

    public function loginForm(){
        require 'views/auth/login.php';
    }

    public function registerForm(){
        require 'views/auth/register.php';
    }

    public function login(){
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . 'index.php?act=login');
        }

        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if(empty($email) || empty($password)) {
            $_SESSION['error'] = 'Email và password không được bỏ trống!';
            redirect(BASE_URL . 'index.php?act=login');
        }

        if(!isValidEmail($email)) {
            $_SESSION['error'] = 'Email không hợp lệ!';
            redirect(BASE_URL . 'index.php?act=login');
        }

        $model = new User();
        $user = $model->findByEmail($email);

        if($user && password_verify($password, $user['password'])){
            $_SESSION['user'] = $user;
            $_SESSION['success'] = 'Đăng nhập thành công!';
            redirect(BASE_URL);
        } else {
            $_SESSION['error'] = 'Email hoặc password không chính xác!';
            redirect(BASE_URL . 'index.php?act=login');
        }
    }

    public function register(){
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . 'index.php?act=register');
        }

        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        // Validate
        if(empty($name) || empty($email) || empty($password) || empty($password_confirm)) {
            $_SESSION['error'] = 'Tất cả trường không được bỏ trống!';
            redirect(BASE_URL . 'index.php?act=register');
        }

        if(!isValidEmail($email)) {
            $_SESSION['error'] = 'Email không hợp lệ!';
            redirect(BASE_URL . 'index.php?act=register');
        }

        if($password !== $password_confirm) {
            $_SESSION['error'] = 'Password không trùng khớp!';
            redirect(BASE_URL . 'index.php?act=register');
        }

        if(strlen($password) < 6) {
            $_SESSION['error'] = 'Password phải từ 6 ký tự trở lên!';
            redirect(BASE_URL . 'index.php?act=register');
        }

        $model = new User();
        $existUser = $model->findByEmail($email);

        if($existUser) {
            $_SESSION['error'] = 'Email đã được đăng ký!';
            redirect(BASE_URL . 'index.php?act=register');
        }

        $data = [
            ':name' => $name,
            ':email' => $email,
            ':password' => password_hash($password, PASSWORD_DEFAULT),
            ':role' => 'customer'
        ];

        if($model->create($data)) {
            $_SESSION['success'] = 'Đăng ký thành công! Vui lòng đăng nhập.';
            redirect(BASE_URL . 'index.php?act=login');
        } else {
            $_SESSION['error'] = 'Đăng ký thất bại! Vui lòng thử lại.';
            redirect(BASE_URL . 'index.php?act=register');
        }
    }

    public function logout(){
        session_destroy();
        redirect(BASE_URL);
    }
}
