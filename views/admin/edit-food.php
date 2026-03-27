<?php require 'views/layouts/header.php'; ?>

<div class="container">
    <h2 class="mb-4">Sửa Món Ăn</h2>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <form method="POST" action="<?= BASE_URL ?>index.php?act=admin-update-food" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $food['id'] ?>">

                <div class="mb-3">
                    <label class="form-label">Tên Món Ăn</label>
                    <input type="text" class="form-control" name="name" value="<?= $food['name'] ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Giá</label>
                    <input type="number" step="0.01" class="form-control" name="price" value="<?= $food['price'] ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Danh Mục</label>
                    <select class="form-control" name="category_id">
                        <option value="">--Chọn Danh Mục--</option>
                        <?php foreach($categories as $cat) { ?>
                            <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $food['category_id'] ? 'selected' : '' ?>>
                                <?= $cat['name'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Trạng Thái</label>
                    <select class="form-control" name="status">
                        <option value="active" <?= $food['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= $food['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Hình Ảnh</label>
                    <?php if(!empty($food['image'])) { ?>
                        <div class="mb-2">
                            <img src="<?= BASE_URL ?>uploads/<?= $food['image'] ?>" style="max-width: 200px;">
                        </div>
                    <?php } ?>
                    <input type="file" class="form-control" name="image" accept="image/*">
                </div>

                <button type="submit" class="btn btn-primary w-100">Cập Nhật</button>
                <a href="<?= BASE_URL ?>index.php?act=admin-foods" class="btn btn-secondary w-100 mt-2">Quay Lại</a>
            </form>
        </div>
    </div>
</div>

<?php require 'views/layouts/footer.php'; ?>
