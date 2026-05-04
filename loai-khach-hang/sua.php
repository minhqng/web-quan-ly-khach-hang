<?php

declare(strict_types=1);

require __DIR__ . '/../dung-chung/khoi-dong.php';
require __DIR__ . '/../dung-chung/kiem-tra-quyen-admin.php';
require __DIR__ . '/ham-loai-khach-hang.php';

$id = max(0, (int) gia_tri_get('id', 0));
$loaiKhachHang = $id > 0 ? lay_loai_khach_hang_theo_id($id) : null;

if (!$loaiKhachHang) {
    thong_bao_loi('Không tìm thấy loại khách hàng cần sửa.');
    chuyen_huong('loai-khach-hang/');
}

$duLieu = [
    'name' => $loaiKhachHang['name'],
    'description' => $loaiKhachHang['description'] ?? '',
    'priority_score' => (int) $loaiKhachHang['priority_score'],
    'color' => mau_loai_khach_hang_an_toan($loaiKhachHang['color']),
    'is_active' => (int) $loaiKhachHang['is_active'],
];
$loi = [];

if (la_post()) {
    yeu_cau_csrf('loai-khach-hang/sua.php?id=' . $id);

    $duLieu = lay_du_lieu_form_loai_khach_hang($_POST);
    $loi = kiem_tra_du_lieu_loai_khach_hang($duLieu, $id);

    if ($loi === []) {
        thuc_thi_lenh(
            'UPDATE customer_types
             SET name = :name,
                 description = :description,
                 priority_score = :priority_score,
                 color = :color,
                 is_active = :is_active
             WHERE id = :id',
            [
                'name' => $duLieu['name'],
                'description' => $duLieu['description'] !== '' ? $duLieu['description'] : null,
                'priority_score' => $duLieu['priority_score'],
                'color' => $duLieu['color'],
                'is_active' => $duLieu['is_active'],
                'id' => $id,
            ]
        );

        thong_bao_thanh_cong('Đã cập nhật loại khách hàng.');
        chuyen_huong('loai-khach-hang/');
    }

    thong_bao_loi('Vui lòng kiểm tra lại thông tin loại khách hàng.');
}

$tieuDe = 'Sửa loại khách hàng';
$tieuDeBieuMau = 'Sửa loại khách hàng';
$moTaBieuMau = 'Cập nhật tên, màu và điểm ưu tiên đang dùng cho dashboard.';
$nhanNut = 'Lưu thay đổi';

require __DIR__ . '/../giao-dien/dau-trang.php';
?>
<div class="page-header">
    <div>
        <p class="eyebrow">Quản trị dữ liệu</p>
        <h1 class="page-title">Sửa loại khách hàng</h1>
        <p class="page-subtitle">Các thay đổi sẽ áp dụng cho khách hàng đang thuộc nhóm này.</p>
    </div>
</div>

<?php require __DIR__ . '/bieu-mau.php'; ?>
<?php require __DIR__ . '/../giao-dien/cuoi-trang.php'; ?>
