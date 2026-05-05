<?php

declare(strict_types=1);

require __DIR__ . '/../dung-chung/khoi-dong.php';
require __DIR__ . '/../dung-chung/kiem-tra-dang-nhap.php';
require_once __DIR__ . '/du-lieu-bao-cao.php';

$boLocBaoCao = lay_bo_loc_bao_cao();
$danhSachNhanVienBaoCao = lay_lua_chon_nhan_vien_bao_cao();
$danhSachLoaiBaoCao = lay_lua_chon_loai_khach_hang_bao_cao();
$theoThoiGian = lay_tuong_tac_theo_thoi_gian_bao_cao($boLocBaoCao);
$theoLoai = lay_tuong_tac_theo_loai_bao_cao($boLocBaoCao);
$theoNhanVien = lay_tuong_tac_theo_nhan_vien_bao_cao($boLocBaoCao);
$nhanTuongTac = nhan_tuong_tac_bao_cao();
$tong30Ngay = tong_cot_bao_cao($theoThoiGian);
$tongTatCa = tong_cot_bao_cao($theoLoai);
$mocCaoNhat = max(1, ...array_map(static fn (array $dong): int => (int) $dong['total'], $theoThoiGian ?: [['total' => 0]]));
$nhanVienCoHoatDong = count(array_filter($theoNhanVien, static fn (array $dong): bool => (int) $dong['total'] > 0));

$tieuDe = 'Báo cáo tương tác';
require __DIR__ . '/../giao-dien/dau-trang.php';
?>
<div class="page-header">
    <div>
        <p class="eyebrow">Lịch sử chăm sóc</p>
        <h1 class="page-title">Báo cáo tương tác</h1>
        <p class="page-subtitle">Theo dõi nhịp chăm sóc khách hàng theo thời gian, kênh liên hệ và nhân viên ghi nhận.</p>
    </div>
</div>

<?php require __DIR__ . '/thanh-dieu-huong-bao-cao.php'; ?>
<?php require __DIR__ . '/bo-loc-bao-cao.php'; ?>

<section class="report-stat-grid">
    <article class="stat-card">
        <div class="stat-label">Tương tác 30 ngày</div>
        <div class="stat-value number"><?= e(number_format($tong30Ngay, 0, ',', '.')) ?></div>
        <p class="text-muted mb-0">Dùng cho demo xu hướng chăm sóc</p>
    </article>
    <article class="stat-card">
        <div class="stat-label">Tổng tương tác</div>
        <div class="stat-value number"><?= e(number_format($tongTatCa, 0, ',', '.')) ?></div>
        <p class="text-muted mb-0">Từ toàn bộ lịch sử đang có</p>
    </article>
    <article class="stat-card">
        <div class="stat-label">Nhân viên có hoạt động</div>
        <div class="stat-value number"><?= e((string) $nhanVienCoHoatDong) ?></div>
        <p class="text-muted mb-0">Có ít nhất một tương tác</p>
    </article>
</section>

<section class="report-grid-two">
    <article class="surface-card">
        <div class="report-section-heading">
            <div>
                <p class="eyebrow">30 ngày gần đây</p>
                <h2 class="card-title">Tương tác theo thời gian</h2>
            </div>
            <span><?= e((string) count($theoThoiGian)) ?> ngày có dữ liệu</span>
        </div>
        <div class="report-chart-list">
            <?php foreach ($theoThoiGian as $dong): ?>
                <?php $tyLe = ti_le_bao_cao((int) $dong['total'], $mocCaoNhat); ?>
                <div class="report-chart-row">
                    <span><?= e(dinh_dang_ngay($dong['report_date'])) ?></span>
                    <div class="report-progress"><span style="width: <?= e((string) $tyLe) ?>%"></span></div>
                    <strong class="number"><?= e((string) $dong['total']) ?></strong>
                </div>
            <?php endforeach; ?>
            <?php if ($theoThoiGian === []): ?>
                <p class="text-muted mb-0">Chưa có tương tác trong 30 ngày gần đây.</p>
            <?php endif; ?>
        </div>
    </article>

    <article class="surface-card">
        <div class="report-section-heading">
            <div>
                <p class="eyebrow">Kênh liên hệ</p>
                <h2 class="card-title">Tương tác theo loại</h2>
            </div>
            <span><?= e((string) count($theoLoai)) ?> loại</span>
        </div>
        <div class="report-stack">
            <?php foreach ($theoLoai as $dong): ?>
                <?php $tyLe = ti_le_bao_cao((int) $dong['total'], max(1, $tongTatCa)); ?>
                <div class="report-bar-row">
                    <div class="report-row-title">
                        <strong><?= e($nhanTuongTac[$dong['interaction_type']] ?? 'Khác') ?></strong>
                        <em><?= e((string) $dong['total']) ?> lần</em>
                    </div>
                    <div class="report-progress"><span style="width: <?= e((string) $tyLe) ?>%"></span></div>
                    <div class="report-row-meta"><span>Tỷ trọng</span><span><?= e($tyLe . '%') ?></span></div>
                </div>
            <?php endforeach; ?>
        </div>
    </article>
</section>

<section class="surface-card report-section">
    <div class="report-section-heading">
        <div>
            <p class="eyebrow">Nhân viên</p>
            <h2 class="card-title">Tần suất ghi nhận tương tác</h2>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Nhân viên</th>
                    <th>Số tương tác</th>
                    <th>Lần gần nhất</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($theoNhanVien as $dong): ?>
                    <tr>
                        <td><strong><?= e($dong['staff_name']) ?></strong></td>
                        <td class="number"><?= e((string) $dong['total']) ?></td>
                        <td><?= e(dinh_dang_ngay_gio($dong['last_interaction_at']) ?: 'Chưa có') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require __DIR__ . '/../giao-dien/cuoi-trang.php'; ?>
