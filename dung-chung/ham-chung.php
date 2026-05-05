<?php

declare(strict_types=1);

final class LoiNghiepVu extends RuntimeException
{
}

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

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_input(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_token_hop_le(?string $token = null): bool
{
    $token ??= (string) ($_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ''));

    return is_string($token) && $token !== '' && hash_equals(csrf_token(), $token);
}

function la_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function yeu_cau_post(string $duongDanQuayLai = 'bang-dieu-khien.php', bool $kiemTraCsrf = true): void
{
    if (!la_post()) {
        thong_bao_loi('Phương thức yêu cầu không hợp lệ.');
        chuyen_huong($duongDanQuayLai);
    }

    if ($kiemTraCsrf && !csrf_token_hop_le()) {
        thong_bao_canh_bao('Phiên biểu mẫu không hợp lệ. Vui lòng thử lại.');
        chuyen_huong($duongDanQuayLai);
    }
}

function yeu_cau_csrf(string $duongDanQuayLai = 'bang-dieu-khien.php'): void
{
    if (!csrf_token_hop_le()) {
        thong_bao_canh_bao('Phiên biểu mẫu không hợp lệ. Vui lòng thử lại.');
        chuyen_huong($duongDanQuayLai);
    }
}

function hien_thi_loi_du_lieu(string $tieuDeTrang, string $noiDung): never
{
    $tieuDe = $tieuDeTrang;

    require __DIR__ . '/../giao-dien/dau-trang.php';
    echo '<div class="page-header"><div><p class="eyebrow">Dữ liệu</p><h1 class="page-title">'
        . e($tieuDeTrang)
        . '</h1><p class="page-subtitle">'
        . e($noiDung)
        . '</p></div></div>';
    echo '<section class="surface-card empty-state-inline"><strong>Chưa thể tải dữ liệu</strong>'
        . '<p>Kiểm tra MySQL trong XAMPP và import file <code>co-so-du-lieu/khoi-tao-co-so-du-lieu.sql</code>.</p>'
        . '<a class="btn btn-primary" href="' . e(duong_dan('dang-nhap.php')) . '">Về đăng nhập</a></section>';
    require __DIR__ . '/../giao-dien/cuoi-trang.php';
    exit;
}
