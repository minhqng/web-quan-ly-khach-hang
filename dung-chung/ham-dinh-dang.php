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
