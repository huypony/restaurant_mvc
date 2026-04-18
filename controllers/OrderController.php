<?php
require_once 'models/Order.php';
require_once 'models/OrderDetail.php';
require_once 'models/Food.php';
require_once 'models/Reservation.php';

class OrderController {

    public function create() {
        if(!isLoggedIn()) {
            redirect(BASE_URL . 'index.php?act=login');
        }
        $reservation_id = intval($_GET['reservation_id'] ?? 0);
        $reservationModel = new Reservation();
        $reservation = $reservationModel->findById($reservation_id);

        if(!$reservation || $reservation['user_id'] != $_SESSION['user']['id']) {
            $_SESSION['error'] = 'Đặt bàn không hợp lệ';
            redirect(BASE_URL . 'index.php?act=reservation-list');
        }

        $foodModel = new Food();
        $foods = $foodModel->all();
        require 'views/orders/create.php';
    }

    public function store() {
        if(!isLoggedIn() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . 'index.php?act=home');
        }

        $reservation_id = intval($_POST['reservation_id'] ?? 0);
        $items = $_POST['items'] ?? []; // [food_id => qty]

        if(!$reservation_id || empty($items)) {
            $_SESSION['error'] = 'Vui lòng chọn món';
            redirect(BASE_URL . 'index.php?act=order-create&reservation_id=' . $reservation_id);
        }

        $foodModel = new Food();
        $orderModel = new Order();
        $detailModel = new OrderDetail();
        $conn = connectDB();

        // tính tổng tiền mới
        $add_total = 0;
        foreach($items as $fid => $qty) {
            $food = $foodModel->findById(intval($fid));
            if($food) $add_total += $food['price'] * intval($qty);
        }

        // kiểm tra đơn pending
        $order = $orderModel->getPendingByReservation($reservation_id);
        if($order) {
            $order_id = $order['id'];
            $new_total = $order['total_price'] + $add_total;
            $orderModel->update($order_id, [':status' => 'pending', ':total_price' => $new_total]);
        } else {
            $order_id = $orderModel->create([
                ':reservation_id' => $reservation_id,
                ':user_id' => $_SESSION['user']['id'],
                ':total_price' => $add_total,
                ':status' => 'pending'
            ]);
        }

        // GỘP món trùng
        foreach($items as $fid => $qty) {
            $fid = intval($fid);
            $qty = intval($qty);
            if($qty <= 0) continue;

            $food = $foodModel->findById($fid);
            if(!$food) continue;

            // kiểm tra đã có chưa
            $sql = "SELECT * FROM order_details WHERE order_id = :oid AND food_id = :fid LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':oid' => $order_id, ':fid' => $fid]);
            $exist = $stmt->fetch();

            if($exist) {
                // update số lượng
                $newQty = $exist['quantity'] + $qty;
                $upd = $conn->prepare("UPDATE order_details SET quantity = :q WHERE id = :id");
                $upd->execute([':q' => $newQty, ':id' => $exist['id']]);
            } else {
                $detailModel->create([
                    ':order_id' => $order_id,
                    ':food_id' => $fid,
                    ':quantity' => $qty,
                    ':price' => $food['price']
                ]);
            }
        }

        $_SESSION['success'] = 'Đã cập nhật món ăn!';
        redirect(BASE_URL . 'index.php?act=order-detail&id=' . $order_id);
    }

    public function list() {
        if(!isLoggedIn()) redirect(BASE_URL . 'index.php?act=login');
        $model = new Order();
        $orders = $model->getByUser($_SESSION['user']['id']);
        require 'views/orders/list.php';
    }

    public function detail() {
        if(!isLoggedIn()) redirect(BASE_URL . 'index.php?act=login');
        $id = intval($_GET['id'] ?? 0);
        $orderModel = new Order();
        $detailModel = new OrderDetail();
        $order = $orderModel->findById($id);

        if(!$order || $order['user_id'] != $_SESSION['user']['id'] && !isAdmin()) {
            $_SESSION['error'] = 'Không có quyền xem';
            redirect(BASE_URL . 'index.php?act=order-list');
        }

        $details = $detailModel->getByOrder($id);
        require 'views/orders/detail.php';
    }
}