<?php

declare(strict_types=1);

require __DIR__ . '/dung-chung/khoi-dong.php';
require __DIR__ . '/dung-chung/kiem-tra-dang-nhap.php';
require __DIR__ . '/bao-cao/du-lieu-bang-dieu-khien.php';

$duLieuBangDieuKhien = lay_du_lieu_bang_dieu_khien();
$kpiCards = $duLieuBangDieuKhien['kpi'];
$topKhachHang = $duLieuBangDieuKhien['top_khach_hang'];
$congViecSapToi = $duLieuBangDieuKhien['cong_viec_sap_toi'];
$congViecQuaHan = $duLieuBangDieuKhien['cong_viec_qua_han'];
$hoatDongGanDay = $duLieuBangDieuKhien['hoat_dong_gan_day'];

$nhanTrangThaiKhach = nhan_trang_thai_khach_hang();
$nhanTrangThaiViec = nhan_trang_thai_cong_viec();
$nhanUuTien = nhan_uu_tien_cong_viec();

function mau_loai_khach_hang_dashboard_an_toan(?string $mau): string
{
    $mau = (string) $mau;

    return preg_match('/^#[0-9a-fA-F]{6}$/', $mau) ? $mau : '#64748b';
}

$tieuDe = 'Bảng điều khiển';
require __DIR__ . '/giao-dien/dau-trang.php';
?>
<section class="dashboard-page">
    <div class="page-header dashboard-header">
        <div>
            <p class="eyebrow">Tổng quan CRM</p>
            <h1 class="page-title">Việc cần xử lý hôm nay</h1>
            <p class="page-subtitle">Theo dõi khách hàng trọng tâm, việc cần xử lý và hoạt động mới nhất trong một màn hình demo gọn gàng.</p>
        </div>
        <div class="dashboard-scope">
            <span>Phạm vi</span>
            <strong><?= e($duLieuBangDieuKhien['pham_vi']) ?></strong>
            <small><?= e(dinh_dang_ngay(date('Y-m-d'))) ?></small>
        </div>
    </div>

    <div class="dashboard-board">
        <section class="dashboard-kpi-column" aria-label="Chỉ số nhanh">
            <?php foreach ($kpiCards as $kpi): ?>
                <article class="dashboard-kpi-card dashboard-kpi-card--<?= e($kpi['bien_the']) ?>">
                    <span class="dashboard-kpi-label"><?= e($kpi['nhan']) ?></span>
                    <strong class="dashboard-kpi-value number"><?= e(number_format((int) $kpi['gia_tri'], 0, ',', '.')) ?></strong>
                    <span class="dashboard-kpi-note"><?= e($kpi['mo_ta']) ?></span>
                </article>
            <?php endforeach; ?>
        </section>

        <section class="dashboard-centerpiece" aria-labelledby="top-khach-hang-title">
            <div class="dashboard-section-heading dashboard-section-heading--center">
                <div>
                    <p class="eyebrow">Trọng tâm demo</p>
                    <h2 id="top-khach-hang-title">Top 3 khách hàng nổi bật</h2>
                </div>
                <span class="dashboard-soft-label">Điểm chăm sóc</span>
            </div>

            <?php if ($topKhachHang === []): ?>
                <div class="empty-state surface-card">
                    <h3>Chưa có dữ liệu khách hàng</h3>
                    <p class="text-muted mb-0">Import dữ liệu mẫu để hiển thị cụm Top 3 khách hàng.</p>
                </div>
            <?php else: ?>
                <div class="podium-cluster">
                    <?php foreach ($topKhachHang as $viTri => $khachHang): ?>
                        <?php
                        $hang = $viTri + 1;
                        $mauLoai = mau_loai_khach_hang_dashboard_an_toan($khachHang['customer_type_color'] ?? null);
                        ?>
                        <article class="podium-card podium-card--rank-<?= e((string) $hang) ?>" style="--customer-type-color: <?= e($mauLoai) ?>">
                            <div class="podium-card-topline">
                                <span class="podium-rank">#<?= e((string) $hang) ?></span>
                                <span class="customer-type-pill"><span></span><?= e($khachHang['customer_type_name']) ?></span>
                            </div>
                            <h3><?= e($khachHang['full_name']) ?></h3>
                            <p class="podium-company"><?= e($khachHang['company_name'] ?: $khachHang['city'] ?: 'Khách hàng cá nhân') ?></p>
                            <div class="podium-score number"><?= e(number_format((float) $khachHang['care_score'], 0, ',', '.')) ?></div>
                            <dl class="podium-meta">
                                <div>
                                    <dt>Phụ trách</dt>
                                    <dd><?= e($khachHang['assigned_user_name'] ?: 'Chưa phân công') ?></dd>
                                </div>
                                <div>
                                    <dt>Tương tác</dt>
                                    <dd><?= e((string) $khachHang['interaction_count']) ?> lần</dd>
                                </div>
                                <div>
                                    <dt>Trạng thái</dt>
                                    <dd><?= e($nhanTrangThaiKhach[$khachHang['status']] ?? 'Không rõ') ?></dd>
                                </div>
                                <div>
                                    <dt>Lịch tới</dt>
                                    <dd><?= e(dinh_dang_ngay_gio($khachHang['next_task_due_at'] ?? null) ?: 'Chưa có') ?></dd>
                                </div>
                            </dl>
                            <div class="podium-base" aria-hidden="true"></div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section class="dashboard-panel dashboard-overdue" aria-labelledby="viec-qua-han-title">
            <div class="dashboard-panel-header">
                <h2 id="viec-qua-han-title">Việc quá hạn <span class="badge badge-soft-danger"><?= e((string) count($congViecQuaHan)) ?></span></h2>
                <a href="<?= e(duong_dan('cong-viec-theo-doi/qua-han.php')) ?>">Xử lý</a>
            </div>
            <div class="dashboard-list">
                <?php foreach ($congViecQuaHan as $congViec): ?>
                    <article class="dashboard-list-row dashboard-list-row--urgent">
                        <div>
                            <h3><?= e($congViec['title']) ?></h3>
                            <p><?= e($congViec['customer_name']) ?> · <?= e($nhanTrangThaiViec[$congViec['status']] ?? 'Chờ xử lý') ?></p>
                        </div>
                        <div class="dashboard-list-meta">
                            <span class="badge <?= e(lop_badge_uu_tien_cong_viec($congViec['priority'])) ?>"><?= e($nhanUuTien[$congViec['priority']] ?? 'Vừa') ?></span>
                            <time><?= e(dinh_dang_ngay_gio($congViec['due_at'])) ?></time>
                        </div>
                    </article>
                <?php endforeach; ?>
                <?php if ($congViecQuaHan === []): ?>
                    <div class="empty-state-inline">
                        <strong>Không có việc quá hạn</strong>
                        <p>Danh sách chăm sóc đang đúng tiến độ.</p>
                        <a class="btn btn-sm btn-outline-primary" href="<?= e(duong_dan('cong-viec-theo-doi/')) ?>">Xem công việc</a>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="dashboard-panel dashboard-upcoming" aria-labelledby="viec-sap-toi-title">
            <div class="dashboard-panel-header">
                <h2 id="viec-sap-toi-title">Việc sắp tới <span class="badge badge-soft-primary"><?= e((string) count($congViecSapToi)) ?></span></h2>
                <a href="<?= e(duong_dan('cong-viec-theo-doi/sap-toi.php')) ?>">Xem tất cả</a>
            </div>
            <div class="dashboard-list">
                <?php foreach ($congViecSapToi as $congViec): ?>
                    <article class="dashboard-list-row">
                        <div>
                            <h3><?= e($congViec['title']) ?></h3>
                            <p><?= e($congViec['customer_name']) ?> · <?= e($congViec['assigned_user_name']) ?></p>
                        </div>
                        <div class="dashboard-list-meta">
                            <span class="badge <?= e(lop_badge_uu_tien_cong_viec($congViec['priority'])) ?>"><?= e($nhanUuTien[$congViec['priority']] ?? 'Vừa') ?></span>
                            <time><?= e(dinh_dang_ngay_gio($congViec['due_at'])) ?></time>
                        </div>
                    </article>
                <?php endforeach; ?>
                <?php if ($congViecSapToi === []): ?>
                    <div class="empty-state-inline">
                        <strong>Chưa có việc sắp tới</strong>
                        <p>Tạo công việc theo dõi để có lịch chăm sóc rõ ràng.</p>
                        <a class="btn btn-sm btn-outline-primary" href="<?= e(duong_dan('cong-viec-theo-doi/them.php')) ?>">Tạo công việc</a>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="dashboard-panel dashboard-activity" aria-labelledby="hoat-dong-title">
            <div class="dashboard-panel-header">
                <h2 id="hoat-dong-title">Hoạt động gần đây</h2>
                <a href="<?= e(duong_dan('tuong-tac/')) ?>">Chi tiết</a>
            </div>
            <div class="dashboard-timeline">
                <?php foreach ($hoatDongGanDay as $hoatDong): ?>
                    <article class="dashboard-timeline-item">
                        <span class="timeline-dot" aria-hidden="true"></span>
                        <div>
                            <h3><?= e($hoatDong['title']) ?></h3>
                            <p><?= e(nhan_loai_tuong_tac_hien_thi($hoatDong['interaction_type'])) ?> với <?= e($hoatDong['customer_name']) ?></p>
                            <time><?= e(dinh_dang_ngay_gio($hoatDong['interaction_at'])) ?> · <?= e($hoatDong['user_name']) ?></time>
                        </div>
                    </article>
                <?php endforeach; ?>
                <?php if ($hoatDongGanDay === []): ?>
                    <div class="empty-state-inline">
                        <strong>Chưa có hoạt động gần đây</strong>
                        <p>Ghi nhận tương tác để timeline chăm sóc có dữ liệu.</p>
                        <a class="btn btn-sm btn-outline-primary" href="<?= e(duong_dan('tuong-tac/them.php')) ?>">Thêm tương tác</a>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</section>
<?php require __DIR__ . '/giao-dien/cuoi-trang.php'; ?>
