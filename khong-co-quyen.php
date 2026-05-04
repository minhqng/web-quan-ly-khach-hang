<?php

declare(strict_types=1);

require __DIR__ . '/dung-chung/khoi-dong.php';

$tieuDe = 'Không có quyền truy cập';
require __DIR__ . '/giao-dien/dau-trang.php';
?>
<section class="app-content">
    <section class="placeholder-panel">
        <p class="eyebrow">Phân quyền</p>
        <h1>Không có quyền truy cập</h1>
        <p>Tài khoản của bạn không có quyền sử dụng chức năng này. Vui lòng quay lại khu vực phù hợp hoặc liên hệ quản trị viên.</p>
        <a class="btn btn-primary" href="<?= e(duong_dan(da_dang_nhap() ? 'bang-dieu-khien.php' : 'dang-nhap.php')) ?>">
            <?= da_dang_nhap() ? 'Về bảng điều khiển' : 'Đăng nhập' ?>
        </a>
    </section>
</section>
<?php require __DIR__ . '/giao-dien/cuoi-trang.php'; ?>
