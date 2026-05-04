<?php

declare(strict_types=1);

require __DIR__ . '/../dung-chung/khoi-dong.php';
require __DIR__ . '/../dung-chung/kiem-tra-quyen-admin.php';
require __DIR__ . '/ham-loai-khach-hang.php';

yeu_cau_post('loai-khach-hang/');

$id = max(0, (int) gia_tri_post('id', 0));
$ketQua = $id > 0 ? xoa_hoac_an_loai_khach_hang($id) : 'khong_ton_tai';

if ($ketQua === 'da_xoa') {
    thong_bao_thanh_cong('Đã xóa loại khách hàng chưa được sử dụng.');
} elseif ($ketQua === 'da_an') {
    thong_bao_canh_bao('Loại khách hàng đang được sử dụng nên hệ thống đã chuyển sang trạng thái ngừng dùng.');
} elseif ($ketQua === 'dang_duoc_su_dung') {
    thong_bao_canh_bao('Không thể xóa vì loại khách hàng vẫn còn được tham chiếu bởi hồ sơ khách hàng.');
} else {
    thong_bao_loi('Không tìm thấy loại khách hàng cần xử lý.');
}

chuyen_huong('loai-khach-hang/');
