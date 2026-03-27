<?php require 'views/layouts/header.php'; ?>

<div class="container">
    <h2 class="mb-4">Thêm Món Ăn</h2>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <form method="POST" action="<?= BASE_URL ?>index.php?act=admin-store-food" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Tên Món Ăn</label>
                    <input type="text" class="form-control" name="name" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Giá</label>
                    <input type="number" step="0.01" class="form-control" name="price" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Danh Mục</label>
                    <select class="form-control" name="category_id">
                        <option value="">--Chọn Danh Mục--</option>
                        <?php foreach($categories as $cat) { ?>
                            <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Hình Ảnh</label>
                    <input type="file" class="form-control" name="image" accept="image/*">
                </div>

                <button type="submit" class="btn btn-primary w-100">Thêm Món Ăn</button>
                <a href="<?= BASE_URL ?>index.php?act=admin-foods" class="btn btn-secondary w-100 mt-2">Quay Lại</a>
            </form>
        </div>
    </div>
</div>

<?php require 'views/layouts/footer.php'; ?>
