<?php

declare(strict_types=1);

function du_lieu_mac_dinh_tuong_tac(?int $maKhachHang = null): array
{
    return [
        'customer_id' => (string) ($maKhachHang ?? ''),
        'interaction_type' => 'call',
        'title' => '',
        'content' => '',
        'result' => '',
        'interaction_at' => date('Y-m-d\TH:i'),
        'create_follow_up' => 0,
        'task_title' => '',
        'task_due_at' => date('Y-m-d\TH:i', strtotime('+1 day')),
        'task_priority' => 'medium',
    ];
}

function lay_du_lieu_form_tuong_tac(array $nguon): array
{
    return [
        'customer_id' => (string) (int) ($nguon['customer_id'] ?? 0),
        'interaction_type' => (string) ($nguon['interaction_type'] ?? 'call'),
        'title' => chuoi_sach($nguon['title'] ?? ''),
        'content' => trim((string) ($nguon['content'] ?? '')),
        'result' => chuoi_sach($nguon['result'] ?? ''),
        'interaction_at' => chuoi_sach($nguon['interaction_at'] ?? ''),
        'create_follow_up' => isset($nguon['create_follow_up']) ? 1 : 0,
        'task_title' => chuoi_sach($nguon['task_title'] ?? ''),
        'task_due_at' => chuoi_sach($nguon['task_due_at'] ?? ''),
        'task_priority' => (string) ($nguon['task_priority'] ?? 'medium'),
    ];
}

function kiem_tra_du_lieu_tuong_tac(array $duLieu, bool $choTaoCongViec = true): array
{
    $loi = [];

    if (!khach_hang_tuong_tac_ton_tai((int) $duLieu['customer_id'])) {
        $loi['customer_id'] = 'Vui lòng chọn khách hàng đang hoạt động.';
    }

    if (!array_key_exists($duLieu['interaction_type'], nhan_loai_tuong_tac())) {
        $loi['interaction_type'] = 'Loại tương tác không hợp lệ.';
    }

    if ($duLieu['title'] === '') {
        $loi['title'] = 'Vui lòng nhập tiêu đề tương tác.';
    } elseif (mb_strlen($duLieu['title']) > 150) {
        $loi['title'] = 'Tiêu đề không được vượt quá 150 ký tự.';
    }

    if ($duLieu['content'] === '') {
        $loi['content'] = 'Vui lòng nhập nội dung trao đổi.';
    }

    if (mb_strlen($duLieu['result']) > 150) {
        $loi['result'] = 'Kết quả không được vượt quá 150 ký tự.';
    }

    $thoiGianTuongTac = $duLieu['interaction_at'] !== '' ? strtotime($duLieu['interaction_at']) : false;
    if ($thoiGianTuongTac === false) {
        $loi['interaction_at'] = 'Thời gian tương tác không hợp lệ.';
    } elseif ($thoiGianTuongTac > time() + 300) {
        $loi['interaction_at'] = 'Thời gian tương tác không được ở tương lai.';
    }

    return $choTaoCongViec ? array_merge($loi, kiem_tra_cong_viec_tu_tuong_tac($duLieu)) : $loi;
}

function kiem_tra_cong_viec_tu_tuong_tac(array $duLieu): array
{
    if ((int) $duLieu['create_follow_up'] !== 1) {
        return [];
    }

    $loi = [];
    if ($duLieu['task_title'] === '') {
        $loi['task_title'] = 'Vui lòng nhập tiêu đề công việc theo dõi.';
    } elseif (mb_strlen($duLieu['task_title']) > 150) {
        $loi['task_title'] = 'Tiêu đề công việc không được vượt quá 150 ký tự.';
    }

    if ($duLieu['task_due_at'] === '' || strtotime($duLieu['task_due_at']) === false) {
        $loi['task_due_at'] = 'Hạn xử lý công việc không hợp lệ.';
    }

    if (!in_array($duLieu['task_priority'], ['low', 'medium', 'high'], true)) {
        $loi['task_priority'] = 'Mức ưu tiên không hợp lệ.';
    }

    return $loi;
}

function gia_tri_datetime_mysql(string $giaTri): string
{
    return date('Y-m-d H:i:s', strtotime($giaTri) ?: time());
}
