<?php

declare(strict_types=1);

require __DIR__ . '/../dung-chung/khoi-dong.php';
require __DIR__ . '/../dung-chung/kiem-tra-dang-nhap.php';
require __DIR__ . '/ham-khach-hang.php';

$duLieu = du_lieu_mac_dinh_khach_hang();
$loi = [];
$danhSachLoai = lay_lua_chon_loai_khach_hang();
$danhSachNhanVien = lay_lua_chon_nhan_vien();

if (la_post()) {
    yeu_cau_csrf('khach-hang/them.php');

    $duLieu = lay_du_lieu_form_khach_hang($_POST);
    $loi = kiem_tra_du_lieu_khach_hang($duLieu);

    if ($loi === []) {
        try {
            $idMoi = tao_khach_hang($duLieu);
            thong_bao_thanh_cong('Đã tạo hồ sơ khách hàng mới.');
            chuyen_huong('khach-hang/chi-tiet.php?id=' . $idMoi);
        } catch (PDOException) {
            thong_bao_loi('Không thể lưu vì số điện thoại hoặc email bị trùng với khách hàng đang hoạt động.');
        }
    } else {
        thong_bao_loi('Vui lòng kiểm tra lại thông tin khách hàng.');
    }
}

$tieuDe = 'Thêm khách hàng';
$tieuDeBieuMau = 'Thêm khách hàng';
$nhanNut = 'Tạo hồ sơ';

require __DIR__ . '/../giao-dien/dau-trang.php';
?>
<div class="page-header">
    <div>
        <p class="eyebrow">Chăm sóc khách hàng</p>
        <h1 class="page-title">Thêm khách hàng</h1>
        <p class="page-subtitle">Tạo hồ sơ có phân loại, người phụ trách và thông tin liên hệ rõ ràng.</p>
    </div>
</div>

<?php require __DIR__ . '/bieu-mau.php'; ?>
<?php require __DIR__ . '/../giao-dien/cuoi-trang.php'; ?>
