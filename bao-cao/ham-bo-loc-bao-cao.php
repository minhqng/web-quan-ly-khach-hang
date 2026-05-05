<?php

declare(strict_types=1);

function lay_bo_loc_bao_cao(): array
{
    $nguoiDung = nguoi_dung_hien_tai();
    $laAdmin = ($nguoiDung['vai_tro'] ?? '') === VAI_TRO_ADMIN;
    $tuNgay = ngay_bao_cao_hop_le((string) gia_tri_get('tu_ngay', ''));
    $denNgay = ngay_bao_cao_hop_le((string) gia_tri_get('den_ngay', ''));

    if ($tuNgay !== '' && $denNgay !== '' && strtotime($tuNgay) > strtotime($denNgay)) {
        [$tuNgay, $denNgay] = [$denNgay, $tuNgay];
    }

    return [
        'tu_ngay' => $tuNgay,
        'den_ngay' => $denNgay,
        'staff_id' => $laAdmin ? max(0, (int) gia_tri_get('staff_id', 0)) : (int) ($nguoiDung['id'] ?? 0),
        'customer_type_id' => max(0, (int) gia_tri_get('customer_type_id', 0)),
    ];
}

function ngay_bao_cao_hop_le(string $ngay): string
{
    if ($ngay === '') {
        return '';
    }

    $dt = DateTime::createFromFormat('Y-m-d', $ngay);

    return $dt && $dt->format('Y-m-d') === $ngay ? $ngay : '';
}

function lay_lua_chon_nhan_vien_bao_cao(): array
{
    $nguoiDung = nguoi_dung_hien_tai();

    if (($nguoiDung['vai_tro'] ?? '') !== VAI_TRO_ADMIN) {
        return lay_nhieu_dong(
            "SELECT id, full_name FROM users WHERE id = :id AND role = 'staff'",
            ['id' => (int) ($nguoiDung['id'] ?? 0)]
        );
    }

    return lay_nhieu_dong("SELECT id, full_name FROM users WHERE role = 'staff' ORDER BY full_name ASC");
}

function lay_lua_chon_loai_khach_hang_bao_cao(): array
{
    return lay_nhieu_dong('SELECT id, name FROM customer_types ORDER BY priority_score DESC, name ASC');
}

function dieu_kien_loc_khach_hang_bao_cao(array $boLoc, string $biDanh = 'c', string $tienTo = 'bc'): array
{
    $sql = '';
    $thamSo = [];

    if ((int) ($boLoc['staff_id'] ?? 0) > 0) {
        $sql .= " AND {$biDanh}.assigned_user_id = :{$tienTo}_staff_id";
        $thamSo["{$tienTo}_staff_id"] = (int) $boLoc['staff_id'];
    }

    if ((int) ($boLoc['customer_type_id'] ?? 0) > 0) {
        $sql .= " AND {$biDanh}.customer_type_id = :{$tienTo}_customer_type_id";
        $thamSo["{$tienTo}_customer_type_id"] = (int) $boLoc['customer_type_id'];
    }

    return ['sql' => $sql, 'tham_so' => $thamSo];
}

function dieu_kien_ngay_bao_cao(array $boLoc, string $cot, string $tienTo): array
{
    $sql = '';
    $thamSo = [];

    if (($boLoc['tu_ngay'] ?? '') !== '') {
        $sql .= " AND DATE({$cot}) >= :{$tienTo}_tu_ngay";
        $thamSo["{$tienTo}_tu_ngay"] = $boLoc['tu_ngay'];
    }

    if (($boLoc['den_ngay'] ?? '') !== '') {
        $sql .= " AND DATE({$cot}) <= :{$tienTo}_den_ngay";
        $thamSo["{$tienTo}_den_ngay"] = $boLoc['den_ngay'];
    }

    return ['sql' => $sql, 'tham_so' => $thamSo];
}
