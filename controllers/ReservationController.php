
<?php
require_once 'models/Reservation.php';
require_once 'models/User.php';

class ReservationController {

    public function create() {
        if(!isLoggedIn()) {
            redirect(BASE_URL . 'index.php?act=login');
        }
        require 'views/reservations/create.php';
    }

    public function store() {
        if(!isLoggedIn() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . 'index.php?act=reservation-create');
        }

        $customer_name = sanitize($_POST['customer_name'] ?? '');
        $customer_phone = sanitize($_POST['customer_phone'] ?? '');
        $reservation_time = $_POST['reservation_time'] ?? '';
        $guest_count = isset($_POST['guest_count']) ? intval($_POST['guest_count']) : 0;

        // Validate
        if(empty($customer_name) || empty($customer_phone) || empty($reservation_time) || $guest_count <= 0) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin!';
            redirect(BASE_URL . 'index.php?act=reservation-create');
        }

        // Verify user is logged in and has valid ID
        if(!isset($_SESSION['user']['id']) || empty($_SESSION['user']['id'])) {
            $_SESSION['error'] = 'Lỗi xác thực người dùng!';
            redirect(BASE_URL . 'index.php?act=reservation-create');
        }

        $model = new Reservation();
        $data = [
            ':user_id' => intval($_SESSION['user']['id']),
            ':customer_name' => $customer_name,
            ':customer_phone' => $customer_phone,
            ':reservation_time' => $reservation_time,
            ':guest_count' => $guest_count,
            ':status' => 'pending'
        ];

        if($model->create($data)) {
            $_SESSION['success'] = 'Đặt bàn thành công!';
            redirect(BASE_URL . 'index.php?act=reservation-list');
        } else {
            $_SESSION['error'] = 'Đặt bàn thất bại!';
            redirect(BASE_URL . 'index.php?act=reservation-create');
        }
    }

    public function list() {
        if(!isLoggedIn()) {
            redirect(BASE_URL . 'index.php?act=login');
        }

        $model = new Reservation();
        $reservations = $model->getByUser($_SESSION['user']['id']);
        require 'views/reservations/list.php';
    }
}
