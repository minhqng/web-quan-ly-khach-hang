<?php

declare(strict_types=1);

require __DIR__ . '/../dung-chung/khoi-dong.php';
require __DIR__ . '/../dung-chung/kiem-tra-quyen-admin.php';
require __DIR__ . '/ham-nguoi-dung.php';

$danhSachNguoiDung = lay_nhieu_dong(
    "SELECT id, full_name, username, email, phone, role, status, last_login_at, created_at
     FROM users
     ORDER BY FIELD(role, 'admin', 'staff'), status ASC, full_name ASC"
);

$tongNguoiDung = count($danhSachNguoiDung);
$tongAdmin = count(array_filter($danhSachNguoiDung, static fn (array $nguoiDung): bool => $nguoiDung['role'] === VAI_TRO_ADMIN));
$tongNhanVien = count(array_filter($danhSachNguoiDung, static fn (array $nguoiDung): bool => $nguoiDung['role'] === VAI_TRO_NHAN_VIEN));
$tongDangHoatDong = count(array_filter($danhSachNguoiDung, static fn (array $nguoiDung): bool => $nguoiDung['status'] === TRANG_THAI_HOAT_DONG));
$nhanVaiTro = nhan_vai_tro_nguoi_dung();
$nhanTrangThai = nhan_trang_thai_nguoi_dung();

$tieuDe = 'Người dùng';
require __DIR__ . '/../giao-dien/dau-trang.php';
?>
<div class="page-header">
    <div>
        <p class="eyebrow">Quản trị hệ thống</p>
        <h1 class="page-title">Người dùng</h1>
        <p class="page-subtitle">Theo dõi tài khoản admin và nhân viên phục vụ đăng nhập, phân quyền và giao việc trong demo.</p>
    </div>
    <a class="btn btn-primary" href="<?= e(duong_dan('nguoi-dung/them.php')) ?>">Thêm người dùng</a>
</div>

<section class="report-stat-grid">
    <article class="stat-card">
        <div class="stat-label">Tổng tài khoản</div>
        <div class="stat-value number"><?= e(number_format($tongNguoiDung, 0, ',', '.')) ?></div>
        <p class="text-muted mb-0">Từ bảng users</p>
    </article>
    <article class="stat-card">
        <div class="stat-label">Quản trị</div>
        <div class="stat-value number"><?= e((string) $tongAdmin) ?></div>
        <p class="text-muted mb-0">Có quyền cấu hình hệ thống</p>
    </article>
    <article class="stat-card">
        <div class="stat-label">Nhân viên</div>
        <div class="stat-value number"><?= e((string) $tongNhanVien) ?></div>
        <p class="text-muted mb-0">Có thể được giao khách và công việc</p>
    </article>
    <article class="stat-card">
        <div class="stat-label">Đang hoạt động</div>
        <div class="stat-value number"><?= e((string) $tongDangHoatDong) ?></div>
        <p class="text-muted mb-0">Được phép đăng nhập</p>
    </article>
</section>

<section class="table-card">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Người dùng</th>
                    <th>Liên hệ</th>
                    <th>Vai trò</th>
                    <th>Trạng thái</th>
                    <th>Lần đăng nhập gần nhất</th>
                    <th>Ngày tạo</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($danhSachNguoiDung as $nguoiDung): ?>
                    <tr>
                        <td>
                            <strong><?= e($nguoiDung['full_name']) ?></strong>
                            <div class="text-muted">@<?= e($nguoiDung['username']) ?></div>
                        </td>
                        <td>
                            <div><?= e($nguoiDung['email']) ?></div>
                            <div class="text-muted"><?= e($nguoiDung['phone'] ?: 'Chưa có số điện thoại') ?></div>
                        </td>
                        <td><?= e($nhanVaiTro[$nguoiDung['role']] ?? 'Không rõ') ?></td>
                        <td>
                            <span class="badge <?= e(lop_badge_trang_thai_nguoi_dung($nguoiDung['status'])) ?>">
                                <?= e($nhanTrangThai[$nguoiDung['status']] ?? 'Không rõ') ?>
                            </span>
                        </td>
                        <td><?= e(dinh_dang_ngay_gio($nguoiDung['last_login_at']) ?: 'Chưa đăng nhập') ?></td>
                        <td><?= e(dinh_dang_ngay($nguoiDung['created_at'])) ?></td>
                        <td>
                            <div class="row-actions justify-content-end">
                                <a class="btn btn-sm btn-outline-secondary" href="<?= e(duong_dan('nguoi-dung/chi-tiet.php?id=' . $nguoiDung['id'])) ?>">Xem</a>
                                <a class="btn btn-sm btn-outline-secondary" href="<?= e(duong_dan('nguoi-dung/sua.php?id=' . $nguoiDung['id'])) ?>">Sửa</a>
                                <a class="btn btn-sm btn-outline-primary" href="<?= e(duong_dan('nguoi-dung/doi-mat-khau.php?id=' . $nguoiDung['id'])) ?>">Mật khẩu</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($danhSachNguoiDung === []): ?>
                    <tr><td class="table-empty-state text-center text-muted py-5" colspan="7">Chưa có tài khoản người dùng.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require __DIR__ . '/../giao-dien/cuoi-trang.php'; ?>
