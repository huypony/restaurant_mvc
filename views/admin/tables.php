<?php require 'views/layouts/header.php'; ?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quản Lý Bàn</h2>
        <a href="<?= BASE_URL ?>index.php?act=admin-add-table" class="btn btn-primary">+ Thêm Bàn</a>
    </div>

    <?php if(!empty($tables)) { ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Số Bàn</th>
                        <th>Sức Chứa</th>
                        <th>Trạng Thái</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($tables as $table) { ?>
                        <tr>
                            <td><?= $table['id'] ?></td>
                            <td><strong><?= $table['table_number'] ?></strong></td>
                            <td><?= $table['capacity'] ?> khách</td>
                            <td>
                                <span class="badge bg-<?= $table['status'] === 'available' ? 'success' : 'warning' ?>">
                                    <?= ucfirst($table['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>index.php?act=admin-edit-table&id=<?= $table['id'] ?>" class="btn btn-sm btn-warning">Sửa</a>
                                <a href="<?= BASE_URL ?>index.php?act=admin-delete-table&id=<?= $table['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn chắc chắn?');">Xóa</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } else { ?>
        <p class="text-center text-muted">Chưa có bàn nào.</p>
    <?php } ?>

    <a href="<?= BASE_URL ?>index.php?act=admin-dashboard" class="btn btn-secondary mt-3">Quay Lại</a>
</div>

<?php require 'views/layouts/footer.php'; ?>
