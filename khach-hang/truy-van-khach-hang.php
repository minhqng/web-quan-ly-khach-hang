<?php

declare(strict_types=1);

function lay_bo_loc_khach_hang(): array
{
    return [
        'tu_khoa' => chuoi_sach(gia_tri_get('tu_khoa', '')),
        'customer_type_id' => max(0, (int) gia_tri_get('customer_type_id', 0)),
        'assigned_user_id' => max(0, (int) gia_tri_get('assigned_user_id', 0)),
        'status' => (string) gia_tri_get('status', ''),
    ];
}

function tao_dieu_kien_loc_khach_hang(array $boLoc): array
{
    $dieuKien = [];
    $thamSo = [];
    $nguoiDung = nguoi_dung_hien_tai();
    $laAdmin = ($nguoiDung['vai_tro'] ?? '') === VAI_TRO_ADMIN;

    if ($boLoc['tu_khoa'] !== '') {
        $tuKhoa = '%' . $boLoc['tu_khoa'] . '%';
        $dieuKienTimKiem = ['c.full_name LIKE :kw_name', 'c.phone LIKE :kw_phone', 'c.email LIKE :kw_email'];
        $thamSo += [
            'kw_name' => $tuKhoa,
            'kw_phone' => $tuKhoa,
            'kw_email' => $tuKhoa,
        ];

        $dienThoaiChuan = chuan_hoa_dien_thoai_khach_hang($boLoc['tu_khoa']);
        if ($dienThoaiChuan !== '') {
            $dieuKienTimKiem[] = 'c.phone_normalized LIKE :kw_phone_norm';
            $thamSo['kw_phone_norm'] = '%' . $dienThoaiChuan . '%';
        }

        $dieuKien[] = '(' . implode(' OR ', $dieuKienTimKiem) . ')';
    }

    if ($boLoc['customer_type_id'] > 0) {
        $dieuKien[] = 'c.customer_type_id = :customer_type_id';
        $thamSo['customer_type_id'] = $boLoc['customer_type_id'];
    }

    if ($laAdmin && $boLoc['assigned_user_id'] > 0) {
        $dieuKien[] = 'c.assigned_user_id = :assigned_user_id';
        $thamSo['assigned_user_id'] = $boLoc['assigned_user_id'];
    } elseif (!$laAdmin) {
        $dieuKien[] = 'c.assigned_user_id = :scope_assigned_user_id';
        $thamSo['scope_assigned_user_id'] = (int) ($nguoiDung['id'] ?? 0);
    }

    if ($laAdmin && $boLoc['status'] === 'da_xoa') {
        $dieuKien[] = 'c.deleted_at IS NOT NULL';
    } else {
        $dieuKien[] = 'c.deleted_at IS NULL';
        if (array_key_exists($boLoc['status'], nhan_trang_thai_khach_hang())) {
            $dieuKien[] = 'c.status = :status';
            $thamSo['status'] = $boLoc['status'];
        }
    }

    return ['sql' => ' WHERE ' . implode(' AND ', $dieuKien), 'tham_so' => $thamSo];
}

function dem_khach_hang_theo_bo_loc(array $boLoc): int
{
    $loc = tao_dieu_kien_loc_khach_hang($boLoc);

    return (int) lay_mot_gia_tri('SELECT COUNT(*) FROM customers c' . $loc['sql'], $loc['tham_so']);
}

function lay_danh_sach_khach_hang(array $boLoc, array $phanTrang): array
{
    $loc = tao_dieu_kien_loc_khach_hang($boLoc);
    $gioiHan = (int) $phanTrang['gioi_han'];
    $boQua = (int) $phanTrang['bo_qua'];

    return lay_nhieu_dong(
        "SELECT c.id, c.full_name, c.company_name, c.phone, c.email, c.city, c.status, c.deleted_at,
            ct.name AS customer_type_name, ct.color AS customer_type_color,
            u.full_name AS assigned_user_name,
            COALESCE(i.interaction_count, 0) AS interaction_count,
            t.next_task_due_at
         FROM customers c
         INNER JOIN customer_types ct ON ct.id = c.customer_type_id
         LEFT JOIN users u ON u.id = c.assigned_user_id
         LEFT JOIN (
            SELECT customer_id, COUNT(*) AS interaction_count FROM interactions GROUP BY customer_id
         ) i ON i.customer_id = c.id
         LEFT JOIN (
            SELECT customer_id, MIN(due_at) AS next_task_due_at
            FROM follow_up_tasks
            WHERE status IN ('pending', 'in_progress') AND due_at >= NOW()
            GROUP BY customer_id
         ) t ON t.customer_id = c.id
         {$loc['sql']}
         ORDER BY c.deleted_at ASC, c.updated_at DESC, c.id DESC
         LIMIT {$gioiHan} OFFSET {$boQua}",
        $loc['tham_so']
    );
}

function lay_chi_tiet_khach_hang(int $id): ?array
{
    [$phamViSql, $thamSoPhamVi] = dieu_kien_pham_vi_khach_hang('c', 'detail_scope');
    $xoaSql = la_admin() ? '' : ' AND c.deleted_at IS NULL';

    return lay_mot_dong(
        "SELECT c.*, ct.name AS customer_type_name, ct.color AS customer_type_color,
            u.full_name AS assigned_user_name, u.email AS assigned_user_email,
            COALESCE(i.interaction_count, 0) AS interaction_count,
            COALESCE(t.open_task_count, 0) AS open_task_count,
            t.next_task_due_at
         FROM customers c
         INNER JOIN customer_types ct ON ct.id = c.customer_type_id
         LEFT JOIN users u ON u.id = c.assigned_user_id
         LEFT JOIN (
            SELECT customer_id, COUNT(*) AS interaction_count FROM interactions GROUP BY customer_id
         ) i ON i.customer_id = c.id
         LEFT JOIN (
            SELECT customer_id,
                SUM(CASE WHEN status IN ('pending', 'in_progress') THEN 1 ELSE 0 END) AS open_task_count,
                MIN(CASE WHEN status IN ('pending', 'in_progress') THEN due_at ELSE NULL END) AS next_task_due_at
            FROM follow_up_tasks GROUP BY customer_id
         ) t ON t.customer_id = c.id
         WHERE c.id = :id{$xoaSql}{$phamViSql}
         LIMIT 1",
        ['id' => $id] + $thamSoPhamVi
    );
}

function lay_tuong_tac_cua_khach_hang(int $id): array
{
    if (!khach_hang_thuoc_pham_vi_hien_tai($id, true)) {
        return [];
    }

    return lay_nhieu_dong(
        "SELECT i.title, i.interaction_type, i.interaction_at, u.full_name AS user_name
         FROM interactions i
         INNER JOIN users u ON u.id = i.user_id
         WHERE i.customer_id = :id
         ORDER BY i.interaction_at DESC
         LIMIT 6",
        ['id' => $id]
    );
}

function lay_cong_viec_cua_khach_hang(int $id): array
{
    if (!khach_hang_thuoc_pham_vi_hien_tai($id, true)) {
        return [];
    }

    return lay_nhieu_dong(
        "SELECT t.title, t.status, t.priority, t.due_at, t.completed_at, u.full_name AS assigned_user_name
         FROM follow_up_tasks t
         INNER JOIN users u ON u.id = t.assigned_user_id
         WHERE t.customer_id = :id
         ORDER BY FIELD(t.status, 'pending', 'in_progress', 'completed', 'cancelled'), t.due_at ASC
         LIMIT 8",
        ['id' => $id]
    );
}

function dieu_kien_pham_vi_khach_hang(string $biDanh = 'c', string $tenThamSo = 'scope_user_id'): array
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

function khach_hang_thuoc_pham_vi_hien_tai(int $id, bool $choPhepDaXoa = false): bool
{
    [$phamViSql, $thamSoPhamVi] = dieu_kien_pham_vi_khach_hang('c', 'scope_customer_user_id');
    $dieuKienXoa = $choPhepDaXoa ? '' : ' AND c.deleted_at IS NULL';

    return (int) lay_mot_gia_tri(
        "SELECT COUNT(*) FROM customers c WHERE c.id = :id{$dieuKienXoa}{$phamViSql}",
        ['id' => $id] + $thamSoPhamVi
    ) > 0;
}
