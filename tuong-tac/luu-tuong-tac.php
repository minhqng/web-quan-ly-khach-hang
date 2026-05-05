<?php

declare(strict_types=1);

function tham_so_luu_tuong_tac(array $duLieu): array
{
    $nguoiDung = nguoi_dung_hien_tai();

    return [
        'customer_id' => (int) $duLieu['customer_id'],
        'user_id' => (int) ($nguoiDung['id'] ?? 0),
        'interaction_type' => $duLieu['interaction_type'],
        'title' => $duLieu['title'],
        'content' => $duLieu['content'] !== '' ? $duLieu['content'] : null,
        'result' => $duLieu['result'] !== '' ? $duLieu['result'] : null,
        'interaction_at' => gia_tri_datetime_mysql($duLieu['interaction_at']),
    ];
}

function tao_tuong_tac(array $duLieu): int
{
    $pdo = lay_ket_noi_csdl();
    $pdo->beginTransaction();

    try {
        $soDong = thuc_thi_lenh(
            'INSERT INTO interactions
                (customer_id, user_id, interaction_type, title, content, result, interaction_at)
             SELECT c.id, :user_id, :interaction_type, :title, :content, :result, :interaction_at
             FROM customers c
             WHERE c.id = :customer_id AND c.deleted_at IS NULL',
            tham_so_luu_tuong_tac($duLieu)
        );
        if ($soDong === 0) {
            throw new LoiNghiepVu('Khách hàng không còn hoạt động để ghi nhận tương tác.');
        }

        $id = (int) lay_id_vua_tao();

        if ((int) $duLieu['create_follow_up'] === 1) {
            tao_cong_viec_tu_tuong_tac($duLieu);
        }

        $pdo->commit();
        return $id;
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $exception;
    }
}

function cap_nhat_tuong_tac(int $id, array $duLieu): void
{
    $tuongTac = lay_tuong_tac_theo_id($id);
    if (!$tuongTac || !co_the_sua_xoa_tuong_tac($tuongTac)) {
        throw new RuntimeException('Không có quyền cập nhật tương tác này.');
    }

    $thamSo = [
        'customer_id' => (int) $duLieu['customer_id'],
        'interaction_type' => $duLieu['interaction_type'],
        'title' => $duLieu['title'],
        'content' => $duLieu['content'] !== '' ? $duLieu['content'] : null,
        'result' => $duLieu['result'] !== '' ? $duLieu['result'] : null,
        'interaction_at' => gia_tri_datetime_mysql($duLieu['interaction_at']),
        'id' => $id,
    ];
    $soDong = thuc_thi_lenh(
        'UPDATE interactions i
         INNER JOIN customers c ON c.id = :customer_id AND c.deleted_at IS NULL
         SET i.customer_id = c.id,
             i.interaction_type = :interaction_type,
             i.title = :title,
             i.content = :content,
             i.result = :result,
             i.interaction_at = :interaction_at
         WHERE i.id = :id',
        $thamSo
    );

    if ($soDong === 0 && !tuong_tac_co_the_cap_nhat_voi_khach_hang($id, (int) $duLieu['customer_id'])) {
        throw new LoiNghiepVu('Khách hàng không còn hoạt động để cập nhật tương tác.');
    }
}

function tuong_tac_co_the_cap_nhat_voi_khach_hang(int $id, int $maKhachHang): bool
{
    return (int) lay_mot_gia_tri(
        "SELECT COUNT(*)
         FROM interactions i
         INNER JOIN customers c ON c.id = :customer_id AND c.deleted_at IS NULL
         WHERE i.id = :id",
        ['id' => $id, 'customer_id' => $maKhachHang]
    ) > 0;
}

function xoa_tuong_tac(int $id): bool
{
    return thuc_thi_lenh('DELETE FROM interactions WHERE id = :id', ['id' => $id]) > 0;
}

function tao_cong_viec_tu_tuong_tac(array $duLieu): void
{
    $nguoiDung = nguoi_dung_hien_tai();
    $maPhuTrach = (int) (lay_mot_gia_tri(
        "SELECT u.id
         FROM customers c
         INNER JOIN users u ON u.id = c.assigned_user_id
         WHERE c.id = :id AND u.role = 'staff' AND u.status = 'active'",
        ['id' => (int) $duLieu['customer_id']]
    ) ?: 0);

    if ($maPhuTrach === 0 && ($nguoiDung['vai_tro'] ?? '') === VAI_TRO_NHAN_VIEN) {
        $maPhuTrach = (int) ($nguoiDung['id'] ?? 0);
    }

    if ($maPhuTrach === 0) {
        $maPhuTrach = (int) lay_mot_gia_tri(
            "SELECT id FROM users WHERE role = 'staff' AND status = 'active' ORDER BY id ASC LIMIT 1"
        );
    }

    if ($maPhuTrach === 0) {
        throw new LoiNghiepVu('Không có nhân viên hoạt động để giao công việc theo dõi.');
    }

    $soDong = thuc_thi_lenh(
        'INSERT INTO follow_up_tasks
            (customer_id, assigned_user_id, created_by, title, description, due_at, status, priority)
         SELECT c.id, :assigned_user_id, :created_by, :title, :description, :due_at, :status, :priority
         FROM customers c
         WHERE c.id = :customer_id AND c.deleted_at IS NULL',
        [
            'customer_id' => (int) $duLieu['customer_id'],
            'assigned_user_id' => $maPhuTrach,
            'created_by' => (int) ($nguoiDung['id'] ?? 0),
            'title' => $duLieu['task_title'],
            'description' => 'Tạo từ tương tác: ' . $duLieu['title'],
            'due_at' => gia_tri_datetime_mysql($duLieu['task_due_at']),
            'status' => 'pending',
            'priority' => $duLieu['task_priority'],
        ]
    );

    if ($soDong === 0) {
        throw new LoiNghiepVu('Khách hàng không còn hoạt động để tạo công việc theo dõi.');
    }
}
