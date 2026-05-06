# Kịch Bản Demo Lớp Học

## Mục tiêu

Demo 8-10 phút, tiếng Việt, tập trung chứng minh hệ thống là CRM mini có dashboard, AJAX, phân quyền, quy trình chăm sóc và báo cáo. Không trình bày theo kiểu CRUD từng bảng.

## Chuẩn bị trước khi demo

- Import `co-so-du-lieu/khoi-tao-co-so-du-lieu.sql`.
- Dùng tài khoản admin: `admin` / `Demo@2026`.
- Mở sẵn URL: `http://localhost/quanly_khachhang/`.
- Chuẩn bị một số điện thoại/email đã tồn tại để test trùng lặp, lấy nhanh từ danh sách khách hàng.
- Chuẩn bị một số điện thoại mới, ví dụ `0909000999`, email mới `demo.khachhang@example.com`.

## Thứ tự màn hình

| Thời lượng | Màn hình | Route | Mục đích |
| --- | --- | --- | --- |
| 0:00-0:45 | Đăng nhập | `dang-nhap.php` | Tạo niềm tin về session, CSRF, vai trò người dùng |
| 0:45-2:00 | Dashboard | `bang-dieu-khien.php` | Gây ấn tượng đầu bằng KPI, Top 3 khách hàng, việc quá hạn |
| 2:00-3:00 | Danh sách khách hàng | `khach-hang/` | Chứng minh tìm kiếm, lọc, phân trang AJAX |
| 3:00-4:15 | Thêm khách hàng | `khach-hang/them.php` | Chứng minh kiểm tra trùng phone/email bằng AJAX và server validation |
| 4:15-5:15 | Chi tiết khách hàng | `khach-hang/chi-tiet.php?id=...` | Hồ sơ 360: thông tin, tương tác, công việc đang mở |
| 5:15-6:30 | Thêm tương tác | `tuong-tac/them.php?customer_id=...` | Ghi nhận chăm sóc, tạo follow-up task trong cùng luồng |
| 6:30-7:30 | Công việc theo dõi | `cong-viec-theo-doi/` | Cập nhật trạng thái AJAX, hạn xử lý, ưu tiên |
| 7:30-8:15 | Việc quá hạn | `cong-viec-theo-doi/qua-han.php` | Chứng minh logic thời gian, cảnh báo việc trễ hạn |
| 8:15-9:45 | Báo cáo | `bao-cao/`, `bao-cao/cong-viec.php` | Kết lại bằng thống kê, hiệu quả nhân viên, tỷ lệ hoàn thành |

## Script nói đề xuất

### 1. Đăng nhập

"Em bắt đầu từ màn hình đăng nhập. Hệ thống không hiện sẵn mật khẩu demo trên giao diện để giống sản phẩm thật hơn. Khi đăng nhập, ứng dụng tạo session, kiểm tra CSRF và phân biệt vai trò admin/nhân viên. Nếu đăng nhập sai nhiều lần, tài khoản bị tạm khóa trong 5 phút."

Thao tác: nhập `admin` / `Demo@2026`, đăng nhập.

### 2. Dashboard với Top 3 khách hàng

"Sau khi vào hệ thống, em không đưa thầy cô vào danh sách CRUD ngay. Màn hình đầu tiên là dashboard vận hành: KPI, Top 3 khách hàng nổi bật, việc quá hạn, việc sắp tới và hoạt động gần đây. Top 3 được tính từ dữ liệu chăm sóc, số lần tương tác, trạng thái và lịch hẹn tiếp theo."

Thao tác: chỉ vào Top 3 khách hàng, việc quá hạn, hoạt động gần đây.

### 3. Danh sách khách hàng với AJAX

"Đây là danh sách khách hàng. Điểm cần nhấn mạnh là tìm kiếm và lọc không phải chỉ submit form cơ bản. Khi em gõ từ khóa hoặc đổi bộ lọc loại khách/trạng thái, bảng dữ liệu, tổng số dòng và phân trang được cập nhật bằng JSON/AJAX, không tải lại toàn trang."

Thao tác: tìm theo tên/công ty, đổi loại khách hàng, đổi trạng thái.

### 4. Thêm khách hàng và kiểm tra trùng lặp

"Khi thêm khách hàng, hệ thống có logic nghiệp vụ tránh trùng hồ sơ. Số điện thoại và email được kiểm tra ngay khi nhập bằng AJAX. Nếu bị trùng, giao diện cảnh báo sớm; khi submit, server vẫn kiểm tra lại để tránh việc bỏ qua JavaScript."

Thao tác: nhập phone/email đã tồn tại để hiện cảnh báo, sau đó đổi sang phone/email mới và lưu.

### 5. Trang chi tiết khách hàng

"Sau khi lưu, em mở hồ sơ chi tiết. Trang này biến một bản ghi khách hàng thành hồ sơ làm việc 360 độ: thông tin quản lý, liên hệ, số tương tác, việc đang mở, lịch sử chăm sóc và lịch tiếp theo."

Thao tác: chỉ vào metric "Tương tác đã ghi nhận", "Việc đang mở", danh sách tương tác.

### 6. Thêm tương tác và tạo follow-up

"CRM không dừng ở việc lưu họ tên và số điện thoại. Mỗi lần gọi điện, email hoặc gặp mặt đều được ghi thành tương tác. Ngay trong form này, em có thể tạo công việc theo dõi tiếp theo để không bỏ sót khách."

Thao tác: bấm "Thêm tương tác", nhập tiêu đề, nội dung, kết quả; bật "Tạo công việc theo dõi", nhập hạn xử lý và ưu tiên, lưu.

### 7. Công việc theo dõi và cập nhật AJAX

"Công việc theo dõi có người phụ trách, hạn xử lý, mức ưu tiên và trạng thái. Ở danh sách này, trạng thái có thể đổi nhanh bằng AJAX. Dòng dữ liệu cập nhật badge, trạng thái và có thể rời khỏi bộ lọc nếu không còn phù hợp."

Thao tác: vào `cong-viec-theo-doi/`, đổi trạng thái một việc từ "Chờ xử lý" sang "Đang xử lý" hoặc "Hoàn thành".

### 8. Việc quá hạn

"Đây là phần thể hiện logic thời gian thật. Việc quá hạn không phải dữ liệu tĩnh; hệ thống so sánh hạn xử lý với thời gian hiện tại, chỉ tính việc đang mở. Việc đã hoàn thành hoặc đã hủy không bị tính quá hạn."

Thao tác: mở tab "Quá hạn", chỉ vào badge ưu tiên, hạn xử lý, trạng thái.

### 9. Báo cáo và thống kê

"Phần cuối là báo cáo. Đây là lý do em xem dự án này vượt CRUD: dữ liệu được tổng hợp thành chỉ số quản trị. Báo cáo cho thấy số khách, tương tác, việc đang mở, tỷ lệ hoàn thành, phân bổ theo loại khách và hiệu quả nhân viên."

Thao tác: mở `bao-cao/`, đổi bộ lọc ngày/nhân viên/loại khách nếu kịp; mở `bao-cao/cong-viec.php` để kết bằng tỷ lệ hoàn thành và việc quá hạn.

## Vì sao thứ tự này hiệu quả khi chấm điểm

1. Mở đầu bằng dashboard nên tạo ấn tượng sản phẩm hoàn chỉnh, không phải bài tập thêm-sửa-xóa.
2. Dòng demo đi theo một nghiệp vụ thật: phát hiện khách quan trọng, tìm hồ sơ, tạo khách mới, chăm sóc, hẹn việc, xử lý quá hạn, xem báo cáo.
3. AJAX được đặt đúng lúc: tìm/lọc khách hàng, kiểm tra trùng lặp, cập nhật trạng thái công việc.
4. Phân quyền/session được nhắc ngay đầu và có bằng chứng qua tài khoản admin, menu quản trị, CSRF, khóa đăng nhập sai.
5. Báo cáo nằm cuối để kết luận rằng dữ liệu nhập vào được biến thành thông tin quản trị.

## Điểm khác biệt cần nhấn mạnh

- Dashboard hiện đại: KPI, Top 3 khách hàng, việc quá hạn, việc sắp tới, timeline gần đây.
- Giao diện tiếng Việt đầy đủ: nhãn nút, thông báo, trạng thái, báo cáo đều phù hợp người dùng Việt Nam.
- AJAX thật: tìm kiếm/lọc, kiểm tra trùng, cập nhật trạng thái công việc.
- Session/role management: admin/nhân viên, chặn trang không có quyền, khóa đăng nhập sai, CSRF cho POST.
- Nghiệp vụ vượt CRUD: xóa mềm/khôi phục, chặn xóa khi còn việc mở, follow-up task, quá hạn, tỷ lệ hoàn thành, hiệu quả nhân viên.

## Câu kết

"Tóm lại, dự án không chỉ quản lý danh sách khách hàng. Nó mô phỏng một quy trình CRM nhỏ: ai phụ trách khách nào, đã chăm sóc ra sao, bước tiếp theo là gì, việc nào quá hạn và nhân viên đang xử lý hiệu quả như thế nào."

## Câu hỏi chưa rõ

- Không có.
