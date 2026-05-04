<?php

declare(strict_types=1);

require __DIR__ . '/../dung-chung/khoi-dong.php';
require __DIR__ . '/../dung-chung/kiem-tra-dang-nhap.php';
require __DIR__ . '/ham-cong-viec-theo-doi.php';

yeu_cau_post('cong-viec-theo-doi/');

$id = max(0, (int) gia_tri_post('id', 0));
$ketQua = $id > 0 ? cap_nhat_trang_thai_cong_viec($id, 'completed') : null;

if ($ketQua) {
    thong_bao_thanh_cong('Đã đánh dấu công việc hoàn thành.');
} else {
    thong_bao_loi('Không thể hoàn thành công việc này.');
}

chuyen_huong('cong-viec-theo-doi/');
