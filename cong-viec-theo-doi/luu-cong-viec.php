<?php

declare(strict_types=1);

function tham_so_luu_cong_viec(array $duLieu): array
{
    $nguoiDung = nguoi_dung_hien_tai();

    return [
        'customer_id' => (int) $duLieu['customer_id'],
        'assigned_user_id' => (int) $duLieu['assigned_user_id'],
        'created_by' => (int) ($nguoiDung['id'] ?? 0),
        'title' => $duLieu['title'],
        'description' => $duLieu['description'] !== '' ? $duLieu['description'] : null,
        'due_at' => datetime_mysql_cong_viec($duLieu['due_at']),
        'status' => $duLieu['status'],
        'priority' => $duLieu['priority'],
    ];
}

function tao_cong_viec_theo_doi(array $duLieu): int
{
    thuc_thi_lenh(
        "INSERT INTO follow_up_tasks
            (customer_id, assigned_user_id, created_by, title, description, due_at, status, priority, completed_at)
         VALUES
            (:customer_id, :assigned_user_id, :created_by, :title, :description, :due_at, :status, :priority,
             CASE WHEN :completed_status = 'completed' THEN NOW() ELSE NULL END)",
        array_merge(tham_so_luu_cong_viec($duLieu), ['completed_status' => $duLieu['status']])
    );

    return (int) lay_id_vua_tao();
}

function cap_nhat_cong_viec_theo_doi(int $id, array $duLieu): void
{
    $thamSo = tham_so_luu_cong_viec($duLieu);
    unset($thamSo['created_by']);
    $thamSo['completed_status'] = $duLieu['status'];
    $thamSo['id'] = $id;

    thuc_thi_lenh(
        "UPDATE follow_up_tasks
         SET customer_id = :customer_id,
             assigned_user_id = :assigned_user_id,
             title = :title,
             description = :description,
             due_at = :due_at,
             status = :status,
             priority = :priority,
             completed_at = CASE WHEN :completed_status = 'completed' THEN COALESCE(completed_at, NOW()) ELSE NULL END
         WHERE id = :id",
        $thamSo
    );
}

function cap_nhat_trang_thai_cong_viec(int $id, string $trangThai): ?array
{
    $trangThai = chuan_hoa_trang_thai_cong_viec($trangThai);

    if (!array_key_exists($trangThai, nhan_trang_thai_cong_viec())) {
        return null;
    }

    $congViec = lay_cong_viec_theo_id($id);
    if (!$congViec || !co_quyen_cap_nhat_cong_viec($congViec)) {
        return null;
    }

    if (!co_the_chuyen_trang_thai_cong_viec((string) $congViec['status'], $trangThai)) {
        return null;
    }

    thuc_thi_lenh(
        "UPDATE follow_up_tasks
         SET status = :status,
             completed_at = CASE WHEN :completed_status = 'completed' THEN NOW() ELSE NULL END
         WHERE id = :id",
        [
            'status' => $trangThai,
            'completed_status' => $trangThai,
            'id' => $id,
        ]
    );

    $congViecSauCapNhat = $congViec;
    $congViecSauCapNhat['status'] = $trangThai;
    $dangMo = in_array($trangThai, trang_thai_cong_viec_dang_mo(), true);
    $quaHan = la_cong_viec_qua_han($congViecSauCapNhat);

    return [
        'status' => $trangThai,
        'status_label' => nhan_trang_thai_cong_viec()[$trangThai],
        'badge_class' => lop_badge_trang_thai_cong_viec($trangThai),
        'is_open' => $dangMo,
        'is_overdue' => $quaHan,
        'due_label' => $quaHan ? 'Quá hạn' : ($dangMo ? 'Đang theo dõi' : 'Đã đóng'),
    ];
}
