<?php

declare(strict_types=1);

function gia_tri_cau_hinh_csdl(string $ten, string $macDinh): string
{
    $giaTri = getenv($ten);

    return $giaTri === false ? $macDinh : $giaTri;
}

defined('DB_HOST') || define('DB_HOST', gia_tri_cau_hinh_csdl('QLKH_DB_HOST', '127.0.0.1'));
defined('DB_PORT') || define('DB_PORT', (int) gia_tri_cau_hinh_csdl('QLKH_DB_PORT', '3306'));
defined('DB_NAME') || define('DB_NAME', gia_tri_cau_hinh_csdl('QLKH_DB_NAME', 'quanly_khachhang'));
defined('DB_USERNAME') || define('DB_USERNAME', gia_tri_cau_hinh_csdl('QLKH_DB_USERNAME', 'root'));
defined('DB_PASSWORD') || define('DB_PASSWORD', gia_tri_cau_hinh_csdl('QLKH_DB_PASSWORD', ''));
defined('DB_CHARSET') || define('DB_CHARSET', 'utf8mb4');
defined('DB_COLLATION') || define('DB_COLLATION', 'utf8mb4_vietnamese_ci');
defined('DB_FALLBACK_COLLATION') || define('DB_FALLBACK_COLLATION', 'utf8mb4_unicode_ci');
