# 🪑 Hướng Dẫn Quản Lý Bàn Nhà Hàng

## 📋 Tổng Quan

Chức năng quản lý bàn cho phép bạn:
- ✅ Tạo và quản lý danh sách bàn
- ✅ Xem sơ đồ phòng với trạng thái bàn (Trống/Có Khách/Đã Đặt)
- ✅ Xem chi tiết từng bàn
- ✅ Theo dõi đặt bàn sắp tới
- ✅ Cập nhật trạng thái bàn

---

## 🎯 Các Đường Dẫn (Routes)

### **Dành Cho Khách Hàng**

| Tên | Đường Dẫn | Mô Tả |
|-----|----------|--------|
| Sơ Đồ Bàn | `/index.php?act=tables-layout` | Xem sơ đồ phòng trực quan |
| Chi Tiết Bàn | `/index.php?act=tables-detail&id=X` | Xem chi tiết bàn cụ thể |

### **Dành Cho Admin**

| Tên | Đường Dẫn | Mô Tả |
|-----|----------|--------|
| Danh Sách Bàn | `/index.php?act=admin-tables` | Quản lý tất cả bàn |
| Thêm Bàn | `/index.php?act=admin-add-table` | Form thêm bàn mới |
| Sửa Bàn | `/index.php?act=admin-edit-table&id=X` | Form sửa bàn |
| Lưu Bàn | `/index.php?act=admin-store-table` | Lưu bàn mới (POST) |
| Cập Nhật | `/index.php?act=admin-update-table` | Cập nhật bàn (POST) |
| Xóa Bàn | `/index.php?act=admin-delete-table&id=X` | Xóa bàn |

---

## 🚀 Cách Sử Dụng

### **Bước 1: Tạo Bàn (Admin)**

1. Đăng nhập với tài khoản Admin
2. Truy cập: `Dashboard → 🪑 Quản Lý Bàn`
3. Nhấn "+ Thêm Bàn"
4. Nhập thông tin:
   - **Số Bàn:** A1, B2, 101, VIP1, etc.
   - **Trạng Thái:** Trống hoặc Có Khách
5. Nhấn "Thêm Bàn"

### **Bước 2: Xem Sơ Đồ Bàn**

**Khách hàng:**
1. Truy cập: `Sơ Đồ Bàn` (trong menu)
2. Xem tất cả bàn với màu sắc:
   - 🟢 **Xanh**: Trống (sẵn sàng)
   - 🔴 **Đỏ**: Có Khách (đang sử dụng)
   - 🟡 **Vàng**: Đã Đặt (sắp tới)
3. Nhấn vào bàn để xem chi tiết

**Admin:**
1. Từ Dashboard, nhấn "📊 Xem Sơ Đồ Phòng"
2. Follow các bước tương tự

### **Bước 3: Xem Chi Tiết Bàn**

1. Từ sơ đồ, nhấn vào một bàn
2. Xem thông tin:
   - Trạng thái bàn (Trống/Có Khách)
   - Khách hàng đặt bàn
   - Thời gian đặt
   - Số khách

### **Bước 4: Quản Lý Bàn (Admin)**

1. Truy cập: `Dashboard → 🪑 Quản Lý Bàn`
2. Các hành động:
   - **Sửa**: Thay đổi số bàn, trạng thái
   - **Xóa**: Xóa bàn khỏi hệ thống

---

## 🎨 Trạng Thái Bàn

| Trạng Thái | Badge | Ý Nghĩa |
|-----------|-------|---------|
| Trống | 🟢 Xanh | Bàn sẵn sàng để khách ngồi |
| Có Khách | 🔴 Đỏ | Bàn đang có khách |
| Đã Đặt | 🟡 Vàng | Bàn có lịch đặt sắp tới |

---

## 💡 Cách Hoạt Động

### **Tự Động Cập Nhật Trạng Thái**

Hệ thống sẽ **tự động cập nhật** trạng thái bàn dựa trên:

1. **Trống (available)**: Nếu không có đặt bàn nào
2. **Có Khách (occupied)**: Nếu đặt bàn đã qua thời gian dự kiến
3. **Đã Đặt (reserved)**: Nếu đặt bàn sắp tới (chưa tới thời gian)

**Ví dụ:**
- Khách đặt bàn A1 lúc 19:00
- 18:45 - 19:45: Bàn A1 hiển thị **Đã Đặt** (Badge vàng)
- 19:45 trở đi: Bàn A1 hiển thị **Có Khách** (Badge đỏ)
- Khi đặt bàn bị hủy: Bàn A1 hiển thị **Trống** (Badge xanh)

---

## 📊 Dashboard Admin

Dashboard hiển thị:
- 👥 Tổng người dùng
- 🍽️ Tổng món ăn
- 🪑 Tổng bàn
- 📅 Tổng đặt bàn
- 📦 Tổng đơn hàng

**+ Menu nhanh để quản lý:**
- Thực đơn
- Danh mục
- Bàn
- **Sơ đồ phòng** (xem trực tiếp)
- Đặt bàn
- Đơn hàng
- Người dùng

---

## 🔧 Hỗ Trợ & Lỗi Thường Gặp

### **Bàn không xuất hiện trong sơ đồ**
- ✅ Kiểm tra bàn đã được thêm vào hệ thống chưa
- ✅ Kiểm tra database có dữ liệu không

### **Trạng thái bàn không cập nhật**
- ✅ Tải lại trang (Ctrl+F5)
- ✅ Kiểm tra ngày giờ trên server có đúng không

### **Không thấy sơ đồ bàn**
- ✅ Đảm bảo bạn đã thêm ít nhất 1 bàn
- ✅ Kiểm tra quyền truy cập

---

## 📚 Tích Hợp Với Các Tính Năng Khác

### **Đặt Bàn**
- Khi khách đặt bàn, số bàn sẽ được cập nhật trạng thái tự động
- Xem sơ đồ để biết bàn nào trống

### **Quản Lý Đặt Bàn**
- Admin có thể xem đặt bàn sắp tới bên dưới sơ đồ
- Cập nhật trạng thái đặt bàn: Pending → Confirmed → Completed

### **Tạo Đơn Hàng**
- Khách có thể tạo đơn hàng từ bàn đã đặt
- Chi phí sẽ được tính dựa trên thực đơn

---

## 🎯 Tip & Thủ Thuật

1. **Thêm nhiều bàn nhanh**: Thêm từng bàn một với số thứ tự (A1, A2, ..., B1, B2, ...)
2. **Theo dõi bàn VIP**: Đặt tên bàn là "VIP1", "VIP2" để dễ phân biệt
3. **Cơm trưa/Tối**: Sử dụng trạng thái để quản lý ca
4. **Báo cáo**: Xem Dashboard để biết tổng số bàn sử dụng

---

## 📞 Hỗ Trợ

Nếu gặp vấn đề, liên hệ Admin hoặc check lại:
- Database có chứa dữ liệu bàn không
- Quyền truy cập có đúng không
- Session có còn hiệu lực không

**Phiên Bản:** 1.1.0  
**Cập Nhật:** 2024-03-20
