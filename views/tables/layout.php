<?php require 'views/layouts/header.php'; ?>

<div class="container mt-4">
    <h2 class="mb-4">🎯 Sơ Đồ Phòng</h2>

    <div class="mb-4 p-3 bg-light rounded">
        <p class="mb-0">
            <span class="badge bg-success me-2">Trống</span>
            <span class="badge bg-danger me-2">Có Khách</span>
            <span class="badge bg-warning">Đã Đặt</span>
        </p>
    </div>

    <div id="tableLayout" class="row g-3">
        <?php if(!empty($tables)) { ?>
            <?php 
            foreach($tables as $table) {
                // Xác định trạng thái bàn dựa trên đặt bàn của bàn này
                $status = $table['status'];
                $hasReservation = false;
                
                // Kiểm tra có đặt bàn trong hôm nay cho bàn này không
                $reservationsForTable = $reservationModel->getReservationsByTable($table['id']);
                
                if(!empty($reservationsForTable)) {
                    $res = $reservationsForTable[0]; // Lấy đặt bàn gần nhất cho bàn này
                    $resTime = strtotime($res['reservation_time']);
                    $now = time();
                    
                    if($now >= $resTime) {
                        $status = 'occupied';
                    } else {
                        $status = 'reserved';
                    }
                    $hasReservation = true;
                }
                
                $badgeClass = 'success';
                $statusText = 'Trống';
                if($status === 'occupied') {
                    $badgeClass = 'danger';
                    $statusText = 'Có Khách';
                } elseif($status === 'reserved') {
                    $badgeClass = 'warning';
                    $statusText = 'Đã Đặt';
                }
            ?>
                <div class="col-md-3 col-sm-4">
                    <a href="<?= BASE_URL ?>index.php?act=tables-detail&id=<?= $table['id'] ?>" class="text-decoration-none">
                        <div class="card text-center h-100 table-card" style="cursor: pointer; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                            <div class="card-body">
                                <h5 class="card-title">🪑 <?= $table['table_number'] ?></h5>
                                <p class="card-text text-muted small">
                                    <?= $table['capacity'] ?> khách
                                </p>
                                <p class="card-text">
                                    <span class="badge bg-<?= $badgeClass ?>">
                                        <?= $statusText ?>
                                    </span>
                                </p>
                            </div>
                            <div class="card-footer bg-light">
                                <small>Chi tiết →</small>
                            </div>
                        </div>
                    </a>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="col-12">
                <p class="text-center text-muted">Chưa có bàn nào được thêm vào hệ thống.</p>
            </div>
        <?php } ?>
    </div>

    <div class="mt-5">
        <div class="card">
            <div class="card-header">
                <h5>Đặt Bàn Sắp Tới</h5>
            </div>
            <div class="card-body">
                <?php 
                $upcoming = $reservationModel->getUpcomingReservations(10);
                if(!empty($upcoming)) {
                ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Khách Hàng</th>
                                    <th>Số Khách</th>
                                    <th>Thời Gian</th>
                                    <th>Trạng Thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($upcoming as $res) { ?>
                                    <tr>
                                        <td><?= $res['customer_name'] ?></td>
                                        <td><?= $res['guest_count'] ?></td>
                                        <td><?= formatDate($res['reservation_time']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $res['status'] === 'confirmed' ? 'success' : 'warning' ?>">
                                                <?= ucfirst($res['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } else { ?>
                    <p class="text-muted mb-0">Không có đặt bàn sắp tới</p>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<style>
.table-card {
    border: 2px solid #dee2e6;
    background-color: #fff;
}
.table-card:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}
</style>

<?php require 'views/layouts/footer.php'; ?>
