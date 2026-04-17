
<?php require 'views/layouts/header.php'; ?>

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body p-5">
                    <h2 class="card-title text-center mb-4">Đặt Bàn</h2>
                    
                    <form method="POST" action="<?= BASE_URL ?>index.php?act=reservation-store" id="reservationForm">
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
                            <input type="datetime-local" class="form-control" id="reservationTime" name="reservation_time" required>
                            <small class="text-danger" id="errorMsg" style="display:none;">Thời gian đặt phải lớn hơn hoặc bằng thời gian hiện tại</small>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const timeInput = document.getElementById('reservationTime');
    const form = document.getElementById('reservationForm');
    const errorMsg = document.getElementById('errorMsg');
    
    // Set minimum datetime to now
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    const minDateTime = now.toISOString().slice(0, 16);
    timeInput.min = minDateTime;
    
    // Validate on form submit
    form.addEventListener('submit', function(e) {
        const selectedTime = new Date(timeInput.value);
        const currentTime = new Date();
        
        if(selectedTime < currentTime) {
            e.preventDefault();
            errorMsg.style.display = 'block';
            return false;
        }
        errorMsg.style.display = 'none';
    });
    
    // Clear error message when user changes input
    timeInput.addEventListener('change', function() {
        const selectedTime = new Date(this.value);
        const currentTime = new Date();
        if(selectedTime >= currentTime) {
            errorMsg.style.display = 'none';
        }
    });
});
</script>

<?php require 'views/layouts/footer.php'; ?>

