<?php
require_once 'models/Order.php';
require_once 'models/OrderDetail.php';
require_once 'models/Food.php';
require_once 'models/Payment.php';
require_once 'models/Reservation.php';

class OrderController {

    public function create() {
        if(!isLoggedIn()) {
            redirect(BASE_URL . 'index.php?act=login');
        }

        $reservation_id = $_GET['reservation_id'] ?? null;
        if(!$reservation_id) {
            $_SESSION['error'] = 'Vui lòng chọn một đơn đặt bàn!';
            redirect(BASE_URL . 'index.php?act=reservation-list');
        }

        $foodModel = new Food();
        $foods = $foodModel->all();

        require 'views/orders/create.php';
    }

    public function store() {
        if(!isLoggedIn() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . 'index.php?act=order-create');
        }

        $reservation_id = intval($_POST['reservation_id'] ?? 0);
        $items = $_POST['items'] ?? [];

        if(!$reservation_id || empty($items)) {
            $_SESSION['error'] = 'Vui lòng chọn ít nhất một món ăn!';
            redirect(BASE_URL . 'index.php?act=order-create');
        }

        $orderModel = new Order();
        $orderDetailModel = new OrderDetail();
        $foodModel = new Food();

        $total_price = 0;
        foreach($items as $item) {
            $food_id = intval($item['food_id']);
            $quantity = intval($item['quantity']);
            
            if($quantity <= 0) continue;

            $food = $foodModel->findById($food_id);
            if(!$food) continue;

            $total_price += $food['price'] * $quantity;
        }

        // Check if there's already a pending order for this reservation
        $existingOrder = $orderModel->getPendingByReservation($reservation_id);
        $isNewOrder = !$existingOrder;
        
        if($existingOrder) {
            // Update existing order
            $order_id = $existingOrder['id'];
            $updateData = [
                ':status' => 'pending',
                ':total_price' => $existingOrder['total_price'] + $total_price
            ];
            
            if(!$orderModel->update($order_id, $updateData)) {
                $_SESSION['error'] = 'Cập nhật đơn hàng thất bại!';
                redirect(BASE_URL . 'index.php?act=order-create&reservation_id=' . $reservation_id);
            }
        } else {
            // Create new order
            $data = [
                ':reservation_id' => $reservation_id,
                ':user_id' => $_SESSION['user']['id'],
                ':total_price' => $total_price,
                ':status' => 'pending'
            ];

            $order_id = $orderModel->create($data);
            
            if(!$order_id) {
                $_SESSION['error'] = 'Tạo đơn hàng thất bại!';
                redirect(BASE_URL . 'index.php?act=order-create');
            }
        }

        // Add order details
        foreach($items as $item) {
            $food_id = intval($item['food_id']);
            $quantity = intval($item['quantity']);
            
            if($quantity <= 0) continue;

            $food = $foodModel->findById($food_id);
            if(!$food) continue;

            $detailData = [
                ':order_id' => $order_id,
                ':food_id' => $food_id,
                ':quantity' => $quantity,
                ':price' => $food['price']
            ];

            if(!$orderDetailModel->create($detailData)) {
                $_SESSION['error'] = 'Lỗi khi thêm chi tiết đơn hàng!';
                redirect(BASE_URL . 'index.php?act=order-detail&id=' . $order_id);
            }
        }

        $_SESSION['success'] = $isNewOrder ? 'Tạo đơn hàng thành công!' : 'Cập nhật đơn hàng thành công!';
        redirect(BASE_URL . 'index.php?act=order-detail&id=' . $order_id);
    }

    public function list() {
        if(!isLoggedIn()) {
            redirect(BASE_URL . 'index.php?act=login');
        }

        $orderModel = new Order();
        $orders = $orderModel->getByUser($_SESSION['user']['id']);

        require 'views/orders/list.php';
    }

    public function detail() {
        if(!isLoggedIn()) {
            redirect(BASE_URL . 'index.php?act=login');
        }

        $order_id = intval($_GET['id'] ?? 0);
        if(!$order_id) {
            redirect(BASE_URL . 'index.php?act=order-list');
        }

        $orderModel = new Order();
        $order = $orderModel->findById($order_id);

        if(!$order || $order['user_id'] != $_SESSION['user']['id']) {
            $_SESSION['error'] = 'Đơn hàng không tồn tại!';
            redirect(BASE_URL . 'index.php?act=order-list');
        }

        $orderDetailModel = new OrderDetail();
        $details = $orderDetailModel->getByOrder($order_id);

        $paymentModel = new Payment();
        $payment = $paymentModel->getByOrder($order_id);

        require 'views/orders/detail.php';
    }
}
