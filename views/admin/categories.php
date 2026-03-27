<?php require 'views/layouts/header.php'; ?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quản Lý Danh Mục</h2>
        <a href="<?= BASE_URL ?>index.php?act=admin-add-category" class="btn btn-primary">+ Thêm Danh Mục</a>
    </div>

    <?php if(!empty($categories)) { ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tên Danh Mục</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($categories as $cat) { ?>
                        <tr>
                            <td><?= $cat['id'] ?></td>
                            <td><?= $cat['name'] ?></td>
                            <td>
                                <a href="<?= BASE_URL ?>index.php?act=admin-edit-category&id=<?= $cat['id'] ?>" class="btn btn-sm btn-warning">Sửa</a>
                                <a href="<?= BASE_URL ?>index.php?act=admin-delete-category&id=<?= $cat['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn chắc chắn?');">Xóa</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } else { ?>
        <p class="text-center text-muted">Chưa có danh mục nào.</p>
    <?php } ?>

    <a href="<?= BASE_URL ?>index.php?act=admin-dashboard" class="btn btn-secondary mt-3">Quay Lại</a>
</div>

<?php require 'views/layouts/footer.php'; ?>
