<?php

declare(strict_types=1);

require __DIR__ . '/../dung-chung/khoi-dong.php';
require __DIR__ . '/../dung-chung/kiem-tra-dang-nhap.php';
require __DIR__ . '/ham-cong-viec-theo-doi.php';

$cheDo = 'my';
$danhSachCongViec = lay_danh_sach_cong_viec_theo_doi($cheDo);
$tieuDe = 'Công việc của tôi';
$tieuDeDanhSach = 'Công việc đang theo dõi';
$moTaDanhSach = la_admin()
    ? 'Quản trị viên xem toàn bộ công việc để điều phối demo; nhân viên chỉ thấy việc được giao.'
    : 'Tập trung các việc được giao cho bạn, sắp xếp theo trạng thái và hạn xử lý.';
$thongDiepRong = 'Chưa có công việc nào cần theo dõi.';

require __DIR__ . '/../giao-dien/dau-trang.php';
?>
<div class="page-header">
    <div>
        <p class="eyebrow">Công việc theo dõi</p>
        <h1 class="page-title">Công việc của tôi</h1>
        <p class="page-subtitle">Theo dõi việc cần làm sau mỗi lần chăm sóc khách hàng, cập nhật nhanh trạng thái mà không phải rời danh sách.</p>
    </div>
</div>

<?php require __DIR__ . '/danh-sach-cong-viec.php'; ?>
<?php require __DIR__ . '/../giao-dien/cuoi-trang.php'; ?>
