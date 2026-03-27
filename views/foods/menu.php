
<?php require 'views/layouts/header.php'; ?>

<style>
    /* Điều chỉnh kích cỡ hình ảnh menu */
    .card-img-top {
        height: 250px;
        object-fit: cover;
        width: 100%;
    }
    
    .card {
        transition: all 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }
</style>

<div class="container">
    <h2 class="mb-4">Thực Đơn</h2>

    <?php if(!empty($categories)) { ?>
        <div class="mb-4">
            <div class="btn-group" role="group">
                <a href="<?= BASE_URL ?>index.php?act=menu" class="btn btn-outline-primary">Tất Cả</a>
                <?php foreach($categories as $cat) { ?>
                    <a href="<?= BASE_URL ?>index.php?act=menu&category_id=<?= $cat['id'] ?>" class="btn btn-outline-primary">
                        <?= $cat['name'] ?>
                    </a>
                <?php } ?>
            </div>
        </div>
    <?php } ?>

    <div class="row">
        <?php if(!empty($foods)) { ?>
            <?php foreach($foods as $f) { ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <?php if(!empty($f['image'])) { ?>
                            <img src="<?= BASE_URL ?>uploads/<?= $f['image'] ?>" class="card-img-top" alt="<?= $f['name'] ?>">
                        <?php } else { ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center">
                                <span class="text-muted">Chưa có ảnh</span>
                            </div>
                        <?php } ?>
                        
                        <div class="card-body">
                            <h5 class="card-title"><?= $f['name'] ?></h5>
                            <?php if(!empty($f['category_name'])) { ?>
                                <p class="text-muted small"><?= $f['category_name'] ?></p>
                            <?php } ?>
                            <p class="card-text text-primary fw-bold"><?= formatMoney($f['price']) ?></p>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="col-12">
                <p class="text-center text-muted">Không có món ăn nào.</p>
            </div>
        <?php } ?>
    </div>
</div>

<?php require 'views/layouts/footer.php'; ?>

