<?php

declare(strict_types=1);

require_once __DIR__ . '/ham-bo-loc-bao-cao.php';
require_once __DIR__ . '/ham-tien-ich-bao-cao.php';
require_once __DIR__ . '/du-lieu-hieu-qua-bao-cao.php';

function lay_tong_quan_bao_cao(array $boLoc = []): array
{
    $locKhach = dieu_kien_loc_khach_hang_bao_cao($boLoc, 'c', 'overview_customer');
    $ngayKhach = dieu_kien_ngay_bao_cao($boLoc, 'c.created_at', 'overview_customer');
    $ngayTuongTac = dieu_kien_ngay_bao_cao($boLoc, 'i.interaction_at', 'overview_interaction');
    $macDinhTuongTac30Ngay = $ngayTuongTac['sql'] === '' ? ' AND i.interaction_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)' : '';
    $ngayViec = dieu_kien_ngay_bao_cao($boLoc, 't.due_at', 'overview_task');
    $sqlKhach = $locKhach['sql'] . $ngayKhach['sql'];
    $thamSoKhach = $locKhach['tham_so'] + $ngayKhach['tham_so'];
    $sqlViec = $locKhach['sql'] . $ngayViec['sql'];
    $thamSoViec = $locKhach['tham_so'] + $ngayViec['tham_so'];

    $khachHang = (int) lay_mot_gia_tri("SELECT COUNT(*) FROM customers c WHERE c.deleted_at IS NULL{$sqlKhach}", $thamSoKhach);
    $nhanVien = (int) lay_mot_gia_tri("SELECT COUNT(*) FROM users WHERE role = 'staff' AND status = 'active'");
    $tuongTac30Ngay = (int) lay_mot_gia_tri(
        "SELECT COUNT(*)
         FROM interactions i
         INNER JOIN customers c ON c.id = i.customer_id
         WHERE c.deleted_at IS NULL{$locKhach['sql']}{$ngayTuongTac['sql']}{$macDinhTuongTac30Ngay}",
        $locKhach['tham_so'] + $ngayTuongTac['tham_so']
    );
    $viecDangMo = (int) lay_mot_gia_tri(
        "SELECT COUNT(*)
         FROM follow_up_tasks t
         INNER JOIN customers c ON c.id = t.customer_id
         WHERE c.deleted_at IS NULL AND t.status IN ('pending', 'in_progress'){$sqlViec}",
        $thamSoViec
    );
    $viecQuaHan = (int) lay_mot_gia_tri(
        "SELECT COUNT(*)
         FROM follow_up_tasks t
         INNER JOIN customers c ON c.id = t.customer_id
         WHERE c.deleted_at IS NULL AND t.status IN ('pending', 'in_progress') AND t.due_at < NOW(){$sqlViec}",
        $thamSoViec
    );
    $tongViec = (int) lay_mot_gia_tri(
        "SELECT COUNT(*)
         FROM follow_up_tasks t
         INNER JOIN customers c ON c.id = t.customer_id
         WHERE c.deleted_at IS NULL{$sqlViec}",
        $thamSoViec
    );
    $viecHoanThanh = (int) lay_mot_gia_tri(
        "SELECT COUNT(*)
         FROM follow_up_tasks t
         INNER JOIN customers c ON c.id = t.customer_id
         WHERE c.deleted_at IS NULL AND t.status = 'completed'{$sqlViec}",
        $thamSoViec
    );

    return [
        'khach_hang' => $khachHang,
        'nhan_vien' => $nhanVien,
        'tuong_tac_30_ngay' => $tuongTac30Ngay,
        'viec_dang_mo' => $viecDangMo,
        'viec_qua_han' => $viecQuaHan,
        'ty_le_hoan_thanh_viec' => ti_le_bao_cao($viecHoanThanh, $tongViec),
    ];
}

function lay_khach_hang_theo_loai_bao_cao(array $boLoc = []): array
{
    $loc = dieu_kien_loc_khach_hang_bao_cao($boLoc, 'c', 'type_customer');
    $ngay = dieu_kien_ngay_bao_cao($boLoc, 'c.created_at', 'type_customer');

    return lay_nhieu_dong(
        "SELECT ct.id, ct.name, ct.color, ct.priority_score,
            COUNT(c.id) AS total,
            SUM(CASE WHEN c.status = 'active' THEN 1 ELSE 0 END) AS active_count,
            SUM(CASE WHEN c.status = 'potential' THEN 1 ELSE 0 END) AS potential_count,
            SUM(CASE WHEN c.status = 'inactive' THEN 1 ELSE 0 END) AS inactive_count
         FROM customer_types ct
         LEFT JOIN customers c ON c.customer_type_id = ct.id AND c.deleted_at IS NULL{$loc['sql']}{$ngay['sql']}
         GROUP BY ct.id, ct.name, ct.color, ct.priority_score
         ORDER BY total DESC, ct.priority_score DESC, ct.name ASC",
        $loc['tham_so'] + $ngay['tham_so']
    );
}

function lay_khach_hang_theo_nhan_vien_bao_cao(array $boLoc = []): array
{
    $loc = dieu_kien_loc_khach_hang_bao_cao($boLoc, 'c', 'staff_customer');
    $ngay = dieu_kien_ngay_bao_cao($boLoc, 'c.created_at', 'staff_customer');

    return lay_nhieu_dong(
        "SELECT COALESCE(u.full_name, 'Chưa phân công') AS staff_name,
            COUNT(c.id) AS total,
            SUM(CASE WHEN c.status = 'active' THEN 1 ELSE 0 END) AS active_count,
            SUM(CASE WHEN c.status = 'potential' THEN 1 ELSE 0 END) AS potential_count,
            SUM(CASE WHEN c.status = 'inactive' THEN 1 ELSE 0 END) AS inactive_count
         FROM customers c
         LEFT JOIN users u ON u.id = c.assigned_user_id
         WHERE c.deleted_at IS NULL{$loc['sql']}{$ngay['sql']}
         GROUP BY c.assigned_user_id, u.full_name
         ORDER BY total DESC, staff_name ASC",
        $loc['tham_so'] + $ngay['tham_so']
    );
}

function lay_tuong_tac_theo_thoi_gian_bao_cao(array $boLoc = []): array
{
    $loc = dieu_kien_loc_khach_hang_bao_cao($boLoc, 'c', 'time_interaction');
    $ngay = dieu_kien_ngay_bao_cao($boLoc, 'i.interaction_at', 'time_interaction');
    $macDinhNgay = $ngay['sql'] === '' ? ' AND i.interaction_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)' : '';

    return lay_nhieu_dong(
        "SELECT DATE(i.interaction_at) AS report_date, COUNT(*) AS total
         FROM interactions i
         INNER JOIN customers c ON c.id = i.customer_id
         WHERE c.deleted_at IS NULL{$loc['sql']}{$ngay['sql']}{$macDinhNgay}
         GROUP BY DATE(i.interaction_at)
         ORDER BY report_date ASC",
        $loc['tham_so'] + $ngay['tham_so']
    );
}

function lay_tuong_tac_theo_loai_bao_cao(array $boLoc = []): array
{
    $loc = dieu_kien_loc_khach_hang_bao_cao($boLoc, 'c', 'type_interaction');
    $ngay = dieu_kien_ngay_bao_cao($boLoc, 'i.interaction_at', 'type_interaction');

    return lay_nhieu_dong(
        "SELECT i.interaction_type, COUNT(*) AS total
         FROM interactions i
         INNER JOIN customers c ON c.id = i.customer_id
         WHERE c.deleted_at IS NULL{$loc['sql']}{$ngay['sql']}
         GROUP BY i.interaction_type
         ORDER BY total DESC, i.interaction_type ASC",
        $loc['tham_so'] + $ngay['tham_so']
    );
}

function lay_tuong_tac_theo_nhan_vien_bao_cao(array $boLoc = []): array
{
    $loc = dieu_kien_loc_khach_hang_bao_cao($boLoc, 'c', 'staff_interaction');
    $ngay = dieu_kien_ngay_bao_cao($boLoc, 'i.interaction_at', 'staff_interaction');
    return lay_nhieu_dong(
        "SELECT u.full_name AS staff_name,
            COUNT(c.id) AS total,
            MAX(CASE WHEN c.id IS NOT NULL THEN i.interaction_at ELSE NULL END) AS last_interaction_at
         FROM users u
         LEFT JOIN interactions i ON i.user_id = u.id{$ngay['sql']}
         LEFT JOIN customers c ON c.id = i.customer_id AND c.deleted_at IS NULL{$loc['sql']}
          WHERE u.role = 'staff' AND u.status = 'active'
          GROUP BY u.id, u.full_name
          ORDER BY total DESC, u.full_name ASC",
        $loc['tham_so'] + $ngay['tham_so']
    );
}

function lay_cong_viec_theo_trang_thai_bao_cao(array $boLoc = []): array
{
    $loc = dieu_kien_loc_khach_hang_bao_cao($boLoc, 'c', 'status_task');
    $ngay = dieu_kien_ngay_bao_cao($boLoc, 't.due_at', 'status_task');

    return lay_nhieu_dong(
        "SELECT t.status, COUNT(*) AS total,
            SUM(CASE WHEN t.status IN ('pending', 'in_progress') AND t.due_at < NOW() THEN 1 ELSE 0 END) AS overdue_count
         FROM follow_up_tasks t
         INNER JOIN customers c ON c.id = t.customer_id
         WHERE c.deleted_at IS NULL{$loc['sql']}{$ngay['sql']}
         GROUP BY t.status
         ORDER BY FIELD(t.status, 'pending', 'in_progress', 'completed', 'cancelled')",
        $loc['tham_so'] + $ngay['tham_so']
    );
}
