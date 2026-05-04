<?php

declare(strict_types=1);

require __DIR__ . '/../dung-chung/khoi-dong.php';
require __DIR__ . '/../dung-chung/kiem-tra-quyen-admin.php';
require __DIR__ . '/ham-loai-khach-hang.php';

$duLieu = du_lieu_mac_dinh_loai_khach_hang();
$loi = [];

if (la_post()) {
    $duLieu = lay_du_lieu_form_loai_khach_hang($_POST);
    $loi = kiem_tra_du_lieu_loai_khach_hang($duLieu);

    if ($loi === []) {
        thuc_thi_lenh(
            'INSERT INTO customer_types (name, description, priority_score, color, is_active)
             VALUES (:name, :description, :priority_score, :color, :is_active)',
            [
                'name' => $duLieu['name'],
                'description' => $duLieu['description'] !== '' ? $duLieu['description'] : null,
                'priority_score' => $duLieu['priority_score'],
                'color' => $duLieu['color'],
                'is_active' => $duLieu['is_active'],
            ]
        );

        thong_bao_thanh_cong('Đã thêm loại khách hàng mới.');
        chuyen_huong('loai-khach-hang/');
    }

    thong_bao_loi('Vui lòng kiểm tra lại thông tin loại khách hàng.');
}

$tieuDe = 'Thêm loại khách hàng';
$tieuDeBieuMau = 'Thêm loại khách hàng';
$moTaBieuMau = 'Tạo nhóm khách hàng mới để dùng trong hồ sơ, báo cáo và dashboard.';
$nhanNut = 'Thêm loại khách hàng';

require __DIR__ . '/../giao-dien/dau-trang.php';
?>
<div class="page-header">
    <div>
        <p class="eyebrow">Quản trị dữ liệu</p>
        <h1 class="page-title">Thêm loại khách hàng</h1>
        <p class="page-subtitle">Giữ tên ngắn gọn, điểm ưu tiên rõ ràng để đội demo dễ giải thích.</p>
    </div>
</div>

<?php require __DIR__ . '/bieu-mau.php'; ?>
<?php require __DIR__ . '/../giao-dien/cuoi-trang.php'; ?>
