<?php

declare(strict_types=1);

require __DIR__ . '/../dung-chung/khoi-dong.php';
require __DIR__ . '/../dung-chung/kiem-tra-dang-nhap.php';
require __DIR__ . '/ham-khach-hang.php';

$id = max(0, (int) gia_tri_get('id', 0));
$khachHang = $id > 0 ? lay_chi_tiet_khach_hang($id) : null;

if (!$khachHang) {
    thong_bao_loi('Không tìm thấy khách hàng cần sửa.');
    chuyen_huong('khach-hang/');
}

if ($khachHang['deleted_at']) {
    thong_bao_canh_bao('Khách hàng đã xóa mềm. Vui lòng khôi phục trước khi chỉnh sửa.');
    chuyen_huong('khach-hang/chi-tiet.php?id=' . $id);
}

$duLieu = [
    'customer_type_id' => (string) $khachHang['customer_type_id'],
    'assigned_user_id' => (string) $khachHang['assigned_user_id'],
    'full_name' => $khachHang['full_name'],
    'company_name' => $khachHang['company_name'] ?? '',
    'gender' => $khachHang['gender'],
    'date_of_birth' => $khachHang['date_of_birth'] ?? '',
    'phone' => $khachHang['phone'] ?? '',
    'email' => $khachHang['email'] ?? '',
    'address' => $khachHang['address'] ?? '',
    'city' => $khachHang['city'] ?? '',
    'source' => $khachHang['source'],
    'status' => $khachHang['status'],
    'notes' => $khachHang['notes'] ?? '',
];
$loi = [];
$danhSachLoai = lay_lua_chon_loai_khach_hang((int) $khachHang['customer_type_id']);
$danhSachNhanVien = lay_lua_chon_nhan_vien();
$idKhachHang = $id;

if (la_post()) {
    yeu_cau_csrf('khach-hang/sua.php?id=' . $id);

    $duLieu = lay_du_lieu_form_khach_hang($_POST);
    if (!la_admin()) {
        $duLieu['assigned_user_id'] = (string) $khachHang['assigned_user_id'];
    }
    $loi = kiem_tra_du_lieu_khach_hang($duLieu, $id);

    if ($loi === []) {
        try {
            cap_nhat_khach_hang($id, $duLieu);
            thong_bao_thanh_cong('Đã cập nhật hồ sơ khách hàng.');
            chuyen_huong('khach-hang/chi-tiet.php?id=' . $id);
        } catch (PDOException) {
            thong_bao_loi('Không thể lưu vì số điện thoại hoặc email bị trùng với khách hàng đang hoạt động.');
        }
    } else {
        thong_bao_loi('Vui lòng kiểm tra lại thông tin khách hàng.');
    }
}

$tieuDe = 'Sửa khách hàng';
$tieuDeBieuMau = 'Sửa khách hàng';
$nhanNut = 'Lưu thay đổi';

require __DIR__ . '/../giao-dien/dau-trang.php';
?>
<div class="page-header">
    <div>
        <p class="eyebrow">Chăm sóc khách hàng</p>
        <h1 class="page-title">Sửa khách hàng</h1>
        <p class="page-subtitle">Cập nhật hồ sơ, phân loại và nhân viên phụ trách của khách hàng.</p>
    </div>
</div>

<?php require __DIR__ . '/bieu-mau.php'; ?>
<?php require __DIR__ . '/../giao-dien/cuoi-trang.php'; ?>
