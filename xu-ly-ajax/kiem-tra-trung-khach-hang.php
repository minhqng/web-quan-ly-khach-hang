<?php

declare(strict_types=1);

require __DIR__ . '/phan-hoi-json.php';
require __DIR__ . '/../khach-hang/ham-khach-hang.php';

$truong = (string) gia_tri_get('truong', '');
$giaTri = chuoi_sach(gia_tri_get('gia_tri', ''));
$boQuaId = max(0, (int) gia_tri_get('bo_qua_id', 0)) ?: null;

if (!in_array($truong, ['phone', 'email'], true)) {
    tra_ve_json([
        'thanh_cong' => false,
        'thong_bao' => 'Trường kiểm tra không hợp lệ.',
    ], 400);
}

$giaTriChuan = $truong === 'phone'
    ? chuan_hoa_dien_thoai_khach_hang($giaTri)
    : chuan_hoa_email_khach_hang($giaTri);

if ($giaTriChuan === '') {
    tra_ve_json([
        'thanh_cong' => true,
        'bi_trung' => false,
        'thong_bao' => '',
    ]);
}

$biTrung = khach_hang_bi_trung($truong, $giaTriChuan, $boQuaId, !la_admin());

tra_ve_json([
    'thanh_cong' => true,
    'bi_trung' => $biTrung,
    'thong_bao' => $biTrung
        ? 'Thông tin này đang được dùng bởi khách hàng khác.'
        : 'Thông tin có thể sử dụng.',
]);
