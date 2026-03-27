<?php require 'views/layouts/header.php'; ?>

<div class="container">
    <h2 class="mb-4">Trang Chủ</h2>

    <div class="row mb-5">
        <div class="col-md-12">
            <div class="jumbotron bg-light p-5 rounded">
                <h1 class="display-4">🍽️ Nhà Hàng Đặt Bàn</h1>
                <p class="lead">Chào mừng đến với ứng dụng đặt bàn nhà hàng trực tuyến</p>
                <?php if(!isLoggedIn()) { ?>
                    <a class="btn btn-primary btn-lg" href="<?= BASE_URL ?>index.php?act=login">Đăng Nhập Để Bắt Đầu</a>
                <?php } else { ?>
                    <a class="btn btn-primary btn-lg" href="<?= BASE_URL ?>index.php?act=menu">Xem Thực Đơn</a>
                    <a class="btn btn-secondary btn-lg" href="<?= BASE_URL ?>index.php?act=reservation-create">Đặt Bàn Ngay</a>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card text-center h-100">
                <div class="card-body">
                    <h5 class="card-title">📱 Dễ Dàng</h5>
                    <p class="card-text">Đặt bàn chỉ trong vài click</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card text-center h-100">
                <div class="card-body">
                    <h5 class="card-title">⏰ Nhanh Chóng</h5>
                    <p class="card-text">Xác nhận ngay lập tức</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card text-center h-100">
                <div class="card-body">
                    <h5 class="card-title">🎉 Tiện Lợi</h5>
                    <p class="card-text">Quản lý đặt bàn dễ dàng</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require 'views/layouts/footer.php'; ?>
