# 🍽️ Hệ Thống Đặt Bàn Nhà Hàng - MVC OOP

Một ứng dụng web hoàn chỉnh để quản lý đặt bàn nhà hàng, thực đơn và đơn hàng được xây dựng bằng **PHP thuần + MVC + OOP**

## 📋 Tính Năng

### Người Dùng Khách
- ✅ Đăng ký tài khoản
- ✅ Đăng nhập
- ✅ Xem thực đơn theo danh mục
- ✅ Đặt bàn
- ✅ Xem lịch sử đặt bàn
- ✅ Tạo đơn hàng từ đặt bàn
- ✅ Xem chi tiết đơn hàng

### Admin
- ✅ Bảng điều khiển (Dashboard)
- ✅ Quản lý thực đơn (Thêm, Sửa, Xóa)
- ✅ Quản lý danh mục
- ✅ Quản lý đặt bàn
- ✅ Quản lý đơn hàng
- ✅ Quản lý người dùng

## 🏗️ Cấu Trúc Dự Án

```
restaurant_mvc/
├── commons/
│   ├── env.php           # Cấu hình môi trường
│   └── function.php      # Hàm hỗ trợ
├── controllers/
│   ├── HomeController.php
│   ├── AuthController.php
│   ├── FoodController.php
│   ├── ReservationController.php
│   ├── OrderController.php
│   └── AdminController.php
├── models/
│   ├── User.php
│   ├── Food.php
│   ├── Reservation.php
│   ├── Order.php
│   ├── OrderDetail.php
│   ├── Payment.php
│   ├── Table.php
│   └── Category.php
├── views/
│   ├── layouts/
│   │   ├── header.php
│   │   └── footer.php
│   ├── auth/
│   │   ├── login.php
│   │   └── register.php
│   ├── foods/
│   │   └── menu.php
│   ├── reservations/
│   │   ├── create.php
│   │   └── list.php
│   ├── orders/
│   │   ├── create.php
│   │   ├── list.php
│   │   └── detail.php
│   ├── admin/
│   │   ├── dashboard.php
│   │   ├── users.php
│   │   ├── foods.php
│   │   ├── add-food.php
│   │   ├── edit-food.php
│   │   ├── reservations.php
│   │   └── orders.php
│   └── home.php
├── uploads/              # Thư mục lưu ảnh
├── index.php             # Điểm vào chính
├── restaurant.sql        # Script tạo database
└── README.md
```

## 🚀 Cài Đặt

### 1. Chuẩn Bị Môi Trường
- PHP 7.4+
- MySQL 5.7+
- Web Server (Apache/Nginx)

### 2. Tải Dự Án
```bash
git clone <repository>
cd restaurant_mvc
```

### 3. Import Database
```bash
# Mở phpMyAdmin hoặc dùng command line
mysql -u root -p < restaurant.sql
```

### 4. Cấu Hình
Chỉnh sửa file `commons/env.php`:
```php
define('BASE_URL', 'http://localhost/restaurant_mvc/');
define('DB_HOST', 'localhost');
define('DB_PORT', 3306);
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'restaurant');
```

### 5. Tạo Thư Mục Uploads
```bash
mkdir uploads
chmod 755 uploads
```

### 6. Chạy Ứng Dụng
```
http://localhost/restaurant_mvc/
```

## 👤 Tài Khoản Mặc Định

### Admin (cần thêm thủ công vào database)
```sql
INSERT INTO users(name, email, password, role) 
VALUES('Admin', 'admin@example.com', '$2y$10$...', 'admin');
```

## 🔗 Routes (Đường Dẫn)

### Public Routes
- `/` - Trang chủ
- `index.php?act=menu` - Xem thực đơn
- `index.php?act=login` - Đăng nhập
- `index.php?act=register` - Đăng ký

### User Routes (cần đăng nhập)
- `index.php?act=reservation-create` - Tạo đặt bàn
- `index.php?act=reservation-list` - Danh sách đặt bàn
- `index.php?act=order-create` - Tạo đơn hàng
- `index.php?act=order-list` - Danh sách đơn hàng
- `index.php?act=order-detail&id=X` - Chi tiết đơn hàng
- `index.php?act=logout` - Đăng xuất

### Admin Routes (cần đăng nhập Admin)
- `index.php?act=admin-dashboard` - Bảng điều khiển
- `index.php?act=admin-users` - Quản lý người dùng
- `index.php?act=admin-foods` - Quản lý thực đơn
- `index.php?act=admin-add-food` - Thêm món ăn
- `index.php?act=admin-edit-food&id=X` - Sửa món ăn
- `index.php?act=admin-reservations` - Quản lý đặt bàn
- `index.php?act=admin-orders` - Quản lý đơn hàng

## 📊 Database Schema

### Users
- id (INT, Primary Key)
- name (VARCHAR 100)
- email (VARCHAR 100)
- password (VARCHAR 255)
- role (VARCHAR 20) - 'admin' or 'customer'
- created_at (TIMESTAMP)

### Foods
- id (INT, Primary Key)
- name (VARCHAR 100)
- price (DECIMAL 10,2)
- image (VARCHAR 255)
- category_id (INT)
- status (VARCHAR 20) - 'active' or 'inactive'
- created_at (TIMESTAMP)

### Reservations
- id (INT, Primary Key)
- user_id (INT)
- customer_name (VARCHAR 100)
- customer_phone (VARCHAR 20)
- reservation_time (DATETIME)
- guest_count (INT)
- status (VARCHAR 20) - 'pending', 'confirmed', 'cancelled'
- created_at (TIMESTAMP)

### Orders
- id (INT, Primary Key)
- reservation_id (INT)
- user_id (INT)
- total_price (DECIMAL 10,2)
- status (VARCHAR 20) - 'pending', 'processing', 'completed', 'cancelled'
- created_at (TIMESTAMP)

### Order Details
- id (INT, Primary Key)
- order_id (INT)
- food_id (INT)
- quantity (INT)
- price (DECIMAL 10,2)

### Payments
- id (INT, Primary Key)
- order_id (INT)
- amount (DECIMAL 10,2)
- payment_method (VARCHAR 50)
- payment_status (VARCHAR 20)
- paid_at (DATETIME)

### Tables
- id (INT, Primary Key)
- table_number (VARCHAR 10)
- status (VARCHAR 20)

### Categories
- id (INT, Primary Key)
- name (VARCHAR 100)

## 🔒 Security Features

- ✅ Password Hashing (PASSWORD_DEFAULT)
- ✅ Input Sanitization (HTMLSpecialChars)
- ✅ Email Validation
- ✅ Session Management
- ✅ Role-based Access Control
- ✅ CSRF Protection (cơ bản)

## 🛠️ Các Hàm Hỗ Trợ

```php
connectDB()              // Kết nối database
isLoggedIn()             // Kiểm tra đăng nhập
getCurrentUser()         // Lấy user hiện tại
isAdmin()                // Kiểm tra quyền admin
redirect($url)           // Chuyển hướng
sanitize($data)          // Xóa dữ liệu đầu vào
isValidEmail($email)     // Kiểm tra email
formatMoney($amount)     // Định dạng tiền tệ
formatDate($dateTime)    // Định dạng ngày giờ
```

## 🎨 UI/UX

- Bootstrap 5 - Responsive Design
- Modern Navigation
- User-friendly Forms
- Status Indicators (Badges)
- Confirmation Dialogs

## 📝 Lưu Ý

1. Hình ảnh được upload vào thư mục `uploads/`
2. Đảm bảo thư mục uploads có quyền ghi
3. Session phải được bắt đầu trong `index.php`
4. Tất cả các route phải đi qua `index.php`

## 🚀 Nâng Cấp Trong Tương Lai

- [ ] Thanh toán trực tuyến (VNPay, Stripe)
- [ ] Email notifications
- [ ] SMS notifications
- [ ] Rating & Review
- [ ] Promotion codes
- [ ] Analytics Dashboard
- [ ] Multi-language Support
- [ ] API REST
- [ ] Mobile App

## 📞 Hỗ Trợ

Nếu gặp vấn đề, vui lòng kiểm tra:
1. Xem file `commons/env.php` có cấu hình đúng không
2. Database có được import đúng không
3. Thư mục uploads có tồn tại không
4. Permissions của thư mục uploads (755)

## 📄 License

Dự án này được phát hành dưới giấy phép MIT.

---

**Phiên Bản:** 1.0.0  
**Ngày Cập Nhật:** 2024-03-20
