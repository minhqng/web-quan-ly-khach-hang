<?php

declare(strict_types=1);

require __DIR__ . '/../dung-chung/khoi-dong.php';
require __DIR__ . '/../dung-chung/kiem-tra-dang-nhap.php';
require __DIR__ . '/ham-tuong-tac.php';

$id = max(0, (int) gia_tri_get('id', 0));
$tuongTac = $id > 0 ? lay_tuong_tac_theo_id($id) : null;

if (!$tuongTac) {
    thong_bao_loi('Không tìm thấy tương tác cần sửa.');
    chuyen_huong('tuong-tac/');
}

if (!co_the_sua_xoa_tuong_tac($tuongTac)) {
    thong_bao_loi('Bạn không có quyền sửa tương tác này.');
    chuyen_huong('tuong-tac/');
}

$duLieu = [
    'customer_id' => (string) $tuongTac['customer_id'],
    'interaction_type' => $tuongTac['interaction_type'] === 'zalo' ? 'chat' : $tuongTac['interaction_type'],
    'title' => $tuongTac['title'],
    'content' => $tuongTac['content'] ?? '',
    'result' => $tuongTac['result'] ?? '',
    'interaction_at' => date('Y-m-d\TH:i', strtotime($tuongTac['interaction_at'])),
];
$loi = [];
$danhSachKhachHang = lay_lua_chon_khach_hang_tuong_tac((int) $tuongTac['customer_id']);

if (la_post()) {
    yeu_cau_csrf('tuong-tac/sua.php?id=' . $id);

    $duLieu = lay_du_lieu_form_tuong_tac($_POST);
    $loi = kiem_tra_du_lieu_tuong_tac($duLieu, false);

    if ($loi === []) {
        cap_nhat_tuong_tac($id, $duLieu);
        thong_bao_thanh_cong('Đã cập nhật tương tác.');
        chuyen_huong('khach-hang/chi-tiet.php?id=' . $duLieu['customer_id']);
    }

    thong_bao_loi('Vui lòng kiểm tra lại thông tin tương tác.');
}

$tieuDe = 'Sửa tương tác';
$tieuDeBieuMau = 'Sửa tương tác';
$nhanNut = 'Lưu thay đổi';
$hienThiTaoCongViec = false;

require __DIR__ . '/../giao-dien/dau-trang.php';
?>
<div class="page-header">
    <div>
        <p class="eyebrow">Lịch sử chăm sóc</p>
        <h1 class="page-title">Sửa tương tác</h1>
        <p class="page-subtitle">Chỉ người ghi nhận tương tác hoặc admin được chỉnh sửa nội dung này.</p>
    </div>
</div>

<?php require __DIR__ . '/bieu-mau.php'; ?>
<?php require __DIR__ . '/../giao-dien/cuoi-trang.php'; ?>
