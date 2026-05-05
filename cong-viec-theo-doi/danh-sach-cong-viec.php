<?php

$cheDo = $cheDo ?? 'my';
$danhSachCongViec = $danhSachCongViec ?? [];
$tieuDeDanhSach = $tieuDeDanhSach ?? 'Danh sách công việc';
$moTaDanhSach = $moTaDanhSach ?? 'Theo dõi hạn xử lý, người phụ trách và trạng thái công việc chăm sóc khách hàng.';
$thongDiepRong = $thongDiepRong ?? 'Chưa có công việc theo dõi.';
$nhanTrangThai = nhan_trang_thai_cong_viec();
$nhanUuTien = nhan_uu_tien_cong_viec();
$tepHienTai = basename(str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? 'index.php'));
$lopTab = static fn (string $tep): string => $tepHienTai === $tep ? ' is-active' : '';
?>
<div class="follow-up-nav">
    <div class="follow-up-tabs segmented-nav" aria-label="Bộ lọc công việc theo dõi">
        <a class="follow-up-tab segmented-nav-link<?= e($lopTab('index.php')) ?>" href="<?= e(duong_dan('cong-viec-theo-doi/')) ?>">Công việc của tôi</a>
        <a class="follow-up-tab segmented-nav-link<?= e($lopTab('qua-han.php')) ?>" href="<?= e(duong_dan('cong-viec-theo-doi/qua-han.php')) ?>">Quá hạn</a>
        <a class="follow-up-tab segmented-nav-link<?= e($lopTab('sap-toi.php')) ?>" href="<?= e(duong_dan('cong-viec-theo-doi/sap-toi.php')) ?>">Sắp tới</a>
    </div>
    <a class="btn btn-primary" href="<?= e(duong_dan('cong-viec-theo-doi/them.php')) ?>">Thêm công việc</a>
</div>

<section class="table-card task-board task-board--<?= e($cheDo) ?>" data-task-board>
    <div class="task-board-heading table-panel-heading">
        <div>
            <h2 class="card-title mb-1"><?= e($tieuDeDanhSach) ?></h2>
            <p class="text-muted mb-0"><?= e($moTaDanhSach) ?></p>
        </div>
        <div class="task-board-feedback" data-task-feedback aria-live="polite">Có thể cập nhật nhanh bằng AJAX</div>
    </div>

    <div class="table-responsive">
        <table class="table align-middle task-table">
            <thead>
                <tr>
                    <th>Công việc</th>
                    <th>Khách hàng</th>
                    <th>Phụ trách</th>
                    <th>Hạn xử lý</th>
                    <th>Ưu tiên</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($danhSachCongViec as $congViec): ?>
                    <?php
                    $dangMo = in_array($congViec['status'], trang_thai_cong_viec_dang_mo(), true);
                    $quaHan = la_cong_viec_qua_han($congViec);
                    $lopDong = trim(($quaHan ? 'is-overdue ' : '') . (!$dangMo ? 'is-finished' : ''));
                    $nhanHan = $quaHan ? 'Quá hạn' : ($dangMo ? 'Đang theo dõi' : 'Đã đóng');
                    $luaChonTrangThai = lua_chon_trang_thai_cong_viec($congViec['status']);
                    ?>
                    <tr class="<?= e($lopDong) ?>" data-task-row="<?= e($congViec['id']) ?>" data-task-status="<?= e($congViec['status']) ?>">
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
                        <td>
                            <div class="task-due-cell">
                                <strong><?= e(dinh_dang_ngay_gio($congViec['due_at'])) ?></strong>
                                <span><?= e($nhanHan) ?></span>
                            </div>
                        </td>
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
                                    <?php foreach ($luaChonTrangThai as $giaTri => $nhan): ?>
                                        <option value="<?= e($giaTri) ?>" <?= $congViec['status'] === $giaTri ? 'selected' : '' ?>><?= e($nhan) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </td>
                        <td>
                            <div class="task-row-actions row-actions">
                                <a class="btn btn-sm btn-outline-secondary" href="<?= e(duong_dan('cong-viec-theo-doi/sua.php?id=' . $congViec['id'])) ?>">Sửa</a>
                                <?php if ($dangMo): ?>
                                    <form method="post" action="<?= e(duong_dan('cong-viec-theo-doi/hoan-thanh.php')) ?>" data-task-complete-form>
                                        <?= csrf_input() ?>
                                        <input type="hidden" name="id" value="<?= e($congViec['id']) ?>">
                                        <button class="btn btn-sm btn-outline-success" type="submit">Hoàn thành</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($danhSachCongViec === []): ?>
                    <tr>
                        <td class="table-empty-state" colspan="7">
                            <div class="empty-state-inline mx-auto">
                                <strong><?= e($thongDiepRong) ?></strong>
                                <p>Tạo công việc theo dõi để lịch chăm sóc không bị bỏ sót.</p>
                                <a class="btn btn-sm btn-primary" href="<?= e(duong_dan('cong-viec-theo-doi/them.php')) ?>">Thêm công việc</a>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
