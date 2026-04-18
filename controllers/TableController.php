<?php
require_once 'models/Table.php';
require_once 'models/Reservation.php';

class TableController {

    // API trả JSON cho sơ đồ bàn theo giờ (dùng trong create.php)
    public function apiTableMap() {
        header('Content-Type: application/json');
        $time = $_GET['time'] ?? date('Y-m-d H:i:s');
        // chuẩn hóa định dạng
        $time = str_replace('T', ' ', $time);
        
        $tableModel = new Table();
        $data = $tableModel->getTableMap($time);
        
        echo json_encode($data);
        exit;
    }

    // Trang sơ đồ tổng quan
    public function layout() {
        $tableModel = new Table();
        $reservationModel = new Reservation();
        
        $tables = $tableModel->all();
        require 'views/tables/layout.php';
    }

    // Chi tiết 1 bàn
    public function detail() {
        $id = intval($_GET['id'] ?? 0);
        if(!$id) {
            redirect(BASE_URL . 'index.php?act=tables-layout');
        }

        $tableModel = new Table();
        $reservationModel = new Reservation();

        $table = $tableModel->findById($id);
        $reservations = $reservationModel->getReservationsByTable($id);

        require 'views/tables/detail.php';
    }
}