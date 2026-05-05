<?php

declare(strict_types=1);

require __DIR__ . '/../dung-chung/khoi-dong.php';
require __DIR__ . '/../dung-chung/kiem-tra-quyen-admin.php';
require __DIR__ . '/ham-nguoi-dung.php';

$id = max(0, (int) gia_tri_get('id', 0));
$nguoiDung = $id > 0 ? lay_nguoi_dung_theo_id($id) : null;

if (!$nguoiDung) {
    thong_bao_loi('Không tìm thấy người dùng cần đổi mật khẩu.');
    chuyen_huong('nguoi-dung/');
}

$loi = [];

if (la_post()) {
    yeu_cau_csrf('nguoi-dung/doi-mat-khau.php?id=' . $id);

    $matKhau = (string) gia_tri_post('password', '');
    $xacNhan = (string) gia_tri_post('password_confirm', '');
    $loi = kiem_tra_mat_khau_nguoi_dung($matKhau, $xacNhan);

    if ($loi === []) {
        cap_nhat_mat_khau_nguoi_dung($id, $matKhau);
        thong_bao_thanh_cong('Đã cập nhật mật khẩu người dùng.');
        chuyen_huong('nguoi-dung/chi-tiet.php?id=' . $id);
    }

    thong_bao_loi('Vui lòng kiểm tra lại mật khẩu.');
}

$tieuDe = 'Đổi mật khẩu';
require __DIR__ . '/../giao-dien/dau-trang.php';
?>
<div class="page-header">
    <div>
        <p class="eyebrow">Quản trị hệ thống</p>
        <h1 class="page-title">Đổi mật khẩu</h1>
        <p class="page-subtitle">Cập nhật mật khẩu cho <?= e($nguoiDung['full_name']) ?> (@<?= e($nguoiDung['username']) ?>).</p>
    </div>
</div>

<form method="post" class="surface-card user-form">
    <?= csrf_input() ?>
    <div class="form-section">
        <div class="form-section-title">
            <p class="eyebrow">Bảo mật tài khoản</p>
            <h2 class="card-title mb-1">Mật khẩu mới</h2>
            <p class="text-muted mb-0">Mật khẩu cần tối thiểu 6 ký tự để phù hợp demo và dễ nhập lại.</p>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="password">Mật khẩu mới <span class="text-danger">*</span></label>
                <input class="form-control<?= isset($loi['password']) ? ' is-invalid' : '' ?>" id="password" name="password" required type="password" autocomplete="new-password">
                <?php if (isset($loi['password'])): ?><div class="invalid-feedback"><?= e($loi['password']) ?></div><?php endif; ?>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="password_confirm">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                <input class="form-control<?= isset($loi['password_confirm']) ? ' is-invalid' : '' ?>" id="password_confirm" name="password_confirm" required type="password" autocomplete="new-password">
                <?php if (isset($loi['password_confirm'])): ?><div class="invalid-feedback"><?= e($loi['password_confirm']) ?></div><?php endif; ?>
            </div>
        </div>
    </div>
    <div class="form-actions user-form-actions">
        <a class="btn btn-outline-secondary" href="<?= e(duong_dan('nguoi-dung/chi-tiet.php?id=' . $id)) ?>">Hủy</a>
        <button class="btn btn-primary" type="submit">Lưu mật khẩu</button>
    </div>
</form>
<?php require __DIR__ . '/../giao-dien/cuoi-trang.php'; ?>
