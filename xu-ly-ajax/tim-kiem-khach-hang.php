<?php

declare(strict_types=1);

require __DIR__ . '/phan-hoi-json.php';
require __DIR__ . '/../khach-hang/ham-khach-hang.php';

$boLoc = lay_bo_loc_khach_hang();
$boLoc['customer_type_id'] = 0;
$boLoc['assigned_user_id'] = 0;
$boLoc['status'] = '';

tra_ve_json(tao_phan_hoi_ajax_danh_sach_khach_hang($boLoc, lay_trang_hien_tai()));
