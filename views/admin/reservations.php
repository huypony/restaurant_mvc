<?php require 'views/layouts/header.php'; ?>

<div class="container">
    <h2 class="mb-4">Quản Lý Đặt Bàn</h2>

    <!-- Form Tìm Kiếm -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="<?= BASE_URL ?>index.php?act=admin-reservations" class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" 
                           value="<?= isset($search) ? htmlspecialchars($search) : '' ?>" 
                           placeholder="Nhập thông tin tìm kiếm...">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="search_type">
                        <option value="phone" <?= isset($searchType) && $searchType === 'phone' ? 'selected' : '' ?>>Tìm theo SĐT</option>
                        <option value="name" <?= isset($searchType) && $searchType === 'name' ? 'selected' : '' ?>>Tìm theo Tên</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">🔍 Tìm Kiếm</button>
                </div>
                <div class="col-md-3">
                    <a href="<?= BASE_URL ?>index.php?act=admin-reservations" class="btn btn-secondary w-100">🔄 Reset</a>
                </div>
            </form>
        </div>
    </div>

    <?php if(!empty($reservations)) { ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tên Khách</th>
                        <th>Điện Thoại</th>
                        <th>🪑 Bàn</th>
                        <th>Thời Gian</th>
                        <th>Số Khách</th>
                        <th>Trạng Thái</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($reservations as $res) { ?>
                        <tr>
                            <td><?= $res['id'] ?></td>
                            <td><?= htmlspecialchars($res['customer_name']) ?></td>
                            <td>
                                <a href="<?= BASE_URL ?>index.php?act=admin-reservations&search=<?= urlencode($res['customer_phone']) ?>&search_type=phone">
                                    <?= htmlspecialchars($res['customer_phone']) ?>
                                </a>
                            </td>
                            <td>
                                <form method="POST" action="<?= BASE_URL ?>index.php?act=admin-update-reservation" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $res['id'] ?>">
                                    <input type="hidden" name="status" value="<?= htmlspecialchars($res['status']) ?>">
                                    <select name="table_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <option value="">-- Chọn Bàn --</option>
                                        <?php foreach($tables as $table) { 
                                            $isSelected = isset($res['table_id']) && $res['table_id'] == $table['id'] ? 'selected' : '';
                                            $statusBadge = ucfirst($table['status'] ?? 'available');
                                        ?>
                                            <option value="<?= $table['id'] ?>" <?= $isSelected ?>>
                                                Bàn <?= htmlspecialchars($table['table_number']) ?> (<?= $statusBadge ?>)
                                            </option>
                                        <?php } ?>
                                    </select>
                                </form>
                            </td>
                            <td><?= formatDate($res['reservation_time']) ?></td>
                            <td><?= htmlspecialchars($res['guest_count']) ?></td>
                            <td>
                                <form method="POST" action="<?= BASE_URL ?>index.php?act=admin-update-reservation" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $res['id'] ?>">
                                    <input type="hidden" name="table_id" value="<?= $res['table_id'] ?? 0 ?>">
                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <option value="pending" <?= $res['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="confirmed" <?= $res['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                        <option value="cancelled" <?= $res['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>index.php?act=order-create&reservation_id=<?= $res['id'] ?>" class="btn btn-sm btn-info">📋</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            <p class="text-muted">
                <?php if(!empty($search)) { ?>
                    Tìm thấy <strong><?= count($reservations) ?></strong> kết quả cho "<strong><?= htmlspecialchars($search) ?></strong>"
                <?php } else { ?>
                    Tổng cộng <strong><?= count($reservations) ?></strong> đặt bàn
                <?php } ?>
            </p>
        </div>
    <?php } else { ?>
        <div class="alert alert-info text-center">
            <?php if(!empty($search)) { ?>
                Không tìm thấy kết quả cho "<strong><?= htmlspecialchars($search) ?></strong>"
            <?php } else { ?>
                Chưa có đặt bàn nào.
            <?php } ?>
        </div>
    <?php } ?>

    <a href="<?= BASE_URL ?>index.php?act=admin-dashboard" class="btn btn-secondary mt-3">← Quay Lại</a>
</div>

<?php require 'views/layouts/footer.php'; ?>
