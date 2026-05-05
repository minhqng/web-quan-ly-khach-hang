<?php

declare(strict_types=1);

require __DIR__ . '/../dung-chung/khoi-dong.php';
require __DIR__ . '/../dung-chung/kiem-tra-quyen-admin.php';
require __DIR__ . '/ham-khach-hang.php';

yeu_cau_post('khach-hang/');

$id = max(0, (int) gia_tri_post('id', 0));

if ($id <= 0) {
    thong_bao_loi('Không tìm thấy khách hàng cần xóa mềm.');
    chuyen_huong('khach-hang/');
}

$ketQua = xoa_mem_khach_hang($id);

if ($ketQua === 'da_xoa') {
    thong_bao_thanh_cong('Đã xóa mềm khách hàng. Có thể khôi phục nếu chưa bị trùng liên hệ.');
} elseif ($ketQua === 'co_cong_viec_mo') {
    thong_bao_canh_bao('Không thể xóa mềm vì khách hàng còn công việc đang mở. Hãy hoàn thành hoặc hủy công việc trước.');
} else {
    thong_bao_canh_bao('Khách hàng không tồn tại hoặc đã được xóa mềm trước đó.');
}

chuyen_huong('khach-hang/');
