# Quản Lý Khách Hàng

Dự án môn Lập trình Web: website quản lý khách hàng bằng PHP thuần và MySQL/MariaDB trên XAMPP.

## Cách chạy dự án

1. Đặt thư mục dự án tại `C:\xampp\htdocs\quanly_khachhang`.
2. Khởi động Apache và MySQL trong XAMPP.
3. Mở `http://localhost/quanly_khachhang/`.
4. Khi có schema, import các file trong `co-so-du-lieu/` bằng phpMyAdmin hoặc MySQL CLI.

## Cấu hình cơ sở dữ liệu

Mặc định dự án dùng cấu hình XAMPP phổ biến: host `127.0.0.1`, port `3306`, database `quanly_khachhang`, user `root`, mật khẩu rỗng.

Nếu máy chấm bài dùng thông tin khác, đặt biến môi trường trước khi chạy Apache/PHP:

- `QLKH_DB_HOST`
- `QLKH_DB_PORT`
- `QLKH_DB_NAME`
- `QLKH_DB_USERNAME`
- `QLKH_DB_PASSWORD`

Ví dụ trong PowerShell trước khi chạy kiểm thử CLI:

```powershell
$env:QLKH_DB_USERNAME = "root"
$env:QLKH_DB_PASSWORD = ""
```

## Tài khoản demo

Sau khi import `co-so-du-lieu/khoi-tao-co-so-du-lieu.sql`, dùng các tài khoản sau để demo phân quyền:

- Admin: `admin` / `Demo@2026`
- Nhân viên: `minhanh` / `Demo@2026`
- Nhân viên: `quocbao` / `Demo@2026`
- Nhân viên: `thutrang` / `Demo@2026`

Trang đăng nhập không hiển thị trực tiếp mật khẩu demo để giao diện giống hệ thống thật hơn.

## Cấu trúc thư mục

- `cau-hinh/`: Cấu hình ứng dụng, kết nối cơ sở dữ liệu, hằng số, phiên làm việc.
- `dung-chung/`: Hàm dùng chung cho bảo mật, định dạng, thông báo, phân trang, truy vấn CSDL.
- `giao-dien/`: Các thành phần giao diện dùng chung như đầu trang, cuối trang, thanh điều hướng, thanh bên.
- `xu-ly-ajax/`: Endpoint AJAX trả JSON cho tìm kiếm, lọc, kiểm tra trùng lặp, cập nhật nhanh.
- `nguoi-dung/`: Quản lý tài khoản admin/nhân viên.
- `loai-khach-hang/`: Quản lý nhóm/loại khách hàng.
- `khach-hang/`: Quản lý hồ sơ khách hàng, chi tiết, xóa mềm, khôi phục.
- `tuong-tac/`: Ghi nhận lịch sử liên hệ, gọi điện, email, gặp mặt.
- `cong-viec-theo-doi/`: Quản lý lịch hẹn, việc cần theo dõi, trạng thái quá hạn/hoàn thành.
- `bao-cao/`: Báo cáo tổng hợp phục vụ dashboard và demo.
- `co-so-du-lieu/`: File SQL tạo schema, seed dữ liệu mẫu, ghi chú import.
- `tai-nguyen/`: CSS, JavaScript, hình ảnh và tài nguyên tĩnh.
- `nhat-ky/`: Nơi lưu log cục bộ khi cần debug. Không commit file log thật.
- `kiem-thu/`: Ghi chú test thủ công và kịch bản demo.

## Nguyên tắc hiện tại

- Không dùng framework PHP.
- Đặt tên file/thư mục bằng tiếng Việt không dấu, kebab-case.
- Giao diện hiển thị bằng tiếng Việt.
- CSDL cần dùng `utf8mb4` và collation tiếng Việt khi triển khai schema.
- Các module chính đang được xây theo từng pha; dashboard, loại khách hàng, khách hàng, tương tác, công việc theo dõi, báo cáo và AJAX trọng điểm đã dùng dữ liệu thật từ 5 bảng lõi.
