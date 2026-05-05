<?php

declare(strict_types=1);

require __DIR__ . '/../dung-chung/khoi-dong.php';
require __DIR__ . '/../dung-chung/kiem-tra-dang-nhap.php';
require __DIR__ . '/ham-khach-hang.php';

$boLoc = lay_bo_loc_khach_hang();
$tongDong = dem_khach_hang_theo_bo_loc($boLoc);
$phanTrang = tinh_phan_trang($tongDong, lay_trang_hien_tai(), 8);
$danhSachKhachHang = lay_danh_sach_khach_hang($boLoc, $phanTrang);
$danhSachLoai = lay_lua_chon_loai_khach_hang();
$danhSachNhanVien = lay_lua_chon_nhan_vien();
$tieuDe = 'Khách hàng';

$taoLienKetTrang = static function (int $trang) use ($boLoc): string {
    return duong_dan('khach-hang/?' . http_build_query(array_filter([
        'tu_khoa' => $boLoc['tu_khoa'],
        'customer_type_id' => $boLoc['customer_type_id'] ?: null,
        'assigned_user_id' => $boLoc['assigned_user_id'] ?: null,
        'status' => $boLoc['status'] ?: null,
        'trang' => $trang,
    ], static fn ($giaTri) => $giaTri !== null && $giaTri !== '')));
};

require __DIR__ . '/../giao-dien/dau-trang.php';
?>
<div class="page-header">
    <div>
        <p class="eyebrow">Chăm sóc khách hàng</p>
        <h1 class="page-title">Khách hàng</h1>
        <p class="page-subtitle">Tìm kiếm, lọc, phân công phụ trách và theo dõi trạng thái chăm sóc từng khách hàng.</p>
    </div>
    <a class="btn btn-primary" href="<?= e(duong_dan('khach-hang/them.php')) ?>">Thêm khách hàng</a>
</div>

<?php require __DIR__ . '/danh-sach-khach-hang.php'; ?>
<?php require __DIR__ . '/../giao-dien/cuoi-trang.php'; ?>
