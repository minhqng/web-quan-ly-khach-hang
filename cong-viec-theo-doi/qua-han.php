<?php

declare(strict_types=1);

require __DIR__ . '/../dung-chung/khoi-dong.php';
require __DIR__ . '/../dung-chung/kiem-tra-dang-nhap.php';
require __DIR__ . '/ham-cong-viec-theo-doi.php';

$cheDo = 'overdue';
$danhSachCongViec = lay_danh_sach_cong_viec_theo_doi($cheDo);
$tieuDe = 'Công việc quá hạn';
$tieuDeDanhSach = 'Cần xử lý ngay';
$moTaDanhSach = 'Chỉ hiển thị công việc còn mở và đã quá hạn xử lý, giúp đội chăm sóc không bỏ sót cam kết với khách hàng.';
$thongDiepRong = 'Không có công việc quá hạn.';

require __DIR__ . '/../giao-dien/dau-trang.php';
?>
<div class="page-header">
    <div>
        <p class="eyebrow">Cảnh báo vận hành</p>
        <h1 class="page-title">Công việc quá hạn</h1>
        <p class="page-subtitle">Ưu tiên các việc đã trễ hạn nhưng vẫn giữ giao diện bình tĩnh, dễ đọc và phù hợp trình bày demo.</p>
    </div>
</div>

<?php require __DIR__ . '/danh-sach-cong-viec.php'; ?>
<?php require __DIR__ . '/../giao-dien/cuoi-trang.php'; ?>
