<?php

declare(strict_types=1);

require __DIR__ . '/../dung-chung/khoi-dong.php';
require __DIR__ . '/../dung-chung/kiem-tra-dang-nhap.php';
require __DIR__ . '/ham-tuong-tac.php';

$maKhachHang = max(0, (int) gia_tri_get('customer_id', 0));
$danhSachKhachHang = lay_lua_chon_khach_hang_tuong_tac($maKhachHang ?: null);
$danhSachTuongTac = lay_danh_sach_tuong_tac($maKhachHang, 60);
$tieuDe = 'Tương tác';

require __DIR__ . '/../giao-dien/dau-trang.php';
?>
<div class="page-header">
    <div>
        <p class="eyebrow">Lịch sử chăm sóc</p>
        <h1 class="page-title">Tương tác khách hàng</h1>
        <p class="page-subtitle">Dòng thời gian ghi lại cuộc gọi, email, gặp mặt, chat và kết quả chăm sóc.</p>
    </div>
    <a class="btn btn-primary" href="<?= e(duong_dan('tuong-tac/them.php' . ($maKhachHang ? '?customer_id=' . $maKhachHang : ''))) ?>">Thêm tương tác</a>
</div>

<form class="surface-card interaction-filter" method="get">
    <div class="row g-3 align-items-end">
        <div class="col-lg-8">
            <label class="form-label" for="customer_id">Xem theo khách hàng</label>
            <select class="form-select" id="customer_id" name="customer_id">
                <option value="">Tất cả khách hàng đang hoạt động</option>
                <?php foreach ($danhSachKhachHang as $khachHang): ?>
                    <option value="<?= e($khachHang['id']) ?>" <?= $maKhachHang === (int) $khachHang['id'] ? 'selected' : '' ?>>
                        <?= e($khachHang['full_name']) ?><?= $khachHang['company_name'] ? ' - ' . e($khachHang['company_name']) : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-lg-4 d-grid">
            <button class="btn btn-primary" type="submit">Lọc lịch sử</button>
        </div>
    </div>
</form>

<section class="interaction-timeline surface-card">
    <?php foreach ($danhSachTuongTac as $tuongTac): ?>
        <article class="interaction-timeline-item">
            <div class="interaction-marker" aria-hidden="true"></div>
            <div class="interaction-card">
                <div class="interaction-card-header">
                    <div>
                        <span class="badge <?= e(lop_badge_loai_tuong_tac($tuongTac['interaction_type'])) ?>">
                            <?= e(nhan_loai_tuong_tac_hien_thi($tuongTac['interaction_type'])) ?>
                        </span>
                        <h2><?= e($tuongTac['title']) ?></h2>
                        <p><?= e($tuongTac['customer_name']) ?> · <?= e($tuongTac['user_name']) ?></p>
                    </div>
                    <time><?= e(dinh_dang_ngay_gio($tuongTac['interaction_at'])) ?></time>
                </div>
                <p class="interaction-content"><?= nl2br(e($tuongTac['content'])) ?></p>
                <?php if ($tuongTac['result']): ?>
                    <p class="interaction-result"><strong>Kết quả:</strong> <?= e($tuongTac['result']) ?></p>
                <?php endif; ?>
                <?php if (co_the_sua_xoa_tuong_tac($tuongTac)): ?>
                    <div class="interaction-actions">
                        <a class="btn btn-sm btn-outline-secondary" href="<?= e(duong_dan('tuong-tac/sua.php?id=' . $tuongTac['id'])) ?>">Sửa</a>
                        <form action="<?= e(duong_dan('tuong-tac/xoa.php')) ?>" method="post">
                            <?= csrf_input() ?>
                            <input type="hidden" name="id" value="<?= e($tuongTac['id']) ?>">
                            <button class="btn btn-sm btn-outline-danger" data-confirm-message="Xóa tương tác này khỏi lịch sử?" type="submit">Xóa</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </article>
    <?php endforeach; ?>
    <?php if ($danhSachTuongTac === []): ?>
        <div class="empty-state-inline">
            <strong>Chưa có tương tác phù hợp</strong>
            <p>Hãy ghi nhận cuộc gọi, email hoặc buổi gặp để hồ sơ chăm sóc có lịch sử rõ ràng.</p>
        </div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/../giao-dien/cuoi-trang.php'; ?>
