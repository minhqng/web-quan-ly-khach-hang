<?php

declare(strict_types=1);

function lay_lua_chon_khach_hang_cong_viec(?int $idHienTai = null): array
{
    [$phamViSql, $thamSo] = dieu_kien_pham_vi_khach_hang_cong_viec('customers', 'scope_task_customer_options');
    $sql = "SELECT id, full_name, company_name FROM customers WHERE deleted_at IS NULL{$phamViSql}";

    if ($idHienTai !== null && la_admin()) {
        $sql .= ' OR id = :id';
        $thamSo['id'] = $idHienTai;
    }

    return lay_nhieu_dong($sql . ' ORDER BY full_name ASC', $thamSo);
}

function lay_lua_chon_nhan_vien_cong_viec(): array
{
    $nguoiDung = nguoi_dung_hien_tai();

    if (($nguoiDung['vai_tro'] ?? '') !== VAI_TRO_ADMIN) {
        return lay_nhieu_dong(
            "SELECT id, full_name
             FROM users
             WHERE id = :id AND role = 'staff' AND status = 'active'",
            ['id' => (int) ($nguoiDung['id'] ?? 0)]
        );
    }

    return lay_nhieu_dong(
        "SELECT id, full_name
         FROM users
         WHERE role = 'staff' AND status = 'active'
         ORDER BY full_name ASC"
    );
}

function khach_hang_cong_viec_ton_tai(int $id): bool
{
    [$phamViSql, $thamSoPhamVi] = dieu_kien_pham_vi_khach_hang_cong_viec('customers', 'scope_task_customer_exists');

    return (int) lay_mot_gia_tri(
        "SELECT COUNT(*) FROM customers WHERE id = :id AND deleted_at IS NULL{$phamViSql}",
        ['id' => $id] + $thamSoPhamVi
    ) > 0;
}

function nhan_vien_cong_viec_ton_tai(int $id): bool
{
    $nguoiDung = nguoi_dung_hien_tai();

    if (($nguoiDung['vai_tro'] ?? '') !== VAI_TRO_ADMIN && $id !== (int) ($nguoiDung['id'] ?? 0)) {
        return false;
    }

    return (int) lay_mot_gia_tri(
        "SELECT COUNT(*) FROM users WHERE id = :id AND role = 'staff' AND status = 'active'",
        ['id' => $id]
    ) > 0;
}

function lay_cong_viec_theo_id(int $id): ?array
{
    return lay_mot_dong(
        'SELECT * FROM follow_up_tasks WHERE id = :id LIMIT 1',
        ['id' => $id]
    );
}

function dieu_kien_pham_vi_khach_hang_cong_viec(string $biDanh, string $tenThamSo): array
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

function co_quyen_cap_nhat_cong_viec(array $congViec): bool
{
    $nguoiDung = nguoi_dung_hien_tai();

    return ($nguoiDung['vai_tro'] ?? '') === VAI_TRO_ADMIN
        || (int) ($nguoiDung['id'] ?? 0) === (int) $congViec['assigned_user_id'];
}

function tao_dieu_kien_pham_vi_cong_viec(string $cheDo): array
{
    $nguoiDung = nguoi_dung_hien_tai();
    $dieuKien = ['c.deleted_at IS NULL'];
    $thamSo = [];

    if (($nguoiDung['vai_tro'] ?? '') !== VAI_TRO_ADMIN) {
        $dieuKien[] = 't.assigned_user_id = :assigned_user_id';
        $thamSo['assigned_user_id'] = (int) ($nguoiDung['id'] ?? 0);
    }

    if ($cheDo === 'overdue') {
        $dieuKien[] = "t.status IN ('pending', 'in_progress')";
        $dieuKien[] = 't.due_at < NOW()';
    } elseif ($cheDo === 'upcoming') {
        $dieuKien[] = "t.status IN ('pending', 'in_progress')";
        $dieuKien[] = 't.due_at >= NOW()';
    }

    return ['sql' => ' WHERE ' . implode(' AND ', $dieuKien), 'tham_so' => $thamSo];
}

function lay_danh_sach_cong_viec_theo_doi(string $cheDo = 'my'): array
{
    $loc = tao_dieu_kien_pham_vi_cong_viec($cheDo);

    return lay_nhieu_dong(
        "SELECT t.id, t.title, t.description, t.due_at, t.status, t.priority, t.completed_at,
            c.full_name AS customer_name, c.company_name,
            u.full_name AS assigned_user_name
         FROM follow_up_tasks t
         INNER JOIN customers c ON c.id = t.customer_id
         INNER JOIN users u ON u.id = t.assigned_user_id
         {$loc['sql']}
         ORDER BY FIELD(t.status, 'pending', 'in_progress', 'completed', 'cancelled'), t.due_at ASC
         LIMIT 60",
        $loc['tham_so']
    );
}
