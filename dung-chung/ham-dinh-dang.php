<?php

declare(strict_types=1);

function dinh_dang_ngay(?string $ngay): string
{
    if (!$ngay) {
        return '';
    }

    $timestamp = strtotime($ngay);

    return $timestamp === false ? '' : date('d/m/Y', $timestamp);
}

function dinh_dang_ngay_gio(?string $ngayGio): string
{
    if (!$ngayGio) {
        return '';
    }

    $timestamp = strtotime($ngayGio);

    return $timestamp === false ? '' : date('d/m/Y H:i', $timestamp);
}

function ngay_html_hop_le(string $ngay): bool
{
    return thoi_gian_theo_dinh_dang_hop_le($ngay, 'Y-m-d');
}

function thoi_gian_html_hop_le(string $thoiGian): bool
{
    return thoi_gian_theo_dinh_dang_hop_le($thoiGian, 'Y-m-d\TH:i');
}

function datetime_mysql_tu_html(string $thoiGian): string
{
    if (!thoi_gian_html_hop_le($thoiGian)) {
        return date('Y-m-d H:i:s');
    }

    $dt = DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $thoiGian);

    return $dt instanceof DateTimeImmutable ? $dt->format('Y-m-d H:i:s') : date('Y-m-d H:i:s');
}

function timestamp_tu_datetime_html(string $thoiGian): ?int
{
    if (!thoi_gian_html_hop_le($thoiGian)) {
        return null;
    }

    $dt = DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $thoiGian);

    return $dt instanceof DateTimeImmutable ? $dt->getTimestamp() : null;
}

function thoi_gian_theo_dinh_dang_hop_le(string $giaTri, string $dinhDang): bool
{
    if ($giaTri === '') {
        return false;
    }

    $dt = DateTimeImmutable::createFromFormat($dinhDang, $giaTri);
    $loi = DateTimeImmutable::getLastErrors();

    return $dt instanceof DateTimeImmutable
        && $dt->format($dinhDang) === $giaTri
        && (
            $loi === false
            || ((int) $loi['warning_count'] === 0 && (int) $loi['error_count'] === 0)
        );
}
