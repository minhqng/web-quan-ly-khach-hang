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
        thuc_thi_lenh(
            'INSERT INTO interactions
                (customer_id, user_id, interaction_type, title, content, result, interaction_at)
             VALUES
                (:customer_id, :user_id, :interaction_type, :title, :content, :result, :interaction_at)',
            tham_so_luu_tuong_tac($duLieu)
        );
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
    thuc_thi_lenh(
        'UPDATE interactions
         SET customer_id = :customer_id,
             interaction_type = :interaction_type,
             title = :title,
             content = :content,
             result = :result,
             interaction_at = :interaction_at
         WHERE id = :id',
        [
            'customer_id' => (int) $duLieu['customer_id'],
            'interaction_type' => $duLieu['interaction_type'],
            'title' => $duLieu['title'],
            'content' => $duLieu['content'] !== '' ? $duLieu['content'] : null,
            'result' => $duLieu['result'] !== '' ? $duLieu['result'] : null,
            'interaction_at' => gia_tri_datetime_mysql($duLieu['interaction_at']),
            'id' => $id,
        ]
    );
}

function xoa_tuong_tac(int $id): bool
{
    return thuc_thi_lenh('DELETE FROM interactions WHERE id = :id', ['id' => $id]) > 0;
}

function tao_cong_viec_tu_tuong_tac(array $duLieu): void
{
    $nguoiDung = nguoi_dung_hien_tai();
    $maPhuTrach = (int) (lay_mot_gia_tri(
        'SELECT assigned_user_id FROM customers WHERE id = :id',
        ['id' => (int) $duLieu['customer_id']]
    ) ?: ($nguoiDung['id'] ?? 0));

    thuc_thi_lenh(
        'INSERT INTO follow_up_tasks
            (customer_id, assigned_user_id, created_by, title, description, due_at, status, priority)
         VALUES
            (:customer_id, :assigned_user_id, :created_by, :title, :description, :due_at, :status, :priority)',
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
}
