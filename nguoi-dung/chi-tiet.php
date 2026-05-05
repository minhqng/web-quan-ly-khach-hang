<?php

declare(strict_types=1);

require __DIR__ . '/../dung-chung/khoi-dong.php';
require __DIR__ . '/../dung-chung/kiem-tra-quyen-admin.php';
require __DIR__ . '/ham-nguoi-dung.php';

$id = max(0, (int) gia_tri_get('id', 0));
$nguoiDung = $id > 0 ? lay_nguoi_dung_theo_id($id) : null;

if (!$nguoiDung) {
    thong_bao_loi('Không tìm thấy người dùng.');
    chuyen_huong('nguoi-dung/');
}

$thongKe = thong_ke_nguoi_dung($id);
$nhanVaiTro = nhan_vai_tro_nguoi_dung();
$nhanTrangThai = nhan_trang_thai_nguoi_dung();

$tieuDe = 'Chi tiết người dùng';
require __DIR__ . '/../giao-dien/dau-trang.php';
?>
<div class="page-header">
    <div>
        <p class="eyebrow">Quản trị hệ thống</p>
        <h1 class="page-title"><?= e($nguoiDung['full_name']) ?></h1>
        <p class="page-subtitle">@<?= e($nguoiDung['username']) ?> · <?= e($nguoiDung['email']) ?></p>
    </div>
    <div class="row-actions">
        <a class="btn btn-outline-secondary" href="<?= e(duong_dan('nguoi-dung/')) ?>">Quay lại</a>
        <a class="btn btn-outline-primary" href="<?= e(duong_dan('nguoi-dung/sua.php?id=' . $id)) ?>">Sửa</a>
        <a class="btn btn-primary" href="<?= e(duong_dan('nguoi-dung/doi-mat-khau.php?id=' . $id)) ?>">Đổi mật khẩu</a>
    </div>
</div>

<section class="report-stat-grid">
    <article class="stat-card">
        <div class="stat-label">Khách phụ trách</div>
        <div class="stat-value number"><?= e((string) $thongKe['customers']) ?></div>
    </article>
    <article class="stat-card">
        <div class="stat-label">Tương tác đã ghi</div>
        <div class="stat-value number"><?= e((string) $thongKe['interactions']) ?></div>
    </article>
    <article class="stat-card">
        <div class="stat-label">Việc đang mở</div>
        <div class="stat-value number"><?= e((string) $thongKe['open_tasks']) ?></div>
    </article>
</section>

<section class="surface-card">
    <div class="report-section-heading">
        <div>
            <p class="eyebrow">Thông tin tài khoản</p>
            <h2 class="card-title">Hồ sơ đăng nhập</h2>
        </div>
        <span class="badge <?= e(lop_badge_trang_thai_nguoi_dung($nguoiDung['status'])) ?>"><?= e($nhanTrangThai[$nguoiDung['status']] ?? 'Không rõ') ?></span>
    </div>
    <dl class="customer-definition-list">
        <div><dt>Vai trò</dt><dd><?= e($nhanVaiTro[$nguoiDung['role']] ?? 'Không rõ') ?></dd></div>
        <div><dt>Số điện thoại</dt><dd><?= e($nguoiDung['phone'] ?: 'Chưa có') ?></dd></div>
        <div><dt>Lần đăng nhập gần nhất</dt><dd><?= e(dinh_dang_ngay_gio($nguoiDung['last_login_at']) ?: 'Chưa đăng nhập') ?></dd></div>
        <div><dt>Ngày tạo</dt><dd><?= e(dinh_dang_ngay_gio($nguoiDung['created_at'])) ?></dd></div>
        <div><dt>Cập nhật gần nhất</dt><dd><?= e(dinh_dang_ngay_gio($nguoiDung['updated_at'])) ?></dd></div>
    </dl>
</section>
<?php require __DIR__ . '/../giao-dien/cuoi-trang.php'; ?>
