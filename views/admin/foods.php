<?php require 'views/layouts/header.php'; ?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quản Lý Thực Đơn</h2>
        <a href="<?= BASE_URL ?>index.php?act=admin-add-food" class="btn btn-primary">+ Thêm Món Ăn</a>
    </div>

    <?php if(!empty($foods)) { ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tên Món</th>
                        <th>Giá</th>
                        <th>Danh Mục</th>
                        <th>Trạng Thái</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($foods as $food) { ?>
                        <tr>
                            <td><?= $food['id'] ?></td>
                            <td><?= $food['name'] ?></td>
                            <td><?= formatMoney($food['price']) ?></td>
                            <td><?= $food['category_name'] ?? 'N/A' ?></td>
                            <td>
                                <span class="badge bg-<?= $food['status'] === 'active' ? 'success' : 'danger' ?>">
                                    <?= ucfirst($food['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>index.php?act=admin-edit-food&id=<?= $food['id'] ?>" class="btn btn-sm btn-warning">Sửa</a>
                                <a href="<?= BASE_URL ?>index.php?act=admin-delete-food&id=<?= $food['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn chắc chắn?');">Xóa</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } else { ?>
        <p class="text-center text-muted">Chưa có món ăn nào.</p>
    <?php } ?>

    <a href="<?= BASE_URL ?>index.php?act=admin-dashboard" class="btn btn-secondary mt-3">Quay Lại</a>
</div>

<?php require 'views/layouts/footer.php'; ?>
