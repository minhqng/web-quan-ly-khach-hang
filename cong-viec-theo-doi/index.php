<?php

declare(strict_types=1);

require __DIR__ . '/../dung-chung/khoi-dong.php';
require __DIR__ . '/../dung-chung/kiem-tra-dang-nhap.php';
require __DIR__ . '/ham-cong-viec-theo-doi.php';

$danhSachCongViec = lay_danh_sach_cong_viec_theo_doi();
$nhanTrangThai = nhan_trang_thai_cong_viec();
$nhanUuTien = nhan_uu_tien_cong_viec();
$tieuDe = 'Công việc theo dõi';

require __DIR__ . '/../giao-dien/dau-trang.php';
?>
<div class="page-header">
    <div>
        <p class="eyebrow">Chăm sóc khách hàng</p>
        <h1 class="page-title">Công việc theo dõi</h1>
        <p class="page-subtitle">Cập nhật trạng thái công việc ngay trên danh sách, không cần tải lại trang.</p>
    </div>
</div>

<section class="table-card task-board" data-task-board>
    <div class="task-board-feedback" data-task-feedback aria-live="polite">AJAX sẵn sàng</div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Công việc</th>
                    <th>Khách hàng</th>
                    <th>Phụ trách</th>
                    <th>Hạn xử lý</th>
                    <th>Ưu tiên</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($danhSachCongViec as $congViec): ?>
                    <tr data-task-row="<?= e($congViec['id']) ?>">
                        <td>
                            <div class="task-title-cell">
                                <strong><?= e($congViec['title']) ?></strong>
                                <span><?= e($congViec['description'] ?: 'Không có mô tả') ?></span>
                            </div>
                        </td>
                        <td>
                            <strong><?= e($congViec['customer_name']) ?></strong>
                            <div class="text-muted"><?= e($congViec['company_name'] ?: 'Khách hàng cá nhân') ?></div>
                        </td>
                        <td><?= e($congViec['assigned_user_name']) ?></td>
                        <td><?= e(dinh_dang_ngay_gio($congViec['due_at'])) ?></td>
                        <td>
                            <span class="badge <?= e(lop_badge_uu_tien_cong_viec($congViec['priority'])) ?>">
                                <?= e($nhanUuTien[$congViec['priority']] ?? 'Vừa') ?>
                            </span>
                        </td>
                        <td>
                            <div class="task-status-control">
                                <span class="badge <?= e(lop_badge_trang_thai_cong_viec($congViec['status'])) ?>" data-task-status-badge>
                                    <?= e($nhanTrangThai[$congViec['status']] ?? 'Không rõ') ?>
                                </span>
                                <select class="form-select form-select-sm" data-task-status-select data-task-id="<?= e($congViec['id']) ?>">
                                    <?php foreach ($nhanTrangThai as $giaTri => $nhan): ?>
                                        <option value="<?= e($giaTri) ?>" <?= $congViec['status'] === $giaTri ? 'selected' : '' ?>><?= e($nhan) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($danhSachCongViec === []): ?>
                    <tr><td class="text-center text-muted py-5" colspan="6">Chưa có công việc theo dõi.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require __DIR__ . '/../giao-dien/cuoi-trang.php'; ?>
