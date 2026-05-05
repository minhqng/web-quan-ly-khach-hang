<?php

declare(strict_types=1);

const SO_LAN_DANG_NHAP_SAI_TOI_DA = 5;
const THOI_GIAN_TAM_KHOA_DANG_NHAP = 300;
const THOI_GIAN_LUU_THU_DANG_NHAP = 900;

function dang_nhap_bi_tam_khoa(string $taiKhoan): bool
{
    return (bool) xu_ly_kho_thu_dang_nhap(function (array &$duLieu) use ($taiKhoan): bool {
        $khoa = khoa_thu_dang_nhap($taiKhoan);
        $banGhi = $duLieu[$khoa] ?? null;

        return is_array($banGhi)
            && (int) ($banGhi['so_lan'] ?? 0) >= SO_LAN_DANG_NHAP_SAI_TOI_DA
            && time() - (int) ($banGhi['luc_cuoi'] ?? 0) < THOI_GIAN_TAM_KHOA_DANG_NHAP;
    });
}

function ghi_nhan_dang_nhap_that_bai(string $taiKhoan): void
{
    xu_ly_kho_thu_dang_nhap(function (array &$duLieu) use ($taiKhoan): void {
        $khoa = khoa_thu_dang_nhap($taiKhoan);
        $banGhi = $duLieu[$khoa] ?? ['so_lan' => 0, 'luc_cuoi' => 0];
        $duLieu[$khoa] = [
            'so_lan' => (int) ($banGhi['so_lan'] ?? 0) + 1,
            'luc_cuoi' => time(),
        ];
    });
}

function xoa_thu_dang_nhap(string $taiKhoan): void
{
    xu_ly_kho_thu_dang_nhap(function (array &$duLieu) use ($taiKhoan): void {
        unset($duLieu[khoa_thu_dang_nhap($taiKhoan)]);
    });
}

function khoa_thu_dang_nhap(string $taiKhoan): string
{
    return hash('sha256', mb_strtolower(trim($taiKhoan), 'UTF-8') . '|' . ($_SERVER['REMOTE_ADDR'] ?? 'local'));
}

function xu_ly_kho_thu_dang_nhap(callable $capNhat): mixed
{
    $duongDan = THU_MUC_GOC . '/nhat-ky/dang-nhap-that-bai.json';
    $thuMuc = dirname($duongDan);

    if (!is_dir($thuMuc)) {
        mkdir($thuMuc, 0775, true);
    }

    $tep = fopen($duongDan, 'c+');
    if ($tep === false) {
        $duLieuTam = [];

        return $capNhat($duLieuTam);
    }

    try {
        flock($tep, LOCK_EX);
        rewind($tep);
        $noiDung = stream_get_contents($tep) ?: '';
        $duLieu = json_decode($noiDung, true);
        $duLieu = is_array($duLieu) ? $duLieu : [];
        $duLieu = loc_thu_dang_nhap_con_han($duLieu);
        $ketQua = $capNhat($duLieu);

        rewind($tep);
        ftruncate($tep, 0);
        fwrite($tep, json_encode($duLieu, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        return $ketQua;
    } finally {
        flock($tep, LOCK_UN);
        fclose($tep);
    }
}

function loc_thu_dang_nhap_con_han(array $duLieu): array
{
    $bayGio = time();

    return array_filter(
        $duLieu,
        static fn (mixed $banGhi): bool => is_array($banGhi)
            && $bayGio - (int) ($banGhi['luc_cuoi'] ?? 0) < THOI_GIAN_LUU_THU_DANG_NHAP
    );
}
