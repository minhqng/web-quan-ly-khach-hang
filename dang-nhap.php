<?php

declare(strict_types=1);

require __DIR__ . '/dung-chung/khoi-dong.php';

if (da_dang_nhap()) {
    chuyen_huong('bang-dieu-khien.php');
}

$tieuDe = 'Đăng nhập';
$taiKhoan = chuoi_sach((string) gia_tri_post('tai_khoan', ''));
$matKhau = (string) gia_tri_post('mat_khau', '');
$loiDangNhap = '';

if (la_post()) {
    yeu_cau_csrf('dang-nhap.php');

    if ($taiKhoan === '' || $matKhau === '') {
        $loiDangNhap = 'Vui lòng nhập đầy đủ tài khoản và mật khẩu.';
    } elseif (dang_nhap_bi_tam_khoa($taiKhoan)) {
        $loiDangNhap = 'Bạn nhập sai quá nhiều lần. Vui lòng thử lại sau 5 phút.';
    } else {
        try {
            $nguoiDung = tim_nguoi_dung_dang_nhap($taiKhoan);

            if (!$nguoiDung || !mat_khau_hop_le($matKhau, (string) $nguoiDung['password_hash'])) {
                ghi_nhan_dang_nhap_that_bai($taiKhoan);
                $loiDangNhap = 'Tài khoản hoặc mật khẩu không đúng.';
            } elseif ($nguoiDung['status'] !== TRANG_THAI_HOAT_DONG) {
                ghi_nhan_dang_nhap_that_bai($taiKhoan);
                $loiDangNhap = 'Tài khoản đang bị khóa. Vui lòng liên hệ quản trị viên.';
            } else {
                xoa_thu_dang_nhap($taiKhoan);
                dang_nhap_nguoi_dung($nguoiDung);
                thuc_thi_lenh(
                    'UPDATE users SET last_login_at = NOW() WHERE id = :id',
                    ['id' => (int) $nguoiDung['id']]
                );
                thong_bao_thanh_cong('Đăng nhập thành công. Chào mừng bạn quay lại.');
                chuyen_huong('bang-dieu-khien.php');
            }
        } catch (Throwable) {
            $loiDangNhap = 'Không thể đăng nhập lúc này. Vui lòng kiểm tra kết nối cơ sở dữ liệu.';
        }
    }
}
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($tieuDe) ?> - <?= e(TEN_UNG_DUNG) ?></title>
    <link href="<?= e(duong_dan('tai-nguyen/vendor/bootstrap/bootstrap.min.css')) ?>" rel="stylesheet">
    <link href="<?= e(duong_dan('tai-nguyen/css/ung-dung.css')) ?>" rel="stylesheet">
</head>
<body class="login-body">
<main class="login-shell">
    <section class="login-panel">
        <div class="login-intro">
            <p class="eyebrow">Quản lý khách hàng</p>
            <h1>Đăng nhập hệ thống</h1>
            <p>Theo dõi khách hàng, lịch sử tương tác và công việc chăm sóc trong một giao diện gọn gàng.</p>
        </div>

        <form class="login-form" method="post" action="<?= e(duong_dan('dang-nhap.php')) ?>">
            <?= csrf_input() ?>
            <?php if ($loiDangNhap !== ''): ?>
                <div class="alert alert-danger" role="alert"><?= e($loiDangNhap) ?></div>
            <?php endif; ?>

            <?php $thongBao = lay_thong_bao(); ?>
            <?php if ($thongBao): ?>
                <div class="alert alert-<?= e($thongBao['loai']) ?>" role="alert"><?= e($thongBao['noi_dung']) ?></div>
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label" for="tai_khoan">Tên đăng nhập hoặc email</label>
                <input
                    class="form-control"
                    id="tai_khoan"
                    name="tai_khoan"
                    type="text"
                    value="<?= e($taiKhoan) ?>"
                    autocomplete="username"
                    required
                    autofocus
                >
            </div>
            <div class="mb-3">
                <label class="form-label" for="mat_khau">Mật khẩu</label>
                <input
                    class="form-control"
                    id="mat_khau"
                    name="mat_khau"
                    type="password"
                    autocomplete="current-password"
                    required
                >
            </div>
            <button class="btn btn-primary w-100" type="submit">Đăng nhập</button>
            <p class="login-hint">Thông tin tài khoản demo nằm trong tài liệu bàn giao dự án.</p>
        </form>
    </section>
</main>
</body>
</html>
