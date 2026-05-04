<?php

declare(strict_types=1);

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

function du_lieu_mac_dinh_khach_hang(): array
{
    $nguoiDung = nguoi_dung_hien_tai();
    $maPhuTrachMacDinh = ($nguoiDung['vai_tro'] ?? '') === VAI_TRO_NHAN_VIEN
        ? (string) ($nguoiDung['id'] ?? '')
        : (string) (lay_mot_gia_tri("SELECT id FROM users WHERE role = 'staff' AND status = 'active' ORDER BY full_name ASC LIMIT 1") ?? '');

    return [
        'customer_type_id' => '',
        'assigned_user_id' => $maPhuTrachMacDinh,
        'full_name' => '',
        'company_name' => '',
        'gender' => 'unknown',
        'date_of_birth' => '',
        'phone' => '',
        'email' => '',
        'address' => '',
        'city' => '',
        'source' => 'other',
        'status' => 'potential',
        'notes' => '',
    ];
}

function lay_du_lieu_form_khach_hang(array $nguon): array
{
    return [
        'customer_type_id' => (string) (int) ($nguon['customer_type_id'] ?? 0),
        'assigned_user_id' => (string) (int) ($nguon['assigned_user_id'] ?? 0),
        'full_name' => chuoi_sach($nguon['full_name'] ?? ''),
        'company_name' => chuoi_sach($nguon['company_name'] ?? ''),
        'gender' => (string) ($nguon['gender'] ?? 'unknown'),
        'date_of_birth' => chuoi_sach($nguon['date_of_birth'] ?? ''),
        'phone' => chuoi_sach($nguon['phone'] ?? ''),
        'email' => chuan_hoa_email_khach_hang($nguon['email'] ?? ''),
        'address' => chuoi_sach($nguon['address'] ?? ''),
        'city' => chuoi_sach($nguon['city'] ?? ''),
        'source' => (string) ($nguon['source'] ?? 'other'),
        'status' => (string) ($nguon['status'] ?? 'potential'),
        'notes' => trim((string) ($nguon['notes'] ?? '')),
    ];
}

function chuan_hoa_dien_thoai_khach_hang(?string $dienThoai): string
{
    return preg_replace('/\D+/', '', (string) $dienThoai) ?? '';
}

function chuan_hoa_email_khach_hang(?string $email): string
{
    return mb_strtolower(trim((string) $email), 'UTF-8');
}

function mau_loai_khach_hang_khach_an_toan(?string $mau): string
{
    $mau = (string) $mau;

    return preg_match('/^#[0-9a-fA-F]{6}$/', $mau) === 1 ? $mau : '#64748b';
}

function kiem_tra_du_lieu_khach_hang(array $duLieu, ?int $boQuaId = null): array
{
    $loi = [];
    $dienThoaiChuan = chuan_hoa_dien_thoai_khach_hang($duLieu['phone']);

    if ($duLieu['full_name'] === '') {
        $loi['full_name'] = 'Vui lòng nhập họ tên khách hàng.';
    } elseif (mb_strlen($duLieu['full_name']) > 150) {
        $loi['full_name'] = 'Họ tên không được vượt quá 150 ký tự.';
    }

    if ((int) $duLieu['customer_type_id'] <= 0 || !loai_khach_hang_ton_tai((int) $duLieu['customer_type_id'])) {
        $loi['customer_type_id'] = 'Vui lòng chọn loại khách hàng hợp lệ.';
    }

    if ((int) $duLieu['assigned_user_id'] <= 0 || !nhan_vien_ton_tai((int) $duLieu['assigned_user_id'])) {
        $loi['assigned_user_id'] = 'Vui lòng chọn nhân viên phụ trách hợp lệ.';
    }

    if ($duLieu['phone'] === '' && $duLieu['email'] === '') {
        $loi['phone'] = 'Vui lòng nhập ít nhất số điện thoại hoặc email.';
    }

    if ($duLieu['phone'] !== '' && (mb_strlen($dienThoaiChuan) < 9 || mb_strlen($dienThoaiChuan) > 15)) {
        $loi['phone'] = 'Số điện thoại cần có từ 9 đến 15 chữ số.';
    } elseif ($dienThoaiChuan !== '' && khach_hang_bi_trung('phone', $dienThoaiChuan, $boQuaId)) {
        $loi['phone'] = 'Số điện thoại đang được dùng bởi khách hàng khác.';
    }

    if ($duLieu['email'] !== '' && !filter_var($duLieu['email'], FILTER_VALIDATE_EMAIL)) {
        $loi['email'] = 'Email không đúng định dạng.';
    } elseif ($duLieu['email'] !== '' && khach_hang_bi_trung('email', $duLieu['email'], $boQuaId)) {
        $loi['email'] = 'Email đang được dùng bởi khách hàng khác.';
    }

    return array_merge($loi, kiem_tra_chi_tiet_khach_hang($duLieu));
}

function kiem_tra_chi_tiet_khach_hang(array $duLieu): array
{
    $loi = [];

    foreach (['company_name' => 150, 'address' => 255, 'city' => 80] as $truong => $gioiHan) {
        if (mb_strlen($duLieu[$truong]) > $gioiHan) {
            $loi[$truong] = 'Thông tin này không được vượt quá ' . $gioiHan . ' ký tự.';
        }
    }

    if (!array_key_exists($duLieu['gender'], nhan_gioi_tinh_khach_hang())) {
        $loi['gender'] = 'Giới tính không hợp lệ.';
    }

    if ($duLieu['date_of_birth'] !== '') {
        $ngaySinh = strtotime($duLieu['date_of_birth']);
        if ($ngaySinh === false) {
            $loi['date_of_birth'] = 'Ngày sinh không hợp lệ.';
        } elseif ($ngaySinh > time()) {
            $loi['date_of_birth'] = 'Ngày sinh không được lớn hơn ngày hiện tại.';
        }
    }

    if (!array_key_exists($duLieu['source'], nhan_nguon_khach_hang())) {
        $loi['source'] = 'Nguồn khách hàng không hợp lệ.';
    }

    if (!array_key_exists($duLieu['status'], nhan_trang_thai_khach_hang())) {
        $loi['status'] = 'Trạng thái khách hàng không hợp lệ.';
    }

    return $loi;
}
