<?php

declare(strict_types=1);

require __DIR__ . '/../dung-chung/khoi-dong.php';
require __DIR__ . '/../dung-chung/kiem-tra-quyen-admin.php';
require __DIR__ . '/ham-khach-hang.php';

yeu_cau_post('khach-hang/');

$id = max(0, (int) gia_tri_post('id', 0));
$ketQua = $id > 0 ? khoi_phuc_khach_hang($id) : 'khong_hop_le';

if ($ketQua === 'da_khoi_phuc') {
    thong_bao_thanh_cong('Đã khôi phục khách hàng vào danh sách đang chăm sóc.');
    chuyen_huong('khach-hang/chi-tiet.php?id=' . $id);
}

if ($ketQua === 'bi_trung') {
    thong_bao_loi('Không thể khôi phục vì số điện thoại hoặc email đang được khách hàng khác sử dụng.');
} else {
    thong_bao_loi('Không tìm thấy khách hàng có thể khôi phục.');
}

chuyen_huong('khach-hang/?status=da_xoa');
