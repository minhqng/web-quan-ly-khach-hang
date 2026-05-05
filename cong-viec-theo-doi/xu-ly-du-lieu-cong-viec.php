<?php

declare(strict_types=1);

function la_cong_viec_qua_han(array $congViec): bool
{
    return in_array($congViec['status'], trang_thai_cong_viec_dang_mo(), true)
        && strtotime($congViec['due_at']) < time();
}

function du_lieu_mac_dinh_cong_viec(?int $maKhachHang = null): array
{
    $nguoiDung = nguoi_dung_hien_tai();

    return [
        'customer_id' => (string) ($maKhachHang ?? ''),
        'assigned_user_id' => ($nguoiDung['vai_tro'] ?? '') === VAI_TRO_NHAN_VIEN ? (string) ($nguoiDung['id'] ?? '') : '',
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

function kiem_tra_du_lieu_cong_viec(array $duLieu, ?array $congViecHienTai = null): array
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

    if (!thoi_gian_html_hop_le($duLieu['due_at'])) {
        $loi['due_at'] = 'Hạn xử lý không hợp lệ.';
    }

    if (!array_key_exists($duLieu['status'], nhan_trang_thai_cong_viec())) {
        $loi['status'] = 'Trạng thái công việc không hợp lệ.';
    } elseif ($congViecHienTai !== null && !co_the_chuyen_trang_thai_cong_viec((string) $congViecHienTai['status'], $duLieu['status'])) {
        $loi['status'] = 'Không thể chuyển trạng thái công việc theo chiều này.';
    }

    if (!array_key_exists($duLieu['priority'], nhan_uu_tien_cong_viec())) {
        $loi['priority'] = 'Mức ưu tiên không hợp lệ.';
    }

    return $loi;
}

function co_the_chuyen_trang_thai_cong_viec(string $hienTai, string $moi): bool
{
    $hienTai = chuan_hoa_trang_thai_cong_viec($hienTai);
    $moi = chuan_hoa_trang_thai_cong_viec($moi);

    if ($hienTai === $moi) {
        return true;
    }

    $luong = [
        'pending' => ['in_progress', 'completed', 'cancelled'],
        'in_progress' => ['pending', 'completed', 'cancelled'],
        'completed' => [],
        'cancelled' => [],
    ];

    return in_array($moi, $luong[$hienTai] ?? [], true);
}

function lua_chon_trang_thai_cong_viec(string $hienTai): array
{
    $nhan = nhan_trang_thai_cong_viec();

    return array_filter(
        $nhan,
        static fn (string $trangThai): bool => co_the_chuyen_trang_thai_cong_viec($hienTai, $trangThai),
        ARRAY_FILTER_USE_KEY
    );
}

function datetime_mysql_cong_viec(string $giaTri): string
{
    return datetime_mysql_tu_html($giaTri);
}
