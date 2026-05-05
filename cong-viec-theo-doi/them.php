<?php

declare(strict_types=1);

require __DIR__ . '/../dung-chung/khoi-dong.php';
require __DIR__ . '/../dung-chung/kiem-tra-dang-nhap.php';
require __DIR__ . '/ham-cong-viec-theo-doi.php';

$maKhachHang = max(0, (int) gia_tri_get('customer_id', 0));
$duLieu = du_lieu_mac_dinh_cong_viec($maKhachHang ?: null);
$loi = [];
$danhSachKhachHang = lay_lua_chon_khach_hang_cong_viec($maKhachHang ?: null);
$danhSachNhanVien = lay_lua_chon_nhan_vien_cong_viec();

if (la_post()) {
    yeu_cau_csrf('cong-viec-theo-doi/them.php' . ($maKhachHang ? '?customer_id=' . $maKhachHang : ''));

    $duLieu = lay_du_lieu_form_cong_viec($_POST);
    $loi = kiem_tra_du_lieu_cong_viec($duLieu);

    if ($loi === []) {
        try {
            tao_cong_viec_theo_doi($duLieu);
            thong_bao_thanh_cong('Đã tạo công việc theo dõi.');
            chuyen_huong('cong-viec-theo-doi/');
        } catch (LoiNghiepVu $exception) {
            $loi['customer_id'] = $exception->getMessage();
        }
    }

    thong_bao_loi('Vui lòng kiểm tra lại thông tin công việc.');
}

$tieuDe = 'Thêm công việc theo dõi';
$tieuDeBieuMau = 'Thêm công việc theo dõi';
$nhanNut = 'Tạo công việc';

require __DIR__ . '/../giao-dien/dau-trang.php';
?>
<div class="page-header">
    <div>
        <p class="eyebrow">Công việc theo dõi</p>
        <h1 class="page-title">Thêm công việc</h1>
        <p class="page-subtitle">Tạo việc chăm sóc có hạn xử lý để không bỏ sót bước tiếp theo với khách hàng.</p>
    </div>
</div>

<?php require __DIR__ . '/bieu-mau.php'; ?>
<?php require __DIR__ . '/../giao-dien/cuoi-trang.php'; ?>
