<?php require 'views/layouts/header.php'; ?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quản Lý Đơn Hàng</h2>
        <a href="<?= BASE_URL ?>index.php?act=admin-revenue" class="btn btn-success">📊 Xem Doanh Thu</a>
    </div>

    <?php if(!empty($orders)) { ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Khách Hàng</th>
                        <th>🪑 Bàn</th>
                        <th>Số Khách</th>
                        <th>Tổng Tiền</th>
                        <th>Trạng Thái</th>
                        <th>Ngày Tạo</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($orders as $order) { 
                        // Lấy danh sách bàn nếu có reservation_id
                        $tableList = 'N/A';
                        if($order['reservation_id']) {
                            $reservationModel = new Reservation();
                            $tableList = $reservationModel->getTableNumbers($order['reservation_id']);
                        }
                    ?>
                        <tr>
                            <td><?= $order['id'] ?></td>
                            <td><?= htmlspecialchars($order['customer_name'] ?? 'N/A') ?></td>
                            <td><?= $tableList ?></td>
                            <td><?= $order['guest_count'] ?? 'N/A' ?></td>
                            <td><?= formatMoney($order['total_price']) ?></td>
                            <td>
                                <form method="POST" action="<?= BASE_URL ?>index.php?act=admin-update-order" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $order['id'] ?>">
                                    <input type="hidden" name="total_price" value="<?= $order['total_price'] ?>">
                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
                                        <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                                        <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                    </select>
                                </form>
                            </td>
                            <td><?= formatDate($order['created_at']) ?></td>
                            <td>
                                <a href="<?= BASE_URL ?>index.php?act=order-detail&id=<?= $order['id'] ?>" class="btn btn-sm btn-info">Chi Tiết</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } else { ?>
        <p class="text-center text-muted">Chưa có đơn hàng nào.</p>
    <?php } ?>

    <a href="<?= BASE_URL ?>index.php?act=admin-dashboard" class="btn btn-secondary mt-3">Quay Lại</a>
</div>

<?php require 'views/layouts/footer.php'; ?>
