<?php require_once 'views/header.php'; ?>

<div class="container">
    <h2>Tạo đơn đặt bàn</h2>
    <form method="POST" action="<?= BASE_URL ?>index.php?act=reservation-store">
        
        <!-- BƯỚC 1: CHỌN THỜI GIAN + BÀN -->
        <div class="card mb-4">
            <div class="card-header">1. Chọn thời gian và bàn</div>
            <div class="card-body">
                <label>Thời gian đặt:</label>
                <input type="datetime-local" id="time-picker" name="reservation_time" 
                       value="<?= date('Y-m-d\TH:i') ?>" onchange="loadTableMap()" required>
                
                <label class="mt-3">Sơ đồ bàn:</label>
                <div id="table-map" class="row g-2 mt-2"></div>
                <input type="hidden" name="table_id" id="table_id" required>
                <small id="selected-table-text" class="text-muted">Chưa chọn bàn</small>
            </div>
        </div>

        <!-- BƯỚC 2: CHỌN MÓN CÓ ẢNH -->
        <div class="card mb-4">
            <div class="card-header">2. Chọn món ăn</div>
            <div class="card-body">
                <div class="row">
                    <?php foreach($foods as $food): ?>
                    <div class="col-md-3 mb-3">
                        <div class="card food-item" onclick="addFood(<?= $food['id'] ?>, '<?= $food['name'] ?>', <?= $food['price'] ?>)">
                            <img src="<?= $food['image'] ?>" class="card-img-top" style="height:150px;object-fit:cover">
                            <div class="card-body p-2">
                                <b><?= $food['name'] ?></b><br>
                                <span class="text-danger"><?= number_format($food['price']) ?>đ</span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- BƯỚC 3: GIỎ HÀNG -->
        <div class="card mb-4">
            <div class="card-header">3. Món đã chọn</div>
            <div class="card-body" id="cart-list">Chưa có món nào</div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg">Xác nhận đặt bàn</button>
    </form>
</div>

<script>
let cart = {};

function loadTableMap() {
    let time = document.getElementById('time-picker').value.replace('T', ' ') + ':00';
    fetch(`<?= BASE_URL ?>index.php?act=api-table-map&time=${time}`)
    .then(res => res.json()).then(data => {
        let html = '';
        data.forEach(t => {
            let color = t.status == 'trong' ? 'success' : t.status == 'da_dat' ? 'warning' : 'danger';
            let disabled = t.status != 'trong' ? 'style="opacity:0.5;pointer-events:none"' : '';
            html += `<div class="col-md-2" ${disabled}>
                        <div class="card bg-${color} text-white text-center p-2" onclick="selectTable(${t.id}, '${t.table_number}')">
                            <b>Bàn ${t.table_number}</b><br>
                            <small>${t.customer_name || 'Trống'}</small><br>
                            <small>${t.capacity} người</small>
                        </div>
                     </div>`;
        });
        document.getElementById('table-map').innerHTML = html;
    });
}

function selectTable(id, number) {
    document.getElementById('table_id').value = id;
    document.getElementById('selected-table-text').innerHTML = `Đã chọn: <b>Bàn ${number}</b>`;
}

function addFood(id, name, price) {
    if (!cart[id]) cart[id] = {name, price, qty: 0};
    cart[id].qty++;
    renderCart();
}

function renderCart() {
    let html = '', total = 0;
    for (let id in cart) {
        let item = cart[id];
        total += item.price * item.qty;
        html += `<div>${item.name} x ${item.qty} = ${Number(item.price * item.qty).toLocaleString()}đ
                 <input type="hidden" name="foods[${id}]" value="${item.qty}"></div>`;
    }
    html += `<hr><b>Tổng: ${total.toLocaleString()}đ</b>`;
    document.getElementById('cart-list').innerHTML = html || 'Chưa có món nào';
}

loadTableMap();
</script>

<?php require_once 'views/footer.php'; ?>