<?php

declare(strict_types=1);

function e(mixed $giaTri): string
{
    return htmlspecialchars((string) ($giaTri ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function duong_dan(string $duongDan = ''): string
{
    return rtrim(DUONG_DAN_GOC, '/') . '/' . ltrim($duongDan, '/');
}

function chuyen_huong(string $duongDan, int $maTrangThai = 302): never
{
    header('Location: ' . duong_dan($duongDan), true, $maTrangThai);
    exit;
}

function chuoi_sach(?string $giaTri): string
{
    $giaTri = trim((string) $giaTri);

    return preg_replace('/\s+/u', ' ', $giaTri) ?? '';
}

function gia_tri_get(string $khoa, mixed $macDinh = null): mixed
{
    return $_GET[$khoa] ?? $macDinh;
}

function gia_tri_post(string $khoa, mixed $macDinh = null): mixed
{
    return $_POST[$khoa] ?? $macDinh;
}

function la_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function yeu_cau_post(string $duongDanQuayLai = 'bang-dieu-khien.php'): void
{
    if (!la_post()) {
        thong_bao_loi('Phương thức yêu cầu không hợp lệ.');
        chuyen_huong($duongDanQuayLai);
    }
}
