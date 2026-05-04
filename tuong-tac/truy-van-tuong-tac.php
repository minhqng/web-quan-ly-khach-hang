<?php

declare(strict_types=1);

function khach_hang_tuong_tac_ton_tai(int $id): bool
{
    return (int) lay_mot_gia_tri(
        'SELECT COUNT(*) FROM customers WHERE id = :id AND deleted_at IS NULL',
        ['id' => $id]
    ) > 0;
}

function lay_lua_chon_khach_hang_tuong_tac(?int $idHienTai = null): array
{
    $sql = 'SELECT id, full_name, company_name FROM customers WHERE deleted_at IS NULL';
    $thamSo = [];

    if ($idHienTai !== null) {
        $sql .= ' OR id = :id';
        $thamSo['id'] = $idHienTai;
    }

    return lay_nhieu_dong($sql . ' ORDER BY full_name ASC', $thamSo);
}

function lay_tuong_tac_theo_id(int $id): ?array
{
    return lay_mot_dong(
        "SELECT i.*, c.full_name AS customer_name, c.company_name, u.full_name AS user_name
         FROM interactions i
         INNER JOIN customers c ON c.id = i.customer_id
         INNER JOIN users u ON u.id = i.user_id
         WHERE i.id = :id
         LIMIT 1",
        ['id' => $id]
    );
}

function lay_danh_sach_tuong_tac(int $maKhachHang = 0, int $gioiHan = 40): array
{
    $where = 'WHERE c.deleted_at IS NULL';
    $thamSo = [];

    if ($maKhachHang > 0) {
        $where .= ' AND i.customer_id = :customer_id';
        $thamSo['customer_id'] = $maKhachHang;
    }

    return lay_nhieu_dong(
        "SELECT i.id, i.customer_id, i.user_id, i.interaction_type, i.title, i.content, i.result,
            i.interaction_at, i.created_at, c.full_name AS customer_name, c.company_name,
            u.full_name AS user_name
         FROM interactions i
         INNER JOIN customers c ON c.id = i.customer_id
         INNER JOIN users u ON u.id = i.user_id
         {$where}
         ORDER BY i.interaction_at DESC, i.id DESC
         LIMIT {$gioiHan}",
        $thamSo
    );
}

function lay_tuong_tac_cua_khach_hang_day_du(int $maKhachHang, int $gioiHan = 8): array
{
    return lay_danh_sach_tuong_tac($maKhachHang, $gioiHan);
}

function co_the_sua_xoa_tuong_tac(array $tuongTac): bool
{
    $nguoiDung = nguoi_dung_hien_tai();

    return ($nguoiDung['vai_tro'] ?? '') === VAI_TRO_ADMIN
        || (int) ($nguoiDung['id'] ?? 0) === (int) ($tuongTac['user_id'] ?? 0);
}
