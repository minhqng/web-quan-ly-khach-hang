<?php

declare(strict_types=1);

function tinh_phan_trang(int $tongDong, int $trangHienTai, int $soDongMoiTrang = SO_DONG_MOI_TRANG): array
{
    $soDongMoiTrang = max(1, $soDongMoiTrang);
    $tongTrang = max(1, (int) ceil($tongDong / $soDongMoiTrang));
    $trang = min(max(1, $trangHienTai), $tongTrang);

    return [
        'trang' => $trang,
        'tong_trang' => $tongTrang,
        'gioi_han' => $soDongMoiTrang,
        'bo_qua' => ($trang - 1) * $soDongMoiTrang,
        'co_trang_truoc' => $trang > 1,
        'co_trang_sau' => $trang < $tongTrang,
    ];
}

function lay_trang_hien_tai(string $tenThamSo = 'trang'): int
{
    return max(1, (int) ($_GET[$tenThamSo] ?? 1));
}
