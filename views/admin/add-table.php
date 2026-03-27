<?php require 'views/layouts/header.php'; ?>

<div class="container">
    <h2 class="mb-4">Thêm Bàn</h2>

    <div class="row justify-content-center">
        <div class="col-md-5">
            <form method="POST" action="<?= BASE_URL ?>index.php?act=admin-store-table">
                <div class="mb-3">
                    <label class="form-label">Số Bàn</label>
                    <input type="text" class="form-control" name="table_number" placeholder="Ví dụ: A1, B2, 101..." required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Sức Chứa (Số Lượng Khách)</label>
                    <input type="number" class="form-control" name="capacity" min="1" value="2" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Trạng Thái</label>
                    <select class="form-control" name="status">
                        <option value="available">Trống</option>
                        <option value="occupied">Có Khách</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary w-100">Thêm Bàn</button>
                <a href="<?= BASE_URL ?>index.php?act=admin-tables" class="btn btn-secondary w-100 mt-2">Quay Lại</a>
            </form>
        </div>
    </div>
</div>

<?php require 'views/layouts/footer.php'; ?>
