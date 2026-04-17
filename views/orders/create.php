<?php require 'views/layouts/header.php'; ?>

<div class="container">
    <h2 class="mb-4">Tạo Đơn Hàng</h2>

    <form method="POST" action="<?= BASE_URL ?>index.php?act=order-store" id="orderForm">
        <input type="hidden" name="reservation_id" value="<?= $reservation_id ?>">

        <div class="row">
            <div class="col-md-8">
                <!-- Filter Section -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Tìm Kiếm Món Ăn</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Tìm theo danh mục:</label>
                                <select id="categoryFilter" class="form-select">
                                    <option value="">-- Tất cả danh mục --</option>
                                    <?php 
                                    // Get unique categories from foods
                                    $categories = [];
                                    foreach($foods as $food) {
                                        if($food['category_name'] && !in_array($food['category_name'], $categories)) {
                                            $categories[] = $food['category_name'];
                                        }
                                    }
                                    sort($categories);
                                    foreach($categories as $category) {
                                        echo '<option value="' . htmlspecialchars($category) . '">' . htmlspecialchars($category) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tìm theo tên:</label>
                                <input type="text" id="foodNameFilter" class="form-control" placeholder="Nhập tên món ăn...">
                            </div>
                        </div>
                    </div>
                </div>

                <table class="table">
                    <thead>
                        <tr>
                            <th>Món Ăn</th>
                            <th>Danh Mục</th>
                            <th>Giá</th>
                            <th>Số Lượng</th>
                            <th>Thành Tiền</th>
                        </tr>
                    </thead>
                    <tbody id="itemsList">
                        <?php foreach($foods as $idx => $food) { ?>
                            <tr class="food-item" 
                                data-price="<?= $food['price'] ?>" 
                                data-food-id="<?= $food['id'] ?>"
                                data-food-name="<?= htmlspecialchars($food['name']) ?>"
                                data-category="<?= htmlspecialchars($food['category_name'] ?? '') ?>">
                                <td><?= $food['name'] ?></td>
                                <td><?= $food['category_name'] ?? 'Không xác định' ?></td>
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
// Filter functions
const categoryFilter = document.getElementById('categoryFilter');
const foodNameFilter = document.getElementById('foodNameFilter');
const foodItems = document.querySelectorAll('.food-item');

function filterFoods() {
    const selectedCategory = categoryFilter.value.toLowerCase();
    const searchName = foodNameFilter.value.toLowerCase();
    
    foodItems.forEach(row => {
        const category = row.dataset.category.toLowerCase();
        const name = row.dataset.foodName.toLowerCase();
        
        let showRow = true;
        
        if(selectedCategory && category !== selectedCategory) {
            showRow = false;
        }
        
        if(searchName && !name.includes(searchName)) {
            showRow = false;
        }
        
        row.style.display = showRow ? '' : 'none';
    });
}

categoryFilter.addEventListener('change', filterFoods);
foodNameFilter.addEventListener('input', filterFoods);

// Quantity and total calculation
document.querySelectorAll('.qty').forEach(input => {
    input.addEventListener('change', updateTotal);
});

function updateTotal() {
    let total = 0;
    document.querySelectorAll('.food-item').forEach(row => {
        if(row.style.display !== 'none') {
            const qtyInput = row.querySelector('.qty[name*="quantity"]');
            const qty = parseInt(qtyInput.value) || 0;
            const price = parseFloat(row.dataset.price);
            const subtotal = qty * price;
            row.querySelector('.total').textContent = (subtotal).toLocaleString('vi-VN') + '₫';
            total += subtotal;
        } else {
            // Also count hidden rows if they have quantity
            const qtyInput = row.querySelector('.qty[name*="quantity"]');
            const qty = parseInt(qtyInput.value) || 0;
            const price = parseFloat(row.dataset.price);
            const subtotal = qty * price;
            total += subtotal;
        }
    });
    document.getElementById('totalPrice').textContent = total.toLocaleString('vi-VN') + '₫';
}
</script>

<?php require 'views/layouts/footer.php'; ?>
