<?php

$loi = $loi ?? [];
$duLieu = array_merge(du_lieu_mac_dinh_loai_khach_hang(), $duLieu ?? []);
$tieuDeBieuMau = $tieuDeBieuMau ?? 'Thông tin loại khách hàng';
$moTaBieuMau = $moTaBieuMau ?? 'Thiết lập cách phân nhóm và ưu tiên chăm sóc khách hàng.';
$nhanNut = $nhanNut ?? 'Lưu thông tin';
$duongDanHuy = $duongDanHuy ?? 'loai-khach-hang/';
$coLoi = static fn (string $truong): string => isset($loi[$truong]) ? ' is-invalid' : '';
?>
<form method="post" class="surface-card type-form">
    <?= csrf_input() ?>
    <div class="mb-4">
        <p class="eyebrow mb-2">Thông tin cấu hình</p>
        <h2 class="card-title mb-1"><?= e($tieuDeBieuMau) ?></h2>
        <p class="text-muted mb-0"><?= e($moTaBieuMau) ?></p>
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <label class="form-label" for="name">Tên loại khách hàng <span class="text-danger">*</span></label>
            <input
                class="form-control<?= e($coLoi('name')) ?>"
                id="name"
                maxlength="80"
                name="name"
                required
                value="<?= e($duLieu['name']) ?>"
            >
            <?php if (isset($loi['name'])): ?>
                <div class="invalid-feedback"><?= e($loi['name']) ?></div>
            <?php endif; ?>
        </div>

        <div class="col-sm-6 col-lg-3">
            <label class="form-label" for="priority_score">Điểm ưu tiên</label>
            <input
                class="form-control<?= e($coLoi('priority_score')) ?>"
                id="priority_score"
                max="100"
                min="0"
                name="priority_score"
                type="number"
                value="<?= e($duLieu['priority_score']) ?>"
            >
            <?php if (isset($loi['priority_score'])): ?>
                <div class="invalid-feedback"><?= e($loi['priority_score']) ?></div>
            <?php endif; ?>
        </div>

        <div class="col-sm-6 col-lg-2">
            <label class="form-label" for="color">Màu</label>
            <input
                class="form-control form-control-color w-100<?= e($coLoi('color')) ?>"
                id="color"
                name="color"
                type="color"
                value="<?= e(mau_loai_khach_hang_an_toan($duLieu['color'])) ?>"
            >
            <?php if (isset($loi['color'])): ?>
                <div class="invalid-feedback d-block"><?= e($loi['color']) ?></div>
            <?php endif; ?>
        </div>

        <div class="col-12">
            <label class="form-label" for="description">Mô tả</label>
            <textarea
                class="form-control<?= e($coLoi('description')) ?>"
                id="description"
                maxlength="500"
                name="description"
                rows="4"
            ><?= e($duLieu['description']) ?></textarea>
            <?php if (isset($loi['description'])): ?>
                <div class="invalid-feedback"><?= e($loi['description']) ?></div>
            <?php endif; ?>
        </div>

        <div class="col-12">
            <div class="form-check form-switch">
                <input
                    class="form-check-input"
                    id="is_active"
                    name="is_active"
                    type="checkbox"
                    value="1"
                    <?= (int) $duLieu['is_active'] === 1 ? 'checked' : '' ?>
                >
                <label class="form-check-label fw-semibold" for="is_active">Đang sử dụng trong hệ thống</label>
            </div>
        </div>
    </div>

    <div class="type-form-actions form-actions">
        <a class="btn btn-outline-secondary" href="<?= e(duong_dan($duongDanHuy)) ?>">Hủy</a>
        <button class="btn btn-primary" type="submit"><?= e($nhanNut) ?></button>
    </div>
</form>
