<?php require 'views/layouts/header.php'; ?>

<div class="container">
    <h2 class="mb-4">Tạo Đơn Hàng</h2>

    <form method="POST" action="<?= BASE_URL ?>index.php?act=order-store" id="orderForm">
        <input type="hidden" name="reservation_id" value="<?= $reservation_id ?>">

        <div class="row">
            <div class="col-md-8">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Món Ăn</th>
                            <th>Giá</th>
                            <th>Số Lượng</th>
                            <th>Thành Tiền</th>
                        </tr>
                    </thead>
                    <tbody id="itemsList">
                        <?php foreach($foods as $idx => $food) { ?>
                            <tr class="food-item" data-price="<?= $food['price'] ?>" data-food-id="<?= $food['id'] ?>">
                                <td><?= $food['name'] ?></td>
                                <td><?= formatMoney($food['price']) ?></td>
                                <td>
                                    <input type="number" class="form-control qty" name="items[<?= $idx ?>][food_id]" value="<?= $food['id'] ?>" hidden>
                                    <input type="number" class="form-control qty" name="items[<?= $idx ?>][quantity]" min="0" value="0" style="width: 70px;">
                                </td>
                                <td class="total">0₫</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Tính Toán</h5>
                        <div class="mb-3">
                            <label>Tổng Tiền:</label>
                            <h4 id="totalPrice">0₫</h4>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Tạo Đơn Hàng</button>
                        <a href="<?= BASE_URL ?>index.php?act=reservation-list" class="btn btn-secondary w-100 mt-2">Quay Lại</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.querySelectorAll('.qty').forEach(input => {
    input.addEventListener('change', updateTotal);
});

function updateTotal() {
    let total = 0;
    document.querySelectorAll('.food-item').forEach(row => {
        const qtyInput = row.querySelector('.qty[name*="quantity"]');
        const qty = parseInt(qtyInput.value) || 0;
        const price = parseFloat(row.dataset.price);
        const subtotal = qty * price;
        row.querySelector('.total').textContent = (subtotal).toLocaleString('vi-VN') + '₫';
        total += subtotal;
    });
    document.getElementById('totalPrice').textContent = total.toLocaleString('vi-VN') + '₫';
}
</script>

<?php require 'views/layouts/footer.php'; ?>
