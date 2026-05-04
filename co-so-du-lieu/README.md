# Cơ sở dữ liệu

Import file `khoi-tao-co-so-du-lieu.sql` bằng phpMyAdmin để tạo lại database `quanly_khachhang`, 5 bảng chính và dữ liệu mẫu.

## Tài khoản demo

- Admin: `admin` / `123456`
- Nhân viên: `minhanh` / `123456`
- Nhân viên: `quocbao` / `123456`

## Ghi chú tương thích

- Script dùng `utf8mb4`.
- Ưu tiên `utf8mb4_vietnamese_ci` nếu máy chủ hỗ trợ.
- Tự fallback sang `utf8mb4_unicode_ci` nếu không có collation tiếng Việt.
- Thiết kế kiểm tra trùng điện thoại/email cho khách hàng đang hoạt động bằng generated columns, tương thích MariaDB 10.4+ trong XAMPP.
