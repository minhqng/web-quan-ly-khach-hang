<?php

declare(strict_types=1);

require __DIR__ . '/../dung-chung/khoi-dong.php';
require __DIR__ . '/../dung-chung/kiem-tra-dang-nhap.php';

$tieuDe = 'Báo cáo';
try {
    require_once __DIR__ . '/du-lieu-bao-cao.php';
    $boLocBaoCao = lay_bo_loc_bao_cao();
    $danhSachNhanVienBaoCao = lay_lua_chon_nhan_vien_bao_cao();
    $danhSachLoaiBaoCao = lay_lua_chon_loai_khach_hang_bao_cao();
    $tongQuan = lay_tong_quan_bao_cao($boLocBaoCao);
    $theoLoai = lay_khach_hang_theo_loai_bao_cao($boLocBaoCao);
    $theoTrangThaiViec = lay_cong_viec_theo_trang_thai_bao_cao($boLocBaoCao);
    $hieuQuaNhanVien = lay_hieu_qua_nhan_vien_bao_cao($boLocBaoCao);
    $topNhanVien = $hieuQuaNhanVien[0] ?? null;
    $nhanKpiTuongTac = ($boLocBaoCao['tu_ngay'] !== '' || $boLocBaoCao['den_ngay'] !== '')
        ? 'Tương tác theo lọc'
        : 'Tương tác 30 ngày';
} catch (Throwable) {
    hien_thi_loi_du_lieu($tieuDe, 'Không thể tải báo cáo. Vui lòng kiểm tra kết nối cơ sở dữ liệu và dữ liệu demo.');
}

require __DIR__ . '/../giao-dien/dau-trang.php';
?>
<div class="page-header">
    <div>
        <p class="eyebrow">Báo cáo quản trị</p>
        <h1 class="page-title">Tổng quan báo cáo</h1>
        <p class="page-subtitle">Các thống kê này chứng minh hệ thống có quản lý, theo dõi và đánh giá vận hành chứ không chỉ thêm-sửa-xóa dữ liệu.</p>
    </div>
</div>

<?php require __DIR__ . '/thanh-dieu-huong-bao-cao.php'; ?>
<?php require __DIR__ . '/bo-loc-bao-cao.php'; ?>

<section class="report-stat-grid">
    <article class="stat-card">
        <div class="stat-label">Khách hàng đang lưu</div>
        <div class="stat-value number"><?= e(number_format($tongQuan['khach_hang'], 0, ',', '.')) ?></div>
        <p class="text-muted mb-0">Không tính hồ sơ đã xóa mềm</p>
    </article>
    <article class="stat-card">
        <div class="stat-label"><?= e($nhanKpiTuongTac) ?></div>
        <div class="stat-value number"><?= e(number_format($tongQuan['tuong_tac_30_ngay'], 0, ',', '.')) ?></div>
        <p class="text-muted mb-0">Dữ liệu từ lịch sử chăm sóc</p>
    </article>
    <article class="stat-card">
        <div class="stat-label">Việc đang mở</div>
        <div class="stat-value number"><?= e(number_format($tongQuan['viec_dang_mo'], 0, ',', '.')) ?></div>
        <p class="text-muted mb-0">Chờ xử lý hoặc đang xử lý</p>
    </article>
    <article class="stat-card">
        <div class="stat-label">Tỷ lệ hoàn thành việc</div>
        <div class="stat-value number"><?= e((string) $tongQuan['ty_le_hoan_thanh_viec']) ?>%</div>
        <div class="report-progress" aria-hidden="true"><span style="width: <?= e((string) $tongQuan['ty_le_hoan_thanh_viec']) ?>%"></span></div>
    </article>
</section>

<section class="report-overview-grid">
    <article class="surface-card report-link-card">
        <p class="eyebrow">Phân nhóm khách hàng</p>
        <h2 class="card-title">Khách hàng theo loại và nhân viên</h2>
        <p class="text-muted">Phù hợp đưa vào trang báo cáo riêng vì cần bảng phân rã để chứng minh phân loại, phân công và trạng thái chăm sóc.</p>
        <div class="report-mini-list">
            <?php foreach (array_slice($theoLoai, 0, 3) as $dong): ?>
                <div><span><?= e($dong['name']) ?></span><strong><?= e((string) $dong['total']) ?></strong></div>
            <?php endforeach; ?>
        </div>
        <a class="btn btn-outline-primary" href="<?= e(duong_dan('bao-cao/khach-hang.php')) ?>">Xem báo cáo khách hàng</a>
    </article>

    <article class="surface-card report-link-card">
        <p class="eyebrow">Công việc theo dõi</p>
        <h2 class="card-title">Trạng thái và quá hạn</h2>
        <p class="text-muted">Dashboard chỉ cần số việc sắp tới/quá hạn; báo cáo riêng cho thấy tỷ trọng trạng thái và hiệu suất xử lý.</p>
        <div class="report-mini-list">
            <?php foreach ($theoTrangThaiViec as $dong): ?>
                <div><span><?= e(nhan_trang_thai_viec_bao_cao()[$dong['status']] ?? $dong['status']) ?></span><strong><?= e((string) $dong['total']) ?></strong></div>
            <?php endforeach; ?>
            <?php if ($theoTrangThaiViec === []): ?>
                <div><span>Chưa có công việc</span><strong>0</strong></div>
            <?php endif; ?>
        </div>
        <a class="btn btn-outline-primary" href="<?= e(duong_dan('bao-cao/cong-viec.php')) ?>">Xem báo cáo công việc</a>
    </article>

    <article class="surface-card report-link-card">
        <p class="eyebrow">Hiệu quả nhân viên</p>
        <h2 class="card-title">Chỉ số vận hành cơ bản</h2>
        <p class="text-muted">Dùng số khách được giao, tương tác, việc hoàn thành và việc quá hạn để tạo câu chuyện quản lý trong demo.</p>
        <?php if ($topNhanVien): ?>
            <div class="report-highlight">
                <span>Nổi bật</span>
                <strong><?= e($topNhanVien['staff_name']) ?></strong>
                <small><?= e((string) $topNhanVien['interaction_count']) ?> tương tác, <?= e((string) $topNhanVien['completed_tasks']) ?> việc hoàn thành</small>
            </div>
        <?php endif; ?>
        <a class="btn btn-outline-primary" href="<?= e(duong_dan('bao-cao/cong-viec.php#hieu-qua-nhan-vien')) ?>">Xem hiệu quả nhân viên</a>
    </article>
</section>
<?php require __DIR__ . '/../giao-dien/cuoi-trang.php'; ?>
