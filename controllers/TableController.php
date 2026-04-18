<?php
require_once 'models/Table.php';
require_once 'models/Reservation.php';

class TableController {

    public function layout() {
        $tableModel = new Table();
        $reservationModel = new Reservation();
        
        $tables = $tableModel->all();

        require 'views/tables/layout.php';
    }

    public function detail() {
        if(!isset($_GET['id'])) {
            redirect(BASE_URL . 'index.php?act=tables-layout');
        }

        $table_id = intval($_GET['id']);
        $tableModel = new Table();
        $reservationModel = new Reservation();
        
        $table = $tableModel->findById($table_id);
        
        if(!$table) {
            $_SESSION['error'] = 'Bàn không tồn tại!';
            redirect(BASE_URL . 'index.php?act=tables-layout');
        }

        // Lấy đặt bàn hiện tại/sắp tới chỉ cho bàn này
        $reservationsForTable = $reservationModel->getReservationsByTable($table_id);
        $reservation = null;
        if(!empty($reservationsForTable)) {
            $reservation = $reservationsForTable[0];
        }

        require 'views/tables/detail.php';
    }


    public function apiTableMap() {
        header('Content-Type: application/json');
        $time = $_GET['time'] ?? date('Y-m-d H:i:s');
        $tableModel = new Table();
        echo json_encode($tableModel->getTableMap($time));
        exit;
    }
}
