<?php require 'views/layouts/header.php'; ?>

<div class="container">
    <h2 class="mb-4">Thêm Danh Mục</h2>

    <div class="row justify-content-center">
        <div class="col-md-5">
            <form method="POST" action="<?= BASE_URL ?>index.php?act=admin-store-category">
                <div class="mb-3">
                    <label class="form-label">Tên Danh Mục</label>
                    <input type="text" class="form-control" name="name" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Thêm Danh Mục</button>
                <a href="<?= BASE_URL ?>index.php?act=admin-categories" class="btn btn-secondary w-100 mt-2">Quay Lại</a>
            </form>
        </div>
    </div>
</div>

<?php require 'views/layouts/footer.php'; ?>
