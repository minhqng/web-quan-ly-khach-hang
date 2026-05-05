<?php

declare(strict_types=1);

require_once __DIR__ . '/../dung-chung/khoi-dong.php';

function tra_ve_json(array $duLieu, int $maTrangThai = 200): never
{
    http_response_code($maTrangThai);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($duLieu, JSON_UNESCAPED_UNICODE);
    exit;
}

if (phien_dang_nhap_het_han()) {
    dang_xuat_nguoi_dung();
    tra_ve_json([
        'thanh_cong' => false,
        'thong_bao' => 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.',
    ], 401);
}

if (!isset($_SESSION['nguoi_dung'])) {
    tra_ve_json([
        'thanh_cong' => false,
        'thong_bao' => 'Vui lòng đăng nhập để tiếp tục.',
    ], 401);
}

if (!phien_nguoi_dung_con_hop_le()) {
    dang_xuat_nguoi_dung();
    tra_ve_json([
        'thanh_cong' => false,
        'thong_bao' => 'Tài khoản đã thay đổi trạng thái. Vui lòng đăng nhập lại.',
    ], 403);
}

cap_nhat_hoat_dong_phien();
