<?php require 'views/layouts/header.php'; ?>

<div class="container">
    <h2 class="mb-4">Sửa Bàn</h2>

    <div class="row justify-content-center">
        <div class="col-md-5">
            <form method="POST" action="<?= BASE_URL ?>index.php?act=admin-update-table">
                <input type="hidden" name="id" value="<?= $table['id'] ?>">

                <div class="mb-3">
                    <label class="form-label">Số Bàn</label>
                    <input type="text" class="form-control" name="table_number" value="<?= $table['table_number'] ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Sức Chứa (Số Lượng Khách)</label>
                    <input type="number" class="form-control" name="capacity" min="1" value="<?= $table['capacity'] ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Trạng Thái</label>
                    <select class="form-control" name="status">
                        <option value="available" <?= $table['status'] === 'available' ? 'selected' : '' ?>>Trống</option>
                        <option value="occupied" <?= $table['status'] === 'occupied' ? 'selected' : '' ?>>Có Khách</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary w-100">Cập Nhật</button>
                <a href="<?= BASE_URL ?>index.php?act=admin-tables" class="btn btn-secondary w-100 mt-2">Quay Lại</a>
            </form>
        </div>
    </div>
</div>

<?php require 'views/layouts/footer.php'; ?>
