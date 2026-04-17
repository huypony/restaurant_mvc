# Hướng Dẫn Chi Tiết Kiến Trúc Hệ Thống Nhà Hàng MVC

## 📌 I. CÓ BAO NHIÊU ACTOR (NGƯỜI DÙNG) TRONG HỆ THỐNG?

### **Hệ thống có 2 Actor chính:**

#### **1. CUSTOMER (Khách Hàng) 👥**

- **Ai?** Người dùng thường (có role = 'customer')
- **Mục đích?** Đặt bàn, tạo đơn hàng, xem menu
- **Quyền hạn:**
  - ✅ Xem menu (foods)
  - ✅ Tạo đặt bàn (reservation)
  - ✅ Tạo đơn hàng (order)
  - ✅ Xem danh sách đơn hàng của mình
  - ❌ Không thể quản lý
  - ❌ Không thể xem doanh thu
  - ❌ Không thể thay đổi status đơn hàng

#### **2. ADMIN (Quản Trị Viên) 👨‍💼**

- **Ai?** Người quản lý nhà hàng (có role = 'admin')
- **Mục đích?** Quản lý toàn bộ hệ thống
- **Quyền hạn:**
  - ✅ Quản lý users (xem, xóa)
  - ✅ Quản lý menu (thêm, sửa, xóa)
  - ✅ Quản lý danh mục (category)
  - ✅ Quản lý bàn (table)
  - ✅ Quản lý đặt bàn (reservation)
  - ✅ Quản lý đơn hàng
  - ✅ Xem doanh thu (revenue)
  - ✅ Thay đổi status đơn hàng

### **⚠️ KHÔNG CÓ NHÂN VIÊN (STAFF) TRONG HỆ THỐNG**

Hệ thống này chỉ có 2 loại người dùng: Khách hàng và Admin. Không có role cho nhân viên, phục vụ viên hay nhân viên bếp.

---

## 📂 II. KIẾN TRÚC THƯ MỤC VÀ CÁC FILE CHÍNH

```
restaurant_mvc/
│
├── index.php                      ← Điểm vào chính (routing)
├── doc.md                         ← Tài liệu
├── restaurant.sql                 ← Database schema
│
├── commons/                       ← Các hàm tiện ích chung
│   ├── env.php                   ← Biến môi trường (DatabaseHost, Port, etc)
│   └── function.php              ← Helper functions (connectDB, isAdmin, redirect, etc)
│
├── controllers/                   ← Xử lý logic business
│   ├── HomeController.php        ← Trang chủ
│   ├── AuthController.php        ← Đăng nhập, đăng ký, đăng xuất
│   ├── FoodController.php        ← Menu, xem thực đơn
│   ├── ReservationController.php ← Đặt bàn
│   ├── OrderController.php       ← Tạo đơn, xem đơn
│   ├── TableController.php       ← Xem sơ đồ bàn
│   └── AdminController.php       ← Tất cả chức năng quản lý (dành cho Admin)
│
├── models/                        ← Tương tác với database (CRUD)
│   ├── User.php                  ← Quản lý người dùng
│   ├── Food.php                  ← Quản lý thực đơn
│   ├── Category.php              ← Quản lý danh mục
│   ├── Reservation.php           ← Quản lý đặt bàn
│   ├── Order.php                 ← Quản lý đơn hàng
│   ├── OrderDetail.php           ← Chi tiết đơn hàng (1 order → nhiều items)
│   ├── Payment.php               ← Quản lý thanh toán
│   └── Table.php                 ← Quản lý bàn
│
├── views/                         ← Giao diện (HTML + PHP)
│   ├── layouts/
│   │   ├── header.php            ← Header chung (navbar)
│   │   └── footer.php            ← Footer chung
│   │
│   ├── admin/                    ← Giao diện quản trị
│   │   ├── dashboard.php
│   │   ├── users.php
│   │   ├── foods.php
│   │   ├── add-food.php
│   │   ├── edit-food.php
│   │   ├── categories.php
│   │   ├── tables.php
│   │   ├── orders.php
│   │   ├── revenue.php
│   │   └── ...
│   │
│   ├── auth/                     ← Giao diện đăng nhập/đăng ký
│   │   ├── login.php
│   │   └── register.php
│   │
│   ├── foods/
│   │   └── menu.php              ← Giao diện xem menu
│   │
│   ├── reservations/
│   │   ├── create.php            ← Tạo đặt bàn
│   │   └── list.php              ← Danh sách đặt bàn
│   │
│   └── orders/
│       ├── create.php            ← Tạo đơn hàng
│       ├── list.php              ← Danh sách đơn hàng
│       └── detail.php            ← Chi tiết đơn hàng
│
└── uploads/                       ← Thư mục chứa hình ảnh
```

---

## 🔄 III. FLOW HỆ THỐNG (LUỒNG DỮ LIỆU)

### **1. Flow Đặt Bàn (Reservation)**

```
CUSTOMER
   ↓
[lunlik] → views/auth/login.php → AuthController::login()
   ↓
SESSION['user'] được tạo
   ↓
Khách click "Đặt Bàn"
   ↓
views/reservations/create.php
   ↓
Form submit → ReservationController::store()
   ↓
Model::Reservation->create($data)
   ↓
INSERT INTO reservations TABLE
   ↓
Redirect → Danh sách đặt bàn của khách
```

### **2. Flow Tạo Đơn Hàng (Order)**

```
CUSTOMER
   ↓
Chọn 1 Reservation từ danh sách
   ↓
views/orders/create.php (với reservation_id)
   ↓
Chọn các món ăn + số lượng
   ↓
Form submit → OrderController::store()
   ↓
Kiểm tra: Đã có order pending cho reservation này chưa?
   ├─ YES → Update existing order (cộng thêm total_price)
   └─ NO → Create new order
   ↓
Thêm items vào order_details (food_id, quantity, price)
   ↓
INSERT INTO order_details TABLE
   ↓
Redirect → Xem chi tiết đơn hàng
```

### **3. Flow Quản Lý (Admin)**

```
ADMIN
   ↓
[admin-dashboard]
   ↓
AdminController::dashboard()
   ↓
Lấy thống kê:
  - Total users
  - Total foods
  - Total orders
  - Monthly revenue
   ↓
Hiển thị các lựa chọn:
  - Quản lý menu
  - Quản lý bàn
  - Quản lý đặt bàn
  - Quản lý đơn hàng
  - Xem doanh thu
```

---

## 🗄️ IV. CẤU TRÚC CỠ SỞ DỮ LIỆU (DATABASE)

### **Bảng users**

```sql
users
├── id (PK)
├── name
├── email (UNIQUE)
├── password (hashed bcrypt)
├── role (customer | admin)
└── created_at
```

### **Bảng foods & categories**

```
categories          foods
├── id          ├── id
├── name        ├── name
└── created_at  ├── price
               ├── image
               ├── category_id (FK → categories)
               ├── status (active | inactive)
               └── created_at
```

### **Bảng reservations & reservation_tables (Junction Table)**

```
reservations                      reservation_tables
├── id                           ├── reservation_id (FK)
├── user_id (FK)                 ├── table_id (FK)
├── customer_name                └── [Liên kết N-N: 1 reservation → N tables]
├── customer_phone
├── reservation_time
├── guest_count
├── status (pending|confirmed|cancelled|completed)
└── created_at
```

**Khái niệm:** 1 khách hàng có thể đặt nhiều bàn (ghép bàn)

- Reservation 1 → Tables 1, 2, 3

### **Bảng orders & order_details**

```
orders                              order_details
├── id                          ├── id
├── reservation_id (FK)         ├── order_id (FK)
├── user_id (FK)                ├── food_id (FK)
├── total_price                 ├── quantity
├── status (pending|processing| ├── price
├── completed|cancelled)        └── [1 order → nhiều items]
└── created_at
```

### **Bảng tables**

```
tables
├── id
├── table_number
├── capacity (sức chứa bàn)
├── status (available | occupied)
└── created_at
```

### **Bảng payments**

```
payments
├── id
├── order_id (FK)
├── amount
├── payment_method
├── payment_status
└── paid_at
```

---

## 🔐 V. LỌC QUYỀN (AUTHORIZATION)

### **Kiểm tra trong Commons/function.php**

```php
function isLoggedIn() {
    return isset($_SESSION['user']);
}

function isAdmin() {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
}
```

### **Kiểm tra trong Controllers**

```php
// Chỉ cho phép đã đăng nhập
if(!isLoggedIn()) {
    redirect('index.php?act=login');
}

// Chỉ cho phép ADMIN
if(!isLoggedIn() || !isAdmin()) {
    redirect('index.php?act=admin-dashboard');
}
```

---

## 🔀 VI. MVC PATTERN GIẢI THÍCH

### **M - Model (models/\*.php)**

- Giao tiếp trực tiếp với database
- CRUD operations (Create, Read, Update, Delete)
- Không có HTML/CSS

**Ví dụ:**

```php
// models/Food.php
class Food {
    public function all() { /* SELECT * FROM foods */ }
    public function findById($id) { /* SELECT WHERE id = ? */ }
    public function create($data) { /* INSERT INTO foods */ }
    public function update($id, $data) { /* UPDATE foods */ }
    public function delete($id) { /* DELETE FROM foods */ }
}
```

### **V - View (views/\*.php)**

- Chỉ hiển thị dữ liệu (HTML + PHP)
- Không có database queries
- Không có business logic phức tạp

**Ví dụ:**

```php
// views/foods/menu.php
<?php foreach($foods as $food) { ?>
    <div class="food-item">
        <h3><?= $food['name'] ?></h3>
        <p>Giá: <?= formatMoney($food['price']) ?></p>
    </div>
<?php } ?>
```

### **C - Controller (controllers/\*.php)**

- Điều phối giữa Model và View
- Xử lý business logic
- Gọi Model để lấy dữ liệu
- Gửi dữ liệu tới View

**Ví dụ:**

```php
// controllers/FoodController.php
public function menu() {
    if(!isLoggedIn()) {
        redirect('index.php?act=login');
    }

    $foodModel = new Food();
    $foods = $foodModel->all();  // Gọi Model

    require 'views/foods/menu.php';  // Gửi dữ liệu tới View
}
```

---

## 🎯 VII. ROUTING (UNL PATTERN)

Tất cả route đi qua `index.php?act=ACTION_NAME`

**Ở file index.php:**

```php
$act = $_GET['act'] ?? '/';

match ($act) {
    '/' => (new HomeController())->index(),
    'login' => (new AuthController())->loginForm(),
    'post-login' => (new AuthController())->login(),
    'menu' => (new FoodController())->menu(),
    'reservation-create' => (new ReservationController())->create(),
    'order-create' => (new OrderController())->create(),
    'admin-dashboard' => (new AdminController())->dashboard(),
    // ... và nhiều route khác
}
```

**Ví dụ URL:**

- `index.php?act=menu` → FoodController::menu()
- `index.php?act=login` → AuthController::loginForm()
- `index.php?act=admin-dashboard` → AdminController::dashboard()

---

## 🔑 VIII. AUTHENTICATION & SESSION

### **Quy trình Đăng Nhập**

1. User điền email + password
2. Form submit → `AuthController::login()`
3. Kiểm tra email trong database
4. So sánh password với bcrypt hash
5. Nếu đúng:
   - `$_SESSION['user'] = [id, name, email, role]`
   - Redirect to dashboard
6. Nếu sai:
   - `$_SESSION['error'] = 'Email hoặc mật khẩu sai'`
   - Redirect to login form

### **Session Storage**

```php
// Khi đăng nhập thành công
$_SESSION['user'] = [
    'id' => 1,
    'name' => 'John Doe',
    'email' => 'john@gmail.com',
    'role' => 'customer'  // hoặc 'admin'
];

// Bất cứ nơi nào có thể check:
if(isAdmin()) { /* cho phép */ }
if(isLoggedIn()) { /* cho phép */ }
```

---

## 📊 IX. MAIN FEATURES (CHỨC NĂNG CHÍNH)

### **Cho Khách Hàng (CUSTOMER):**

1. ✅ Đăng ký tài khoản
2. ✅ Đăng nhập
3. ✅ Xem menu thực đơn
4. ✅ Đặt bàn + chọn bàn
5. ✅ Thêm/cập nhật đơn hàng (smart logic: create hoặc update)
6. ✅ Xem lịch sử đơn hàng

### **Cho Admin:**

1. ✅ Dashboard (thống kê)
2. ✅ Quản lý người dùng (xem, xóa)
3. ✅ Quản lý menu:
   - Thêm/sửa/xóa món ăn
   - Thêm/sửa/xóa danh mục
4. ✅ Quản lý bàn:
   - Thêm/sửa/xóa bàn
   - Xem sơ đồ phòng
5. ✅ Quản lý đặt bàn:
   - Xem tất cả đặt bàn
   - Thay đổi trạng thái
   - Assign table
6. ✅ Quản lý đơn hàng:
   - Xem tất cả đơn
   - Thay đổi status (pending → processing → completed)
7. ✅ Xem doanh thu (revenue):
   - Tổng doanh thu
   - Lọc theo ngày/tháng
   - Thống kê theo trạng thái

---

## 🚀 X. CÁC TÍNH NĂNG ĐẶC BIỆT

### **1. Ghép Bàn (Multi-Table Reservation)**

- Một đặt bàn có thể gán nhiều bàn
- Sử dụng Junction Table: `reservation_tables`
- Admin có thể chọn nhiều bàn khi tạo đặt bàn

### **2. Smart Order Logic**

- Nếu reservation chưa có order → Tạo mới
- Nếu reservation đã có order pending → Cập nhật (cộng tổng tiền)
- Không tạo multiple pending orders cho 1 reservation

### **3. Chức Năng Tìm Kiếm Menu**

- Filter theo danh mục (dropdown)
- Filter theo tên món ăn (search text)
- Combine cả 2 filter

### **4. Validasi Đặt Bàn**

- Ngày/giờ đặt phải ≥ hiện tại
- Client-side validation (JavaScript)
- Server-side validation

### **5. Doanh Thu (Revenue Tracking)**

- Chỉ tính completed orders
- Filter theo date range
- Breakdown theo status, date, month
- Hiển thị bảng chi tiết completed orders

---

## 💡 XI. HELPER FUNCTIONS (commons/function.php)

```php
// Kết nối database
connectDB()

// Auth checks
isLoggedIn()      // Đã đăng nhập?
isAdmin()         // Là admin?
getCurrentUser()  // Lấy user hiện tại

// Navigation
redirect($url)    // Chuyển hướng

// Format display
formatMoney($amount)     // Hiển thị tiền
formatDate($datetime)    // Hiển thị ngày
sanitize($input)         // Lọc input (escape)

// Message display
showError($msg)   // Hiển thị lỗi
showSuccess($msg) // Hiển thị thành công
```

---

## 🎓 TÓMTẮT NHANH

| Khía Cạnh           | Chi Tiết                                                                         |
| ------------------- | -------------------------------------------------------------------------------- |
| **Actors**          | 2: Customer + Admin                                                              |
| **Staff/Nhân viên** | ❌ KHÔNG CÓ                                                                      |
| **Roles**           | customer \| admin                                                                |
| **Models**          | 8 models (User, Food, Category, Reservation, Order, OrderDetail, Payment, Table) |
| **Controllers**     | 7 controllers                                                                    |
| **Views**           | ~20+ views                                                                       |
| **Database**        | MySQL với 8 bảng chính                                                           |
| **Pattern**         | MVC (Model-View-Controller)                                                      |
| **Routing**         | index.php?act=ACTION_NAME                                                        |
| **Auth**            | Session-based (bcrypt password)                                                  |
| **Key Feature**     | Multi-table reservation, Smart order logic, Revenue tracking                     |

---

## 🛠️ XII. HƯỚNG DẪN CODE LẠI TỪ ĐẦU (STEP BY STEP)

Nếu bạn muốn code lại toàn bộ từ đầu để hiểu rõ logic, hãy làm theo thứ tự này:

### **GIAI ĐOẠN 1: SETUP CƠ BẢN (1-2 giờ)**

#### **Bước 1: Tạo cấu trúc thư mục**

```
project/
├── commons/
├── controllers/
├── models/
├── views/
├── uploads/
├── index.php
└── restaurant.sql
```

#### **Bước 2: Tạo commons/env.php - Biến môi trường**

```php
<?php
define('DB_HOST', 'localhost');
define('DB_PORT', 3306);
define('DB_NAME', 'restaurant');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('BASE_URL', 'http://localhost/restaurant_mvc/');
?>
```

#### **Bước 3: Tạo commons/function.php - Helper functions**

Tạo các hàm helper cơ bản:

- `connectDB()` - Kết nối database
- `isLoggedIn()` - Kiểm tra đã login?
- `isAdmin()` - Kiểm tra là admin?
- `redirect($url)` - Chuyển hướng
- `formatMoney($amount)` - Định dạng tiền
- `formatDate($datetime)` - Định dạng ngày

**Tại sao trước tiên?**

- Vì tất cả file khác đều phụ thuộc vào những hàm này
- Database connection là nền tảng

---

### **GIAI ĐOẠN 2: DATABASE & MODELS (2-3 giờ)**

#### **Bước 4: Tạo database (restaurant.sql)**

Tạo 8 bảng SQL theo thứ tự này:

1. `users` - Lưu thông tin người dùng
2. `categories` - Lưu danh mục thực đơn
3. `foods` - Lưu thông tin món ăn
4. `tables` - Lưu thông tin bàn
5. `reservations` - Lưu thông tin đặt bàn
6. `reservation_tables` - Junction table (ghép bàn)
7. `orders` - Lưu thông tin đơn hàng
8. `order_details` - Lưu chi tiết đơn hàng

**Tại sao trước tiên?**

- Bạn cần hiểu cấu trúc data trước khi code queries
- Giúp hiểu relationship giữa các bảng

#### **Bước 5: Tạo Models (lần lượt)**

Tạo file theo thứ tự phụ thuộc:

1. **models/User.php** - Model đơn giản nhất
   - `all()` - Lấy tất cả users
   - `findById($id)` - Lấy 1 user
   - `create($data)` - Tạo user mới
   - `findByEmail($email)` - Tìm user bằng email

2. **models/Category.php** - Phụ thuộc không
   - `all()`, `findById()`, `create()`, `update()`, `delete()`

3. **models/Food.php** - Phụ thuộc Category
   - `all()` - JOIN với categories
   - `findById()`, `create()`, `update()`, `delete()`

4. **models/Table.php** - Độc lập
   - `all()`, `findById()`, `create()`, `update()`, `delete()`

5. **models/Reservation.php** - Phụ thuộc User
   - `all()`, `findById()`, `create()`, `update()`, `getByUser()`

6. **models/ReservationTable.php** - Phụ thuộc Reservation + Table
   - `assignTable($reservation_id, $table_id)` - Assign bàn
   - `getTablesByReservation()` - Lấy bàn của 1 đặt bàn

7. **models/Order.php** - Phụ thuộc Reservation + User
   - `all()`, `findById()`, `create()`, `update()`, `getByUser()`, `getByReservation()`
   - `getPendingByReservation()` - Lấy order pending của reservation

8. **models/OrderDetail.php** - Phụ thuộc Order + Food
   - `create()`, `getByOrder()`, `delete()`

9. **models/Payment.php** - Phụ thuộc Order
   - `create()`, `findByOrder()`, `update()`

**Tại sao tạo theo thứ tự này?**

- Independent models trước (User, Category, Table, Food)
- Sau đó dependent models (Reservation, Order)
- Cuối cùng junction tables + detail tables

**Mẹo:**

- Viết CRUD cơ bản trước (Create, Read, Update, Delete)
- Sau đó thêm queries đặc biệt (findByEmail, gget Pending, etc)

---

### **GIAI ĐOẠN 3: AUTHENTICATION (1-2 giờ)**

#### **Bước 6: Tạo AuthController**

Viết logic đăng nhập/đăng ký:

1. **registerForm()** - Hiển thị form đăng ký
2. **register()** - Xử lý đăng ký
   - Validate input (email, password)
   - Hash password bằng bcrypt
   - Insert vào database với role = 'customer'
   - Set session hoặc redirect to login
3. **loginForm()** - Hiển thị form login
4. **login()** - Xử lý login
   - Lấy email từ POST
   - Query user từ database
   - So sánh password với hash
   - Nếu đúng: set `$_SESSION['user']` và redirect
   - Nếu sai: set error message
5. **logout()** - Đăng xuất
   - Unset SESSION['user']
   - Redirect to home

**Tại sao tại đây?**

- Bạn cần auth để test các tính năng khác
- Nó là nền tảng cho role-based access

**Code mẫu:**

```php
public function login() {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if(empty($email) || empty($password)) {
        $_SESSION['error'] = 'Email và password không được trống!';
        redirect('index.php?act=login');
    }

    $userModel = new User();
    $user = $userModel->findByEmail($email);

    if(!$user || !password_verify($password, $user['password'])) {
        $_SESSION['error'] = 'Email hoặc password sai!';
        redirect('index.php?act=login');
    }

    $_SESSION['user'] = [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role']
    ];

    redirect('index.php?act=/');
}
```

---

### **GIAI ĐOẠN 4: VIEWS CƠ BẢN (1-2 giờ)**

#### **Bước 7: Tạo Layout Views**

1. **views/layouts/header.php** - Header chung
   - Navigation bar
   - Logo
   - Menu login/logout

2. **views/layouts/footer.php** - Footer chung

3. **views/auth/register.php** - Form đăng ký
4. **views/auth/login.php** - Form đăng nhập

**Tại sao tại đây?**

- Cần UI để test auth logic
- Layout chung giúp consistency

---

### **GIAI ĐOẠN 5: TÍNH NĂNG MENU & DANH MỤC (2-3 giờ)**

#### **Bước 8: Admin - Quản Lý Menu**

1. **AdminController::categories()** - Danh sách danh mục
2. **AdminController::addCategory()** - Form thêm danh mục
3. **AdminController::storeCategory()** - Xử lý thêm danh mục
4. **AdminController::updateCategory()** - Xử lý sửa danh mục
5. **AdminController::deleteCategory()** - Xóa danh mục

6. **AdminController::foods()** - Danh sách món ăn
7. **AdminController::addFood()** - Form thêm món ăn
8. **AdminController::storeFood()** - Xử lý thêm
9. **AdminController::updateFood()** - Xử lý sửa
10. **AdminController::deleteFood()** - Xóa

Tạo views tương ứng:

- **views/admin/categories.php**
- **views/admin/foods.php**
- **views/admin/add-food.php**
- **views/admin/edit-food.php**

#### **Bước 9: Customer - Xem Menu**

1. **FoodController::menu()** - Lấy tất cả foods từ Model
2. **views/foods/menu.php** - Hiển thị menu

**Tại sao tại đây?**

- Menu là nền tảng để chọn món khi tạo đơn
- Admin cần quản lý trước tiên

---

### **GIAI ĐOẠN 6: QUẢN LÝ BÀN (1-2 giờ)**

#### **Bước 10: Admin - Quản Lý Bàn**

1. **AdminController::tables()** - Danh sách bàn
2. **AdminController::addTable()** - Form thêm bàn
3. **AdminController::storeTable()** - Xử lý thêm
4. **AdminController::updateTable()** - Xử lý sửa
5. **AdminController::deleteTable()** - Xóa

Views:

- **views/admin/tables.php**
- **views/admin/add-table.php**
- **views/admin/edit-table.php**

**Tại sao ở đây?**

- Bàn cần được tạo trước khi assign cho đặt bàn
- Sau đó mới đặt bàn

---

### **GIAI ĐOẠN 7: QUẢN LÝ RESERVATIONS (2-3 giờ)**

#### **Bước 11: Customer - Đặt Bàn**

1. **ReservationController::create()** - Hiển thị form
2. **ReservationController::store()** - Xử lý tạo reservation
   - Validate ngày/giờ ≥ hiện tại
   - Insert into reservations
3. **ReservationController::list()** - Hiển thị đặt bàn của khách
4. **views/reservations/create.php** - Form đặt bàn
5. **views/reservations/list.php** - Danh sách đặt bàn

#### **Bước 12: Admin - Quản Lý Reservations**

1. **AdminController::reservations()** - Danh sách tất cả
2. **AdminController::assignTable()** - Assign bàn cho reservation
   - Có thể chọn nhiều bàn (ghép bàn)
   - Insert into reservation_tables
3. **views/admin/reservations.php**

**Tại sao ở đây?**

- Reservation là bước chuẩn bị cho Order
- Khách phải đặt bàn trước khi tạo đơn

---

### **GIAI ĐOẠN 8: ORDER & ORDER DETAILS (2-3 giờ)**

#### **Bước 13: Customer - Tạo Đơn Hàng**

1. **OrderController::create()** - Hiển thị danh sách foods
   - `views/orders/create.php`
   - Hiển thị form tạo đơn với filters (danh mục, tên)
2. **OrderController::store()** - Xử lý tạo/cập nhật đơn
   - Check: Đã có order pending cho reservation này chưa?
   - Nếu YES → Update (cộng total_price)
   - Nếu NO → Create new
   - Thêm items vào order_details
3. **OrderController::list()** - Danh sách đơn của khách
4. **OrderController::detail()** - Chi tiết 1 đơn
   - Lấy order + order_details
   - Hiển thị từng food + quantity + price

#### **Bước 14: Admin - Quản Lý Orders**

1. **AdminController::orders()** - Danh sách tất cả orders
2. **AdminController::updateOrder()** - Thay đổi status
   - pending → processing → completed → paid
   - Hoặc canceled
3. **views/admin/orders.php**

**Tại sao ở đây?**

- Order phụ thuộc vào Reservation + Foods
- Và order_details phụ thuộc vào Order
- Đây là logic phức tạp nhất

---

### **GIAI ĐOẠN 9: DASHBOARD & THỐNG KÊ (1-2 giờ)**

#### **Bước 15: Admin Dashboard**

1. **AdminController::dashboard()** - Lấy thống kê
   - Total users
   - Total foods
   - Total tables
   - Total reservations
   - Total orders
   - Monthly revenue
2. **views/admin/dashboard.php** - Hiển thị thống kê

#### **Bước 16: Revenue Tracking** (Optional nhưng quan trọng)

1. **Order Model methods:**
   - `getTotalRevenue($from_date, $to_date)`
   - `getRevenueByDate($from_date, $to_date)`
   - `getRevenueByMonth($year)`
   - `getCompletedOrders($from_date, $to_date)`
   - `getRevenueStatistics($from_date, $to_date)`

2. **AdminController::revenue()** - Hiển thị revenue dashboard
3. **views/admin/revenue.php** - Bảng thống kê doanh thu

**Tại sao ở đây?**

- Cần hoàn thành order logic trước
- Doanh thu = tổng những orders có status = 'completed'

---

### **GIAI ĐOẠN 10: ROUTER & MIDDLEWARE (30 phút)**

#### **Bước 17: Tạo index.php - Router chính**

```php
<?php
session_start();

require_once 'commons/env.php';
require_once 'commons/function.php';

// Require tất cả controllers và models
require_once 'controllers/HomeController.php';
require_once 'controllers/AuthController.php';
// ... etc

$act = $_GET['act'] ?? '/';

match ($act) {
    '/' => (new HomeController())->index(),
    'login' => (new AuthController())->loginForm(),
    'post-login' => (new AuthController())->login(),
    // ... tất cả routes
    default => redirect('index.php')
};
?>
```

**Tại sao cuối cùng?**

- Cần có tất cả controllers trước
- Router chính tập hợp tất cả vào

---

## 📋 ĐỨC RA HÀNG CHỜ ĐỀ ĐỀ XUẤT

Thứ tự code:

1. **commons/env.php** → **commons/function.php** (15 min)
2. **restaurant.sql** (30 min)
3. **Models** (User → Category → Food → Table → Reservation → Order) (3-4 giờ)
4. **AuthController + Views** (2 giờ)
5. **FoodController & CategoryController** (1 giờ)
6. **TableController** (1 giờ)
7. **ReservationController** (2 giờ)
8. **OrderController** (3 giờ) ← Phức tạp nhất!
9. **AdminController** (4-5 giờ)
10. **index.php Router** (30 min)

**Tổng cộng:** ~20-24 giờ để code từ đầu và hiểu rõ logic

---

## 🎯 MẸO QUAN TRỌNG KHI CODE

1. **Test Model trước View**
   - Viết Query → Test Query trên database
   - Sau đó mới tạo View

2. **Validate input ở Controller**

   ```php
   if(empty($_POST['name'])) {
       $_SESSION['error'] = 'Tên không được trống!';
       redirect('...');
   }
   ```

3. **Separate logic từ display**
   - Controller: business logic
   - Model: database queries
   - View: HTML/CSS chỉ

4. **Sử dụng prepared statements với PDO**
   - Tránh SQL injection

   ```php
   $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
   $stmt->execute([':email' => $email]);
   ```

5. **Hash mật khẩu trước khi lưu**

   ```php
   $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
   ```

6. **Luôn check quyền trước**

   ```php
   if(!isAdmin()) {
       redirect('index.php');
   }
   ```

7. **Escape output để tránh XSS**
   ```php
   <td><?= htmlspecialchars($user['name']) ?></td>
   ```

---

**Hy vọng tài liệu này giúp bạn hiểu rõ kiến trúc hệ thống! 🚀**
