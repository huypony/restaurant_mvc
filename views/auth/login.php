
<?php require 'views/layouts/header.php'; ?>

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-5">
            <div class="card">
                <div class="card-body p-5">
                    <h2 class="card-title text-center mb-4">Đăng Nhập</h2>
                    
                    <form method="POST" action="<?= BASE_URL ?>index.php?act=post-login">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mật Khẩu</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Đăng Nhập</button>
                    </form>

                    <p class="text-center mt-3">
                        Chưa có tài khoản? <a href="<?= BASE_URL ?>index.php?act=register">Đăng ký tại đây</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require 'views/layouts/footer.php'; ?>

