
<?php require 'views/layouts/header.php'; ?>

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body p-5">
                    <h2 class="card-title text-center mb-4">Đặt Bàn</h2>
                    
                    <form method="POST" action="<?= BASE_URL ?>index.php?act=reservation-store">
                        <div class="mb-3">
                            <label class="form-label">Họ Tên</label>
                            <input type="text" class="form-control" name="customer_name" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Số Điện Thoại</label>
                            <input type="tel" class="form-control" name="customer_phone" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Thời Gian Đặt</label>
                            <input type="datetime-local" class="form-control" name="reservation_time" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Số Khách</label>
                            <input type="number" class="form-control" name="guest_count" min="1" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Đặt Bàn</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require 'views/layouts/footer.php'; ?>

