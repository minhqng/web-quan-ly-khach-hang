<?php

declare(strict_types=1);

require __DIR__ . '/../dung-chung/khoi-dong.php';
require __DIR__ . '/../dung-chung/kiem-tra-dang-nhap.php';

$tieuDe = 'Báo cáo công việc';
try {
    require_once __DIR__ . '/du-lieu-bao-cao.php';
    $boLocBaoCao = lay_bo_loc_bao_cao();
    $danhSachNhanVienBaoCao = lay_lua_chon_nhan_vien_bao_cao();
    $danhSachLoaiBaoCao = lay_lua_chon_loai_khach_hang_bao_cao();
    $theoTrangThai = lay_cong_viec_theo_trang_thai_bao_cao($boLocBaoCao);
    $hieuQuaNhanVien = lay_hieu_qua_nhan_vien_bao_cao($boLocBaoCao);
    $nhanTrangThai = nhan_trang_thai_viec_bao_cao();
    $tongViec = tong_cot_bao_cao($theoTrangThai);
    $viecQuaHan = tong_cot_bao_cao($theoTrangThai, 'overdue_count');
    $viecMo = 0;
    $viecHoanThanh = 0;

    foreach ($theoTrangThai as $dong) {
        if (in_array($dong['status'], ['pending', 'in_progress'], true)) {
            $viecMo += (int) $dong['total'];
        }
        if ($dong['status'] === 'completed') {
            $viecHoanThanh = (int) $dong['total'];
        }
    }

    $tyLeHoanThanh = ti_le_bao_cao($viecHoanThanh, $tongViec);
} catch (Throwable) {
    hien_thi_loi_du_lieu($tieuDe, 'Không thể tải báo cáo công việc. Vui lòng kiểm tra kết nối cơ sở dữ liệu và dữ liệu demo.');
}

require __DIR__ . '/../giao-dien/dau-trang.php';
?>
<div class="page-header">
    <div>
        <p class="eyebrow">Theo dõi vận hành</p>
        <h1 class="page-title">Báo cáo công việc</h1>
        <p class="page-subtitle">Tổng hợp trạng thái follow-up, việc quá hạn và hiệu quả xử lý theo hạn công việc.</p>
    </div>
</div>

<?php require __DIR__ . '/thanh-dieu-huong-bao-cao.php'; ?>
<?php require __DIR__ . '/bo-loc-bao-cao.php'; ?>

<section class="report-stat-grid">
    <article class="stat-card">
        <div class="stat-label">Tổng công việc</div>
        <div class="stat-value number"><?= e(number_format($tongViec, 0, ',', '.')) ?></div>
        <p class="text-muted mb-0">Tính theo hạn xử lý công việc</p>
    </article>
    <article class="stat-card">
        <div class="stat-label">Đang mở</div>
        <div class="stat-value number"><?= e(number_format($viecMo, 0, ',', '.')) ?></div>
        <p class="text-muted mb-0">Chờ xử lý hoặc đang xử lý</p>
    </article>
    <article class="stat-card">
        <div class="stat-label">Quá hạn</div>
        <div class="stat-value number"><?= e(number_format($viecQuaHan, 0, ',', '.')) ?></div>
        <p class="text-muted mb-0">Cần xử lý trước khi demo</p>
    </article>
    <article class="stat-card">
        <div class="stat-label">Tỷ lệ hoàn thành</div>
        <div class="stat-value number"><?= e((string) $tyLeHoanThanh) ?>%</div>
        <div class="report-progress" aria-hidden="true"><span style="width: <?= e((string) $tyLeHoanThanh) ?>%"></span></div>
    </article>
</section>

<section class="report-grid-two">
    <article class="surface-card">
        <div class="report-section-heading">
            <div>
                <p class="eyebrow">Trạng thái</p>
                <h2 class="card-title">Công việc theo trạng thái</h2>
            </div>
        </div>
        <div class="report-stack">
            <?php foreach ($theoTrangThai as $dong): ?>
                <?php $tyLe = ti_le_bao_cao((int) $dong['total'], max(1, $tongViec)); ?>
                <div class="report-bar-row">
                    <div class="report-row-title">
                        <strong><?= e($nhanTrangThai[$dong['status']] ?? $dong['status']) ?></strong>
                        <em><?= e((string) $dong['total']) ?> việc</em>
                    </div>
                    <div class="report-progress"><span style="width: <?= e((string) $tyLe) ?>%"></span></div>
                    <div class="report-row-meta">
                        <span><?= e($tyLe . '%') ?> tổng việc</span>
                        <span><?= (int) $dong['overdue_count'] > 0 ? e($dong['overdue_count'] . ' quá hạn') : 'Không quá hạn' ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if ($theoTrangThai === []): ?>
                <p class="text-muted mb-0">Chưa có công việc theo dõi.</p>
            <?php endif; ?>
        </div>
    </article>

    <article class="surface-card report-note-card">
        <p class="eyebrow">Ý nghĩa demo</p>
        <h2 class="card-title">Vì sao báo cáo công việc quan trọng?</h2>
        <p>Module này chứng minh hệ thống có quy trình chăm sóc sau tương tác: mỗi việc có người phụ trách, hạn xử lý, trạng thái và cảnh báo quá hạn.</p>
        <ul class="report-check-list">
            <li>Việc quá hạn dùng điều kiện thời gian thật từ CSDL.</li>
            <li>Hoàn thành/cancel không bị tính là quá hạn.</li>
            <li>Hiệu quả nhân viên dựa trên dữ liệu giao việc và tương tác.</li>
        </ul>
    </article>
</section>

<section class="surface-card report-section" id="hieu-qua-nhan-vien">
    <div class="report-section-heading">
        <div>
            <p class="eyebrow">Hiệu quả nhân viên</p>
            <h2 class="card-title">Chỉ số vận hành cơ bản</h2>
        </div>
        <span>Bộ lọc ngày trong báo cáo này áp dụng theo hạn xử lý</span>
    </div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Nhân viên</th>
                    <th>Khách phụ trách</th>
                    <th>Tương tác</th>
                    <th>Việc hoàn thành</th>
                    <th>Việc quá hạn</th>
                    <th>Tỷ lệ hoàn thành</th>
                    <th>Nhận xét</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($hieuQuaNhanVien as $dong): ?>
                    <?php
                    $tyLeNhanVien = ti_le_bao_cao((int) $dong['completed_tasks'], (int) $dong['total_tasks']);
                    $coQuaHan = (int) $dong['overdue_tasks'] > 0;
                    ?>
                    <tr>
                        <td><strong><?= e($dong['staff_name']) ?></strong></td>
                        <td class="number"><?= e((string) $dong['assigned_customers']) ?></td>
                        <td class="number"><?= e((string) $dong['interaction_count']) ?></td>
                        <td class="number"><?= e((string) $dong['completed_tasks']) ?></td>
                        <td class="number"><?= e((string) $dong['overdue_tasks']) ?></td>
                        <td>
                            <div class="report-progress report-progress--compact"><span style="width: <?= e((string) $tyLeNhanVien) ?>%"></span></div>
                            <small class="text-muted"><?= e($tyLeNhanVien . '%') ?></small>
                        </td>
                        <td>
                            <span class="badge <?= $coQuaHan ? 'badge-soft-warning' : 'badge-soft-success' ?>">
                                <?= $coQuaHan ? 'Cần theo sát' : 'Ổn định' ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($hieuQuaNhanVien === []): ?>
                    <tr><td class="table-empty-state text-center text-muted py-5" colspan="7">Chưa có dữ liệu nhân viên để tính hiệu quả.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require __DIR__ . '/../giao-dien/cuoi-trang.php'; ?>
