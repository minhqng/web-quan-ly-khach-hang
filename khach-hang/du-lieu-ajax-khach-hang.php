<?php

declare(strict_types=1);

function tao_du_lieu_ajax_khach_hang(array $khachHang): array
{
    $daXoa = !empty($khachHang['deleted_at']);

    return [
        'id' => (int) $khachHang['id'],
        'full_name' => $khachHang['full_name'],
        'subtitle' => $khachHang['company_name'] ?: $khachHang['city'] ?: 'Khách hàng cá nhân',
        'phone' => $khachHang['phone'] ?: 'Chưa có SĐT',
        'email' => $khachHang['email'] ?: 'Chưa có email',
        'customer_type_name' => $khachHang['customer_type_name'],
        'customer_type_color' => mau_loai_khach_hang_khach_an_toan($khachHang['customer_type_color']),
        'assigned_user_name' => $khachHang['assigned_user_name'] ?: 'Chưa phân công',
        'status_label' => nhan_hien_thi_trang_thai_khach_hang($khachHang['status'], $khachHang['deleted_at']),
        'status_badge_class' => lop_badge_trang_thai_khach_hang($khachHang['status'], $khachHang['deleted_at']),
        'interaction_count' => (int) $khachHang['interaction_count'],
        'next_task_label' => dinh_dang_ngay_gio($khachHang['next_task_due_at']) ?: 'Chưa có lịch',
        'is_deleted' => $daXoa,
        'can_manage_delete' => la_admin(),
        'detail_url' => duong_dan('khach-hang/chi-tiet.php?id=' . $khachHang['id']),
        'edit_url' => duong_dan('khach-hang/sua.php?id=' . $khachHang['id']),
        'soft_delete_url' => duong_dan('khach-hang/xoa-mem.php'),
        'restore_url' => duong_dan('khach-hang/khoi-phuc.php'),
    ];
}

function tao_phan_hoi_ajax_danh_sach_khach_hang(array $boLoc, int $trang = 1): array
{
    $tongDong = dem_khach_hang_theo_bo_loc($boLoc);
    $phanTrang = tinh_phan_trang($tongDong, $trang, 8);
    $danhSach = lay_danh_sach_khach_hang($boLoc, $phanTrang);

    return [
        'thanh_cong' => true,
        'tong_dong' => $tongDong,
        'khach_hang' => array_map('tao_du_lieu_ajax_khach_hang', $danhSach),
        'phan_trang' => [
            'trang' => $phanTrang['trang'],
            'tong_trang' => $phanTrang['tong_trang'],
            'co_trang_truoc' => $phanTrang['co_trang_truoc'],
            'co_trang_sau' => $phanTrang['co_trang_sau'],
        ],
    ];
}
