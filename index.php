<?php 

session_start();

// Require file Common
require_once './commons/env.php'; // Khai báo biến môi trường
require_once './commons/function.php'; // Hàm hỗ trợ

// Require toàn bộ file Controllers
require_once './controllers/HomeController.php';
require_once './controllers/AuthController.php';
require_once './controllers/FoodController.php';
require_once './controllers/ReservationController.php';
require_once './controllers/OrderController.php';
require_once './controllers/AdminController.php';
require_once './controllers/TableController.php';

// Require toàn bộ file Models
require_once './models/User.php';
require_once './models/Food.php';
require_once './models/Reservation.php';
require_once './models/Order.php';
require_once './models/OrderDetail.php';
require_once './models/Payment.php';
require_once './models/Table.php';
require_once './models/Category.php';

// Route
$act = $_GET['act'] ?? '/';

// Routing match
match ($act) {
    // Trang chủ
    '/' => (new HomeController())->index(),
    'menu' => (new FoodController())->menu(),
    
    // Auth routes
    'login' => (new AuthController())->loginForm(),
    'register' => (new AuthController())->registerForm(),
    'post-login' => (new AuthController())->login(),
    'post-register' => (new AuthController())->register(),
    'logout' => (new AuthController())->logout(),
    
    // Reservation routes
    'reservation-create' => (new ReservationController())->create(),
    'reservation-store' => (new ReservationController())->store(),
    'reservation-list' => (new ReservationController())->list(),
    
    // Order routes
    'order-create' => (new OrderController())->create(),
    'order-store' => (new OrderController())->store(),
    'order-list' => (new OrderController())->list(),
    'order-detail' => (new OrderController())->detail(),
    
    // Table routes
    'tables-layout' => (new TableController())->layout(),
    'tables-detail' => (new TableController())->detail(),
    
    // Admin routes
    'admin-dashboard' => (new AdminController())->dashboard(),
    'admin-users' => (new AdminController())->users(),
    'admin-foods' => (new AdminController())->foods(),
    'admin-add-food' => (new AdminController())->addFood(),
    'admin-store-food' => (new AdminController())->storeFood(),
    'admin-edit-food' => (new AdminController())->editFood(),
    'admin-update-food' => (new AdminController())->updateFood(),
    'admin-delete-food' => (new AdminController())->deleteFood(),
    'admin-categories' => (new AdminController())->categories(),
    'admin-add-category' => (new AdminController())->addCategory(),
    'admin-tables' => (new AdminController())->tables(),
    'admin-add-table' => (new AdminController())->addTable(),
    'admin-store-table' => (new AdminController())->storeTable(),
    'admin-edit-table' => (new AdminController())->editTable(),
    'admin-update-table' => (new AdminController())->updateTable(),
    'admin-delete-table' => (new AdminController())->deleteTable(),
    'admin-store-category' => (new AdminController())->storeCategory(),
    'admin-edit-category' => (new AdminController())->editCategory(),
    'admin-update-category' => (new AdminController())->updateCategory(),
    'admin-delete-category' => (new AdminController())->deleteCategory(),
    'admin-reservations' => (new AdminController())->reservations(),
    'admin-update-reservation' => (new AdminController())->updateReservation(),
    'admin-orders' => (new AdminController())->orders(),
    'admin-update-order' => (new AdminController())->updateOrder(),
    
    default => (new HomeController())->index(),
};  