<?php

declare(strict_types=1);

require __DIR__ . '/../dung-chung/khoi-dong.php';
require __DIR__ . '/../dung-chung/kiem-tra-dang-nhap.php';
require __DIR__ . '/ham-tuong-tac.php';

yeu_cau_post('tuong-tac/');

$id = max(0, (int) gia_tri_post('id', 0));
$tuongTac = $id > 0 ? lay_tuong_tac_theo_id($id) : null;

if (!$tuongTac) {
    thong_bao_loi('Không tìm thấy tương tác cần xóa.');
    chuyen_huong('tuong-tac/');
}

if (!co_the_sua_xoa_tuong_tac($tuongTac)) {
    thong_bao_loi('Bạn không có quyền xóa tương tác này.');
    chuyen_huong('tuong-tac/');
}

xoa_tuong_tac($id);
thong_bao_thanh_cong('Đã xóa tương tác khỏi lịch sử.');
chuyen_huong('khach-hang/chi-tiet.php?id=' . $tuongTac['customer_id']);
