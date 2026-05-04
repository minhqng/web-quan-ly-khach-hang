<?php

declare(strict_types=1);

function lay_du_lieu_bang_dieu_khien(): array
{
    $nguoiDung = nguoi_dung_hien_tai();
    $maNhanVien = ($nguoiDung['vai_tro'] ?? '') === VAI_TRO_ADMIN ? null : (int) ($nguoiDung['id'] ?? 0);

    return [
        'kpi' => lay_kpi_bang_dieu_khien($maNhanVien),
        'top_khach_hang' => lay_top_khach_hang_bang_dieu_khien($maNhanVien),
        'cong_viec_sap_toi' => lay_cong_viec_sap_toi_bang_dieu_khien($maNhanVien),
        'cong_viec_qua_han' => lay_cong_viec_qua_han_bang_dieu_khien($maNhanVien),
        'hoat_dong_gan_day' => lay_hoat_dong_gan_day_bang_dieu_khien($maNhanVien),
        'pham_vi' => $maNhanVien === null ? 'Toàn hệ thống' : 'Công việc của tôi',
    ];
}

function lay_kpi_bang_dieu_khien(?int $maNhanVien): array
{
    [$phamViKhachSql, $thamSoKhach] = lay_pham_vi_khach_hang_bang_dieu_khien($maNhanVien);
    [$phamViViecSql, $thamSoViec] = lay_pham_vi_cong_viec_bang_dieu_khien($maNhanVien);

    $khachDangChamSoc = (int) lay_mot_gia_tri(
        "SELECT COUNT(*)
         FROM customers c
         WHERE c.deleted_at IS NULL
           AND c.status IN ('active', 'potential'){$phamViKhachSql}",
        $thamSoKhach
    );

    $khachVip = (int) lay_mot_gia_tri(
        "SELECT COUNT(*)
         FROM customers c
         INNER JOIN customer_types ct ON ct.id = c.customer_type_id
         WHERE c.deleted_at IS NULL
           AND c.status IN ('active', 'potential')
           AND ct.name = 'VIP'{$phamViKhachSql}",
        $thamSoKhach
    );

    $viecSapToi = (int) lay_mot_gia_tri(
        "SELECT COUNT(*)
         FROM follow_up_tasks t
         INNER JOIN customers c ON c.id = t.customer_id
         WHERE c.deleted_at IS NULL
           AND t.status IN ('pending', 'in_progress')
           AND t.due_at >= NOW(){$phamViViecSql}",
        $thamSoViec
    );

    $viecQuaHan = (int) lay_mot_gia_tri(
        "SELECT COUNT(*)
         FROM follow_up_tasks t
         INNER JOIN customers c ON c.id = t.customer_id
         WHERE c.deleted_at IS NULL
           AND t.status IN ('pending', 'in_progress')
           AND t.due_at < NOW(){$phamViViecSql}",
        $thamSoViec
    );

    return [
        ['nhan' => 'Khách đang chăm sóc', 'gia_tri' => $khachDangChamSoc, 'mo_ta' => 'Không tính hồ sơ đã xóa mềm', 'bien_the' => 'primary'],
        ['nhan' => 'Khách VIP', 'gia_tri' => $khachVip, 'mo_ta' => 'Nhóm cần ưu tiên chăm sóc', 'bien_the' => 'accent'],
        ['nhan' => 'Việc sắp tới', 'gia_tri' => $viecSapToi, 'mo_ta' => 'Đang chờ hoặc đang xử lý', 'bien_the' => 'success'],
        ['nhan' => 'Việc quá hạn', 'gia_tri' => $viecQuaHan, 'mo_ta' => 'Cần xử lý trước buổi demo', 'bien_the' => 'danger'],
    ];
}

function lay_top_khach_hang_bang_dieu_khien(?int $maNhanVien): array
{
    [$phamViSql, $thamSo] = lay_pham_vi_khach_hang_bang_dieu_khien($maNhanVien);

    return lay_nhieu_dong(
        "SELECT
            c.id,
            c.full_name,
            c.company_name,
            c.city,
            c.status,
            ct.name AS customer_type_name,
            ct.color AS customer_type_color,
            u.full_name AS assigned_user_name,
            COALESCE(i.interaction_count, 0) AS interaction_count,
            i.last_interaction_at,
            COALESCE(t.completed_task_count, 0) AS completed_task_count,
            COALESCE(t.overdue_task_count, 0) AS overdue_task_count,
            t.next_task_due_at,
            (
                COALESCE(ct.priority_score, 0)
                + COALESCE(i.interaction_count, 0) * 6
                + COALESCE(t.completed_task_count, 0) * 8
                - COALESCE(t.overdue_task_count, 0) * 5
                + CASE c.status WHEN 'active' THEN 12 WHEN 'potential' THEN 8 ELSE 0 END
            ) AS care_score
         FROM customers c
         INNER JOIN customer_types ct ON ct.id = c.customer_type_id
         LEFT JOIN users u ON u.id = c.assigned_user_id
         LEFT JOIN (
            SELECT customer_id, COUNT(*) AS interaction_count, MAX(interaction_at) AS last_interaction_at
            FROM interactions
            GROUP BY customer_id
         ) i ON i.customer_id = c.id
         LEFT JOIN (
            SELECT
                customer_id,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed_task_count,
                SUM(CASE WHEN status IN ('pending', 'in_progress') AND due_at < NOW() THEN 1 ELSE 0 END) AS overdue_task_count,
                MIN(CASE WHEN status IN ('pending', 'in_progress') AND due_at >= NOW() THEN due_at ELSE NULL END) AS next_task_due_at
            FROM follow_up_tasks
            GROUP BY customer_id
         ) t ON t.customer_id = c.id
         WHERE c.deleted_at IS NULL{$phamViSql}
         ORDER BY care_score DESC, i.last_interaction_at DESC, c.created_at DESC
         LIMIT 3",
        $thamSo
    );
}

function lay_cong_viec_sap_toi_bang_dieu_khien(?int $maNhanVien): array
{
    [$phamViSql, $thamSo] = lay_pham_vi_cong_viec_bang_dieu_khien($maNhanVien);

    return lay_nhieu_dong(
        "SELECT t.id, t.title, t.due_at, t.status, t.priority, c.full_name AS customer_name, u.full_name AS assigned_user_name
         FROM follow_up_tasks t
         INNER JOIN customers c ON c.id = t.customer_id
         INNER JOIN users u ON u.id = t.assigned_user_id
         WHERE c.deleted_at IS NULL
           AND t.status IN ('pending', 'in_progress')
           AND t.due_at >= NOW(){$phamViSql}
         ORDER BY t.due_at ASC, FIELD(t.priority, 'high', 'medium', 'low')
         LIMIT 5",
        $thamSo
    );
}

function lay_cong_viec_qua_han_bang_dieu_khien(?int $maNhanVien): array
{
    [$phamViSql, $thamSo] = lay_pham_vi_cong_viec_bang_dieu_khien($maNhanVien);

    return lay_nhieu_dong(
        "SELECT t.id, t.title, t.due_at, t.status, t.priority, c.full_name AS customer_name, u.full_name AS assigned_user_name
         FROM follow_up_tasks t
         INNER JOIN customers c ON c.id = t.customer_id
         INNER JOIN users u ON u.id = t.assigned_user_id
         WHERE c.deleted_at IS NULL
           AND t.status IN ('pending', 'in_progress')
           AND t.due_at < NOW(){$phamViSql}
         ORDER BY t.due_at ASC, FIELD(t.priority, 'high', 'medium', 'low')
         LIMIT 5",
        $thamSo
    );
}

function lay_hoat_dong_gan_day_bang_dieu_khien(?int $maNhanVien): array
{
    [$phamViSql, $thamSo] = lay_pham_vi_khach_hang_bang_dieu_khien($maNhanVien);

    return lay_nhieu_dong(
        "SELECT i.id, i.title, i.interaction_type, i.interaction_at, c.full_name AS customer_name, u.full_name AS user_name
         FROM interactions i
         INNER JOIN customers c ON c.id = i.customer_id
         INNER JOIN users u ON u.id = i.user_id
         WHERE c.deleted_at IS NULL{$phamViSql}
         ORDER BY i.interaction_at DESC
         LIMIT 6",
        $thamSo
    );
}

function lay_pham_vi_khach_hang_bang_dieu_khien(?int $maNhanVien): array
{
    return $maNhanVien === null ? ['', []] : [' AND c.assigned_user_id = :ma_nhan_vien', ['ma_nhan_vien' => $maNhanVien]];
}

function lay_pham_vi_cong_viec_bang_dieu_khien(?int $maNhanVien): array
{
    return $maNhanVien === null ? ['', []] : [' AND t.assigned_user_id = :ma_nhan_vien', ['ma_nhan_vien' => $maNhanVien]];
}
