<?php

declare(strict_types=1);

function lay_hieu_qua_nhan_vien_bao_cao(array $boLoc = []): array
{
    $locKhach = dieu_kien_loc_khach_hang_bao_cao($boLoc, 'c', 'perf_customer');
    $ngayKhach = dieu_kien_ngay_bao_cao($boLoc, 'c.created_at', 'perf_customer');
    $locTuongTac = dieu_kien_loc_khach_hang_bao_cao($boLoc, 'c', 'perf_interaction');
    $ngayTuongTac = dieu_kien_ngay_bao_cao($boLoc, 'i.interaction_at', 'perf_interaction');
    $locViec = dieu_kien_loc_khach_hang_bao_cao($boLoc, 'c', 'perf_task');
    $ngayViec = dieu_kien_ngay_bao_cao($boLoc, 't.due_at', 'perf_task');
    $locNhanVien = (int) ($boLoc['staff_id'] ?? 0) > 0 ? ' AND u.id = :perf_user_id' : '';
    $thamSoNhanVien = (int) ($boLoc['staff_id'] ?? 0) > 0 ? ['perf_user_id' => (int) $boLoc['staff_id']] : [];

    return lay_nhieu_dong(
        "SELECT u.id, u.full_name AS staff_name,
            COALESCE(c.assigned_customers, 0) AS assigned_customers,
            COALESCE(i.interaction_count, 0) AS interaction_count,
            COALESCE(t.total_tasks, 0) AS total_tasks,
            COALESCE(t.completed_tasks, 0) AS completed_tasks,
            COALESCE(t.open_tasks, 0) AS open_tasks,
            COALESCE(t.overdue_tasks, 0) AS overdue_tasks
         FROM users u
         LEFT JOIN (
            SELECT assigned_user_id, COUNT(*) AS assigned_customers
            FROM customers c
            WHERE c.deleted_at IS NULL{$locKhach['sql']}{$ngayKhach['sql']}
            GROUP BY assigned_user_id
         ) c ON c.assigned_user_id = u.id
         LEFT JOIN (
            SELECT i.user_id, COUNT(*) AS interaction_count
            FROM interactions i
            INNER JOIN customers c ON c.id = i.customer_id
            WHERE c.deleted_at IS NULL{$locTuongTac['sql']}{$ngayTuongTac['sql']}
            GROUP BY i.user_id
         ) i ON i.user_id = u.id
         LEFT JOIN (
            SELECT t.assigned_user_id,
                COUNT(*) AS total_tasks,
                SUM(CASE WHEN t.status = 'completed' THEN 1 ELSE 0 END) AS completed_tasks,
                SUM(CASE WHEN t.status IN ('pending', 'in_progress') THEN 1 ELSE 0 END) AS open_tasks,
                SUM(CASE WHEN t.status IN ('pending', 'in_progress') AND t.due_at < NOW() THEN 1 ELSE 0 END) AS overdue_tasks
            FROM follow_up_tasks t
            INNER JOIN customers c ON c.id = t.customer_id
            WHERE c.deleted_at IS NULL{$locViec['sql']}{$ngayViec['sql']}
            GROUP BY t.assigned_user_id
         ) t ON t.assigned_user_id = u.id
         WHERE u.role = 'staff' AND u.status = 'active'{$locNhanVien}
         ORDER BY completed_tasks DESC, interaction_count DESC, overdue_tasks ASC, u.full_name ASC",
        $locKhach['tham_so'] + $ngayKhach['tham_so']
        + $locTuongTac['tham_so'] + $ngayTuongTac['tham_so']
        + $locViec['tham_so'] + $ngayViec['tham_so']
        + $thamSoNhanVien
    );
}
