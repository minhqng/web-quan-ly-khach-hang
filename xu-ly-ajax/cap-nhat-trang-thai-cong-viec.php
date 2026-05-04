<?php

declare(strict_types=1);

require __DIR__ . '/phan-hoi-json.php';
require __DIR__ . '/../cong-viec-theo-doi/ham-cong-viec-theo-doi.php';

if (!la_post()) {
    tra_ve_json([
        'thanh_cong' => false,
        'thong_bao' => 'Phương thức yêu cầu không hợp lệ.',
    ], 405);
}

$id = max(0, (int) gia_tri_post('id', 0));
$trangThai = (string) gia_tri_post('status', '');
$duLieu = $id > 0 ? cap_nhat_trang_thai_cong_viec($id, $trangThai) : null;

if (!$duLieu) {
    tra_ve_json([
        'thanh_cong' => false,
        'thong_bao' => 'Không thể cập nhật trạng thái công việc.',
    ], 400);
}

tra_ve_json([
    'thanh_cong' => true,
    'thong_bao' => 'Đã cập nhật trạng thái công việc.',
    'du_lieu' => $duLieu,
]);
