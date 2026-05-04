<?php

declare(strict_types=1);

function du_lieu_mac_dinh_loai_khach_hang(): array
{
    return [
        'name' => '',
        'description' => '',
        'priority_score' => 50,
        'color' => '#2563eb',
        'is_active' => 1,
    ];
}

function lay_du_lieu_form_loai_khach_hang(array $nguon): array
{
    return [
        'name' => chuoi_sach($nguon['name'] ?? ''),
        'description' => trim((string) ($nguon['description'] ?? '')),
        'priority_score' => (int) ($nguon['priority_score'] ?? 0),
        'color' => chuoi_sach($nguon['color'] ?? ''),
        'is_active' => isset($nguon['is_active']) ? 1 : 0,
    ];
}

function lay_danh_sach_loai_khach_hang(): array
{
    return lay_nhieu_dong(
        'SELECT
            ct.id,
            ct.name,
            ct.description,
            ct.priority_score,
            ct.color,
            ct.is_active,
            ct.updated_at,
            COUNT(c.id) AS customer_count
         FROM customer_types ct
         LEFT JOIN customers c ON c.customer_type_id = ct.id
         GROUP BY ct.id
         ORDER BY ct.is_active DESC, ct.priority_score DESC, ct.name ASC'
    );
}

function lay_loai_khach_hang_theo_id(int $id): ?array
{
    return lay_mot_dong(
        'SELECT id, name, description, priority_score, color, is_active
         FROM customer_types
         WHERE id = :id
         LIMIT 1',
        ['id' => $id]
    );
}

function ten_loai_khach_hang_da_ton_tai(string $ten, ?int $boQuaId = null): bool
{
    $sql = 'SELECT COUNT(*) FROM customer_types WHERE name = :name';
    $thamSo = ['name' => $ten];

    if ($boQuaId !== null) {
        $sql .= ' AND id <> :id';
        $thamSo['id'] = $boQuaId;
    }

    return (int) lay_mot_gia_tri($sql, $thamSo) > 0;
}

function kiem_tra_du_lieu_loai_khach_hang(array $duLieu, ?int $boQuaId = null): array
{
    $loi = [];

    if ($duLieu['name'] === '') {
        $loi['name'] = 'Vui lòng nhập tên loại khách hàng.';
    } elseif (mb_strlen($duLieu['name']) > 80) {
        $loi['name'] = 'Tên loại khách hàng không được vượt quá 80 ký tự.';
    } elseif (ten_loai_khach_hang_da_ton_tai($duLieu['name'], $boQuaId)) {
        $loi['name'] = 'Tên loại khách hàng đã tồn tại.';
    }

    if (mb_strlen($duLieu['description']) > 500) {
        $loi['description'] = 'Mô tả không được vượt quá 500 ký tự.';
    }

    if ($duLieu['priority_score'] < 0 || $duLieu['priority_score'] > 100) {
        $loi['priority_score'] = 'Điểm ưu tiên phải nằm trong khoảng 0 đến 100.';
    }

    if (!mau_loai_khach_hang_hop_le($duLieu['color'])) {
        $loi['color'] = 'Màu hiển thị phải có dạng #RRGGBB.';
    }

    return $loi;
}

function dem_khach_hang_theo_loai(int $id): int
{
    return (int) lay_mot_gia_tri(
        'SELECT COUNT(*) FROM customers WHERE customer_type_id = :id',
        ['id' => $id]
    );
}

function xoa_hoac_an_loai_khach_hang(int $id): string
{
    $loaiKhachHang = lay_loai_khach_hang_theo_id($id);

    if (!$loaiKhachHang) {
        return 'khong_ton_tai';
    }

    if (dem_khach_hang_theo_loai($id) > 0) {
        if ((int) $loaiKhachHang['is_active'] === 1) {
            thuc_thi_lenh(
                'UPDATE customer_types SET is_active = 0 WHERE id = :id',
                ['id' => $id]
            );

            return 'da_an';
        }

        return 'dang_duoc_su_dung';
    }

    thuc_thi_lenh('DELETE FROM customer_types WHERE id = :id', ['id' => $id]);

    return 'da_xoa';
}

function mau_loai_khach_hang_hop_le(string $mau): bool
{
    return preg_match('/^#[0-9a-fA-F]{6}$/', $mau) === 1;
}

function mau_loai_khach_hang_an_toan(?string $mau): string
{
    $mau = (string) $mau;

    return mau_loai_khach_hang_hop_le($mau) ? $mau : '#64748b';
}
