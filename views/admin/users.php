<?php require 'views/layouts/header.php'; ?>

<div class="container">
    <h2 class="mb-4">Quản Lý Người Dùng</h2>

    <?php if(!empty($users)) { ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tên</th>
                        <th>Email</th>
                        <th>Vai Trò</th>
                        <th>Ngày Tạo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $user) { ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= $user['name'] ?></td>
                            <td><?= $user['email'] ?></td>
                            <td>
                                <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : 'primary' ?>">
                                    <?= ucfirst($user['role']) ?>
                                </span>
                            </td>
                            <td><?= formatDate($user['created_at']) ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } else { ?>
        <p class="text-center text-muted">Chưa có người dùng nào.</p>
    <?php } ?>

    <a href="<?= BASE_URL ?>index.php?act=admin-dashboard" class="btn btn-secondary mt-3">Quay Lại</a>
</div>

<?php require 'views/layouts/footer.php'; ?>
