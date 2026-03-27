<?php require 'views/layouts/header.php'; ?>

<div class="container">
    <h2 class="mb-4">Bảng Điều Khiển Quản Lý</h2>

    <div class="row mb-5">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">👥 Người Dùng</h5>
                    <h2 class="text-primary"><?= $totalUsers ?></h2>
                    <a href="<?= BASE_URL ?>index.php?act=admin-users" class="btn btn-sm btn-primary">Xem Chi Tiết</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">🍽️ Món Ăn</h5>
                    <h2 class="text-success"><?= $totalFoods ?></h2>
                    <a href="<?= BASE_URL ?>index.php?act=admin-foods" class="btn btn-sm btn-success">Xem Chi Tiết</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">🪑 Bàn</h5>
                    <h2 class="text-secondary"><?= $totalTables ?? 0 ?></h2>
                    <a href="<?= BASE_URL ?>index.php?act=admin-tables" class="btn btn-sm btn-secondary">Xem Chi Tiết</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">📅 Đặt Bàn</h5>
                    <h2 class="text-warning"><?= $totalReservations ?></h2>
                    <a href="<?= BASE_URL ?>index.php?act=admin-reservations" class="btn btn-sm btn-warning">Xem Chi Tiết</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">📦 Đơn Hàng</h5>
                    <h2 class="text-info"><?= $totalOrders ?></h2>
                    <a href="<?= BASE_URL ?>index.php?act=admin-orders" class="btn btn-sm btn-info">Xem Chi Tiết</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Các Chức Năng Quản Lý</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="<?= BASE_URL ?>index.php?act=admin-foods" class="list-group-item list-group-item-action">
                            🍽️ Quản Lý Thực Đơn
                        </a>
                        <a href="<?= BASE_URL ?>index.php?act=admin-categories" class="list-group-item list-group-item-action">
                            📂 Quản Lý Danh Mục
                        </a>
                        <a href="<?= BASE_URL ?>index.php?act=admin-tables" class="list-group-item list-group-item-action">
                            🪑 Quản Lý Bàn
                        </a>
                        <a href="<?= BASE_URL ?>index.php?act=tables-layout" class="list-group-item list-group-item-action" target="_blank">
                            📊 Xem Sơ Đồ Phòng
                        </a>
                        <a href="<?= BASE_URL ?>index.php?act=admin-reservations" class="list-group-item list-group-item-action">
                            📅 Quản Lý Đặt Bàn
                        </a>
                        <a href="<?= BASE_URL ?>index.php?act=admin-orders" class="list-group-item list-group-item-action">
                            📦 Quản Lý Đơn Hàng
                        </a>
                        <a href="<?= BASE_URL ?>index.php?act=admin-users" class="list-group-item list-group-item-action">
                            👥 Quản Lý Người Dùng
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require 'views/layouts/footer.php'; ?>
