<?php require 'views/layouts/header.php'; ?>

<div class="container-fluid">
    <h2 class="mb-4">📊 Báo Cáo Doanh Thu</h2>

    <!-- Filter Date -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="<?= BASE_URL ?>index.php?act=admin-revenue" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Từ Ngày</label>
                    <input type="date" class="form-control" name="from_date" value="<?= htmlspecialchars($from_date) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Đến Ngày</label>
                    <input type="date" class="form-control" name="to_date" value="<?= htmlspecialchars($to_date) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">🔍 Lọc</button>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <a href="<?= BASE_URL ?>index.php?act=admin-revenue" class="btn btn-secondary w-100">🔄 Reset</a>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <a href="<?= BASE_URL ?>index.php?act=admin-orders" class="btn btn-warning w-100">📋 Đơn Hàng</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Total Revenue Card -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h6 class="card-title text-muted">Tổng Doanh Thu</h6>
                    <h3 class="card-text text-success">
                        <strong><?= formatMoney($totalRevenue) ?></strong>
                    </h3>
                    <small class="text-muted"><?= $from_date ?> đến <?= $to_date ?></small>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-info">
                <div class="card-body text-center">
                    <h6 class="card-title text-muted">Số Đơn Hoàn Tất</h6>
                    <h3 class="card-text text-info">
                        <strong><?= count($completedOrders) ?></strong>
                    </h3>
                    <small class="text-muted">Đơn hàng</small>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <h6 class="card-title text-muted">Doanh Thu Trung Bình</h6>
                    <h3 class="card-text text-warning">
                        <strong><?= count($completedOrders) > 0 ? formatMoney($totalRevenue / count($completedOrders)) : formatMoney(0) ?></strong>
                    </h3>
                    <small class="text-muted">Mỗi đơn</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue by Status -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Thống Kê Theo Trạng Thái</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Trạng Thái</th>
                                    <th>Số Đơn</th>
                                    <th>Doanh Thu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($revenueStatistics as $stat) { ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-<?= 
                                                $stat['status'] === 'completed' ? 'success' : 
                                                ($stat['status'] === 'pending' ? 'warning' : 
                                                ($stat['status'] === 'processing' ? 'info' : 
                                                ($stat['status'] === 'cancelled' ? 'danger' : 'secondary')))
                                            ?>">
                                                <?= ucfirst($stat['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= $stat['order_count'] ?></td>
                                        <td><strong><?= formatMoney($stat['status_revenue'] ?? 0) ?></strong></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Doanh Thu Theo Tháng</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Tháng</th>
                                    <th>Số Đơn</th>
                                    <th>Doanh Thu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($revenueByMonth as $monthData) { 
                                    $monthName = "Tháng " . str_pad($monthData['month'], 2, '0', STR_PAD_LEFT) . " Năm " . $monthData['year'];
                                ?>
                                    <tr>
                                        <td><?= $monthName ?></td>
                                        <td><?= $monthData['order_count'] ?></td>
                                        <td><strong><?= formatMoney($monthData['monthly_revenue'] ?? 0) ?></strong></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue by Date -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Doanh Thu Theo Ngày</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Ngày</th>
                            <th>Số Đơn</th>
                            <th>Doanh Thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($revenueByDate as $dateData) { ?>
                            <tr>
                                <td><?= formatDate($dateData['revenue_date']) ?></td>
                                <td><?= $dateData['order_count'] ?></td>
                                <td><strong><?= formatMoney($dateData['daily_revenue'] ?? 0) ?></strong></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Completed Orders -->
    <div class="card">
        <div class="card-header">
            <h5>Chi Tiết Đơn Hàng Đã Hoàn Tất</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Khách Hàng</th>
                            <th>Số Khách</th>
                            <th>Doanh Thu</th>
                            <th>Ngày Tạo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($completedOrders as $order) { ?>
                            <tr>
                                <td><?= $order['id'] ?></td>
                                <td><?= htmlspecialchars($order['customer_name'] ?? 'N/A') ?></td>
                                <td><?= $order['guest_count'] ?? 'N/A' ?></td>
                                <td><strong><?= formatMoney($order['total_price']) ?></strong></td>
                                <td><?= formatDate($order['created_at']) ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <a href="<?= BASE_URL ?>index.php?act=admin-dashboard" class="btn btn-secondary mt-4">← Quay Lại Dashboard</a>
</div>

<?php require 'views/layouts/footer.php'; ?>
