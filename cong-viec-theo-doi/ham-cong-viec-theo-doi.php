<?php

declare(strict_types=1);

function nhan_trang_thai_cong_viec(): array
{
    return [
        'pending' => 'Chờ xử lý',
        'in_progress' => 'Đang xử lý',
        'completed' => 'Hoàn thành',
        'cancelled' => 'Đã hủy',
    ];
}

function lop_badge_trang_thai_cong_viec(string $trangThai): string
{
    return match ($trangThai) {
        'pending' => 'badge-soft-warning',
        'in_progress' => 'badge-soft-primary',
        'completed' => 'badge-soft-success',
        'cancelled' => 'badge-soft-danger',
        default => 'badge-soft-primary',
    };
}

function nhan_uu_tien_cong_viec(): array
{
    return ['high' => 'Cao', 'medium' => 'Vừa', 'low' => 'Thấp'];
}

function lop_badge_uu_tien_cong_viec(string $uuTien): string
{
    return match ($uuTien) {
        'high' => 'badge-soft-danger',
        'medium' => 'badge-soft-warning',
        'low' => 'badge-soft-success',
        default => 'badge-soft-primary',
    };
}

function lay_danh_sach_cong_viec_theo_doi(): array
{
    $nguoiDung = nguoi_dung_hien_tai();
    $laAdmin = ($nguoiDung['vai_tro'] ?? '') === VAI_TRO_ADMIN;
    $phamViSql = $laAdmin ? '' : ' AND t.assigned_user_id = :assigned_user_id';
    $thamSo = $laAdmin ? [] : ['assigned_user_id' => (int) ($nguoiDung['id'] ?? 0)];

    return lay_nhieu_dong(
        "SELECT t.id, t.title, t.description, t.due_at, t.status, t.priority, t.completed_at,
            c.full_name AS customer_name, c.company_name,
            u.full_name AS assigned_user_name
         FROM follow_up_tasks t
         INNER JOIN customers c ON c.id = t.customer_id
         INNER JOIN users u ON u.id = t.assigned_user_id
         WHERE c.deleted_at IS NULL{$phamViSql}
         ORDER BY FIELD(t.status, 'pending', 'in_progress', 'completed', 'cancelled'), t.due_at ASC
         LIMIT 40",
        $thamSo
    );
}

function lay_cong_viec_theo_id(int $id): ?array
{
    return lay_mot_dong(
        'SELECT id, assigned_user_id, status FROM follow_up_tasks WHERE id = :id LIMIT 1',
        ['id' => $id]
    );
}

function co_quyen_cap_nhat_cong_viec(array $congViec): bool
{
    $nguoiDung = nguoi_dung_hien_tai();

    return ($nguoiDung['vai_tro'] ?? '') === VAI_TRO_ADMIN
        || (int) ($nguoiDung['id'] ?? 0) === (int) $congViec['assigned_user_id'];
}

function cap_nhat_trang_thai_cong_viec(int $id, string $trangThai): ?array
{
    if (!array_key_exists($trangThai, nhan_trang_thai_cong_viec())) {
        return null;
    }

    $congViec = lay_cong_viec_theo_id($id);
    if (!$congViec || !co_quyen_cap_nhat_cong_viec($congViec)) {
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

    return [
        'status' => $trangThai,
        'status_label' => nhan_trang_thai_cong_viec()[$trangThai],
        'badge_class' => lop_badge_trang_thai_cong_viec($trangThai),
    ];
}
