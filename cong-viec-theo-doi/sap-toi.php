<?php

declare(strict_types=1);

require __DIR__ . '/../dung-chung/khoi-dong.php';
require __DIR__ . '/../dung-chung/kiem-tra-dang-nhap.php';
require __DIR__ . '/ham-cong-viec-theo-doi.php';

$cheDo = 'upcoming';
$danhSachCongViec = lay_danh_sach_cong_viec_theo_doi($cheDo);
$tieuDe = 'Công việc sắp tới';
$tieuDeDanhSach = 'Việc cần chuẩn bị';
$moTaDanhSach = 'Hiển thị các công việc còn mở có hạn xử lý từ hiện tại trở đi, phù hợp cho lập kế hoạch chăm sóc trong ngày.';
$thongDiepRong = 'Chưa có công việc sắp tới.';

require __DIR__ . '/../giao-dien/dau-trang.php';
?>
<div class="page-header">
    <div>
        <p class="eyebrow">Lịch chăm sóc</p>
        <h1 class="page-title">Công việc sắp tới</h1>
        <p class="page-subtitle">Giúp nhân viên nhìn trước các bước cần làm với khách hàng theo hạn xử lý gần nhất.</p>
    </div>
</div>

<?php require __DIR__ . '/danh-sach-cong-viec.php'; ?>
<?php require __DIR__ . '/../giao-dien/cuoi-trang.php'; ?>
