<?php
require_once 'models/User.php';
require_once 'models/Food.php';
require_once 'models/Category.php';
require_once 'models/Reservation.php';
require_once 'models/Order.php';
require_once 'models/Payment.php';
require_once 'models/Table.php';

class AdminController {

    public function __construct() {
        if(!isLoggedIn() || !isAdmin()) {
            redirect(BASE_URL);
        }
    }

    public function dashboard() {
        $userModel = new User();
        $foodModel = new Food();
        $reservationModel = new Reservation();
        $orderModel = new Order();
        $tableModel = new Table();

        $totalUsers = count($userModel->all());
        $totalFoods = count($foodModel->all());
        $totalReservations = count($reservationModel->all());
        $totalOrders = count($orderModel->all());
        $totalTables = count($tableModel->all());
        
        // Get current month revenue
        $monthStart = date('Y-m-01');
        $monthEnd = date('Y-m-d');
        $monthlyRevenue = $orderModel->getTotalRevenue($monthStart, $monthEnd);

        require 'views/admin/dashboard.php';
    }

    public function users() {
        $model = new User();
        $users = $model->all();
        require 'views/admin/users.php';
    }

    public function foods() {
        $model = new Food();
        $foods = $model->all();
        require 'views/admin/foods.php';
    }

    public function addFood() {
        $categoryModel = new Category();
        $categories = $categoryModel->all();
        require 'views/admin/add-food.php';
    }

    public function storeFood() {
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . 'index.php?act=admin-add-food');
        }

        $name = sanitize($_POST['name'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $category_id = intval($_POST['category_id'] ?? 0);

        if(empty($name) || $price <= 0) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin!';
            redirect(BASE_URL . 'index.php?act=admin-add-food');
        }

        $image = '';
        if(isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
            $image = $this->uploadImage($_FILES['image']);
        }

        $data = [
            ':name' => $name,
            ':price' => $price,
            ':image' => $image,
            ':category_id' => $category_id,
            ':status' => 'active'
        ];

        $model = new Food();
        if($model->create($data)) {
            $_SESSION['success'] = 'Thêm món ăn thành công!';
            redirect(BASE_URL . 'index.php?act=admin-foods');
        } else {
            $_SESSION['error'] = 'Thêm món ăn thất bại!';
            redirect(BASE_URL . 'index.php?act=admin-add-food');
        }
    }

    public function editFood() {
        $food_id = intval($_GET['id'] ?? 0);
        if(!$food_id) {
            redirect(BASE_URL . 'index.php?act=admin-foods');
        }

        $foodModel = new Food();
        $food = $foodModel->findById($food_id);

        if(!$food) {
            $_SESSION['error'] = 'Món ăn không tồn tại!';
            redirect(BASE_URL . 'index.php?act=admin-foods');
        }

        $categoryModel = new Category();
        $categories = $categoryModel->all();

        require 'views/admin/edit-food.php';
    }

    public function updateFood() {
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . 'index.php?act=admin-foods');
        }

        $food_id = intval($_POST['id'] ?? 0);
        $name = sanitize($_POST['name'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $category_id = intval($_POST['category_id'] ?? 0);

        if(!$food_id || empty($name) || $price <= 0) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin!';
            redirect(BASE_URL . 'index.php?act=admin-edit-food&id=' . $food_id);
        }

        $foodModel = new Food();
        $food = $foodModel->findById($food_id);
        if(!$food) {
            $_SESSION['error'] = 'Món ăn không tồn tại!';
            redirect(BASE_URL . 'index.php?act=admin-foods');
        }

        $image = $food['image'];
        if(isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
            $image = $this->uploadImage($_FILES['image']);
        }

        $data = [
            ':name' => $name,
            ':price' => $price,
            ':image' => $image,
            ':category_id' => $category_id,
            ':status' => $_POST['status'] ?? 'active'
        ];

        if($foodModel->update($food_id, $data)) {
            $_SESSION['success'] = 'Cập nhật món ăn thành công!';
            redirect(BASE_URL . 'index.php?act=admin-foods');
        } else {
            $_SESSION['error'] = 'Cập nhật món ăn thất bại!';
            redirect(BASE_URL . 'index.php?act=admin-edit-food&id=' . $food_id);
        }
    }

    public function deleteFood() {
        $food_id = intval($_GET['id'] ?? 0);
        if(!$food_id) {
            redirect(BASE_URL . 'index.php?act=admin-foods');
        }

        $foodModel = new Food();
        if($foodModel->delete($food_id)) {
            $_SESSION['success'] = 'Xóa món ăn thành công!';
        } else {
            $_SESSION['error'] = 'Xóa món ăn thất bại!';
        }

        redirect(BASE_URL . 'index.php?act=admin-foods');
    }

    public function reservations() {
        $reservationModel = new Reservation();
        $tableModel = new Table();
        
        // Tìm kiếm
        $search = sanitize($_GET['search'] ?? '');
        $searchType = sanitize($_GET['search_type'] ?? 'phone'); // phone hoặc name
        
        if(!empty($search)) {
            if($searchType === 'name') {
                $reservations = $reservationModel->searchByCustomerName($search);
            } else {
                $reservations = $reservationModel->searchByPhone($search);
            }
        } else {
            $reservations = $reservationModel->allWithTable();
        }
        
        $tables = $tableModel->all();
        
        require 'views/admin/reservations.php';
    }

    public function updateReservation() {
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . 'index.php?act=admin-reservations');
        }

        $reservation_id = intval($_POST['id'] ?? 0);
        $status = sanitize($_POST['status'] ?? 'pending');
        $table_id = intval($_POST['table_id'] ?? 0);

        if(!$reservation_id) {
            redirect(BASE_URL . 'index.php?act=admin-reservations');
        }

        $reservationModel = new Reservation();
        $tableModel = new Table();
        
        // Lấy thông tin đặt bàn cũ
        $oldReservation = $reservationModel->findById($reservation_id);
        $oldTableId = $oldReservation['table_id'] ?? null;
        
        $data = [];
        
        // Cập nhật status
        if($status !== ($oldReservation['status'] ?? '')) {
            $data[':status'] = $status;
        }
        
        // Cập nhật table_id nếu có chọn bàn
        if($table_id > 0 && $table_id != $oldTableId) {
            $data[':table_id'] = $table_id;
            
            // Giải phóng bàn cũ nếu có
            if($oldTableId && $oldTableId > 0) {
                $tableModel->update($oldTableId, [':status' => 'available']);
            }
            
            // Chiếm bàn mới
            $newTableStatus = ($status === 'confirmed' || $status === 'pending' || $status === 'checkin') ? 'occupied' : 'available';
            $tableModel->update($table_id, [':status' => $newTableStatus]);
        } else if($table_id > 0 && isset($data[':status'])) {
            // Cập nhật status bàn khi trạng thái đặt bàn thay đổi
            $tableStatus = 'available';
            if($data[':status'] === 'confirmed' || $data[':status'] === 'pending' || $data[':status'] === 'checkin') {
                $tableStatus = 'occupied';
            }
            $tableModel->update($table_id, [':status' => $tableStatus]);
        }

        if(!empty($data)) {
            if($reservationModel->update($reservation_id, $data)) {
                $_SESSION['success'] = 'Cập nhật đặt bàn thành công!';
            } else {
                $_SESSION['error'] = 'Cập nhật đặt bàn thất bại!';
            }
        }

        redirect(BASE_URL . 'index.php?act=admin-reservations');
    }

    public function manageTables() {
        if(!isLoggedIn() || !isAdmin()) {
            redirect(BASE_URL);
        }

        $reservation_id = intval($_GET['id'] ?? 0);
        if(!$reservation_id) {
            redirect(BASE_URL . 'index.php?act=admin-reservations');
        }

        $reservationModel = new Reservation();
        $tableModel = new Table();

        $reservation = $reservationModel->findById($reservation_id);
        if(!$reservation) {
            $_SESSION['error'] = 'Đặt bàn không tồn tại!';
            redirect(BASE_URL . 'index.php?act=admin-reservations');
        }

        // Lấy bàn đã gán cho đặt bàn này
        $assignedTables = $reservationModel->getTablesByReservation($reservation_id);
        
        // Lấy danh sách bàn trống
        $availableTables = $tableModel->all();

        require 'views/admin/manage-tables.php';
    }

    public function assignTables() {
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . 'index.php?act=admin-reservations');
        }

        $reservation_id = intval($_POST['reservation_id'] ?? 0);
        $table_ids = $_POST['table_ids'] ?? [];

        if(!$reservation_id) {
            $_SESSION['error'] = 'Đặt bàn không hợp lệ!';
            redirect(BASE_URL . 'index.php?act=admin-reservations');
        }

        $reservationModel = new Reservation();
        $tableModel = new Table();
        $reservation = $reservationModel->findById($reservation_id);

        if(!$reservation) {
            $_SESSION['error'] = 'Đặt bàn không tồn tại!';
            redirect(BASE_URL . 'index.php?act=admin-reservations');
        }

        // Xóa tất cả bàn cũ
        $reservationModel->clearTables($reservation_id);

        // Tính tổng sức chứa
        $total_capacity = 0;
        $table_numbers = [];

        if(!empty($table_ids)) {
            foreach($table_ids as $table_id) {
                $table_id = intval($table_id);
                if($table_id > 0) {
                    // Gán bàn mới
                    $reservationModel->assignTable($reservation_id, $table_id);
                    
                    // Cập nhật status bàn thành occupied
                    $table = $tableModel->findById($table_id);
                    if($table) {
                        $tableModel->update($table_id, [':status' => 'occupied']);
                        $total_capacity += $table['capacity'];
                        $table_numbers[] = $table['table_number'];
                    }
                }
            }
        }

        $_SESSION['success'] = 'Gán bàn thành công! (' . count($table_numbers) . ' bàn, sức chứa: ' . $total_capacity . ' khách)';
        redirect(BASE_URL . 'index.php?act=admin-manage-tables&id=' . $reservation_id);
    }

    public function removeTableAssignment() {
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . 'index.php?act=admin-reservations');
        }

        $reservation_id = intval($_POST['reservation_id'] ?? 0);
        $table_id = intval($_POST['table_id'] ?? 0);

        if(!$reservation_id || !$table_id) {
            redirect(BASE_URL . 'index.php?act=admin-reservations');
        }

        $reservationModel = new Reservation();
        $tableModel = new Table();

        // Xóa bàn khỏi đặt bàn
        $reservationModel->removeTable($reservation_id, $table_id);

        // Cập nhật status bàn thành available
        $tableModel->update($table_id, [':status' => 'available']);

        $_SESSION['success'] = 'Xóa bàn thành công!';
        redirect(BASE_URL . 'index.php?act=admin-manage-tables&id=' . $reservation_id);
    }

    public function orders() {
        $model = new Order();
        $orders = $model->all();
        require 'views/admin/orders.php';
    }

    public function updateOrder() {
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . 'index.php?act=admin-orders');
        }

        $order_id = intval($_POST['id'] ?? 0);
        $status = sanitize($_POST['status'] ?? 'pending');

        if(!$order_id) {
            redirect(BASE_URL . 'index.php?act=admin-orders');
        }

        $model = new Order();
        $data = [
            ':status' => $status,
            ':total_price' => floatval($_POST['total_price'] ?? 0)
        ];

        if($model->update($order_id, $data)) {
            $_SESSION['success'] = 'Cập nhật trạng thái đơn hàng thành công!';
        } else {
            $_SESSION['error'] = 'Cập nhật trạng thái đơn hàng thất bại!';
        }

        redirect(BASE_URL . 'index.php?act=admin-orders');
    }

    public function revenue() {
        if(!isLoggedIn() || !isAdmin()) {
            redirect(BASE_URL);
        }

        $orderModel = new Order();
        
        // Lấy date range từ request
        $from_date = sanitize($_GET['from_date'] ?? '');
        $to_date = sanitize($_GET['to_date'] ?? '');
        
        // Nếu không có date range, lấy tháng hiện tại
        if(empty($from_date)) {
            $from_date = date('Y-m-01');
        }
        if(empty($to_date)) {
            $to_date = date('Y-m-d');
        }

        // Lấy các dữ liệu cần thiết
        $totalRevenue = $orderModel->getTotalRevenue($from_date, $to_date);
        $revenueByDate = $orderModel->getRevenueByDate($from_date, $to_date);
        $revenueStatistics = $orderModel->getRevenueStatistics($from_date, $to_date);
        $completedOrders = $orderModel->getCompletedOrders($from_date, $to_date);
        
        // Lấy tất cả tháng
        $revenueByMonth = $orderModel->getRevenueByMonth();

        require 'views/admin/revenue.php';
    }

    public function categories() {
        $model = new Category();
        $categories = $model->all();
        require 'views/admin/categories.php';
    }

    public function addCategory() {
        require 'views/admin/add-category.php';
    }

    public function storeCategory() {
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . 'index.php?act=admin-categories');
        }

        $name = sanitize($_POST['name'] ?? '');

        if(empty($name)) {
            $_SESSION['error'] = 'Vui lòng nhập tên danh mục!';
            redirect(BASE_URL . 'index.php?act=admin-add-category');
        }

        $model = new Category();
        $data = [':name' => $name];

        if($model->create($data)) {
            $_SESSION['success'] = 'Thêm danh mục thành công!';
            redirect(BASE_URL . 'index.php?act=admin-categories');
        } else {
            $_SESSION['error'] = 'Thêm danh mục thất bại!';
            redirect(BASE_URL . 'index.php?act=admin-add-category');
        }
    }

    public function editCategory() {
        $category_id = intval($_GET['id'] ?? 0);
        if(!$category_id) {
            redirect(BASE_URL . 'index.php?act=admin-categories');
        }

        $model = new Category();
        $category = $model->findById($category_id);

        if(!$category) {
            $_SESSION['error'] = 'Danh mục không tồn tại!';
            redirect(BASE_URL . 'index.php?act=admin-categories');
        }

        require 'views/admin/edit-category.php';
    }

    public function updateCategory() {
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . 'index.php?act=admin-categories');
        }

        $category_id = intval($_POST['id'] ?? 0);
        $name = sanitize($_POST['name'] ?? '');

        if(!$category_id || empty($name)) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin!';
            redirect(BASE_URL . 'index.php?act=admin-edit-category&id=' . $category_id);
        }

        $model = new Category();
        $data = [':name' => $name];

        if($model->update($category_id, $data)) {
            $_SESSION['success'] = 'Cập nhật danh mục thành công!';
            redirect(BASE_URL . 'index.php?act=admin-categories');
        } else {
            $_SESSION['error'] = 'Cập nhật danh mục thất bại!';
            redirect(BASE_URL . 'index.php?act=admin-edit-category&id=' . $category_id);
        }
    }

    public function deleteCategory() {
        $category_id = intval($_GET['id'] ?? 0);
        if(!$category_id) {
            redirect(BASE_URL . 'index.php?act=admin-categories');
        }

        $model = new Category();
        if($model->delete($category_id)) {
            $_SESSION['success'] = 'Xóa danh mục thành công!';
        } else {
            $_SESSION['error'] = 'Xóa danh mục thất bại!';
        }

        redirect(BASE_URL . 'index.php?act=admin-categories');
    }

    public function tables() {
        $model = new Table();
        $tables = $model->all();
        require 'views/admin/tables.php';
    }

    public function addTable() {
        require 'views/admin/add-table.php';
    }

    public function storeTable() {
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . 'index.php?act=admin-tables');
        }

        $table_number = sanitize($_POST['table_number'] ?? '');
        $capacity = intval($_POST['capacity'] ?? 2);
        $status = sanitize($_POST['status'] ?? 'available');

        if(empty($table_number) || $capacity <= 0) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin!';
            redirect(BASE_URL . 'index.php?act=admin-add-table');
        }

        $model = new Table();
        $data = [
            ':table_number' => $table_number,
            ':capacity' => $capacity,
            ':status' => $status
        ];

        if($model->create($data)) {
            $_SESSION['success'] = 'Thêm bàn thành công!';
            redirect(BASE_URL . 'index.php?act=admin-tables');
        } else {
            $_SESSION['error'] = 'Thêm bàn thất bại!';
            redirect(BASE_URL . 'index.php?act=admin-add-table');
        }
    }

    public function editTable() {
        $table_id = intval($_GET['id'] ?? 0);
        if(!$table_id) {
            redirect(BASE_URL . 'index.php?act=admin-tables');
        }

        $model = new Table();
        $table = $model->findById($table_id);

        if(!$table) {
            $_SESSION['error'] = 'Bàn không tồn tại!';
            redirect(BASE_URL . 'index.php?act=admin-tables');
        }

        require 'views/admin/edit-table.php';
    }

    public function updateTable() {
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . 'index.php?act=admin-tables');
        }

        $table_id = intval($_POST['id'] ?? 0);
        $table_number = sanitize($_POST['table_number'] ?? '');
        $capacity = intval($_POST['capacity'] ?? 2);
        $status = sanitize($_POST['status'] ?? 'available');

        if(!$table_id || empty($table_number) || $capacity <= 0) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin!';
            redirect(BASE_URL . 'index.php?act=admin-edit-table&id=' . $table_id);
        }

        $model = new Table();
        $data = [
            ':table_number' => $table_number,
            ':capacity' => $capacity,
            ':status' => $status
        ];

        if($model->update($table_id, $data)) {
            $_SESSION['success'] = 'Cập nhật bàn thành công!';
            redirect(BASE_URL . 'index.php?act=admin-tables');
        } else {
            $_SESSION['error'] = 'Cập nhật bàn thất bại!';
            redirect(BASE_URL . 'index.php?act=admin-edit-table&id=' . $table_id);
        }
    }

    public function deleteTable() {
        $table_id = intval($_GET['id'] ?? 0);
        if(!$table_id) {
            redirect(BASE_URL . 'index.php?act=admin-tables');
        }

        $model = new Table();
        if($model->delete($table_id)) {
            $_SESSION['success'] = 'Xóa bàn thành công!';
        } else {
            $_SESSION['error'] = 'Xóa bàn thất bại!';
        }

        redirect(BASE_URL . 'index.php?act=admin-tables');
    }

    private function uploadImage($file) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($file["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($file["tmp_name"]);
        if($check === false) {
            return '';
        }

        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            return '';
        }

        $new_name = time() . '.' . $imageFileType;
        $target_file = $target_dir . $new_name;

        if(move_uploaded_file($file["tmp_name"], $target_file)) {
            return $new_name;
        }

        return '';
    }
}
