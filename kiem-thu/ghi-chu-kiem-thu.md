# Báo cáo kiểm thử thủ công

## Môi trường kiểm thử

- Máy chủ: XAMPP, Apache, MariaDB/MySQL.
- Đường dẫn: `http://localhost/quanly_khachhang/`.
- Cơ sở dữ liệu: import `co-so-du-lieu/khoi-tao-co-so-du-lieu.sql`.
- Tài khoản admin: `admin` / `Demo@2026`.
- Tài khoản nhân viên: `minhanh`, `quocbao`, `thutrang` / `Demo@2026`.
- Trình duyệt kiểm thử: Chrome hoặc Edge bản mới.

## Bảng kết quả

| ID | Mục đích | Thiết lập/Dữ liệu | Kết quả mong đợi | Kết quả thực tế | Trạng thái |
| --- | --- | --- | --- | --- | --- |
| TC-01 | Đăng nhập admin | Mở `dang-nhap.php`, nhập `admin` / `Demo@2026` | Đăng nhập thành công, chuyển đến bảng điều khiển, hiển thị vai trò Quản trị | Chuyển vào dashboard, thanh điều hướng hiển thị người dùng admin | Đạt |
| TC-02 | Đăng xuất | Từ tài khoản admin, bấm Đăng xuất | Phiên bị hủy, quay về trang đăng nhập, không truy cập được dashboard nếu chưa đăng nhập lại | Quay về trang đăng nhập, truy cập trang nội bộ yêu cầu đăng nhập | Đạt |
| TC-03 | Chặn đăng nhập sai nhiều lần | Nhập sai mật khẩu của `admin` 5 lần | Hệ thống tạm khóa thử đăng nhập trong 5 phút | Hiển thị cảnh báo nhập sai quá nhiều lần | Đạt |
| TC-04 | Đăng nhập nhân viên | Đăng nhập `minhanh` / `Demo@2026` | Vào dashboard với vai trò Nhân viên, không thấy menu quản trị người dùng/loại khách hàng | Giao diện chỉ hiển thị chức năng nhân viên được phép dùng | Đạt |
| TC-05 | Phân quyền admin | Admin mở `nguoi-dung/` và `loai-khach-hang/` | Trang tải được và cho phép quản lý dữ liệu hệ thống | Admin xem được danh sách, thêm/sửa được dữ liệu quản trị | Đạt |
| TC-06 | Chặn quyền nhân viên | Nhân viên truy cập trực tiếp `nguoi-dung/` | Hệ thống chuyển sang trang không có quyền hoặc báo lỗi quyền truy cập | Nhân viên bị chặn, không xem được danh sách người dùng | Đạt |
| TC-07 | Thêm khách hàng | Admin hoặc nhân viên mở `khach-hang/them.php`, nhập họ tên, loại, nhân viên phụ trách, điện thoại/email mới | Lưu thành công và chuyển đến trang chi tiết khách hàng | Hồ sơ mới xuất hiện trong danh sách và trang chi tiết | Đạt |
| TC-08 | Sửa khách hàng | Mở một hồ sơ khách hàng đang hoạt động, đổi công ty/ghi chú/trạng thái | Dữ liệu cập nhật, không mất thông tin liên hệ | Trang chi tiết hiển thị dữ liệu mới sau khi lưu | Đạt |
| TC-09 | Kiểm tra trùng khách hàng | Nhập số điện thoại/email đã tồn tại trong form thêm/sửa | AJAX báo trùng và server không cho lưu bản ghi trùng đang hoạt động | Giao diện hiển thị cảnh báo trùng, submit bị chặn bởi validation server | Đạt |
| TC-10 | Tìm kiếm/lọc AJAX | Ở danh sách khách hàng, nhập từ khóa, đổi loại khách hàng, đổi trạng thái | Bảng cập nhật không tải lại toàn trang, tổng số dòng và phân trang thay đổi đúng | Danh sách, tổng dòng và phân trang cập nhật qua JSON | Đạt |
| TC-11 | Phân trang khách hàng | Dùng nút Trước/Sau ở danh sách khách hàng | Chuyển trang đúng, giữ bộ lọc hiện tại | URL và bảng dữ liệu giữ đúng bộ lọc | Đạt |
| TC-12 | Ghi nhận tương tác | Mở `tuong-tac/them.php`, chọn khách hàng, nhập loại, tiêu đề, nội dung, kết quả | Tạo tương tác và hiển thị trong lịch sử khách hàng | Tương tác mới xuất hiện ở trang chi tiết và danh sách tương tác | Đạt |
| TC-13 | Tạo công việc từ tương tác | Khi thêm tương tác, chọn tạo công việc theo dõi, nhập tiêu đề và hạn xử lý | Lưu cả tương tác và công việc trong một thao tác | Tương tác được lưu, công việc mới xuất hiện trong module công việc | Đạt |
| TC-14 | Quản lý công việc theo dõi | Tạo công việc, sửa hạn xử lý, đổi trạng thái từ chờ xử lý sang đang xử lý/hoàn thành | Trạng thái hợp lệ được lưu, trạng thái đã đóng không mở ngược tùy tiện | Danh sách cập nhật đúng nhãn, badge và ngày hoàn thành | Đạt |
| TC-15 | Cập nhật trạng thái AJAX | Ở danh sách công việc, đổi trạng thái bằng dropdown | AJAX cập nhật server, dòng giao diện đổi badge, không tải lại trang | Trạng thái đổi ngay, dòng rời khỏi bộ lọc quá hạn/sắp tới nếu không còn phù hợp | Đạt |
| TC-16 | Xóa mềm khách hàng | Admin xóa mềm khách hàng không còn công việc mở | Hồ sơ rời khỏi danh sách đang chăm sóc, vẫn xem được trong bộ lọc đã xóa | Khách hàng chuyển sang trạng thái đã xóa mềm | Đạt |
| TC-17 | Chặn xóa mềm khi còn việc mở | Admin xóa mềm khách hàng đang có việc `pending` hoặc `in_progress` | Hệ thống báo không thể xóa vì còn công việc mở | Không xóa hồ sơ, hiển thị cảnh báo xử lý công việc trước | Đạt |
| TC-18 | Khôi phục khách hàng | Admin lọc trạng thái đã xóa mềm rồi bấm Khôi phục | Hồ sơ quay lại danh sách đang chăm sóc nếu không trùng liên hệ | Hồ sơ được khôi phục và mở lại trang chi tiết | Đạt |
| TC-19 | Dashboard | Đăng nhập admin và mở `bang-dieu-khien.php` | Hiển thị KPI, Top 3 khách hàng, việc quá hạn, việc sắp tới, hoạt động gần đây | Dashboard tải dữ liệu từ 5 bảng lõi, không còn placeholder | Đạt |
| TC-20 | Báo cáo tổng quan | Mở `bao-cao/` và thay đổi bộ lọc ngày/nhân viên/loại khách | KPI và các khối báo cáo thay đổi theo bộ lọc | Số liệu báo cáo cập nhật đúng phạm vi được chọn | Đạt |
| TC-21 | Báo cáo khách hàng | Mở `bao-cao/khach-hang.php` | Có thống kê theo loại, trạng thái, nguồn, nhân viên phụ trách | Bảng báo cáo hiển thị dữ liệu phân rã rõ ràng | Đạt |
| TC-22 | Báo cáo tương tác | Mở `bao-cao/tuong-tac.php` | Có thống kê theo ngày, loại tương tác và danh sách hoạt động | Báo cáo hiển thị xu hướng chăm sóc và dữ liệu chi tiết | Đạt |
| TC-23 | Báo cáo công việc | Mở `bao-cao/cong-viec.php` | Có trạng thái công việc, quá hạn, hiệu quả nhân viên | Báo cáo hiển thị tỷ lệ hoàn thành và công việc quá hạn | Đạt |
| TC-24 | CSRF cho thao tác POST | Gửi POST xóa mềm/đăng xuất/cập nhật công việc thiếu token | Server từ chối và báo phiên biểu mẫu không hợp lệ | Thao tác bị chặn, dữ liệu không thay đổi | Đạt |
| TC-25 | Import SQL | Import lại `khoi-tao-co-so-du-lieu.sql` trên database rỗng hoặc demo | Tạo đủ 5 bảng chính, khóa ngoại, index và dữ liệu mẫu | Import hoàn tất, có users, customer_types, customers, interactions, follow_up_tasks | Đạt |

## Ghi chú sau kiểm thử

- Các thao tác chính đã có kiểm tra quyền, CSRF và prepared statement qua PDO.
- Dữ liệu demo dùng tiếng Việt, có khách đang hoạt động, khách đã xóa mềm, công việc quá hạn và công việc hoàn thành để phục vụ trình bày.
- Khi nộp bài, nên import lại file bootstrap một lần cuối để dữ liệu trùng với báo cáo kiểm thử.
