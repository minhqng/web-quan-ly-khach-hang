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

function nhan_trang_thai_chinh_cong_viec(): array
{
    return ['pending' => 'Chờ xử lý', 'completed' => 'Hoàn thành', 'cancelled' => 'Đã hủy'];
}

function chuan_hoa_trang_thai_cong_viec(string $trangThai): string
{
    return $trangThai === 'done' ? 'completed' : $trangThai;
}

function trang_thai_cong_viec_dang_mo(): array
{
    return ['pending', 'in_progress'];
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

function la_cong_viec_qua_han(array $congViec): bool
{
    return in_array($congViec['status'], trang_thai_cong_viec_dang_mo(), true)
        && strtotime($congViec['due_at']) < time();
}

function du_lieu_mac_dinh_cong_viec(?int $maKhachHang = null): array
{
    return [
        'customer_id' => (string) ($maKhachHang ?? ''),
        'assigned_user_id' => '',
        'title' => '',
        'description' => '',
        'due_at' => date('Y-m-d\TH:i', strtotime('+1 day')),
        'status' => 'pending',
        'priority' => 'medium',
    ];
}

function lay_du_lieu_form_cong_viec(array $nguon): array
{
    return [
        'customer_id' => (string) (int) ($nguon['customer_id'] ?? 0),
        'assigned_user_id' => (string) (int) ($nguon['assigned_user_id'] ?? 0),
        'title' => chuoi_sach($nguon['title'] ?? ''),
        'description' => trim((string) ($nguon['description'] ?? '')),
        'due_at' => chuoi_sach($nguon['due_at'] ?? ''),
        'status' => chuan_hoa_trang_thai_cong_viec((string) ($nguon['status'] ?? 'pending')),
        'priority' => (string) ($nguon['priority'] ?? 'medium'),
    ];
}

function kiem_tra_du_lieu_cong_viec(array $duLieu): array
{
    $loi = [];

    if (!khach_hang_cong_viec_ton_tai((int) $duLieu['customer_id'])) {
        $loi['customer_id'] = 'Vui lòng chọn khách hàng đang hoạt động.';
    }

    if (!nhan_vien_cong_viec_ton_tai((int) $duLieu['assigned_user_id'])) {
        $loi['assigned_user_id'] = 'Vui lòng chọn nhân viên phụ trách hợp lệ.';
    }

    if ($duLieu['title'] === '') {
        $loi['title'] = 'Vui lòng nhập tiêu đề công việc.';
    } elseif (mb_strlen($duLieu['title']) > 150) {
        $loi['title'] = 'Tiêu đề không được vượt quá 150 ký tự.';
    }

    if ($duLieu['due_at'] === '' || strtotime($duLieu['due_at']) === false) {
        $loi['due_at'] = 'Hạn xử lý không hợp lệ.';
    }

    if (!array_key_exists($duLieu['status'], nhan_trang_thai_cong_viec())) {
        $loi['status'] = 'Trạng thái công việc không hợp lệ.';
    }

    if (!array_key_exists($duLieu['priority'], nhan_uu_tien_cong_viec())) {
        $loi['priority'] = 'Mức ưu tiên không hợp lệ.';
    }

    return $loi;
}

function datetime_mysql_cong_viec(string $giaTri): string
{
    return date('Y-m-d H:i:s', strtotime($giaTri) ?: time());
}
