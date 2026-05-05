<?php

declare(strict_types=1);

require __DIR__ . '/../dung-chung/khoi-dong.php';
require __DIR__ . '/../dung-chung/kiem-tra-dang-nhap.php';
require __DIR__ . '/ham-khach-hang.php';
require __DIR__ . '/../tuong-tac/ham-tuong-tac.php';

$id = max(0, (int) gia_tri_get('id', 0));
$khachHang = $id > 0 ? lay_chi_tiet_khach_hang($id) : null;

if (!$khachHang) {
    thong_bao_loi('Không tìm thấy hồ sơ khách hàng.');
    chuyen_huong('khach-hang/');
}

$tuongTacGanDay = lay_tuong_tac_cua_khach_hang_day_du($id, 8, !empty($khachHang['deleted_at']));
$congViecTheoDoi = lay_cong_viec_cua_khach_hang($id);
$nhanCongViec = ['pending' => 'Chờ xử lý', 'in_progress' => 'Đang xử lý', 'completed' => 'Hoàn thành', 'cancelled' => 'Đã hủy'];
$lopCongViec = ['pending' => 'badge-soft-warning', 'in_progress' => 'badge-soft-primary', 'completed' => 'badge-soft-success', 'cancelled' => 'badge-soft-danger'];
$tieuDe = 'Chi tiết khách hàng';

require __DIR__ . '/../giao-dien/dau-trang.php';
?>
<section class="customer-profile-page">
    <div class="customer-profile-hero" style="--type-color: <?= e(mau_loai_khach_hang_khach_an_toan($khachHang['customer_type_color'])) ?>">
        <div>
            <p class="eyebrow">Hồ sơ khách hàng</p>
            <h1><?= e($khachHang['full_name']) ?></h1>
            <p><?= e($khachHang['company_name'] ?: $khachHang['city'] ?: 'Khách hàng cá nhân') ?></p>
            <div class="customer-profile-badges">
                <span class="customer-type-chip"><?= e($khachHang['customer_type_name']) ?></span>
                <span class="badge <?= e(lop_badge_trang_thai_khach_hang($khachHang['status'], $khachHang['deleted_at'])) ?>">
                    <?= e(nhan_hien_thi_trang_thai_khach_hang($khachHang['status'], $khachHang['deleted_at'])) ?>
                </span>
            </div>
        </div>
        <div class="customer-profile-actions">
            <a class="btn btn-outline-primary" href="<?= e(duong_dan('khach-hang/')) ?>">Quay lại</a>
            <?php if ($khachHang['deleted_at'] && la_admin()): ?>
                <form action="<?= e(duong_dan('khach-hang/khoi-phuc.php')) ?>" method="post">
                    <?= csrf_input() ?>
                    <input type="hidden" name="id" value="<?= e((string) $id) ?>">
                    <button class="btn btn-success" type="submit">Khôi phục</button>
                </form>
            <?php elseif (!$khachHang['deleted_at']): ?>
                <a class="btn btn-outline-primary" href="<?= e(duong_dan('tuong-tac/them.php?customer_id=' . $id)) ?>">Thêm tương tác</a>
                <a class="btn btn-primary" href="<?= e(duong_dan('khach-hang/sua.php?id=' . $id)) ?>">Sửa hồ sơ</a>
                <?php if (la_admin()): ?>
                    <form action="<?= e(duong_dan('khach-hang/xoa-mem.php')) ?>" method="post">
                        <?= csrf_input() ?>
                        <input type="hidden" name="id" value="<?= e((string) $id) ?>">
                        <button class="btn btn-outline-danger" data-confirm-message="Xóa mềm khách hàng này khỏi danh sách đang chăm sóc?" type="submit">Xóa mềm</button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="customer-profile-grid">
        <article class="surface-card customer-info-card">
            <h2 class="card-title">Thông tin quản lý</h2>
            <dl class="customer-definition-list">
                <div><dt>Nhân viên phụ trách</dt><dd><?= e($khachHang['assigned_user_name'] ?: 'Chưa phân công') ?></dd></div>
                <div><dt>Email nhân viên</dt><dd><?= e($khachHang['assigned_user_email'] ?: 'Chưa có') ?></dd></div>
                <div><dt>Nguồn khách</dt><dd><?= e(nhan_nguon_khach_hang()[$khachHang['source']] ?? 'Khác') ?></dd></div>
                <div><dt>Ngày tạo</dt><dd><?= e(dinh_dang_ngay_gio($khachHang['created_at'])) ?></dd></div>
                <div><dt>Cập nhật</dt><dd><?= e(dinh_dang_ngay_gio($khachHang['updated_at'])) ?></dd></div>
                <div><dt>Xóa mềm</dt><dd><?= e(dinh_dang_ngay_gio($khachHang['deleted_at']) ?: 'Không') ?></dd></div>
            </dl>
        </article>

        <article class="surface-card customer-info-card">
            <h2 class="card-title">Liên hệ</h2>
            <dl class="customer-definition-list">
                <div><dt>Điện thoại</dt><dd><?= e($khachHang['phone'] ?: 'Chưa có') ?></dd></div>
                <div><dt>Email</dt><dd><?= e($khachHang['email'] ?: 'Chưa có') ?></dd></div>
                <div><dt>Địa chỉ</dt><dd><?= e($khachHang['address'] ?: 'Chưa có') ?></dd></div>
                <div><dt>Tỉnh/thành</dt><dd><?= e($khachHang['city'] ?: 'Chưa có') ?></dd></div>
                <div><dt>Giới tính</dt><dd><?= e(nhan_gioi_tinh_khach_hang()[$khachHang['gender']] ?? 'Chưa xác định') ?></dd></div>
                <div><dt>Ngày sinh</dt><dd><?= e(dinh_dang_ngay($khachHang['date_of_birth']) ?: 'Chưa có') ?></dd></div>
            </dl>
        </article>

        <article class="surface-card customer-metric-card">
            <span class="number"><?= e((string) $khachHang['interaction_count']) ?></span>
            <strong>Tương tác đã ghi nhận</strong>
            <p class="text-muted mb-0">Lịch sử chăm sóc giúp hồ sơ không chỉ là dữ liệu tĩnh.</p>
        </article>

        <article class="surface-card customer-metric-card">
            <span class="number"><?= e((string) $khachHang['open_task_count']) ?></span>
            <strong>Việc đang mở</strong>
            <p class="text-muted mb-0">Lịch tiếp theo: <?= e(dinh_dang_ngay_gio($khachHang['next_task_due_at']) ?: 'chưa có') ?></p>
        </article>
    </div>

    <div class="customer-ops-grid">
        <section class="surface-card">
            <div class="interaction-section-heading">
                <h2 class="card-title">Tương tác gần đây</h2>
                <?php if (!$khachHang['deleted_at']): ?>
                    <a class="btn btn-sm btn-outline-primary" href="<?= e(duong_dan('tuong-tac/them.php?customer_id=' . $id)) ?>">Ghi nhận</a>
                <?php endif; ?>
            </div>
            <div class="customer-interaction-feed">
                <?php foreach ($tuongTacGanDay as $tuongTac): ?>
                    <article>
                        <span class="interaction-dot" aria-hidden="true"></span>
                        <div>
                            <div class="interaction-feed-title">
                                <strong><?= e($tuongTac['title']) ?></strong>
                                <span class="badge <?= e(lop_badge_loai_tuong_tac($tuongTac['interaction_type'])) ?>">
                                    <?= e(nhan_loai_tuong_tac_hien_thi($tuongTac['interaction_type'])) ?>
                                </span>
                            </div>
                            <p><?= nl2br(e($tuongTac['content'])) ?></p>
                            <?php if ($tuongTac['result']): ?>
                                <p class="interaction-result"><strong>Kết quả:</strong> <?= e($tuongTac['result']) ?></p>
                            <?php endif; ?>
                            <time><?= e(dinh_dang_ngay_gio($tuongTac['interaction_at'])) ?> · <?= e($tuongTac['user_name']) ?></time>
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
                <?php if ($tuongTacGanDay === []): ?>
                    <div class="empty-state-inline"><strong>Chưa có tương tác</strong><p>Ghi nhận trao đổi đầu tiên để hồ sơ này có lịch sử chăm sóc.</p></div>
                <?php endif; ?>
            </div>
        </section>

        <section class="surface-card">
            <h2 class="card-title">Công việc theo dõi</h2>
            <div class="customer-mini-list">
                <?php foreach ($congViecTheoDoi as $congViec): ?>
                    <article>
                        <strong><?= e($congViec['title']) ?></strong>
                        <span><?= e($congViec['assigned_user_name']) ?> · <?= e(dinh_dang_ngay_gio($congViec['due_at'])) ?></span>
                        <span class="badge <?= e($lopCongViec[$congViec['status']] ?? 'badge-soft-primary') ?>"><?= e($nhanCongViec[$congViec['status']] ?? 'Không rõ') ?></span>
                    </article>
                <?php endforeach; ?>
                <?php if ($congViecTheoDoi === []): ?>
                    <div class="empty-state-inline"><strong>Chưa có công việc theo dõi</strong><p>Tạo việc sau tương tác để không bỏ sót bước chăm sóc tiếp theo.</p></div>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <?php if ($khachHang['notes']): ?>
        <section class="surface-card">
            <h2 class="card-title">Ghi chú chăm sóc</h2>
            <p class="mb-0"><?= nl2br(e($khachHang['notes'])) ?></p>
        </section>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/../giao-dien/cuoi-trang.php'; ?>
