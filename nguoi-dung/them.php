<?php

declare(strict_types=1);

require __DIR__ . '/../dung-chung/khoi-dong.php';
require __DIR__ . '/../dung-chung/kiem-tra-quyen-admin.php';
require __DIR__ . '/ham-nguoi-dung.php';

$duLieu = du_lieu_mac_dinh_nguoi_dung();
$loi = [];

if (la_post()) {
    yeu_cau_csrf('nguoi-dung/them.php');

    $duLieu = lay_du_lieu_form_nguoi_dung($_POST);
    $loi = kiem_tra_du_lieu_nguoi_dung($duLieu, null, true);

    if ($loi === []) {
        try {
            $idMoi = tao_nguoi_dung($duLieu);
            thong_bao_thanh_cong('Đã tạo tài khoản người dùng.');
            chuyen_huong('nguoi-dung/chi-tiet.php?id=' . $idMoi);
        } catch (PDOException) {
            thong_bao_loi('Không thể tạo tài khoản vì tên đăng nhập hoặc email bị trùng.');
        }
    } else {
        thong_bao_loi('Vui lòng kiểm tra lại thông tin người dùng.');
    }
}

$tieuDe = 'Thêm người dùng';
$tieuDeBieuMau = 'Thêm người dùng';
$nhanNut = 'Tạo tài khoản';
$hienThiMatKhau = true;

require __DIR__ . '/../giao-dien/dau-trang.php';
?>
<div class="page-header">
    <div>
        <p class="eyebrow">Quản trị hệ thống</p>
        <h1 class="page-title">Thêm người dùng</h1>
        <p class="page-subtitle">Tạo tài khoản admin hoặc nhân viên để phân quyền, giao khách hàng và theo dõi công việc.</p>
    </div>
</div>

<?php require __DIR__ . '/bieu-mau.php'; ?>
<?php require __DIR__ . '/../giao-dien/cuoi-trang.php'; ?>
