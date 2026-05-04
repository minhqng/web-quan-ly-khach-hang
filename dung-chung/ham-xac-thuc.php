<?php

declare(strict_types=1);

function da_dang_nhap(): bool
{
    return isset($_SESSION['nguoi_dung']) && !phien_dang_nhap_het_han();
}

function nguoi_dung_hien_tai(): ?array
{
    return $_SESSION['nguoi_dung'] ?? null;
}

function la_admin(): bool
{
    return co_vai_tro(VAI_TRO_ADMIN);
}

function co_vai_tro(string|array $vaiTro): bool
{
    $vaiTroHienTai = nguoi_dung_hien_tai()['vai_tro'] ?? null;
    $danhSachVaiTro = is_array($vaiTro) ? $vaiTro : [$vaiTro];

    return $vaiTroHienTai !== null && in_array($vaiTroHienTai, $danhSachVaiTro, true);
}

function dang_nhap_nguoi_dung(array $nguoiDung): void
{
    session_regenerate_id(true);

    $_SESSION['nguoi_dung'] = [
        'id' => (int) ($nguoiDung['id'] ?? 0),
        'ho_ten' => (string) ($nguoiDung['full_name'] ?? $nguoiDung['ho_ten'] ?? ''),
        'ten_dang_nhap' => (string) ($nguoiDung['username'] ?? $nguoiDung['ten_dang_nhap'] ?? ''),
        'email' => (string) ($nguoiDung['email'] ?? ''),
        'vai_tro' => (string) ($nguoiDung['role'] ?? $nguoiDung['vai_tro'] ?? VAI_TRO_NHAN_VIEN),
    ];
    $_SESSION['dang_nhap_luc'] = time();
    $_SESSION['hoat_dong_cuoi'] = time();
    $_SESSION['tai_tao_luc'] = time();
}

function dang_xuat_nguoi_dung(): void
{
    $_SESSION = [];

    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }

    $_SESSION['khoi_tao_luc'] = time();
    $_SESSION['hoat_dong_cuoi'] = time();
    $_SESSION['tai_tao_luc'] = time();
}

function phien_dang_nhap_het_han(): bool
{
    if (!isset($_SESSION['nguoi_dung'], $_SESSION['hoat_dong_cuoi'])) {
        return false;
    }

    return time() - (int) $_SESSION['hoat_dong_cuoi'] > THOI_GIAN_HET_HAN_SESSION;
}

function cap_nhat_hoat_dong_phien(): void
{
    if (time() - (int) ($_SESSION['tai_tao_luc'] ?? 0) > 900) {
        session_regenerate_id(true);
        $_SESSION['tai_tao_luc'] = time();
    }

    $_SESSION['hoat_dong_cuoi'] = time();
}

function yeu_cau_dang_nhap(): void
{
    if (phien_dang_nhap_het_han()) {
        dang_xuat_nguoi_dung();
        thong_bao_canh_bao('Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.');
        chuyen_huong('dang-nhap.php');
    }

    if (!isset($_SESSION['nguoi_dung'])) {
        thong_bao_canh_bao('Vui lòng đăng nhập để tiếp tục.');
        chuyen_huong('dang-nhap.php');
    }

    cap_nhat_hoat_dong_phien();
}

function yeu_cau_vai_tro(string|array $vaiTro): void
{
    yeu_cau_dang_nhap();

    if (!co_vai_tro($vaiTro)) {
        thong_bao_loi('Tài khoản không có quyền truy cập chức năng này.');
        chuyen_huong('khong-co-quyen.php');
    }
}

function yeu_cau_admin(): void
{
    yeu_cau_vai_tro(VAI_TRO_ADMIN);
}

function tim_nguoi_dung_dang_nhap(string $tenDangNhapHoacEmail): ?array
{
    return lay_mot_dong(
        'SELECT id, full_name, username, email, password_hash, role, status
         FROM users
         WHERE username = :ten_dang_nhap OR email = :email
         LIMIT 1',
        [
            'ten_dang_nhap' => $tenDangNhapHoacEmail,
            'email' => $tenDangNhapHoacEmail,
        ]
    );
}

function mat_khau_hop_le(string $matKhau, string $hash): bool
{
    return password_verify($matKhau, $hash);
}

function tao_hash_mat_khau(string $matKhau): string
{
    return password_hash($matKhau, PASSWORD_DEFAULT);
}
