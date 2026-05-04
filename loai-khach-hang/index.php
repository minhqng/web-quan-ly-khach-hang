<?php

declare(strict_types=1);

require __DIR__ . '/../dung-chung/khoi-dong.php';
require __DIR__ . '/../dung-chung/kiem-tra-quyen-admin.php';
require __DIR__ . '/ham-loai-khach-hang.php';

$danhSachLoai = lay_danh_sach_loai_khach_hang();
$tieuDe = 'Loại khách hàng';

require __DIR__ . '/../giao-dien/dau-trang.php';
?>
<div class="page-header">
    <div>
        <p class="eyebrow">Quản trị dữ liệu</p>
        <h1 class="page-title">Loại khách hàng</h1>
        <p class="page-subtitle">Phân nhóm khách hàng, đặt màu hiển thị và điểm ưu tiên cho dashboard.</p>
    </div>
    <a class="btn btn-primary" href="<?= e(duong_dan('loai-khach-hang/them.php')) ?>">Thêm loại khách hàng</a>
</div>

<section class="table-card">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Loại khách hàng</th>
                    <th>Điểm</th>
                    <th>Trạng thái</th>
                    <th>Số khách</th>
                    <th>Cập nhật</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($danhSachLoai as $loai): ?>
                    <?php
                    $soKhach = (int) $loai['customer_count'];
                    $dangHoatDong = (int) $loai['is_active'] === 1;
                    $nhanNutXoa = $soKhach > 0 ? 'Ngừng dùng' : 'Xóa';
                    $noiDungXacNhan = $soKhach > 0
                        ? 'Loại này đang có khách hàng sử dụng. Hệ thống sẽ ngừng dùng thay vì xóa. Tiếp tục?'
                        : 'Bạn có chắc muốn xóa loại khách hàng này?';
                    ?>
                    <tr>
                        <td>
                            <div class="type-name-cell">
                                <span
                                    class="type-color-swatch"
                                    style="--type-color: <?= e(mau_loai_khach_hang_an_toan($loai['color'])) ?>"
                                    aria-hidden="true"
                                ></span>
                                <div>
                                    <strong><?= e($loai['name']) ?></strong>
                                    <p><?= e($loai['description'] ?: 'Chưa có mô tả') ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="number fw-semibold"><?= e((string) $loai['priority_score']) ?></td>
                        <td>
                            <?php if ($dangHoatDong): ?>
                                <span class="badge badge-soft-success">Đang dùng</span>
                            <?php else: ?>
                                <span class="badge badge-soft-warning">Ngừng dùng</span>
                            <?php endif; ?>
                        </td>
                        <td class="number"><?= e((string) $soKhach) ?></td>
                        <td><?= e(dinh_dang_ngay_gio($loai['updated_at'])) ?></td>
                        <td>
                            <div class="type-actions">
                                <a class="btn btn-sm btn-outline-primary" href="<?= e(duong_dan('loai-khach-hang/sua.php?id=' . $loai['id'])) ?>">Sửa</a>
                                <?php if ($soKhach === 0 || $dangHoatDong): ?>
                                    <form method="post" action="<?= e(duong_dan('loai-khach-hang/xoa.php')) ?>">
                                        <?= csrf_input() ?>
                                        <input type="hidden" name="id" value="<?= e($loai['id']) ?>">
                                        <button
                                            class="btn btn-sm btn-outline-danger"
                                            data-confirm-message="<?= e($noiDungXacNhan) ?>"
                                            type="submit"
                                        ><?= e($nhanNutXoa) ?></button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-outline-secondary" disabled>Đang tham chiếu</button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($danhSachLoai === []): ?>
                    <tr>
                        <td class="table-empty-state text-center text-muted py-5" colspan="6">Chưa có loại khách hàng nào. Hãy tạo loại đầu tiên để phân nhóm khách hàng.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require __DIR__ . '/../giao-dien/cuoi-trang.php'; ?>
