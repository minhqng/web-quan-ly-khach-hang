<?php

declare(strict_types=1);

function dat_thong_bao(string $loai, string $noiDung): void
{
    $loaiHopLe = [FLASH_THANH_CONG, FLASH_LOI, FLASH_CANH_BAO, FLASH_THONG_TIN];
    $loai = in_array($loai, $loaiHopLe, true) ? $loai : FLASH_THONG_TIN;

    $_SESSION['thong_bao'] = [
        'loai' => $loai,
        'noi_dung' => $noiDung,
    ];
}

function lay_thong_bao(): ?array
{
    $thongBao = $_SESSION['thong_bao'] ?? null;
    unset($_SESSION['thong_bao']);

    return $thongBao;
}

function thong_bao_thanh_cong(string $noiDung): void
{
    dat_thong_bao(FLASH_THANH_CONG, $noiDung);
}

function thong_bao_loi(string $noiDung): void
{
    dat_thong_bao(FLASH_LOI, $noiDung);
}

function thong_bao_canh_bao(string $noiDung): void
{
    dat_thong_bao(FLASH_CANH_BAO, $noiDung);
}
