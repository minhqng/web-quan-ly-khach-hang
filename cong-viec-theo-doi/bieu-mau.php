<?php

$loi = $loi ?? [];
$duLieu = array_merge(du_lieu_mac_dinh_cong_viec(), $duLieu ?? []);
$danhSachKhachHang = $danhSachKhachHang ?? [];
$danhSachNhanVien = $danhSachNhanVien ?? [];
$tieuDeBieuMau = $tieuDeBieuMau ?? 'Thông tin công việc';
$nhanNut = $nhanNut ?? 'Lưu công việc';
$coLoi = static fn (string $truong): string => isset($loi[$truong]) ? ' is-invalid' : '';
?>
<form method="post" class="surface-card follow-up-form">
    <?= csrf_input() ?>
    <div class="follow-up-form-heading">
        <p class="eyebrow">Theo dõi sau chăm sóc</p>
        <h2 class="card-title mb-1"><?= e($tieuDeBieuMau) ?></h2>
        <p class="text-muted mb-0">Một công việc tốt phải có khách hàng, người phụ trách, hạn xử lý và trạng thái rõ ràng.</p>
    </div>

    <div class="form-section">
        <div class="form-section-title">
            <h3>Thông tin chính</h3>
            <p>Nội dung công việc và khách hàng liên quan.</p>
        </div>
        <div class="row g-3">
            <div class="col-lg-6">
                <label class="form-label" for="customer_id">Khách hàng <span class="text-danger">*</span></label>
                <select class="form-select<?= e($coLoi('customer_id')) ?>" id="customer_id" name="customer_id" required>
                    <option value="">Chọn khách hàng</option>
                    <?php foreach ($danhSachKhachHang as $khachHang): ?>
                        <option value="<?= e($khachHang['id']) ?>" <?= (string) $duLieu['customer_id'] === (string) $khachHang['id'] ? 'selected' : '' ?>>
                            <?= e($khachHang['full_name']) ?><?= $khachHang['company_name'] ? ' - ' . e($khachHang['company_name']) : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($loi['customer_id'])): ?><div class="invalid-feedback"><?= e($loi['customer_id']) ?></div><?php endif; ?>
            </div>
            <div class="col-lg-6">
                <label class="form-label" for="title">Tiêu đề công việc <span class="text-danger">*</span></label>
                <input class="form-control<?= e($coLoi('title')) ?>" id="title" maxlength="150" name="title" required value="<?= e($duLieu['title']) ?>">
                <?php if (isset($loi['title'])): ?><div class="invalid-feedback"><?= e($loi['title']) ?></div><?php endif; ?>
            </div>
            <div class="col-12">
                <label class="form-label" for="description">Mô tả</label>
                <textarea class="form-control" id="description" name="description" rows="4"><?= e($duLieu['description']) ?></textarea>
            </div>
        </div>
    </div>

    <div class="form-section">
        <div class="form-section-title">
            <h3>Phân công & trạng thái</h3>
            <p>Thiết lập người chịu trách nhiệm, thời hạn và tiến độ.</p>
        </div>
        <div class="row g-3">
            <div class="col-lg-6">
                <label class="form-label" for="assigned_user_id">Nhân viên phụ trách <span class="text-danger">*</span></label>
                <select class="form-select<?= e($coLoi('assigned_user_id')) ?>" id="assigned_user_id" name="assigned_user_id" required <?= la_admin() ? '' : 'disabled' ?>>
                    <option value="">Chọn nhân viên</option>
                    <?php foreach ($danhSachNhanVien as $nhanVien): ?>
                        <option value="<?= e($nhanVien['id']) ?>" <?= (string) $duLieu['assigned_user_id'] === (string) $nhanVien['id'] ? 'selected' : '' ?>>
                            <?= e($nhanVien['full_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!la_admin()): ?><input type="hidden" name="assigned_user_id" value="<?= e($duLieu['assigned_user_id']) ?>"><?php endif; ?>
                <?php if (isset($loi['assigned_user_id'])): ?><div class="invalid-feedback"><?= e($loi['assigned_user_id']) ?></div><?php endif; ?>
            </div>
            <div class="col-md-6 col-lg-3">
                <label class="form-label" for="due_at">Hạn xử lý <span class="text-danger">*</span></label>
                <input class="form-control<?= e($coLoi('due_at')) ?>" id="due_at" name="due_at" required type="datetime-local" value="<?= e($duLieu['due_at']) ?>">
                <?php if (isset($loi['due_at'])): ?><div class="invalid-feedback"><?= e($loi['due_at']) ?></div><?php endif; ?>
            </div>
            <div class="col-md-6 col-lg-3">
                <label class="form-label" for="priority">Ưu tiên</label>
                <select class="form-select<?= e($coLoi('priority')) ?>" id="priority" name="priority">
                    <?php foreach (nhan_uu_tien_cong_viec() as $giaTri => $nhan): ?>
                        <option value="<?= e($giaTri) ?>" <?= $duLieu['priority'] === $giaTri ? 'selected' : '' ?>><?= e($nhan) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="status">Trạng thái</label>
                <select class="form-select<?= e($coLoi('status')) ?>" id="status" name="status">
                    <?php foreach (lua_chon_trang_thai_cong_viec($duLieu['status']) as $giaTri => $nhan): ?>
                        <option value="<?= e($giaTri) ?>" <?= $duLieu['status'] === $giaTri ? 'selected' : '' ?>><?= e($nhan) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($loi['status'])): ?><div class="invalid-feedback"><?= e($loi['status']) ?></div><?php endif; ?>
            </div>
        </div>
    </div>

    <div class="follow-up-form-actions form-actions">
        <a class="btn btn-outline-secondary" href="<?= e(duong_dan('cong-viec-theo-doi/')) ?>">Hủy</a>
        <button class="btn btn-primary" type="submit"><?= e($nhanNut) ?></button>
    </div>
</form>
