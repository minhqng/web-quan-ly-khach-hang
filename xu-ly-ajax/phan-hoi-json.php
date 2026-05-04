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

if (!da_dang_nhap()) {
    tra_ve_json([
        'thanh_cong' => false,
        'thong_bao' => 'Vui lòng đăng nhập để tiếp tục.',
    ], 401);
}

cap_nhat_hoat_dong_phien();
