<?php

declare(strict_types=1);

require __DIR__ . '/phan-hoi-json.php';
require __DIR__ . '/../khach-hang/ham-khach-hang.php';

$boLoc = lay_bo_loc_khach_hang();
$trang = lay_trang_hien_tai();

tra_ve_json(tao_phan_hoi_ajax_danh_sach_khach_hang($boLoc, $trang));
