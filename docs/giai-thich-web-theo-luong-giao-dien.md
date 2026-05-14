# Giải Thích Web Theo Luồng Giao Diện

## Tổng Quan

Tài liệu này giải thích theo cách người dùng lướt web từ trên xuống dưới, từ trái sang phải. Mỗi vị trí trên giao diện đều chỉ ra code render hoặc code xử lý liên quan.

Phạm vi bao phủ:

- Trang public: đăng nhập, không có quyền.
- Layout chung sau đăng nhập: navbar, sidebar, flash message, footer, xác nhận thao tác.
- Dashboard.
- Khách hàng: danh sách, thêm/sửa form, chi tiết, xóa mềm/khôi phục.
- Tương tác: danh sách timeline, thêm/sửa form, xóa.
- Công việc theo dõi: 3 tab danh sách, thêm/sửa form, hoàn thành, đổi trạng thái AJAX.
- Báo cáo: tổng quan, khách hàng, tương tác, công việc, bộ lọc, tab báo cáo.
- Quản trị: loại khách hàng, người dùng, chi tiết người dùng, đổi mật khẩu.
- JavaScript/AJAX làm giao diện động.

Ghi chú: máy hiện không kết nối được `http://localhost/quanly_khachhang/`, nên tài liệu được đối chiếu trực tiếp từ mã nguồn.

## Luồng Vào Web

### 1. Mở `/`

Người dùng vào root dự án sẽ được chuyển thẳng sang dashboard.

- Code: `index.php:5` nạp bootstrap chung.
- Code: `index.php:7` gọi `chuyen_huong('bang-dieu-khien.php')`.
- Ý nghĩa: root không render giao diện riêng, chỉ là entry redirect.

### 2. Nếu Chưa Đăng Nhập

Các trang cần đăng nhập đều nạp kiểm tra quyền:

- Code: `bang-dieu-khien.php:6`, `khach-hang/index.php:6`, `tuong-tac/index.php:6`, `cong-viec-theo-doi/index.php:6`, `bao-cao/index.php:6`.
- Helper kiểm tra: `dung-chung/kiem-tra-dang-nhap.php` gọi logic xác thực.
- Nếu chưa đăng nhập, hệ thống chuyển về `dang-nhap.php`.

## Trang Đăng Nhập

URL: `dang-nhap.php`

### Vị Trí 1: Logic Trước Khi Hiển Thị

- Code: `dang-nhap.php:5` nạp `dung-chung/khoi-dong.php`.
- Code: `dang-nhap.php:7-9` nếu đã đăng nhập thì chuyển sang dashboard.
- Code: `dang-nhap.php:11-14` chuẩn bị tiêu đề, tài khoản, mật khẩu, lỗi.
- Code: `dang-nhap.php:16-47` xử lý POST đăng nhập.

Chức năng:

- Bắt CSRF: `dang-nhap.php:17`.
- Kiểm tra thiếu tài khoản/mật khẩu: `dang-nhap.php:19-20`.
- Chặn brute-force tạm thời: `dang-nhap.php:21-22`.
- Tìm user, kiểm tra password hash: `dang-nhap.php:25-29`.
- Chặn tài khoản bị khóa: `dang-nhap.php:30-32`.
- Lưu session, cập nhật `last_login_at`, báo thành công, chuyển dashboard: `dang-nhap.php:34-41`.

### Vị Trí 2: Head HTML

- Code: `dang-nhap.php:49-57`.
- Hiển thị metadata HTML, title, Bootstrap CSS, CSS app.

### Vị Trí 3: Panel Giới Thiệu

- Code: `dang-nhap.php:58-65`.
- Người dùng thấy nền login, nhãn "Quản lý khách hàng", tiêu đề "Đăng nhập hệ thống", mô tả hệ thống CRM.

### Vị Trí 4: Form Đăng Nhập

- Code: `dang-nhap.php:67-104`.
- Trường ẩn CSRF: `dang-nhap.php:68`.
- Alert lỗi đăng nhập: `dang-nhap.php:69-71`.
- Alert flash message: `dang-nhap.php:73-76`.
- Input tài khoản/email: `dang-nhap.php:78-90`.
- Input mật khẩu: `dang-nhap.php:92-101`.
- Nút "Đăng nhập" và gợi ý tài khoản demo: `dang-nhap.php:103-104`.

## Layout Chung Sau Đăng Nhập

Các trang nội bộ đều dùng `giao-dien/dau-trang.php` ở đầu và `giao-dien/cuoi-trang.php` ở cuối.

### Vị Trí 1: Head Chung

- Code: `giao-dien/dau-trang.php:7-15`.
- Hiển thị HTML5, `lang="vi"`, title theo biến `$tieuDe`, Bootstrap CSS, `ung-dung.css`.

### Vị Trí 2: Skip Link

- Code: `giao-dien/dau-trang.php:17`.
- Link "Bỏ qua menu" đưa focus tới `#noi-dung-chinh`, hỗ trợ dùng bàn phím.

### Vị Trí 3: Thanh Điều Hướng Trên Cùng

- Code: `giao-dien/dau-trang.php:18` nạp `giao-dien/thanh-dieu-huong.php`.
- Code navbar: `giao-dien/thanh-dieu-huong.php:1-20`.
- Người dùng thấy brand "CRM" + tên ứng dụng.
- Nếu đã đăng nhập: hiển thị họ tên, vai trò, form nút "Đăng xuất".
- Nếu chưa đăng nhập: hiển thị nút "Đăng nhập".

### Vị Trí 4: Sidebar Trái

- Code: `giao-dien/dau-trang.php:20` chỉ nạp sidebar khi đã đăng nhập.
- Code sidebar: `giao-dien/thanh-ben.php:1-28`.
- Nhóm "Tổng quan": link Bảng điều khiển.
- Nhóm "Chăm sóc khách hàng": link Khách hàng, Tương tác, Công việc theo dõi, Báo cáo.
- Nhóm "Quản trị": link Loại khách hàng, Người dùng, chỉ hiện khi `la_admin()`.
- Active state tính bằng URL hiện tại: `giao-dien/thanh-ben.php:3-12`.

### Vị Trí 5: Vùng Nội Dung Chính

- Code: `giao-dien/dau-trang.php:21-23`.
- Mọi trang render nội dung vào `<main class="app-main" id="noi-dung-chinh">`.
- Flash message được nạp tại `giao-dien/dau-trang.php:22`.

### Vị Trí 6: Flash Message

- Code: `giao-dien/thong-bao.php:1-9`.
- Nếu có thông báo trong session, hiển thị alert Bootstrap có nút đóng.
- Nội dung lấy qua `lay_thong_bao()`.

### Vị Trí 7: Footer

- Code: `giao-dien/cuoi-trang.php:1` nạp `giao-dien/chan-trang.php`.
- Code footer: `giao-dien/chan-trang.php:1-5`.
- Hiển thị tên ứng dụng, "Demo môn Lập trình Web", "PHP thuần + MySQL/MariaDB trên XAMPP".

### Vị Trí 8: Script Chung

- Code: `giao-dien/cuoi-trang.php:3-15`.
- Gắn `window.APP_BASE_URL`, `window.APP_CSRF_TOKEN`.
- Nạp Bootstrap JS, `ung-dung.js`, các file AJAX khách hàng và công việc.

### Vị Trí 9: Xác Nhận Thao Tác

- Code modal: `giao-dien/hop-thoai-xac-nhan.php:1-15`.
- Gồm tiêu đề "Xác nhận thao tác", body sẽ được JS gắn nội dung, nút Hủy, nút Xác nhận.
- Hiện tại JS chung dùng `window.confirm` cho các phần tử có `data-confirm-message`: `tai-nguyen/js/ung-dung.js:27-35`.

## Dashboard

URL: `bang-dieu-khien.php`

### Vị Trí 1: Chuẩn Bị Dữ Liệu

- Code: `bang-dieu-khien.php:5-8` nạp bootstrap, kiểm tra đăng nhập, nạp dữ liệu dashboard.
- Code: `bang-dieu-khien.php:9-11` nếu lỗi DB thì hiện trang lỗi dữ liệu.
- Dữ liệu lấy từ `bao-cao/du-lieu-bang-dieu-khien.php`.

### Vị Trí 2: Header Dashboard

- Code: `bang-dieu-khien.php:34-45`.
- Hiển thị eyebrow "Tổng quan CRM", tiêu đề "Việc cần xử lý hôm nay", mô tả dashboard.
- Bên phải hiển thị "Phạm vi", tên phạm vi dữ liệu, ngày hiện tại.

### Vị Trí 3: Cột KPI Nhanh

- Code: `bang-dieu-khien.php:48-57`.
- Lặp `$kpiCards`.
- Mỗi card hiển thị nhãn, giá trị số, mô tả.
- Dữ liệu KPI tính ở `bao-cao/du-lieu-bang-dieu-khien.php:20-69`.

### Vị Trí 4: Top 3 Khách Hàng Nổi Bật

- Code heading: `bang-dieu-khien.php:60-66`.
- Nếu rỗng, hiện empty state: `bang-dieu-khien.php:68-72`.
- Nếu có dữ liệu, lặp podium card: `bang-dieu-khien.php:74-109`.
- Mỗi khách hiển thị rank, loại khách, họ tên, công ty/thành phố, điểm chăm sóc, phụ trách, số tương tác, trạng thái, lịch tới.
- Dữ liệu tính điểm tại `bao-cao/du-lieu-bang-dieu-khien.php:71-134`.

### Vị Trí 5: Việc Quá Hạn

- Code: `bang-dieu-khien.php:113-139`.
- Header hiển thị số việc quá hạn và link "Xử lý" sang `cong-viec-theo-doi/qua-han.php`.
- Mỗi dòng hiển thị tiêu đề việc, khách hàng, trạng thái, độ ưu tiên, hạn xử lý.
- Nếu rỗng, hiện "Không có việc quá hạn" và nút xem công việc.

### Vị Trí 6: Việc Sắp Tới

- Code: `bang-dieu-khien.php:141-167`.
- Header hiển thị số việc sắp tới và link "Xem tất cả".
- Mỗi dòng hiển thị tiêu đề, khách hàng, nhân viên phụ trách, ưu tiên, hạn xử lý.
- Nếu rỗng, hiện CTA "Tạo công việc".

### Vị Trí 7: Hoạt Động Gần Đây

- Code: `bang-dieu-khien.php:169-193`.
- Hiển thị timeline các tương tác gần nhất.
- Mỗi item có tiêu đề, loại tương tác, khách hàng, thời gian, nhân viên ghi nhận.
- Nếu rỗng, hiện CTA "Thêm tương tác".

## Khách Hàng

### Trang Danh Sách

URL: `khach-hang/`

#### Vị Trí 1: Header

- Code: `khach-hang/index.php:27-39`.
- Hiển thị tiêu đề "Khách hàng", mô tả, nút "Thêm khách hàng".
- Nạp bảng danh sách từ `khach-hang/danh-sach-khach-hang.php`.

#### Vị Trí 2: Bộ Lọc

- Code: `khach-hang/danh-sach-khach-hang.php:9-49`.
- Ô tìm kiếm họ tên/điện thoại/email: `:12-13`.
- Select loại khách: `:16-22`.
- Select phụ trách: `:25-31`, staff không được đổi vì disabled nếu không admin.
- Select trạng thái: `:34-43`, admin có thêm "Đã xóa mềm".
- Nút "Lọc danh sách": `:45-47`.
- Logic lọc server ở `khach-hang/truy-van-khach-hang.php:5-63`.

#### Vị Trí 3: Tóm Tắt Kết Quả

- Code: `khach-hang/danh-sach-khach-hang.php:51-58`.
- Hiển thị tổng số hồ sơ phù hợp và trạng thái AJAX.

#### Vị Trí 4: Bảng Khách Hàng

- Code bảng: `khach-hang/danh-sach-khach-hang.php:59-148`.
- Cột: Khách hàng, Liên hệ, Loại, Phụ trách, Trạng thái, Thao tác: `:69-77`.
- Mỗi dòng hiển thị:
  - Tên khách, công ty/thành phố, số tương tác, lịch tới: `:83-89`.
  - Số điện thoại, email: `:91-95`.
  - Loại khách với màu: `:97-100`.
  - Người phụ trách: `:102`.
  - Badge trạng thái: `:103-107`.
  - Nút Xem, Sửa, Xóa mềm hoặc Khôi phục tùy quyền/trạng thái: `:108-127`.
- Empty state: `:131-144`.

#### Vị Trí 5: Phân Trang

- Code: `khach-hang/danh-sach-khach-hang.php:150-154`.
- Nút Trước/Sau và số trang hiện tại.
- Tính phân trang từ `dung-chung/ham-phan-trang.php`.

#### Vị Trí 6: AJAX Lọc Danh Sách

- Code JS bind form: `tai-nguyen/js/ajax-khach-hang-danh-sach.js:1-25`.
- Khi submit, gõ từ khóa, đổi select hoặc bấm phân trang, gọi AJAX: `:27-49`.
- Render lại rows: `:51-68`.
- Render lại phân trang: `:70-89`.
- Endpoint server: `xu-ly-ajax/loc-khach-hang.php`.

### Trang Thêm/Sửa Khách Hàng

URL: `khach-hang/them.php`, `khach-hang/sua.php?id=...`

#### Vị Trí 1: Header Trang

- Thêm: `khach-hang/them.php:40-51`.
- Sửa: `khach-hang/sua.php:68-79`.
- Cả hai nạp form chung `khach-hang/bieu-mau.php`.

#### Vị Trí 2: Form - Thông Tin Chính

- Code: `khach-hang/bieu-mau.php:12-68`.
- Tiêu đề form: `:16`.
- Họ tên bắt buộc: `:27-29`.
- Công ty: `:32-34`.
- Giới tính: `:37-42`.
- Ngày sinh: `:45-47`.
- Loại khách hàng bắt buộc: `:50-57`.
- Nguồn khách: `:60-65`.

#### Vị Trí 3: Form - Liên Hệ

- Code: `khach-hang/bieu-mau.php:70-99`.
- Số điện thoại: `:77-80`.
- Email: `:83-86`.
- Địa chỉ: `:89-91`.
- Tỉnh/thành: `:94-96`.
- Phone/email có `data-duplicate-field` để JS kiểm tra trùng.

#### Vị Trí 4: Form - Phân Công & Trạng Thái

- Code: `khach-hang/bieu-mau.php:101-131`.
- Nhân viên phụ trách bắt buộc: `:108-116`, staff bị disabled và dùng hidden input.
- Trạng thái chăm sóc: `:119-124`.
- Ghi chú chăm sóc: `:127-128`.

#### Vị Trí 5: Nút Form

- Code: `khach-hang/bieu-mau.php:133-136`.
- Nút Hủy về danh sách.
- Nút submit theo biến `$nhanNut`.

#### Vị Trí 6: Kiểm Tra Trùng Phone/Email

- Code JS: `tai-nguyen/js/ajax-khach-hang-trung-lap.js:1-15`.
- Khi input/blur, gọi endpoint: `:17-38`.
- Cập nhật trạng thái trùng/có thể dùng: `:40-45`.
- Endpoint server: `xu-ly-ajax/kiem-tra-trung-khach-hang.php`.

### Trang Chi Tiết Khách Hàng

URL: `khach-hang/chi-tiet.php?id=...`

#### Vị Trí 1: Hero Hồ Sơ

- Code: `khach-hang/chi-tiet.php:26-59`.
- Hiển thị họ tên, công ty/thành phố, loại khách, trạng thái.
- Nút Quay lại, Thêm tương tác, Sửa hồ sơ, Xóa mềm hoặc Khôi phục.

#### Vị Trí 2: Thông Tin Quản Lý

- Code: `khach-hang/chi-tiet.php:61-72`.
- Hiển thị phụ trách, email nhân viên, nguồn khách, ngày tạo, cập nhật, xóa mềm.

#### Vị Trí 3: Liên Hệ

- Code: `khach-hang/chi-tiet.php:74-84`.
- Hiển thị điện thoại, email, địa chỉ, tỉnh/thành, giới tính, ngày sinh.

#### Vị Trí 4: Metric Card

- Code: `khach-hang/chi-tiet.php:86-96`.
- Hiển thị số tương tác đã ghi nhận và số việc đang mở.

#### Vị Trí 5: Tương Tác Gần Đây

- Code: `khach-hang/chi-tiet.php:99-132`.
- Hiển thị timeline tương tác, loại tương tác, nội dung, kết quả, thời gian, người ghi.
- Có nút Ghi nhận nếu khách chưa bị xóa.
- Có nút Sửa/Xóa nếu user có quyền.

#### Vị Trí 6: Công Việc Theo Dõi Của Khách

- Code: `khach-hang/chi-tiet.php:142-156`.
- Hiển thị các việc liên quan khách hàng, trạng thái, ưu tiên, hạn xử lý.
- Dữ liệu lấy bằng `lay_cong_viec_cua_khach_hang()` ở `khach-hang/truy-van-khach-hang.php:150-164`.

#### Vị Trí 7: Ghi Chú Chăm Sóc

- Code: `khach-hang/chi-tiet.php:159-164`.
- Chỉ hiển thị nếu khách hàng có `notes`.
- Nội dung được escape rồi giữ xuống dòng bằng `nl2br(e(...))`.

### Xóa Mềm Và Khôi Phục

- Xóa mềm submit tới `khach-hang/xoa-mem.php` từ danh sách `khach-hang/danh-sach-khach-hang.php:120-124` hoặc chi tiết `khach-hang/chi-tiet.php:51-55`.
- Khôi phục submit tới `khach-hang/khoi-phuc.php` từ danh sách `:112-116` hoặc chi tiết `khach-hang/chi-tiet.php:42-46`.
- Logic lưu/xóa/khôi phục ở `khach-hang/luu-khach-hang.php:152-194`.

## Tương Tác

### Trang Danh Sách Timeline

URL: `tuong-tac/`

#### Vị Trí 1: Header

- Code: `tuong-tac/index.php:16-23`.
- Hiển thị "Tương tác khách hàng", mô tả, nút "Thêm tương tác".

#### Vị Trí 2: Bộ Lọc Theo Khách Hàng

- Code: `tuong-tac/index.php:25-42`.
- Select khách hàng: `:28-36`.
- Nút "Lọc lịch sử": `:38-40`.
- Dữ liệu khách lấy từ `tuong-tac/truy-van-tuong-tac.php:15-25`.

#### Vị Trí 3: Timeline

- Code: `tuong-tac/index.php:44-82`.
- Mỗi item hiển thị badge loại tương tác, tiêu đề, khách hàng, nhân viên, thời gian, nội dung, kết quả.
- Nút Sửa/Xóa chỉ hiện nếu `co_the_sua_xoa_tuong_tac()`: `:63-72`.
- Empty state nếu chưa có tương tác: `:76-81`.

### Trang Thêm/Sửa Tương Tác

URL: `tuong-tac/them.php`, `tuong-tac/sua.php?id=...`

#### Vị Trí 1: Header Trang

- Thêm: `tuong-tac/them.php:44-55`.
- Sửa: `tuong-tac/sua.php:57-68`.
- Cả hai nạp form chung `tuong-tac/bieu-mau.php`.

#### Vị Trí 2: Form Tương Tác

- Code: `tuong-tac/bieu-mau.php:12-62`.
- Khách hàng bắt buộc: `:21-31`.
- Loại tương tác: `:33-40`.
- Thời gian: `:41-45`.
- Tiêu đề bắt buộc: `:47-50`.
- Kết quả: `:52-55`.
- Nội dung trao đổi bắt buộc: `:57-60`.

#### Vị Trí 3: Tạo Công Việc Sau Tương Tác

- Code: `tuong-tac/bieu-mau.php:64-91`.
- Chỉ hiện khi `$hienThiTaoCongViec = true`, thường ở trang thêm.
- Checkbox "Tạo công việc theo dõi sau tương tác": `:66-69`.
- Tiêu đề công việc: `:71-74`.
- Hạn xử lý: `:76-79`.
- Ưu tiên: `:81-88`.
- JS tự bật required khi checkbox được chọn: `tai-nguyen/js/ung-dung.js:13-25`.

#### Vị Trí 4: Nút Form

- Code: `tuong-tac/bieu-mau.php:93-96`.
- Nút Hủy về timeline.
- Nút submit lưu tương tác.

#### Vị Trí 5: Lưu/Xóa

- Tạo tương tác: `tuong-tac/luu-tuong-tac.php:20-52`.
- Cập nhật tương tác: `tuong-tac/luu-tuong-tac.php:55-86`.
- Xóa tương tác: `tuong-tac/luu-tuong-tac.php:100-102`.
- Tạo follow-up từ tương tác: `tuong-tac/luu-tuong-tac.php:105-149`.

## Công Việc Theo Dõi

### Trang Danh Sách

URL:

- `cong-viec-theo-doi/`: công việc của tôi.
- `cong-viec-theo-doi/qua-han.php`: quá hạn.
- `cong-viec-theo-doi/sap-toi.php`: sắp tới.

#### Vị Trí 1: Header Từng Trang

- Công việc của tôi: `cong-viec-theo-doi/index.php:18-29`.
- Quá hạn: `cong-viec-theo-doi/qua-han.php:16-27`.
- Sắp tới: `cong-viec-theo-doi/sap-toi.php:16-27`.
- Cả ba nạp `cong-viec-theo-doi/danh-sach-cong-viec.php`.

#### Vị Trí 2: Tabs Và Nút Thêm

- Code: `cong-viec-theo-doi/danh-sach-cong-viec.php:13-20`.
- Tabs: Công việc của tôi, Quá hạn, Sắp tới.
- Nút "Thêm công việc".

#### Vị Trí 3: Heading Bảng Và Feedback AJAX

- Code: `cong-viec-theo-doi/danh-sach-cong-viec.php:22-29`.
- Hiển thị tiêu đề danh sách, mô tả, dòng "Có thể cập nhật nhanh bằng AJAX".

#### Vị Trí 4: Bảng Công Việc

- Code: `cong-viec-theo-doi/danh-sach-cong-viec.php:31-116`.
- Cột: Công việc, Khách hàng, Phụ trách, Hạn xử lý, Ưu tiên, Trạng thái, Thao tác.
- Mỗi dòng:
  - Tiêu đề và mô tả: `:54-59`.
  - Khách hàng và công ty/cá nhân: `:60-63`.
  - Người phụ trách: `:64`.
  - Hạn và nhãn quá hạn/đang theo dõi/đã đóng: `:65-70`.
  - Ưu tiên: `:71-75`.
  - Badge trạng thái và select đổi nhanh: `:76-86`.
  - Nút Sửa, nút Hoàn thành nếu việc còn mở: `:88-98`.
- Empty state: `:102-112`.

#### Vị Trí 5: AJAX Đổi Trạng Thái

- Code JS bind select: `tai-nguyen/js/ajax-cong-viec-theo-doi.js:1-10`.
- Gửi POST id/status/CSRF: `:12-28`.
- Nếu thành công, cập nhật badge, row class, nút hoàn thành: `:44-62`.
- Nếu ở tab quá hạn/sắp tới và việc không còn phù hợp bộ lọc, xóa dòng khỏi bảng: `:64-89`.
- Endpoint server: `xu-ly-ajax/cap-nhat-trang-thai-cong-viec.php`.

### Trang Thêm/Sửa Công Việc

URL: `cong-viec-theo-doi/them.php`, `cong-viec-theo-doi/sua.php?id=...`

#### Vị Trí 1: Header Trang

- Thêm: `cong-viec-theo-doi/them.php:38-49`.
- Sửa: `cong-viec-theo-doi/sua.php:58-69`.
- Cả hai nạp form chung `cong-viec-theo-doi/bieu-mau.php`.

#### Vị Trí 2: Form - Thông Tin Chính

- Code: `cong-viec-theo-doi/bieu-mau.php:11-47`.
- Khách hàng bắt buộc: `:25-35`.
- Tiêu đề công việc bắt buộc: `:37-40`.
- Mô tả: `:42-45`.

#### Vị Trí 3: Form - Phân Công & Trạng Thái

- Code: `cong-viec-theo-doi/bieu-mau.php:49-91`.
- Nhân viên phụ trách bắt buộc, staff bị disabled: `:55-66`.
- Hạn xử lý bắt buộc: `:68-71`.
- Ưu tiên: `:73-79`.
- Trạng thái: `:81-88`.

#### Vị Trí 4: Nút Form

- Code: `cong-viec-theo-doi/bieu-mau.php:93-96`.
- Hủy về danh sách.
- Submit lưu công việc.

#### Vị Trí 5: Hoàn Thành Nhanh

- Nút hoàn thành trong bảng submit tới `cong-viec-theo-doi/hoan-thanh.php`: `cong-viec-theo-doi/danh-sach-cong-viec.php:92-96`.
- Handler cập nhật trạng thái và báo kết quả: `cong-viec-theo-doi/hoan-thanh.php:5-14`.
- Logic cập nhật trạng thái: `cong-viec-theo-doi/luu-cong-viec.php:78-115`.

## Báo Cáo

### Thành Phần Chung Của Báo Cáo

#### Vị Trí 1: Tabs Báo Cáo

- Code: `bao-cao/thanh-dieu-huong-bao-cao.php:1-11`.
- Tabs: Tổng quan, Khách hàng, Tương tác, Công việc.
- Active tab tính theo file hiện tại.

#### Vị Trí 2: Bộ Lọc Báo Cáo

- Code: `bao-cao/bo-loc-bao-cao.php:7-39`.
- Từ ngày: `:9-11`.
- Đến ngày: `:13-15`.
- Nhân viên phụ trách: `:17-24`, staff bị disabled.
- Loại khách hàng: `:26-33`.
- Nút "Lọc báo cáo": `:35-37`.
- Logic chuẩn hóa filter: `bao-cao/ham-bo-loc-bao-cao.php:5-87`.

### Báo Cáo Tổng Quan

URL: `bao-cao/`

#### Vị Trí 1: Header

- Code: `bao-cao/index.php:28-34`.
- Hiển thị "Tổng quan báo cáo" và mô tả mục đích thống kê.

#### Vị Trí 2: KPI Tổng Quan

- Code: `bao-cao/index.php:39-60`.
- Khách hàng đang lưu.
- Tương tác theo lọc hoặc 30 ngày.
- Việc đang mở.
- Tỷ lệ hoàn thành việc với progress bar.

#### Vị Trí 3: Card Điều Hướng Báo Cáo Con

- Code: `bao-cao/index.php:62-103`.
- Card khách hàng: top 3 loại khách và link báo cáo khách hàng.
- Card công việc: trạng thái việc và link báo cáo công việc.
- Card hiệu quả nhân viên: nhân viên nổi bật và link tới section hiệu quả.

### Báo Cáo Khách Hàng

URL: `bao-cao/khach-hang.php`

- Header: `bao-cao/khach-hang.php:27-33`.
- KPI tổng khách, đang chăm sóc, tiềm năng: `:38-54`.
- Biểu đồ danh sách khách hàng theo loại: `:56-82`.
- Bảng khách hàng theo nhân viên: `:84-124`.
- Dữ liệu lấy từ `lay_khach_hang_theo_loai_bao_cao()` và `lay_khach_hang_theo_nhan_vien_bao_cao()` trong `bao-cao/du-lieu-bao-cao.php`.

### Báo Cáo Tương Tác

URL: `bao-cao/tuong-tac.php`

- Header: `bao-cao/tuong-tac.php:31-37`.
- KPI tương tác theo lọc/30 ngày, tổng tương tác, nhân viên có hoạt động: `:42-58`.
- Chart list tương tác theo thời gian: `:60-82`.
- Tương tác theo loại/kênh liên hệ: `:84-109`.
- Bảng tương tác theo nhân viên phụ trách: `:111-138`.

### Báo Cáo Công Việc

URL: `bao-cao/cong-viec.php`

- Header: `bao-cao/cong-viec.php:38-44`.
- KPI tổng công việc, đang mở, quá hạn, tỷ lệ hoàn thành: `:49-70`.
- Công việc theo trạng thái: `:72-99`.
- Card giải thích ý nghĩa demo: `:101-110`.
- Bảng hiệu quả nhân viên: `:113-163`.

## Quản Trị Loại Khách Hàng

Chỉ admin thấy menu và vào được trang này.

### Trang Danh Sách Loại Khách Hàng

URL: `loai-khach-hang/`

- Kiểm tra admin: `loai-khach-hang/index.php:6`.
- Header và nút thêm: `loai-khach-hang/index.php:14-21`.
- Bảng: `loai-khach-hang/index.php:23-98`.
- Cột: loại khách hàng, điểm, trạng thái, số khách, cập nhật, thao tác: `:26-34`.
- Mỗi dòng hiển thị swatch màu, tên, mô tả, điểm ưu tiên, badge đang dùng/ngừng dùng, số khách, thời gian cập nhật: `:46-69`.
- Nút Sửa: `:72`.
- Nút Xóa hoặc Ngừng dùng tùy số khách đang tham chiếu: `:73-85`.
- Empty state: `:90-94`.

### Trang Thêm/Sửa Loại Khách Hàng

URL: `loai-khach-hang/them.php`, `loai-khach-hang/sua.php?id=...`

- Form chung: `loai-khach-hang/bieu-mau.php:11-98`.
- Tên loại bắt buộc: `:20-32`.
- Điểm ưu tiên 0-100: `:35-48`.
- Màu hiển thị: `:51-62`.
- Mô tả: `:65-76`.
- Switch đang sử dụng: `:79-90`.
- Nút Hủy/Lưu: `:94-97`.
- Validate và xóa/ngừng dùng ở `loai-khach-hang/ham-loai-khach-hang.php`.

## Quản Trị Người Dùng

Chỉ admin thấy menu và vào được trang này.

### Trang Danh Sách Người Dùng

URL: `nguoi-dung/`

- Kiểm tra admin: `nguoi-dung/index.php:6`.
- Query danh sách user: `nguoi-dung/index.php:9-13`.
- Header và nút thêm: `nguoi-dung/index.php:25-32`.
- KPI tổng tài khoản, quản trị, nhân viên, đang hoạt động: `:34-55`.
- Bảng người dùng: `:57-105`.
- Cột: người dùng, liên hệ, vai trò, trạng thái, lần đăng nhập gần nhất, ngày tạo, thao tác: `:60-69`.
- Mỗi dòng hiển thị họ tên, username, email, phone, vai trò, trạng thái, last login, ngày tạo: `:72-89`.
- Nút Xem, Sửa, Mật khẩu: `:91-95`.
- Empty state: `:99-101`.

### Trang Thêm/Sửa Người Dùng

URL: `nguoi-dung/them.php`, `nguoi-dung/sua.php?id=...`

- Form chung: `nguoi-dung/bieu-mau.php:10-85`.
- Họ tên: `:19-22`.
- Tên đăng nhập: `:24-27`.
- Email: `:29-32`.
- Số điện thoại: `:34-37`.
- Vai trò: `:39-46`.
- Trạng thái: `:48-55`.
- Khi thêm mới có khối mật khẩu ban đầu: `:60-79`.
- Nút Hủy/Lưu: `:81-84`.

### Trang Chi Tiết Người Dùng

URL: `nguoi-dung/chi-tiet.php?id=...`

- Header họ tên, username, email và nút Quay lại/Sửa/Đổi mật khẩu: `nguoi-dung/chi-tiet.php:24-35`.
- KPI khách phụ trách, tương tác đã ghi, việc đang mở: `:37-50`.
- Hồ sơ đăng nhập: vai trò, phone, last login, ngày tạo, cập nhật, trạng thái: `:52-67`.

### Trang Đổi Mật Khẩu

URL: `nguoi-dung/doi-mat-khau.php?id=...`

- Header: `nguoi-dung/doi-mat-khau.php:38-44`.
- Form mật khẩu mới: `:46-71`.
- Trường mật khẩu mới: `:55-58`.
- Trường xác nhận mật khẩu: `:60-63`.
- Nút Hủy/Lưu mật khẩu: `:67-70`.
- Xử lý POST và validate: `:19-33`.

## Trang Không Có Quyền

URL: `khong-co-quyen.php`

- Code: `khong-co-quyen.php:10-19`.
- Hiển thị eyebrow "Phân quyền", tiêu đề "Không có quyền truy cập", mô tả, nút về dashboard hoặc đăng nhập.
- Dùng khi user không đủ vai trò, ví dụ staff vào trang admin.

## Đăng Xuất

URL: `dang-xuat.php`

- Code: `dang-xuat.php:5-11`.
- Chỉ nhận POST: `:7`.
- Xóa session: `:8`.
- Đặt thông báo thành công: `:9`.
- Chuyển về đăng nhập: `:11`.
- Form đăng xuất nằm ở navbar: `giao-dien/thanh-dieu-huong.php:11-15`.

## JavaScript Chung

### Validate Form Bootstrap

- Code: `tai-nguyen/js/ung-dung.js:1-11`.
- Khi submit mọi form, nếu HTML5 validation không hợp lệ thì chặn submit và thêm class `was-validated`.

### Bật Required Cho Follow-Up Từ Tương Tác

- Code: `tai-nguyen/js/ung-dung.js:13-25`.
- Khi tick "Tạo công việc theo dõi sau tương tác", trường tiêu đề việc và hạn xử lý trở thành bắt buộc.

### Xác Nhận Trước Khi Xóa/Ngừng Dùng

- Code: `tai-nguyen/js/ung-dung.js:27-35`.
- Mọi nút có `data-confirm-message` sẽ hiện xác nhận trước khi submit.
- Vị trí dùng: xóa mềm khách hàng, xóa tương tác, xóa/ngừng dùng loại khách hàng.

## Endpoint AJAX Không Render Trang Nhưng Tác Động Giao Diện

### Lọc Khách Hàng

- Endpoint: `xu-ly-ajax/loc-khach-hang.php`.
- JS gọi tại `tai-nguyen/js/ajax-khach-hang-danh-sach.js:35`.
- Cập nhật bảng, tổng số, phân trang, URL query.

### Kiểm Tra Trùng Khách Hàng

- Endpoint: `xu-ly-ajax/kiem-tra-trung-khach-hang.php`.
- JS gọi tại `tai-nguyen/js/ajax-khach-hang-trung-lap.js:31`.
- Hiển thị phản hồi ngay dưới phone/email trong form khách hàng.

### Cập Nhật Trạng Thái Công Việc

- Endpoint: `xu-ly-ajax/cap-nhat-trang-thai-cong-viec.php`.
- JS gọi tại `tai-nguyen/js/ajax-cong-viec-theo-doi.js:22`.
- Cập nhật badge, class dòng, nút hoàn thành, hoặc xóa dòng khỏi tab hiện tại nếu không còn phù hợp.

## Bản Đồ Route Nhanh

| URL | Giao diện chính | File render |
| --- | --- | --- |
| `/` | Redirect dashboard | `index.php` |
| `/dang-nhap.php` | Login | `dang-nhap.php` |
| `/bang-dieu-khien.php` | Dashboard | `bang-dieu-khien.php` |
| `/khach-hang/` | Danh sách khách | `khach-hang/index.php`, `khach-hang/danh-sach-khach-hang.php` |
| `/khach-hang/them.php` | Thêm khách | `khach-hang/them.php`, `khach-hang/bieu-mau.php` |
| `/khach-hang/sua.php` | Sửa khách | `khach-hang/sua.php`, `khach-hang/bieu-mau.php` |
| `/khach-hang/chi-tiet.php` | Chi tiết khách | `khach-hang/chi-tiet.php` |
| `/tuong-tac/` | Timeline tương tác | `tuong-tac/index.php` |
| `/tuong-tac/them.php` | Thêm tương tác | `tuong-tac/them.php`, `tuong-tac/bieu-mau.php` |
| `/tuong-tac/sua.php` | Sửa tương tác | `tuong-tac/sua.php`, `tuong-tac/bieu-mau.php` |
| `/cong-viec-theo-doi/` | Công việc của tôi | `cong-viec-theo-doi/index.php`, `danh-sach-cong-viec.php` |
| `/cong-viec-theo-doi/qua-han.php` | Việc quá hạn | `cong-viec-theo-doi/qua-han.php`, `danh-sach-cong-viec.php` |
| `/cong-viec-theo-doi/sap-toi.php` | Việc sắp tới | `cong-viec-theo-doi/sap-toi.php`, `danh-sach-cong-viec.php` |
| `/cong-viec-theo-doi/them.php` | Thêm việc | `cong-viec-theo-doi/them.php`, `bieu-mau.php` |
| `/cong-viec-theo-doi/sua.php` | Sửa việc | `cong-viec-theo-doi/sua.php`, `bieu-mau.php` |
| `/bao-cao/` | Báo cáo tổng quan | `bao-cao/index.php` |
| `/bao-cao/khach-hang.php` | Báo cáo khách hàng | `bao-cao/khach-hang.php` |
| `/bao-cao/tuong-tac.php` | Báo cáo tương tác | `bao-cao/tuong-tac.php` |
| `/bao-cao/cong-viec.php` | Báo cáo công việc | `bao-cao/cong-viec.php` |
| `/loai-khach-hang/` | Quản trị loại khách | `loai-khach-hang/index.php` |
| `/loai-khach-hang/them.php` | Thêm loại khách | `loai-khach-hang/them.php`, `bieu-mau.php` |
| `/loai-khach-hang/sua.php` | Sửa loại khách | `loai-khach-hang/sua.php`, `bieu-mau.php` |
| `/nguoi-dung/` | Quản trị người dùng | `nguoi-dung/index.php` |
| `/nguoi-dung/them.php` | Thêm user | `nguoi-dung/them.php`, `bieu-mau.php` |
| `/nguoi-dung/sua.php` | Sửa user | `nguoi-dung/sua.php`, `bieu-mau.php` |
| `/nguoi-dung/chi-tiet.php` | Chi tiết user | `nguoi-dung/chi-tiet.php` |
| `/nguoi-dung/doi-mat-khau.php` | Đổi mật khẩu user | `nguoi-dung/doi-mat-khau.php` |
| `/khong-co-quyen.php` | Không có quyền | `khong-co-quyen.php` |

## Unresolved Questions

- Chưa xác nhận được giao diện bằng trình duyệt vì `http://localhost/quanly_khachhang/` không kết nối được tại thời điểm viết tài liệu.
