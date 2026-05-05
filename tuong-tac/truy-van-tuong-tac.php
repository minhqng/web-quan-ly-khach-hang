<?php

declare(strict_types=1);

function khach_hang_tuong_tac_ton_tai(int $id): bool
{
    [$phamViSql, $thamSoPhamVi] = dieu_kien_pham_vi_khach_hang_tuong_tac('customers', 'scope_customer_interaction');

    return (int) lay_mot_gia_tri(
        "SELECT COUNT(*) FROM customers WHERE id = :id AND deleted_at IS NULL{$phamViSql}",
        ['id' => $id] + $thamSoPhamVi
    ) > 0;
}

function lay_lua_chon_khach_hang_tuong_tac(?int $idHienTai = null): array
{
    [$phamViSql, $thamSo] = dieu_kien_pham_vi_khach_hang_tuong_tac('customers', 'scope_customer_options');
    $sql = "SELECT id, full_name, company_name FROM customers WHERE deleted_at IS NULL{$phamViSql}";

    if ($idHienTai !== null && la_admin()) {
        $sql .= ' OR id = :id';
        $thamSo['id'] = $idHienTai;
    }

    return lay_nhieu_dong($sql . ' ORDER BY full_name ASC', $thamSo);
}

function lay_tuong_tac_theo_id(int $id): ?array
{
    [$phamViSql, $thamSoPhamVi] = dieu_kien_pham_vi_khach_hang_tuong_tac('c', 'scope_interaction_detail');

    return lay_mot_dong(
        "SELECT i.*, c.full_name AS customer_name, c.company_name, u.full_name AS user_name
         FROM interactions i
         INNER JOIN customers c ON c.id = i.customer_id
         INNER JOIN users u ON u.id = i.user_id
         WHERE i.id = :id{$phamViSql}
         LIMIT 1",
        ['id' => $id] + $thamSoPhamVi
    );
}

function lay_danh_sach_tuong_tac(int $maKhachHang = 0, int $gioiHan = 40, bool $choPhepKhachDaXoa = false): array
{
    $where = $choPhepKhachDaXoa ? 'WHERE 1 = 1' : 'WHERE c.deleted_at IS NULL';
    [$phamViSql, $thamSo] = dieu_kien_pham_vi_khach_hang_tuong_tac('c', 'scope_interaction_list');
    $where .= $phamViSql;

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

function lay_tuong_tac_cua_khach_hang_day_du(int $maKhachHang, int $gioiHan = 8, bool $choPhepKhachDaXoa = false): array
{
    return lay_danh_sach_tuong_tac($maKhachHang, $gioiHan, $choPhepKhachDaXoa);
}

function co_the_sua_xoa_tuong_tac(array $tuongTac): bool
{
    $nguoiDung = nguoi_dung_hien_tai();

    return ($nguoiDung['vai_tro'] ?? '') === VAI_TRO_ADMIN
        || (int) ($nguoiDung['id'] ?? 0) === (int) ($tuongTac['user_id'] ?? 0);
}

function dieu_kien_pham_vi_khach_hang_tuong_tac(string $biDanh, string $tenThamSo): array
{
    $nguoiDung = nguoi_dung_hien_tai();

    if (($nguoiDung['vai_tro'] ?? '') === VAI_TRO_ADMIN) {
        return ['', []];
    }

    return [
        " AND {$biDanh}.assigned_user_id = :{$tenThamSo}",
        [$tenThamSo => (int) ($nguoiDung['id'] ?? 0)],
    ];
}
