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
    $soDong = thuc_thi_lenh(
        "INSERT INTO follow_up_tasks
            (customer_id, assigned_user_id, created_by, title, description, due_at, status, priority, completed_at)
         SELECT c.id, :assigned_user_id, :created_by, :title, :description, :due_at, :status, :priority,
             CASE WHEN :completed_status = 'completed' THEN NOW() ELSE NULL END
         FROM customers c
         WHERE c.id = :customer_id AND c.deleted_at IS NULL",
        array_merge(tham_so_luu_cong_viec($duLieu), ['completed_status' => $duLieu['status']])
    );

    if ($soDong === 0) {
        throw new LoiNghiepVu('Khách hàng không còn hoạt động để tạo công việc theo dõi.');
    }

    return (int) lay_id_vua_tao();
}

function cap_nhat_cong_viec_theo_doi(int $id, array $duLieu): void
{
    $thamSo = tham_so_luu_cong_viec($duLieu);
    unset($thamSo['created_by']);
    $thamSo['completed_status'] = $duLieu['status'];
    $thamSo['id'] = $id;

    $soDong = thuc_thi_lenh(
        "UPDATE follow_up_tasks t
         INNER JOIN customers c ON c.id = :customer_id AND c.deleted_at IS NULL
         SET t.customer_id = c.id,
             t.assigned_user_id = :assigned_user_id,
             t.title = :title,
             t.description = :description,
             t.due_at = :due_at,
             t.status = :status,
             t.priority = :priority,
             t.completed_at = CASE WHEN :completed_status = 'completed' THEN COALESCE(t.completed_at, NOW()) ELSE NULL END
         WHERE t.id = :id",
        $thamSo
    );

    if ($soDong === 0 && !cong_viec_co_the_cap_nhat_voi_khach_hang($id, (int) $duLieu['customer_id'])) {
        throw new LoiNghiepVu('Khách hàng không còn hoạt động để cập nhật công việc theo dõi.');
    }
}

function cong_viec_co_the_cap_nhat_voi_khach_hang(int $id, int $maKhachHang): bool
{
    return (int) lay_mot_gia_tri(
        "SELECT COUNT(*)
         FROM follow_up_tasks t
         INNER JOIN customers c ON c.id = :customer_id AND c.deleted_at IS NULL
         WHERE t.id = :id",
        ['id' => $id, 'customer_id' => $maKhachHang]
    ) > 0;
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
