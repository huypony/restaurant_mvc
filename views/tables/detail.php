<?php require 'views/layouts/header.php'; ?>

<div class="container mt-4">
    <h2 class="mb-4">Chi Tiết Bàn #<?= $table['table_number'] ?></h2>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Thông Tin Bàn</h5>
                </div>
                <div class="card-body">
                    <p><strong>Số Bàn:</strong> <span class="h5">#<?= $table['table_number'] ?></span></p>
                    <p><strong>Sức Chứa:</strong> <?= $table['capacity'] ?> khách</p>
                    <p>
                        <strong>Trạng Thái:</strong>
                        <span class="badge bg-<?= ($table['status'] === 'available' || !$reservation) ? 'success' : 'danger' ?>">
                            <?= ($table['status'] === 'available' || !$reservation) ? '✅ Trống' : '🚫 Có Khách' ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Thông Tin Đặt Bàn Hiện Tại</h5>
                </div>
                <div class="card-body">
                    <?php if($reservation && $reservation['status'] !== 'cancelled') { ?>
                        <div class="alert alert-info">
                            <strong>✅ Bàn này đã được đặt</strong>
                        </div>
                        <p><strong>👤 Khách Hàng:</strong> <?= htmlspecialchars($reservation['customer_name']) ?></p>
                        <p><strong>📞 Số Điện Thoại:</strong> <a href="tel:<?= $reservation['customer_phone'] ?>"><?= $reservation['customer_phone'] ?></a></p>
                        <p><strong>👥 Số Khách:</strong> <?= $reservation['guest_count'] ?> người</p>
                        <p><strong>⏰ Thời Gian Đặt:</strong> <?= formatDate($reservation['reservation_time']) ?></p>
                        <p>
                            <strong>✓ Trạng Thái Đặt:</strong>
                            <span class="badge bg-<?= $reservation['status'] === 'confirmed' ? 'success' : 'warning' ?>">
                                <?= $reservation['status'] === 'confirmed' ? 'Xác Nhận' : 'Chờ Xác Nhận' ?>
                            </span>
                        </p>
                    <?php } else { ?>
                        <div class="alert alert-secondary text-center">
                            <strong>ℹ️ Bàn này hiện không có đặt bàn nào</strong>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Ghi Chú</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <strong>Badge Xanh (Trống):</strong> Bàn sẵn sàng đón khách
                        </li>
                        <li class="list-group-item">
                            <strong>Badge Đỏ (Có Khách):</strong> Bàn đang có khách
                        </li>
                        <li class="list-group-item">
                            <strong>Badge Vàng (Đã Đặt):</strong> Bàn được đặt sắp tới
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="<?= BASE_URL ?>index.php?act=tables-layout" class="btn btn-primary">← Quay Lại Sơ Đồ</a>
    </div>
</div>

<?php require 'views/layouts/footer.php'; ?>
