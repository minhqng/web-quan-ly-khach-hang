<?php

declare(strict_types=1);

function lay_lua_chon_loai_khach_hang(?int $idHienTai = null): array
{
    $sql = 'SELECT id, name, color FROM customer_types WHERE is_active = 1';
    $thamSo = [];

    if ($idHienTai !== null) {
        $sql .= ' OR id = :id';
        $thamSo['id'] = $idHienTai;
    }

    return lay_nhieu_dong($sql . ' ORDER BY priority_score DESC, name ASC', $thamSo);
}

function lay_lua_chon_nhan_vien(): array
{
    $nguoiDung = nguoi_dung_hien_tai();

    if (($nguoiDung['vai_tro'] ?? '') !== VAI_TRO_ADMIN) {
        return lay_nhieu_dong(
            "SELECT id, full_name, role
             FROM users
             WHERE id = :id AND status = 'active' AND role = 'staff'",
            ['id' => (int) ($nguoiDung['id'] ?? 0)]
        );
    }

    return lay_nhieu_dong(
        "SELECT id, full_name, role
         FROM users
         WHERE status = 'active'
           AND role = 'staff'
         ORDER BY full_name ASC"
    );
}

function loai_khach_hang_ton_tai(int $id): bool
{
    return (int) lay_mot_gia_tri(
        'SELECT COUNT(*) FROM customer_types WHERE id = :id',
        ['id' => $id]
    ) > 0;
}

function nhan_vien_ton_tai(int $id): bool
{
    $nguoiDung = nguoi_dung_hien_tai();

    if (($nguoiDung['vai_tro'] ?? '') !== VAI_TRO_ADMIN) {
        return $id === (int) ($nguoiDung['id'] ?? 0)
            && (int) lay_mot_gia_tri(
                "SELECT COUNT(*) FROM users WHERE id = :id AND role = 'staff' AND status = 'active'",
                ['id' => $id]
            ) > 0;
    }

    return (int) lay_mot_gia_tri(
        "SELECT COUNT(*) FROM users WHERE id = :id AND role = 'staff' AND status = 'active'",
        ['id' => $id]
    ) > 0;
}

function khach_hang_bi_trung(string $truong, string $giaTriChuan, ?int $boQuaId = null, bool $chiTrongPhamViHienTai = false): bool
{
    if ($giaTriChuan === '' || !in_array($truong, ['phone', 'email'], true)) {
        return false;
    }

    $cot = $truong === 'phone' ? 'phone_normalized' : 'email_normalized';
    $sql = "SELECT COUNT(*) FROM customers WHERE deleted_at IS NULL AND {$cot} = :gia_tri";
    $thamSo = ['gia_tri' => $giaTriChuan];

    if ($chiTrongPhamViHienTai && !la_admin()) {
        $sql .= ' AND assigned_user_id = :duplicate_scope_user_id';
        $thamSo['duplicate_scope_user_id'] = (int) (nguoi_dung_hien_tai()['id'] ?? 0);
    }

    if ($boQuaId !== null) {
        $sql .= ' AND id <> :id';
        $thamSo['id'] = $boQuaId;
    }

    return (int) lay_mot_gia_tri($sql, $thamSo) > 0;
}

function tham_so_luu_khach_hang(array $duLieu): array
{
    return [
        'customer_type_id' => (int) $duLieu['customer_type_id'],
        'assigned_user_id' => (int) $duLieu['assigned_user_id'],
        'full_name' => $duLieu['full_name'],
        'company_name' => $duLieu['company_name'] !== '' ? $duLieu['company_name'] : null,
        'gender' => $duLieu['gender'],
        'date_of_birth' => $duLieu['date_of_birth'] !== '' ? $duLieu['date_of_birth'] : null,
        'phone' => $duLieu['phone'] !== '' ? $duLieu['phone'] : null,
        'phone_normalized' => chuan_hoa_dien_thoai_khach_hang($duLieu['phone']) ?: null,
        'email' => $duLieu['email'] !== '' ? $duLieu['email'] : null,
        'email_normalized' => $duLieu['email'] !== '' ? $duLieu['email'] : null,
        'address' => $duLieu['address'] !== '' ? $duLieu['address'] : null,
        'city' => $duLieu['city'] !== '' ? $duLieu['city'] : null,
        'source' => $duLieu['source'],
        'status' => $duLieu['status'],
        'notes' => $duLieu['notes'] !== '' ? $duLieu['notes'] : null,
    ];
}

function tao_khach_hang(array $duLieu): int
{
    thuc_thi_lenh(
        'INSERT INTO customers
            (customer_type_id, assigned_user_id, full_name, company_name, gender, date_of_birth,
             phone, phone_normalized, email, email_normalized, address, city, source, status, notes)
         VALUES
            (:customer_type_id, :assigned_user_id, :full_name, :company_name, :gender, :date_of_birth,
             :phone, :phone_normalized, :email, :email_normalized, :address, :city, :source, :status, :notes)',
        tham_so_luu_khach_hang($duLieu)
    );

    return (int) lay_id_vua_tao();
}

function cap_nhat_khach_hang(int $id, array $duLieu): void
{
    $thamSo = tham_so_luu_khach_hang($duLieu);
    $thamSo['id'] = $id;
    [$phamViSql, $thamSoPhamVi] = dieu_kien_pham_vi_khach_hang('', 'update_scope_user_id');

    if ($phamViSql !== '') {
        $phamViSql = ' AND assigned_user_id = :update_scope_user_id';
    }

    thuc_thi_lenh(
        'UPDATE customers
         SET customer_type_id = :customer_type_id,
             assigned_user_id = :assigned_user_id,
             full_name = :full_name,
             company_name = :company_name,
             gender = :gender,
             date_of_birth = :date_of_birth,
             phone = :phone,
             phone_normalized = :phone_normalized,
             email = :email,
             email_normalized = :email_normalized,
             address = :address,
             city = :city,
             source = :source,
             status = :status,
             notes = :notes
         WHERE id = :id' . $phamViSql,
        $thamSo + $thamSoPhamVi
    );
}

function xoa_mem_khach_hang(int $id): string
{
    if ((int) lay_mot_gia_tri(
        "SELECT COUNT(*) FROM follow_up_tasks
         WHERE customer_id = :id AND status IN ('pending', 'in_progress')",
        ['id' => $id]
    ) > 0) {
        return 'co_cong_viec_mo';
    }

    return thuc_thi_lenh(
        'UPDATE customers SET deleted_at = NOW() WHERE id = :id AND deleted_at IS NULL',
        ['id' => $id]
    ) > 0 ? 'da_xoa' : 'khong_hop_le';
}

function khoi_phuc_khach_hang(int $id): string
{
    $khachHang = lay_chi_tiet_khach_hang($id);

    if (!$khachHang || !$khachHang['deleted_at']) {
        return 'khong_hop_le';
    }

    if (($khachHang['phone_normalized'] && khach_hang_bi_trung('phone', $khachHang['phone_normalized'], $id))
        || ($khachHang['email_normalized'] && khach_hang_bi_trung('email', $khachHang['email_normalized'], $id))) {
        return 'bi_trung';
    }

    thuc_thi_lenh('UPDATE customers SET deleted_at = NULL WHERE id = :id', ['id' => $id]);

    return 'da_khoi_phuc';
}
