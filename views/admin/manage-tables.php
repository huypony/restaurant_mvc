<?php require 'views/layouts/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <h2 class="mb-4">Ghép Bàn - Đặt Bàn #<?= $reservation['id'] ?></h2>

            <!-- Thông tin đặt bàn -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5>Thông Tin Đặt Bàn</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Khách Hàng:</strong> <?= htmlspecialchars($reservation['customer_name']) ?></p>
                            <p><strong>Số Điện Thoại:</strong> <?= htmlspecialchars($reservation['customer_phone']) ?></p>
                            <p><strong>Số Khách:</strong> <?= $reservation['guest_count'] ?> người</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Thời Gian Đặt:</strong> <?= formatDate($reservation['reservation_time']) ?></p>
                            <p><strong>Trạng Thái:</strong> <span class="badge bg-warning"><?= ucfirst($reservation['status']) ?></span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bàn đã gán -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5>Bàn Đã Gán (<?= count($assignedTables) ?>)</h5>
                </div>
                <div class="card-body">
                    <?php if(!empty($assignedTables)) { 
                        $totalCapacity = 0;
                    ?>
                        <div class="row">
                            <?php foreach($assignedTables as $assigned) {
                                $totalCapacity += $assigned['capacity'];
                            ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card border-success">
                                        <div class="card-body">
                                            <h5 class="card-title">Bàn <?= htmlspecialchars($assigned['table_number']) ?></h5>
                                            <p class="card-text mb-2">
                                                <strong>Sức Chứa:</strong> <?= $assigned['capacity'] ?> khách
                                            </p>
                                            <form method="POST" action="<?= BASE_URL ?>index.php?act=admin-remove-table" style="display:inline;">
                                                <input type="hidden" name="reservation_id" value="<?= $reservation['id'] ?>">
                                                <input type="hidden" name="table_id" value="<?= $assigned['table_id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Xóa bàn này?')">❌ Xóa</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>

                        <div class="alert alert-info mt-3">
                            <strong>Tổng Sức Chứa:</strong> <?= $totalCapacity ?> khách 
                            <?php if($totalCapacity >= $reservation['guest_count']) { ?>
                                <span class="badge bg-success">✓ Đủ chỗ</span>
                            <?php } else { ?>
                                <span class="badge bg-warning">⚠ Chưa đủ (cần thêm <?= $reservation['guest_count'] - $totalCapacity ?> khách)</span>
                            <?php } ?>
                        </div>
                    <?php } else { ?>
                        <p class="text-muted">Chưa có bàn nào được gán.</p>
                    <?php } ?>
                </div>
            </div>

            <!-- Form chọn bàn -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5>Chọn Bàn Thêm</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= BASE_URL ?>index.php?act=admin-assign-tables">
                        <input type="hidden" name="reservation_id" value="<?= $reservation['id'] ?>">
                        
                        <div class="mb-3">
                            <label class="form-label"><strong>Danh Sách Bàn Trống</strong></label>
                            <div class="row" style="max-height: 400px; overflow-y: auto;">
                                <?php 
                                $availableCount = 0;
                                foreach($availableTables as $table) { 
                                    // Kiểm tra xem bàn này đã được gán chưa
                                    $isAssigned = false;
                                    foreach($assignedTables as $assigned) {
                                        if($assigned['table_id'] == $table['id']) {
                                            $isAssigned = true;
                                            break;
                                        }
                                    }

                                    if(!$isAssigned && $table['status'] === 'available') {
                                        $availableCount++;
                                ?>
                                    <div class="col-md-4 mb-3">
                                        <div class="card" style="cursor: pointer; transition: all 0.3s;">
                                            <div class="card-body">
                                                <div class="form-check">
                                                    <input class="form-check-input table-checkbox" type="checkbox" name="table_ids[]" value="<?= $table['id'] ?>" id="table_<?= $table['id'] ?>">
                                                    <label class="form-check-label" for="table_<?= $table['id'] ?>" style="cursor: pointer; width: 100%;">
                                                        <strong>Bàn <?= htmlspecialchars($table['table_number']) ?></strong>
                                                        <br>
                                                        <small class="text-muted">Sức chứa: <?= $table['capacity'] ?> khách</small>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                    }
                                } 

                                if($availableCount === 0) {
                                    echo '<p class="text-muted">Không có bàn trống nào.</p>';
                                }
                                ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-success btn-lg w-100">✓ Xác Nhận Gán Bàn</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header bg-info text-white">
                    <h5>Hướng Dẫn</h5>
                </div>
                <div class="card-body">
                    <p><strong>Các Bước:</strong></p>
                    <ol>
                        <li>Chọn bàn trống từ danh sách bên trái</li>
                        <li>Bàn sẽ được thêm vào "Bàn Đã Gán"</li>
                        <li>Kiểm tra tổng sức chứa >= số khách</li>
                        <li>Click "Xác Nhận Gán Bàn" để lưu</li>
                    </ol>

                    <hr>

                    <p><strong>Lưu Ý:</strong></p>
                    <ul>
                        <li>Có thể gán nhiều bàn cho 1 đặt bàn</li>
                        <li>Chỉ hiển thị bàn trống</li>
                        <li>Tổng sức chứa phải >= số khách đặt</li>
                    </ul>

                    <hr>

                    <a href="<?= BASE_URL ?>index.php?act=admin-reservations" class="btn btn-secondary w-100">← Quay Lại</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require 'views/layouts/footer.php'; ?>
