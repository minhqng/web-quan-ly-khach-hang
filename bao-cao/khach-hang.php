<?php

declare(strict_types=1);

require __DIR__ . '/../dung-chung/khoi-dong.php';
require __DIR__ . '/../dung-chung/kiem-tra-dang-nhap.php';

$tieuDe = 'Báo cáo khách hàng';
try {
    require_once __DIR__ . '/du-lieu-bao-cao.php';
    $boLocBaoCao = lay_bo_loc_bao_cao();
    $danhSachNhanVienBaoCao = lay_lua_chon_nhan_vien_bao_cao();
    $danhSachLoaiBaoCao = lay_lua_chon_loai_khach_hang_bao_cao();
    $theoLoai = lay_khach_hang_theo_loai_bao_cao($boLocBaoCao);
    $theoNhanVien = lay_khach_hang_theo_nhan_vien_bao_cao($boLocBaoCao);
    $tongKhach = tong_cot_bao_cao($theoLoai);
    $tongTheoNhanVien = max(1, tong_cot_bao_cao($theoNhanVien));
    $tongDangChamSoc = tong_cot_bao_cao($theoLoai, 'active_count');
    $tongTiemNang = tong_cot_bao_cao($theoLoai, 'potential_count');
    $nhanTrangThai = nhan_trang_thai_khach_bao_cao();
} catch (Throwable) {
    hien_thi_loi_du_lieu($tieuDe, 'Không thể tải báo cáo khách hàng. Vui lòng kiểm tra kết nối cơ sở dữ liệu và dữ liệu demo.');
}

require __DIR__ . '/../giao-dien/dau-trang.php';
?>
<div class="page-header">
    <div>
        <p class="eyebrow">Phân tích khách hàng</p>
        <h1 class="page-title">Báo cáo khách hàng</h1>
        <p class="page-subtitle">Cho thấy khách hàng được phân loại, giao người phụ trách và theo dõi theo trạng thái chăm sóc.</p>
    </div>
</div>

<?php require __DIR__ . '/thanh-dieu-huong-bao-cao.php'; ?>
<?php require __DIR__ . '/bo-loc-bao-cao.php'; ?>

<section class="report-stat-grid">
    <article class="stat-card">
        <div class="stat-label">Tổng khách hàng</div>
        <div class="stat-value number"><?= e(number_format($tongKhach, 0, ',', '.')) ?></div>
        <p class="text-muted mb-0">Không tính khách đã xóa mềm</p>
    </article>
    <article class="stat-card">
        <div class="stat-label"><?= e($nhanTrangThai['active']) ?></div>
        <div class="stat-value number"><?= e(number_format($tongDangChamSoc, 0, ',', '.')) ?></div>
        <p class="text-muted mb-0">Khách cần duy trì chăm sóc</p>
    </article>
    <article class="stat-card">
        <div class="stat-label"><?= e($nhanTrangThai['potential']) ?></div>
        <div class="stat-value number"><?= e(number_format($tongTiemNang, 0, ',', '.')) ?></div>
        <p class="text-muted mb-0">Cơ hội cần theo sát</p>
    </article>
</section>

<section class="report-grid-two">
    <article class="surface-card">
        <div class="report-section-heading">
            <div>
                <p class="eyebrow">Phân loại</p>
                <h2 class="card-title">Khách hàng theo loại</h2>
            </div>
            <span><?= e((string) count($theoLoai)) ?> loại</span>
        </div>
        <div class="report-stack">
            <?php foreach ($theoLoai as $dong): ?>
                <?php $tyLe = ti_le_bao_cao((int) $dong['total'], max(1, $tongKhach)); ?>
                <div class="report-bar-row">
                    <div class="report-row-title">
                        <span class="report-color-dot" style="background: <?= e(mau_bao_cao_an_toan($dong['color'])) ?>"></span>
                        <strong><?= e($dong['name']) ?></strong>
                        <em><?= e((string) $dong['priority_score']) ?> điểm ưu tiên</em>
                    </div>
                    <div class="report-progress"><span style="width: <?= e((string) $tyLe) ?>%"></span></div>
                    <div class="report-row-meta">
                        <span><?= e((string) $dong['total']) ?> khách</span>
                        <span><?= e($tyLe . '%') ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </article>

    <article class="surface-card">
        <div class="report-section-heading">
            <div>
                <p class="eyebrow">Phân công</p>
                <h2 class="card-title">Khách hàng theo nhân viên</h2>
            </div>
            <span><?= e((string) count($theoNhanVien)) ?> nhóm</span>
        </div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Nhân viên</th>
                        <th>Tổng</th>
                        <th>Đang chăm sóc</th>
                        <th>Tiềm năng</th>
                        <th>Tỷ trọng</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($theoNhanVien as $dong): ?>
                        <?php $tyLe = ti_le_bao_cao((int) $dong['total'], $tongTheoNhanVien); ?>
                        <tr>
                            <td><strong><?= e($dong['staff_name']) ?></strong></td>
                            <td class="number"><?= e((string) $dong['total']) ?></td>
                            <td class="number"><?= e((string) $dong['active_count']) ?></td>
                            <td class="number"><?= e((string) $dong['potential_count']) ?></td>
                            <td>
                                <div class="report-progress report-progress--compact"><span style="width: <?= e((string) $tyLe) ?>%"></span></div>
                                <small class="text-muted"><?= e($tyLe . '%') ?></small>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($theoNhanVien === []): ?>
                        <tr><td class="table-empty-state text-center text-muted py-5" colspan="5">Chưa có dữ liệu khách hàng để báo cáo.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </article>
</section>
<?php require __DIR__ . '/../giao-dien/cuoi-trang.php'; ?>
