<?php

declare(strict_types=1);

require __DIR__ . '/../dung-chung/khoi-dong.php';
require __DIR__ . '/../dung-chung/kiem-tra-quyen-admin.php';
require __DIR__ . '/ham-nguoi-dung.php';

$id = max(0, (int) gia_tri_get('id', 0));
$nguoiDung = $id > 0 ? lay_nguoi_dung_theo_id($id) : null;

if (!$nguoiDung) {
    thong_bao_loi('Không tìm thấy người dùng cần sửa.');
    chuyen_huong('nguoi-dung/');
}

$duLieu = [
    'full_name' => $nguoiDung['full_name'],
    'username' => $nguoiDung['username'],
    'email' => $nguoiDung['email'],
    'phone' => $nguoiDung['phone'] ?? '',
    'role' => $nguoiDung['role'],
    'status' => $nguoiDung['status'],
];
$loi = [];

if (la_post()) {
    yeu_cau_csrf('nguoi-dung/sua.php?id=' . $id);

    $duLieu = lay_du_lieu_form_nguoi_dung($_POST);
    $loi = kiem_tra_du_lieu_nguoi_dung($duLieu, $id, false);

    if ($loi === []) {
        try {
            cap_nhat_nguoi_dung($id, $duLieu);
            thong_bao_thanh_cong('Đã cập nhật tài khoản người dùng.');
            chuyen_huong('nguoi-dung/chi-tiet.php?id=' . $id);
        } catch (PDOException) {
            thong_bao_loi('Không thể cập nhật vì tên đăng nhập hoặc email bị trùng.');
        }
    } else {
        thong_bao_loi('Vui lòng kiểm tra lại thông tin người dùng.');
    }
}

$tieuDe = 'Sửa người dùng';
$tieuDeBieuMau = 'Sửa người dùng';
$nhanNut = 'Lưu thay đổi';
$hienThiMatKhau = false;

require __DIR__ . '/../giao-dien/dau-trang.php';
?>
<div class="page-header">
    <div>
        <p class="eyebrow">Quản trị hệ thống</p>
        <h1 class="page-title">Sửa người dùng</h1>
        <p class="page-subtitle">Cập nhật thông tin đăng nhập, vai trò và trạng thái hoạt động.</p>
    </div>
</div>

<?php require __DIR__ . '/bieu-mau.php'; ?>
<?php require __DIR__ . '/../giao-dien/cuoi-trang.php'; ?>
