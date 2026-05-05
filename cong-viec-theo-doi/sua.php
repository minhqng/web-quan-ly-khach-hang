<?php

declare(strict_types=1);

require __DIR__ . '/../dung-chung/khoi-dong.php';
require __DIR__ . '/../dung-chung/kiem-tra-dang-nhap.php';
require __DIR__ . '/ham-cong-viec-theo-doi.php';

$id = max(0, (int) gia_tri_get('id', 0));
$congViec = $id > 0 ? lay_cong_viec_theo_id($id) : null;

if (!$congViec) {
    thong_bao_loi('Không tìm thấy công việc cần sửa.');
    chuyen_huong('cong-viec-theo-doi/');
}

if (!co_quyen_cap_nhat_cong_viec($congViec)) {
    thong_bao_loi('Bạn không có quyền sửa công việc này.');
    chuyen_huong('cong-viec-theo-doi/');
}

$duLieu = [
    'customer_id' => (string) $congViec['customer_id'],
    'assigned_user_id' => (string) $congViec['assigned_user_id'],
    'title' => $congViec['title'],
    'description' => $congViec['description'] ?? '',
    'due_at' => date('Y-m-d\TH:i', strtotime($congViec['due_at'])),
    'status' => $congViec['status'],
    'priority' => $congViec['priority'],
];
$loi = [];
$danhSachKhachHang = lay_lua_chon_khach_hang_cong_viec((int) $congViec['customer_id']);
$danhSachNhanVien = lay_lua_chon_nhan_vien_cong_viec();

if (la_post()) {
    yeu_cau_csrf('cong-viec-theo-doi/sua.php?id=' . $id);

    $duLieu = lay_du_lieu_form_cong_viec($_POST);
    $loi = kiem_tra_du_lieu_cong_viec($duLieu, $congViec);

    if ($loi === []) {
        cap_nhat_cong_viec_theo_doi($id, $duLieu);
        thong_bao_thanh_cong('Đã cập nhật công việc theo dõi.');
        chuyen_huong('cong-viec-theo-doi/');
    }

    thong_bao_loi('Vui lòng kiểm tra lại thông tin công việc.');
}

$tieuDe = 'Sửa công việc theo dõi';
$tieuDeBieuMau = 'Sửa công việc theo dõi';
$nhanNut = 'Lưu thay đổi';

require __DIR__ . '/../giao-dien/dau-trang.php';
?>
<div class="page-header">
    <div>
        <p class="eyebrow">Công việc theo dõi</p>
        <h1 class="page-title">Sửa công việc</h1>
        <p class="page-subtitle">Cập nhật người phụ trách, hạn xử lý và trạng thái chăm sóc.</p>
    </div>
</div>

<?php require __DIR__ . '/bieu-mau.php'; ?>
<?php require __DIR__ . '/../giao-dien/cuoi-trang.php'; ?>
