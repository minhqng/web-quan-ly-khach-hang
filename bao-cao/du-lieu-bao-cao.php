<?php

declare(strict_types=1);

function nhan_trang_thai_khach_bao_cao(): array
{
    return ['active' => 'Đang chăm sóc', 'potential' => 'Tiềm năng', 'inactive' => 'Tạm ngưng'];
}

function nhan_trang_thai_viec_bao_cao(): array
{
    return ['pending' => 'Chờ xử lý', 'in_progress' => 'Đang xử lý', 'completed' => 'Hoàn thành', 'cancelled' => 'Đã hủy'];
}

function nhan_tuong_tac_bao_cao(): array
{
    return [
        'call' => 'Cuộc gọi',
        'email' => 'Email',
        'meeting' => 'Gặp mặt',
        'note' => 'Ghi chú',
        'chat' => 'Chat',
        'zalo' => 'Zalo',
        'other' => 'Khác',
    ];
}

function ti_le_bao_cao(int|float $giaTri, int|float $tong): int
{
    return $tong > 0 ? (int) round(($giaTri / $tong) * 100) : 0;
}

function tong_cot_bao_cao(array $dong, string $cot = 'total'): int
{
    return array_reduce($dong, static fn (int $tong, array $muc): int => $tong + (int) ($muc[$cot] ?? 0), 0);
}

function mau_bao_cao_an_toan(?string $mau): string
{
    return preg_match('/^#[0-9a-fA-F]{6}$/', (string) $mau) ? (string) $mau : '#64748b';
}

function lay_tong_quan_bao_cao(): array
{
    $khachHang = (int) lay_mot_gia_tri("SELECT COUNT(*) FROM customers WHERE deleted_at IS NULL");
    $nhanVien = (int) lay_mot_gia_tri("SELECT COUNT(*) FROM users WHERE role = 'staff' AND status = 'active'");
    $tuongTac30Ngay = (int) lay_mot_gia_tri(
        "SELECT COUNT(*)
         FROM interactions i
         INNER JOIN customers c ON c.id = i.customer_id
         WHERE c.deleted_at IS NULL AND i.interaction_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)"
    );
    $viecDangMo = (int) lay_mot_gia_tri(
        "SELECT COUNT(*)
         FROM follow_up_tasks t
         INNER JOIN customers c ON c.id = t.customer_id
         WHERE c.deleted_at IS NULL AND t.status IN ('pending', 'in_progress')"
    );
    $viecQuaHan = (int) lay_mot_gia_tri(
        "SELECT COUNT(*)
         FROM follow_up_tasks t
         INNER JOIN customers c ON c.id = t.customer_id
         WHERE c.deleted_at IS NULL AND t.status IN ('pending', 'in_progress') AND t.due_at < NOW()"
    );
    $tongViec = (int) lay_mot_gia_tri("SELECT COUNT(*) FROM follow_up_tasks");
    $viecHoanThanh = (int) lay_mot_gia_tri("SELECT COUNT(*) FROM follow_up_tasks WHERE status = 'completed'");

    return [
        'khach_hang' => $khachHang,
        'nhan_vien' => $nhanVien,
        'tuong_tac_30_ngay' => $tuongTac30Ngay,
        'viec_dang_mo' => $viecDangMo,
        'viec_qua_han' => $viecQuaHan,
        'ty_le_hoan_thanh_viec' => ti_le_bao_cao($viecHoanThanh, $tongViec),
    ];
}

function lay_khach_hang_theo_loai_bao_cao(): array
{
    return lay_nhieu_dong(
        "SELECT ct.id, ct.name, ct.color, ct.priority_score,
            COUNT(c.id) AS total,
            SUM(CASE WHEN c.status = 'active' THEN 1 ELSE 0 END) AS active_count,
            SUM(CASE WHEN c.status = 'potential' THEN 1 ELSE 0 END) AS potential_count,
            SUM(CASE WHEN c.status = 'inactive' THEN 1 ELSE 0 END) AS inactive_count
         FROM customer_types ct
         LEFT JOIN customers c ON c.customer_type_id = ct.id AND c.deleted_at IS NULL
         GROUP BY ct.id, ct.name, ct.color, ct.priority_score
         ORDER BY total DESC, ct.priority_score DESC, ct.name ASC"
    );
}

function lay_khach_hang_theo_nhan_vien_bao_cao(): array
{
    return lay_nhieu_dong(
        "SELECT COALESCE(u.full_name, 'Chưa phân công') AS staff_name,
            COUNT(c.id) AS total,
            SUM(CASE WHEN c.status = 'active' THEN 1 ELSE 0 END) AS active_count,
            SUM(CASE WHEN c.status = 'potential' THEN 1 ELSE 0 END) AS potential_count,
            SUM(CASE WHEN c.status = 'inactive' THEN 1 ELSE 0 END) AS inactive_count
         FROM customers c
         LEFT JOIN users u ON u.id = c.assigned_user_id
         WHERE c.deleted_at IS NULL
         GROUP BY c.assigned_user_id, u.full_name
         ORDER BY total DESC, staff_name ASC"
    );
}

function lay_tuong_tac_theo_thoi_gian_bao_cao(): array
{
    return lay_nhieu_dong(
        "SELECT DATE(i.interaction_at) AS report_date, COUNT(*) AS total
         FROM interactions i
         INNER JOIN customers c ON c.id = i.customer_id
         WHERE c.deleted_at IS NULL AND i.interaction_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
         GROUP BY DATE(i.interaction_at)
         ORDER BY report_date ASC"
    );
}

function lay_tuong_tac_theo_loai_bao_cao(): array
{
    return lay_nhieu_dong(
        "SELECT i.interaction_type, COUNT(*) AS total
         FROM interactions i
         INNER JOIN customers c ON c.id = i.customer_id
         WHERE c.deleted_at IS NULL
         GROUP BY i.interaction_type
         ORDER BY total DESC, i.interaction_type ASC"
    );
}

function lay_tuong_tac_theo_nhan_vien_bao_cao(): array
{
    return lay_nhieu_dong(
        "SELECT u.full_name AS staff_name, COUNT(i.id) AS total, MAX(i.interaction_at) AS last_interaction_at
         FROM users u
         LEFT JOIN interactions i ON i.user_id = u.id
         WHERE u.role = 'staff' AND u.status = 'active'
         GROUP BY u.id, u.full_name
         ORDER BY total DESC, u.full_name ASC"
    );
}

function lay_cong_viec_theo_trang_thai_bao_cao(): array
{
    return lay_nhieu_dong(
        "SELECT t.status, COUNT(*) AS total,
            SUM(CASE WHEN t.status IN ('pending', 'in_progress') AND t.due_at < NOW() THEN 1 ELSE 0 END) AS overdue_count
         FROM follow_up_tasks t
         INNER JOIN customers c ON c.id = t.customer_id
         WHERE c.deleted_at IS NULL
         GROUP BY t.status
         ORDER BY FIELD(t.status, 'pending', 'in_progress', 'completed', 'cancelled')"
    );
}

function lay_hieu_qua_nhan_vien_bao_cao(): array
{
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
            FROM customers
            WHERE deleted_at IS NULL
            GROUP BY assigned_user_id
         ) c ON c.assigned_user_id = u.id
         LEFT JOIN (
            SELECT i.user_id, COUNT(*) AS interaction_count
            FROM interactions i
            INNER JOIN customers c ON c.id = i.customer_id
            WHERE c.deleted_at IS NULL
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
            WHERE c.deleted_at IS NULL
            GROUP BY t.assigned_user_id
         ) t ON t.assigned_user_id = u.id
         WHERE u.role = 'staff' AND u.status = 'active'
         ORDER BY completed_tasks DESC, interaction_count DESC, overdue_tasks ASC, u.full_name ASC"
    );
}
