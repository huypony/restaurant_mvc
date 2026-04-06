<?php require 'views/layouts/header.php'; ?>

<div class="container">
    <h2 class="mb-4">Chi Tiết Đơn Hàng #<?= $order['id'] ?></h2>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Thông Tin Đơn Hàng</h5>
                </div>
                <div class="card-body">
                    <p><strong>Mã Đơn:</strong> #<?= $order['id'] ?></p>
                    <p><strong>Trạng Thái:</strong> <span class="badge bg-warning"><?= ucfirst($order['status']) ?></span></p>
                    <p><strong>Tổng Tiền:</strong> <strong><?= formatMoney($order['total_price']) ?></strong></p>
                    <p><strong>Ngày Tạo:</strong> <?= formatDate($order['created_at']) ?></p>
                    
                    <?php if($order['reservation_id']) { 
                        $reservationModel = new Reservation();
                        $tableList = $reservationModel->getTableNumbers($order['reservation_id']);
                    ?>
                        <p><strong>🪑 Bàn:</strong> <?= $tableList ?></p>
                    <?php } ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>Chi Tiết Món Ăn</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Món Ăn</th>
                                <th>Số Lượng</th>
                                <th>Giá</th>
                                <th>Thành Tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($details as $detail) { ?>
                                <tr>
                                    <td><?= $detail['name'] ?></td>
                                    <td><?= $detail['quantity'] ?></td>
                                    <td><?= formatMoney($detail['price']) ?></td>
                                    <td><?= formatMoney($detail['price'] * $detail['quantity']) ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Thanh Toán</h5>
                </div>
                <div class="card-body">
                    <?php if($payment) { ?>
                        <p><strong>Trạng Thái:</strong> <span class="badge bg-<?= $payment['payment_status'] === 'completed' ? 'success' : 'warning' ?>"><?= ucfirst($payment['payment_status']) ?></span></p>
                        <p><strong>Phương Thức:</strong> <?= $payment['payment_method'] ?></p>
                        <p><strong>Số Tiền:</strong> <?= formatMoney($payment['amount']) ?></p>
                        <?php if($payment['paid_at']) { ?>
                            <p><strong>Ngày Thanh Toán:</strong> <?= formatDate($payment['paid_at']) ?></p>
                        <?php } ?>
                    <?php } else { ?>
                        <p class="text-muted">Chưa có thanh toán</p>
                    <?php } ?>
                    
                    <a href="<?= BASE_URL ?>index.php?act=order-list" class="btn btn-secondary w-100 mt-3">Quay Lại</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require 'views/layouts/footer.php'; ?>
