# Cơ sở dữ liệu

Import file `khoi-tao-co-so-du-lieu.sql` bằng phpMyAdmin để tạo lại database `quanly_khachhang`, 5 bảng chính và bộ dữ liệu demo tiếng Việt.

## Tài khoản demo

- Admin: `admin` / `Demo@2026`
- Nhân viên: `minhanh` / `Demo@2026`
- Nhân viên: `quocbao` / `Demo@2026`
- Nhân viên: `thutrang` / `Demo@2026`

Mật khẩu demo đủ 8 ký tự để phù hợp chính sách kiểm tra biểu mẫu. Khi triển khai ngoài máy demo, đổi mật khẩu ngay sau khi import.

## Dữ liệu demo

- 5 loại khách hàng: VIP, khách trung thành, khách tiềm năng, khách mới, tạm ngưng.
- 12 hồ sơ khách hàng với tên, công ty, địa chỉ, ghi chú và nguồn khách bằng tiếng Việt.
- Có một hồ sơ đã xóa mềm để kiểm thử trùng điện thoại/email và khôi phục.
- Tương tác gồm gọi điện, email, gặp mặt, chat, Zalo, ghi chú.
- Công việc có đủ trạng thái chờ xử lý, đang xử lý, hoàn thành, đã hủy và các việc quá hạn.
- Top 3 dashboard được cân bằng để có thứ hạng rõ: An Phú, Minh Long Logistics, Khoa Tech.

## Ghi chú tương thích

- Script dùng `utf8mb4`.
- Ưu tiên `utf8mb4_vietnamese_ci` nếu máy chủ hỗ trợ.
- Tự fallback sang `utf8mb4_unicode_ci` nếu không có collation tiếng Việt.
- Thiết kế kiểm tra trùng điện thoại/email cho khách hàng đang hoạt động bằng generated columns, tương thích MariaDB 10.4+ trong XAMPP.
