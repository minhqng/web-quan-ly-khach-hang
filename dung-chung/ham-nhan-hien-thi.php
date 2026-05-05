<?php

declare(strict_types=1);

function nhan_vai_tro_nguoi_dung(): array
{
    return [VAI_TRO_ADMIN => 'Quản trị', VAI_TRO_NHAN_VIEN => 'Nhân viên'];
}

function nhan_trang_thai_nguoi_dung(): array
{
    return [TRANG_THAI_HOAT_DONG => 'Đang hoạt động', TRANG_THAI_TAM_KHOA => 'Đã khóa'];
}

function lop_badge_trang_thai_nguoi_dung(string $trangThai): string
{
    return match ($trangThai) {
        TRANG_THAI_HOAT_DONG => 'badge-soft-success',
        TRANG_THAI_TAM_KHOA => 'badge-soft-danger',
        default => 'badge-soft-primary',
    };
}

function nhan_trang_thai_khach_hang(): array
{
    return ['active' => 'Đang chăm sóc', 'potential' => 'Tiềm năng', 'inactive' => 'Tạm ngưng'];
}

function nhan_gioi_tinh_khach_hang(): array
{
    return ['unknown' => 'Chưa xác định', 'male' => 'Nam', 'female' => 'Nữ', 'other' => 'Khác'];
}

function nhan_nguon_khach_hang(): array
{
    return [
        'website' => 'Website',
        'facebook' => 'Facebook',
        'referral' => 'Giới thiệu',
        'walk_in' => 'Đến trực tiếp',
        'phone' => 'Điện thoại',
        'other' => 'Khác',
    ];
}

function lop_badge_trang_thai_khach_hang(string $trangThai, ?string $deletedAt = null): string
{
    if ($deletedAt) {
        return 'badge-soft-danger';
    }

    return match ($trangThai) {
        'active' => 'badge-soft-success',
        'potential' => 'badge-soft-primary',
        'inactive' => 'badge-soft-warning',
        default => 'badge-soft-primary',
    };
}

function nhan_hien_thi_trang_thai_khach_hang(string $trangThai, ?string $deletedAt = null): string
{
    if ($deletedAt) {
        return 'Đã xóa mềm';
    }

    return nhan_trang_thai_khach_hang()[$trangThai] ?? 'Không rõ';
}

function nhan_loai_tuong_tac(): array
{
    return [
        'call' => 'Gọi điện',
        'email' => 'Email',
        'meeting' => 'Gặp mặt',
        'chat' => 'Chat/Zalo',
        'note' => 'Ghi chú',
        'other' => 'Khác',
    ];
}

function lop_badge_loai_tuong_tac(string $loai): string
{
    return match ($loai) {
        'call' => 'badge-soft-success',
        'email' => 'badge-soft-primary',
        'meeting' => 'badge-soft-warning',
        'chat', 'zalo' => 'badge-soft-primary',
        'note' => 'badge-soft-success',
        default => 'badge-soft-primary',
    };
}

function nhan_loai_tuong_tac_hien_thi(string $loai): string
{
    if ($loai === 'zalo') {
        return 'Zalo';
    }

    return nhan_loai_tuong_tac()[$loai] ?? 'Khác';
}

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

function nhan_trang_thai_khach_bao_cao(): array
{
    return nhan_trang_thai_khach_hang();
}

function nhan_trang_thai_viec_bao_cao(): array
{
    return nhan_trang_thai_cong_viec();
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
