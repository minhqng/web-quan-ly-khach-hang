<?php

declare(strict_types=1);

require __DIR__ . '/../dung-chung/khoi-dong.php';
require __DIR__ . '/../dung-chung/kiem-tra-dang-nhap.php';
require __DIR__ . '/ham-tuong-tac.php';

$maKhachHang = max(0, (int) gia_tri_get('customer_id', 0));
$duLieu = du_lieu_mac_dinh_tuong_tac($maKhachHang ?: null);
$loi = [];
$danhSachKhachHang = lay_lua_chon_khach_hang_tuong_tac($maKhachHang ?: null);

if (la_post()) {
    $duLieu = lay_du_lieu_form_tuong_tac($_POST);
    $loi = kiem_tra_du_lieu_tuong_tac($duLieu, true);

    if ($loi === []) {
        tao_tuong_tac($duLieu);
        thong_bao_thanh_cong(
            (int) $duLieu['create_follow_up'] === 1
                ? 'Đã ghi nhận tương tác và tạo công việc theo dõi.'
                : 'Đã ghi nhận tương tác khách hàng.'
        );
        chuyen_huong('khach-hang/chi-tiet.php?id=' . $duLieu['customer_id']);
    }

    thong_bao_loi('Vui lòng kiểm tra lại thông tin tương tác.');
}

$tieuDe = 'Thêm tương tác';
$tieuDeBieuMau = 'Thêm tương tác';
$nhanNut = 'Ghi nhận tương tác';
$hienThiTaoCongViec = true;

require __DIR__ . '/../giao-dien/dau-trang.php';
?>
<div class="page-header">
    <div>
        <p class="eyebrow">Lịch sử chăm sóc</p>
        <h1 class="page-title">Thêm tương tác</h1>
        <p class="page-subtitle">Sau mỗi trao đổi, có thể tạo ngay công việc theo dõi để biến kết quả thành hành động.</p>
    </div>
</div>

<?php require __DIR__ . '/bieu-mau.php'; ?>
<?php require __DIR__ . '/../giao-dien/cuoi-trang.php'; ?>
