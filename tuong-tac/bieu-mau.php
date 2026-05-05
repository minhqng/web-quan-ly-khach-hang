<?php

$loi = $loi ?? [];
$duLieu = array_merge(du_lieu_mac_dinh_tuong_tac(), $duLieu ?? []);
$danhSachKhachHang = $danhSachKhachHang ?? [];
$nhanNut = $nhanNut ?? 'Lưu tương tác';
$tieuDeBieuMau = $tieuDeBieuMau ?? 'Thông tin tương tác';
$hienThiTaoCongViec = $hienThiTaoCongViec ?? false;
$coLoi = static fn (string $truong): string => isset($loi[$truong]) ? ' is-invalid' : '';
$nhanUuTien = ['high' => 'Cao', 'medium' => 'Vừa', 'low' => 'Thấp'];
?>
<form method="post" class="surface-card interaction-form">
    <?= csrf_input() ?>
    <div class="interaction-form-heading">
        <p class="eyebrow">Lịch sử chăm sóc</p>
        <h2 class="card-title mb-1"><?= e($tieuDeBieuMau) ?></h2>
        <p class="text-muted mb-0">Ghi lại nội dung trao đổi và kết quả để biến hồ sơ khách hàng thành lịch sử làm việc thực tế.</p>
    </div>

    <div class="row g-3">
        <div class="col-lg-5">
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
        <div class="col-md-6 col-lg-3">
            <label class="form-label" for="interaction_type">Loại tương tác</label>
            <select class="form-select<?= e($coLoi('interaction_type')) ?>" id="interaction_type" name="interaction_type">
                <?php foreach (nhan_loai_tuong_tac() as $giaTri => $nhan): ?>
                    <option value="<?= e($giaTri) ?>" <?= $duLieu['interaction_type'] === $giaTri ? 'selected' : '' ?>><?= e($nhan) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6 col-lg-4">
            <label class="form-label" for="interaction_at">Thời gian</label>
            <input class="form-control<?= e($coLoi('interaction_at')) ?>" id="interaction_at" name="interaction_at" required type="datetime-local" value="<?= e($duLieu['interaction_at']) ?>">
            <?php if (isset($loi['interaction_at'])): ?><div class="invalid-feedback"><?= e($loi['interaction_at']) ?></div><?php endif; ?>
        </div>

        <div class="col-lg-7">
            <label class="form-label" for="title">Tiêu đề <span class="text-danger">*</span></label>
            <input class="form-control<?= e($coLoi('title')) ?>" id="title" maxlength="150" name="title" required value="<?= e($duLieu['title']) ?>">
            <?php if (isset($loi['title'])): ?><div class="invalid-feedback"><?= e($loi['title']) ?></div><?php endif; ?>
        </div>
        <div class="col-lg-5">
            <label class="form-label" for="result">Kết quả</label>
            <input class="form-control<?= e($coLoi('result')) ?>" id="result" maxlength="150" name="result" placeholder="Ví dụ: hẹn demo, cần báo giá..." value="<?= e($duLieu['result']) ?>">
            <?php if (isset($loi['result'])): ?><div class="invalid-feedback"><?= e($loi['result']) ?></div><?php endif; ?>
        </div>
        <div class="col-12">
            <label class="form-label" for="content">Nội dung trao đổi <span class="text-danger">*</span></label>
            <textarea class="form-control<?= e($coLoi('content')) ?>" id="content" name="content" required rows="5"><?= e($duLieu['content']) ?></textarea>
            <?php if (isset($loi['content'])): ?><div class="invalid-feedback"><?= e($loi['content']) ?></div><?php endif; ?>
        </div>
    </div>

    <?php if ($hienThiTaoCongViec): ?>
        <div class="interaction-follow-up-box">
            <div class="form-check form-switch">
                <input class="form-check-input" id="create_follow_up" name="create_follow_up" type="checkbox" value="1" <?= (int) $duLieu['create_follow_up'] === 1 ? 'checked' : '' ?>>
                <label class="form-check-label fw-semibold" for="create_follow_up">Tạo công việc theo dõi sau tương tác</label>
            </div>
            <div class="row g-3 mt-1">
                <div class="col-lg-5">
                    <label class="form-label" for="task_title">Tiêu đề công việc</label>
                    <input class="form-control<?= e($coLoi('task_title')) ?>" id="task_title" maxlength="150" name="task_title" value="<?= e($duLieu['task_title']) ?>">
                    <?php if (isset($loi['task_title'])): ?><div class="invalid-feedback"><?= e($loi['task_title']) ?></div><?php endif; ?>
                </div>
                <div class="col-md-6 col-lg-4">
                    <label class="form-label" for="task_due_at">Hạn xử lý</label>
                    <input class="form-control<?= e($coLoi('task_due_at')) ?>" id="task_due_at" name="task_due_at" type="datetime-local" value="<?= e($duLieu['task_due_at']) ?>">
                    <?php if (isset($loi['task_due_at'])): ?><div class="invalid-feedback"><?= e($loi['task_due_at']) ?></div><?php endif; ?>
                </div>
                <div class="col-md-6 col-lg-3">
                    <label class="form-label" for="task_priority">Ưu tiên</label>
                    <select class="form-select<?= e($coLoi('task_priority')) ?>" id="task_priority" name="task_priority">
                        <?php foreach ($nhanUuTien as $giaTri => $nhan): ?>
                            <option value="<?= e($giaTri) ?>" <?= $duLieu['task_priority'] === $giaTri ? 'selected' : '' ?>><?= e($nhan) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="interaction-form-actions form-actions">
        <a class="btn btn-outline-secondary" href="<?= e(duong_dan('tuong-tac/')) ?>">Hủy</a>
        <button class="btn btn-primary" type="submit"><?= e($nhanNut) ?></button>
    </div>
</form>
