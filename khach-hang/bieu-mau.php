<?php

$loi = $loi ?? [];
$duLieu = array_merge(du_lieu_mac_dinh_khach_hang(), $duLieu ?? []);
$danhSachLoai = $danhSachLoai ?? [];
$danhSachNhanVien = $danhSachNhanVien ?? [];
$idKhachHang = (int) ($idKhachHang ?? 0);
$nhanNut = $nhanNut ?? 'Lưu khách hàng';
$tieuDeBieuMau = $tieuDeBieuMau ?? 'Thông tin khách hàng';
$coLoi = static fn (string $truong): string => isset($loi[$truong]) ? ' is-invalid' : '';
?>
<form method="post" class="surface-card customer-form" data-customer-form data-customer-id="<?= e((string) $idKhachHang) ?>" novalidate>
    <div class="customer-form-heading">
        <div>
            <p class="eyebrow">Hồ sơ quản lý</p>
            <h2 class="card-title mb-1"><?= e($tieuDeBieuMau) ?></h2>
            <p class="text-muted mb-0">Các trường liên hệ được dùng để kiểm tra trùng lặp khách hàng đang hoạt động.</p>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <label class="form-label" for="full_name">Họ tên khách hàng <span class="text-danger">*</span></label>
            <input class="form-control<?= e($coLoi('full_name')) ?>" id="full_name" maxlength="150" name="full_name" required value="<?= e($duLieu['full_name']) ?>">
            <?php if (isset($loi['full_name'])): ?><div class="invalid-feedback"><?= e($loi['full_name']) ?></div><?php endif; ?>
        </div>
        <div class="col-lg-6">
            <label class="form-label" for="company_name">Công ty</label>
            <input class="form-control<?= e($coLoi('company_name')) ?>" id="company_name" maxlength="150" name="company_name" value="<?= e($duLieu['company_name']) ?>">
            <?php if (isset($loi['company_name'])): ?><div class="invalid-feedback"><?= e($loi['company_name']) ?></div><?php endif; ?>
        </div>

        <div class="col-md-6 col-lg-3">
            <label class="form-label" for="phone">Số điện thoại</label>
            <input class="form-control<?= e($coLoi('phone')) ?>" data-duplicate-field="phone" id="phone" maxlength="32" name="phone" value="<?= e($duLieu['phone']) ?>">
            <div class="duplicate-feedback" data-duplicate-target="phone"></div>
            <?php if (isset($loi['phone'])): ?><div class="invalid-feedback d-block"><?= e($loi['phone']) ?></div><?php endif; ?>
        </div>
        <div class="col-md-6 col-lg-3">
            <label class="form-label" for="email">Email</label>
            <input class="form-control<?= e($coLoi('email')) ?>" data-duplicate-field="email" id="email" maxlength="191" name="email" type="email" value="<?= e($duLieu['email']) ?>">
            <div class="duplicate-feedback" data-duplicate-target="email"></div>
            <?php if (isset($loi['email'])): ?><div class="invalid-feedback d-block"><?= e($loi['email']) ?></div><?php endif; ?>
        </div>
        <div class="col-md-6 col-lg-3">
            <label class="form-label" for="gender">Giới tính</label>
            <select class="form-select<?= e($coLoi('gender')) ?>" id="gender" name="gender">
                <?php foreach (nhan_gioi_tinh_khach_hang() as $giaTri => $nhan): ?>
                    <option value="<?= e($giaTri) ?>" <?= $duLieu['gender'] === $giaTri ? 'selected' : '' ?>><?= e($nhan) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6 col-lg-3">
            <label class="form-label" for="date_of_birth">Ngày sinh</label>
            <input class="form-control<?= e($coLoi('date_of_birth')) ?>" id="date_of_birth" name="date_of_birth" type="date" value="<?= e($duLieu['date_of_birth']) ?>">
            <?php if (isset($loi['date_of_birth'])): ?><div class="invalid-feedback"><?= e($loi['date_of_birth']) ?></div><?php endif; ?>
        </div>

        <div class="col-md-6 col-lg-3">
            <label class="form-label" for="customer_type_id">Loại khách hàng <span class="text-danger">*</span></label>
            <select class="form-select<?= e($coLoi('customer_type_id')) ?>" id="customer_type_id" name="customer_type_id" required>
                <option value="">Chọn loại</option>
                <?php foreach ($danhSachLoai as $loai): ?>
                    <option value="<?= e($loai['id']) ?>" <?= (string) $duLieu['customer_type_id'] === (string) $loai['id'] ? 'selected' : '' ?>><?= e($loai['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($loi['customer_type_id'])): ?><div class="invalid-feedback"><?= e($loi['customer_type_id']) ?></div><?php endif; ?>
        </div>
        <div class="col-md-6 col-lg-3">
            <label class="form-label" for="assigned_user_id">Nhân viên phụ trách <span class="text-danger">*</span></label>
            <select class="form-select<?= e($coLoi('assigned_user_id')) ?>" id="assigned_user_id" name="assigned_user_id" required>
                <option value="">Chọn nhân viên</option>
                <?php foreach ($danhSachNhanVien as $nhanVien): ?>
                    <option value="<?= e($nhanVien['id']) ?>" <?= (string) $duLieu['assigned_user_id'] === (string) $nhanVien['id'] ? 'selected' : '' ?>><?= e($nhanVien['full_name']) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($loi['assigned_user_id'])): ?><div class="invalid-feedback"><?= e($loi['assigned_user_id']) ?></div><?php endif; ?>
        </div>
        <div class="col-md-6 col-lg-3">
            <label class="form-label" for="status">Trạng thái</label>
            <select class="form-select<?= e($coLoi('status')) ?>" id="status" name="status">
                <?php foreach (nhan_trang_thai_khach_hang() as $giaTri => $nhan): ?>
                    <option value="<?= e($giaTri) ?>" <?= $duLieu['status'] === $giaTri ? 'selected' : '' ?>><?= e($nhan) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6 col-lg-3">
            <label class="form-label" for="source">Nguồn</label>
            <select class="form-select<?= e($coLoi('source')) ?>" id="source" name="source">
                <?php foreach (nhan_nguon_khach_hang() as $giaTri => $nhan): ?>
                    <option value="<?= e($giaTri) ?>" <?= $duLieu['source'] === $giaTri ? 'selected' : '' ?>><?= e($nhan) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-8">
            <label class="form-label" for="address">Địa chỉ</label>
            <input class="form-control<?= e($coLoi('address')) ?>" id="address" maxlength="255" name="address" value="<?= e($duLieu['address']) ?>">
            <?php if (isset($loi['address'])): ?><div class="invalid-feedback"><?= e($loi['address']) ?></div><?php endif; ?>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="city">Tỉnh/thành</label>
            <input class="form-control<?= e($coLoi('city')) ?>" id="city" maxlength="80" name="city" value="<?= e($duLieu['city']) ?>">
            <?php if (isset($loi['city'])): ?><div class="invalid-feedback"><?= e($loi['city']) ?></div><?php endif; ?>
        </div>
        <div class="col-12">
            <label class="form-label" for="notes">Ghi chú chăm sóc</label>
            <textarea class="form-control" id="notes" name="notes" rows="4"><?= e($duLieu['notes']) ?></textarea>
        </div>
    </div>

    <div class="customer-form-actions">
        <a class="btn btn-outline-secondary" href="<?= e(duong_dan('khach-hang/')) ?>">Hủy</a>
        <button class="btn btn-primary" type="submit"><?= e($nhanNut) ?></button>
    </div>
</form>
