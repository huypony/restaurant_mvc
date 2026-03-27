<?php require 'views/layouts/header.php'; ?>

<div class="container">
    <h2 class="mb-4">Danh Sách Đơn Hàng</h2>

    <?php if(!empty($orders)) { ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tổng Tiền</th>
                        <th>Trạng Thái</th>
                        <th>Ngày Tạo</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($orders as $order) { ?>
                        <tr>
                            <td><?= $order['id'] ?></td>
                            <td><?= formatMoney($order['total_price']) ?></td>
                            <td>
                                <span class="badge bg-<?= $order['status'] === 'completed' ? 'success' : ($order['status'] === 'cancelled' ? 'danger' : 'warning') ?>">
                                    <?= ucfirst($order['status']) ?>
                                </span>
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
</div>

<?php require 'views/layouts/footer.php'; ?>
