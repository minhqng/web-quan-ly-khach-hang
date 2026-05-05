<?php

declare(strict_types=1);

function lay_nguoi_dung_theo_id(int $id): ?array
{
    return lay_mot_dong(
        'SELECT id, full_name, username, email, phone, role, status, last_login_at, created_at, updated_at
         FROM users
         WHERE id = :id
         LIMIT 1',
        ['id' => $id]
    );
}

function du_lieu_mac_dinh_nguoi_dung(): array
{
    return [
        'full_name' => '',
        'username' => '',
        'email' => '',
        'phone' => '',
        'role' => VAI_TRO_NHAN_VIEN,
        'status' => TRANG_THAI_HOAT_DONG,
        'password' => '',
        'password_confirm' => '',
    ];
}

function lay_du_lieu_form_nguoi_dung(array $nguon): array
{
    return [
        'full_name' => chuoi_sach($nguon['full_name'] ?? ''),
        'username' => mb_strtolower(chuoi_sach($nguon['username'] ?? ''), 'UTF-8'),
        'email' => mb_strtolower(chuoi_sach($nguon['email'] ?? ''), 'UTF-8'),
        'phone' => chuoi_sach($nguon['phone'] ?? ''),
        'role' => (string) ($nguon['role'] ?? VAI_TRO_NHAN_VIEN),
        'status' => (string) ($nguon['status'] ?? TRANG_THAI_HOAT_DONG),
        'password' => (string) ($nguon['password'] ?? ''),
        'password_confirm' => (string) ($nguon['password_confirm'] ?? ''),
    ];
}

function kiem_tra_du_lieu_nguoi_dung(array $duLieu, ?int $boQuaId = null, bool $batBuocMatKhau = false): array
{
    $loi = [];

    if ($duLieu['full_name'] === '') {
        $loi['full_name'] = 'Vui lòng nhập họ tên.';
    } elseif (mb_strlen($duLieu['full_name']) > 120) {
        $loi['full_name'] = 'Họ tên không được vượt quá 120 ký tự.';
    }

    if (!preg_match('/^[a-z0-9_.-]{3,50}$/', $duLieu['username'])) {
        $loi['username'] = 'Tên đăng nhập cần 3-50 ký tự, chỉ dùng chữ thường, số, dấu chấm, gạch dưới hoặc gạch ngang.';
    } elseif (nguoi_dung_bi_trung('username', $duLieu['username'], $boQuaId)) {
        $loi['username'] = 'Tên đăng nhập đã được sử dụng.';
    }

    if (!filter_var($duLieu['email'], FILTER_VALIDATE_EMAIL)) {
        $loi['email'] = 'Email không đúng định dạng.';
    } elseif (nguoi_dung_bi_trung('email', $duLieu['email'], $boQuaId)) {
        $loi['email'] = 'Email đã được sử dụng.';
    }

    if ($duLieu['phone'] !== '' && mb_strlen($duLieu['phone']) > 32) {
        $loi['phone'] = 'Số điện thoại không được vượt quá 32 ký tự.';
    }

    if (!in_array($duLieu['role'], VAI_TRO_HOP_LE, true)) {
        $loi['role'] = 'Vai trò không hợp lệ.';
    }

    if (!array_key_exists($duLieu['status'], nhan_trang_thai_nguoi_dung())) {
        $loi['status'] = 'Trạng thái không hợp lệ.';
    }

    if ($batBuocMatKhau || $duLieu['password'] !== '' || $duLieu['password_confirm'] !== '') {
        $loi += kiem_tra_mat_khau_nguoi_dung($duLieu['password'], $duLieu['password_confirm']);
    }

    if ($boQuaId !== null && !co_the_doi_vai_tro_trang_thai_nguoi_dung($boQuaId, $duLieu['role'], $duLieu['status'])) {
        $loi['status'] = 'Cần giữ lại ít nhất một tài khoản quản trị đang hoạt động.';
    }

    return $loi;
}

function kiem_tra_mat_khau_nguoi_dung(string $matKhau, string $xacNhan): array
{
    $loi = [];

    if (mb_strlen($matKhau) < 8) {
        $loi['password'] = 'Mật khẩu cần ít nhất 8 ký tự.';
    }

    if ($matKhau !== $xacNhan) {
        $loi['password_confirm'] = 'Mật khẩu xác nhận không khớp.';
    }

    return $loi;
}

function nguoi_dung_bi_trung(string $truong, string $giaTri, ?int $boQuaId = null): bool
{
    if (!in_array($truong, ['username', 'email'], true)) {
        return false;
    }

    $sql = "SELECT COUNT(*) FROM users WHERE {$truong} = :gia_tri";
    $thamSo = ['gia_tri' => $giaTri];

    if ($boQuaId !== null) {
        $sql .= ' AND id <> :id';
        $thamSo['id'] = $boQuaId;
    }

    return (int) lay_mot_gia_tri($sql, $thamSo) > 0;
}

function co_the_doi_vai_tro_trang_thai_nguoi_dung(int $id, string $vaiTroMoi, string $trangThaiMoi): bool
{
    $nguoiDung = lay_nguoi_dung_theo_id($id);

    if (!$nguoiDung || $nguoiDung['role'] !== VAI_TRO_ADMIN || $nguoiDung['status'] !== TRANG_THAI_HOAT_DONG) {
        return true;
    }

    if ($vaiTroMoi === VAI_TRO_ADMIN && $trangThaiMoi === TRANG_THAI_HOAT_DONG) {
        return true;
    }

    return (int) lay_mot_gia_tri(
        "SELECT COUNT(*) FROM users
         WHERE role = 'admin' AND status = 'active' AND id <> :id",
        ['id' => $id]
    ) > 0;
}

function tao_nguoi_dung(array $duLieu): int
{
    thuc_thi_lenh(
        'INSERT INTO users (full_name, username, email, phone, password_hash, role, status)
         VALUES (:full_name, :username, :email, :phone, :password_hash, :role, :status)',
        [
            'full_name' => $duLieu['full_name'],
            'username' => $duLieu['username'],
            'email' => $duLieu['email'],
            'phone' => $duLieu['phone'] !== '' ? $duLieu['phone'] : null,
            'password_hash' => tao_hash_mat_khau($duLieu['password']),
            'role' => $duLieu['role'],
            'status' => $duLieu['status'],
        ]
    );

    return (int) lay_id_vua_tao();
}

function cap_nhat_nguoi_dung(int $id, array $duLieu): void
{
    thuc_thi_lenh(
        'UPDATE users
         SET full_name = :full_name, username = :username, email = :email, phone = :phone,
             role = :role, status = :status
         WHERE id = :id',
        [
            'full_name' => $duLieu['full_name'],
            'username' => $duLieu['username'],
            'email' => $duLieu['email'],
            'phone' => $duLieu['phone'] !== '' ? $duLieu['phone'] : null,
            'role' => $duLieu['role'],
            'status' => $duLieu['status'],
            'id' => $id,
        ]
    );
}

function cap_nhat_mat_khau_nguoi_dung(int $id, string $matKhau): void
{
    thuc_thi_lenh(
        'UPDATE users SET password_hash = :password_hash WHERE id = :id',
        ['password_hash' => tao_hash_mat_khau($matKhau), 'id' => $id]
    );
}

function thong_ke_nguoi_dung(int $id): array
{
    return [
        'customers' => (int) lay_mot_gia_tri('SELECT COUNT(*) FROM customers WHERE assigned_user_id = :id AND deleted_at IS NULL', ['id' => $id]),
        'interactions' => (int) lay_mot_gia_tri('SELECT COUNT(*) FROM interactions WHERE user_id = :id', ['id' => $id]),
        'open_tasks' => (int) lay_mot_gia_tri("SELECT COUNT(*) FROM follow_up_tasks WHERE assigned_user_id = :id AND status IN ('pending', 'in_progress')", ['id' => $id]),
    ];
}
