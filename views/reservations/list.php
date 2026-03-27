<?php require 'views/layouts/header.php'; ?>

<div class="container">
    <h2 class="mb-4">Danh Sách Đặt Bàn</h2>

    <div class="mb-3">
        <a href="<?= BASE_URL ?>index.php?act=reservation-create" class="btn btn-primary">Đặt Bàn Mới</a>
    </div>

    <?php if(!empty($reservations)) { ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Họ Tên</th>
                        <th>Điện Thoại</th>
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
                            <td><?= $res['customer_name'] ?></td>
                            <td><?= $res['customer_phone'] ?></td>
                            <td><?= formatDate($res['reservation_time']) ?></td>
                            <td><?= $res['guest_count'] ?></td>
                            <td>
                                <span class="badge bg-<?= $res['status'] === 'confirmed' ? 'success' : ($res['status'] === 'cancelled' ? 'danger' : 'warning') ?>">
                                    <?= ucfirst($res['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>index.php?act=order-create&reservation_id=<?= $res['id'] ?>" class="btn btn-sm btn-info">Tạo Đơn</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } else { ?>
        <p class="text-center text-muted">Chưa có đặt bàn nào.</p>
    <?php } ?>
</div>

<?php require 'views/layouts/footer.php'; ?>
