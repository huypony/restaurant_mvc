<?php
require_once 'models/Reservation.php';
require_once 'models/Food.php';
require_once 'models/Order.php';
require_once 'models/OrderDetail.php';
require_once 'models/Table.php';

class ReservationController {

    // Hiển thị form đặt bàn + chọn món (view mới của bạn)
    public function create() {
        if(!isLoggedIn()) {
            redirect(BASE_URL . 'index.php?act=login');
        }
        $foodModel = new Food();
        $foods = $foodModel->all(); // view cần $foods để vẽ món
        require 'views/reservations/create.php';
    }

    // Xử lý đặt bàn + tạo order luôn
    public function store() {
        if(!isLoggedIn() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . 'index.php?act=reservation-create');
        }

        $table_id = intval($_POST['table_id'] ?? 0);
        $reservation_time = $_POST['reservation_time'] ?? '';
        $foods = $_POST['foods'] ?? []; // dạng [food_id => qty]

        if(!$table_id || empty($reservation_time) || empty($foods)) {
            $_SESSION['error'] = 'Vui lòng chọn thời gian, bàn và ít nhất 1 món!';
            redirect(BASE_URL . 'index.php?act=reservation-create');
        }

        $user = $_SESSION['user'];
        $tableModel = new Table();
        $table = $tableModel->findById($table_id);

        $dataRes = [
            ':user_id' => intval($user['id']),
            ':table_id' => $table_id,
            ':customer_name' => $user['name'],
            ':customer_phone' => $user['phone'] ?? '',
            ':reservation_time' => $reservation_time,
            ':guest_count' => $table ? intval($table['capacity']) : 2,
            ':status' => 'pending'
        ];

        $reservationModel = new Reservation();

        try {
            $reservation_id = $reservationModel->create($dataRes);
            if(!$reservation_id) {
                throw new Exception('Tạo đặt bàn thất bại');
            }
        } catch (Exception $e) {
            // Bắt lỗi trùng bàn 2 tiếng trong model
            $_SESSION['error'] = $e->getMessage();
            redirect(BASE_URL . 'index.php?act=reservation-create');
        }

        // Tạo order
        $foodModel = new Food();
        $orderModel = new Order();
        $orderDetailModel = new OrderDetail();

        $total_price = 0;
        foreach($foods as $fid => $qty) {
            $food = $foodModel->findById(intval($fid));
            if($food) $total_price += $food['price'] * intval($qty);
        }

        $order_id = $orderModel->create([
            ':reservation_id' => $reservation_id,
            ':user_id' => $user['id'],
            ':total_price' => $total_price,
            ':status' => 'pending'
        ]);

        foreach($foods as $fid => $qty) {
            $food = $foodModel->findById(intval($fid));
            if(!$food || $qty <= 0) continue;
            $orderDetailModel->create([
                ':order_id' => $order_id,
                ':food_id' => $fid,
                ':quantity' => $qty,
                ':price' => $food['price']
            ]);
        }

        $_SESSION['success'] = 'Đặt bàn và gọi món thành công!';
        redirect(BASE_URL . 'index.php?act=order-detail&id=' . $order_id);
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