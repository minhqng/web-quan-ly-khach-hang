<?php

declare(strict_types=1);

require __DIR__ . '/dung-chung/khoi-dong.php';

yeu_cau_post('bang-dieu-khien.php');
dang_xuat_nguoi_dung();
thong_bao_thanh_cong('Bạn đã đăng xuất khỏi hệ thống.');

chuyen_huong('dang-nhap.php');
