<?php

declare(strict_types=1);

require __DIR__ . '/../dung-chung/khoi-dong.php';
require __DIR__ . '/../dung-chung/kiem-tra-dang-nhap.php';
require __DIR__ . '/ham-khach-hang.php';

$boLoc = lay_bo_loc_khach_hang();
$tongDong = dem_khach_hang_theo_bo_loc($boLoc);
$phanTrang = tinh_phan_trang($tongDong, lay_trang_hien_tai(), 8);
$danhSachKhachHang = lay_danh_sach_khach_hang($boLoc, $phanTrang);
$danhSachLoai = lay_lua_chon_loai_khach_hang();
$danhSachNhanVien = lay_lua_chon_nhan_vien();
$tieuDe = 'Khách hàng';

$taoLienKetTrang = static function (int $trang) use ($boLoc): string {
    return duong_dan('khach-hang/?' . http_build_query(array_filter([
        'tu_khoa' => $boLoc['tu_khoa'],
        'customer_type_id' => $boLoc['customer_type_id'] ?: null,
        'assigned_user_id' => $boLoc['assigned_user_id'] ?: null,
        'status' => $boLoc['status'] ?: null,
        'trang' => $trang,
    ], static fn ($giaTri) => $giaTri !== null && $giaTri !== '')));
};

require __DIR__ . '/../giao-dien/dau-trang.php';
?>
<div class="page-header">
    <div>
        <p class="eyebrow">Chăm sóc khách hàng</p>
        <h1 class="page-title">Khách hàng</h1>
        <p class="page-subtitle">Tìm kiếm, lọc, phân công phụ trách và theo dõi trạng thái chăm sóc từng khách hàng.</p>
    </div>
    <a class="btn btn-primary" href="<?= e(duong_dan('khach-hang/them.php')) ?>">Thêm khách hàng</a>
</div>

<form class="surface-card customer-filter-bar" data-customer-filter-form method="get">
    <div class="row g-3 align-items-end">
        <div class="col-lg-4">
            <label class="form-label" for="tu_khoa">Tìm kiếm</label>
            <input class="form-control" id="tu_khoa" name="tu_khoa" placeholder="Họ tên, điện thoại, email" value="<?= e($boLoc['tu_khoa']) ?>">
        </div>
        <div class="col-md-6 col-lg-2">
            <label class="form-label" for="customer_type_id">Loại</label>
            <select class="form-select" id="customer_type_id" name="customer_type_id">
                <option value="">Tất cả</option>
                <?php foreach ($danhSachLoai as $loai): ?>
                    <option value="<?= e($loai['id']) ?>" <?= $boLoc['customer_type_id'] === (int) $loai['id'] ? 'selected' : '' ?>><?= e($loai['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6 col-lg-2">
            <label class="form-label" for="assigned_user_id">Phụ trách</label>
            <select class="form-select" id="assigned_user_id" name="assigned_user_id">
                <option value="">Tất cả</option>
                <?php foreach ($danhSachNhanVien as $nhanVien): ?>
                    <option value="<?= e($nhanVien['id']) ?>" <?= $boLoc['assigned_user_id'] === (int) $nhanVien['id'] ? 'selected' : '' ?>><?= e($nhanVien['full_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6 col-lg-2">
            <label class="form-label" for="status">Trạng thái</label>
            <select class="form-select" id="status" name="status">
                <option value="">Đang hiển thị</option>
                <?php foreach (nhan_trang_thai_khach_hang() as $giaTri => $nhan): ?>
                    <option value="<?= e($giaTri) ?>" <?= $boLoc['status'] === $giaTri ? 'selected' : '' ?>><?= e($nhan) ?></option>
                <?php endforeach; ?>
                <option value="da_xoa" <?= $boLoc['status'] === 'da_xoa' ? 'selected' : '' ?>>Đã xóa mềm</option>
            </select>
        </div>
        <div class="col-md-6 col-lg-2 d-grid">
            <button class="btn btn-primary" type="submit">Lọc danh sách</button>
        </div>
    </div>
</form>

<section class="table-card" data-customer-list-region>
    <div class="customer-list-summary">
        <strong data-customer-total><?= e((string) $tongDong) ?></strong>
        <span>hồ sơ phù hợp</span>
        <span class="customer-ajax-status" data-customer-ajax-status aria-live="polite"></span>
    </div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Khách hàng</th>
                    <th>Liên hệ</th>
                    <th>Loại</th>
                    <th>Phụ trách</th>
                    <th>Trạng thái</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody data-customer-table-body>
                <?php foreach ($danhSachKhachHang as $khachHang): ?>
                    <tr>
                        <td>
                            <div class="customer-name-cell">
                                <strong><?= e($khachHang['full_name']) ?></strong>
                                <span><?= e($khachHang['company_name'] ?: $khachHang['city'] ?: 'Khách hàng cá nhân') ?></span>
                                <small><?= e((string) $khachHang['interaction_count']) ?> tương tác · Lịch tới: <?= e(dinh_dang_ngay_gio($khachHang['next_task_due_at']) ?: 'chưa có') ?></small>
                            </div>
                        </td>
                        <td>
                            <div class="customer-contact-cell">
                                <span><?= e($khachHang['phone'] ?: 'Chưa có SĐT') ?></span>
                                <span><?= e($khachHang['email'] ?: 'Chưa có email') ?></span>
                            </div>
                        </td>
                        <td>
                            <span class="customer-type-chip" style="--type-color: <?= e(mau_loai_khach_hang_khach_an_toan($khachHang['customer_type_color'])) ?>">
                                <?= e($khachHang['customer_type_name']) ?>
                            </span>
                        </td>
                        <td><?= e($khachHang['assigned_user_name'] ?: 'Chưa phân công') ?></td>
                        <td>
                            <span class="badge <?= e(lop_badge_trang_thai_khach_hang($khachHang['status'], $khachHang['deleted_at'])) ?>">
                                <?= e(nhan_hien_thi_trang_thai_khach_hang($khachHang['status'], $khachHang['deleted_at'])) ?>
                            </span>
                        </td>
                        <td>
                            <div class="customer-row-actions">
                                <a class="btn btn-sm btn-outline-primary" href="<?= e(duong_dan('khach-hang/chi-tiet.php?id=' . $khachHang['id'])) ?>">Xem</a>
                                <?php if ($khachHang['deleted_at']): ?>
                                    <form action="<?= e(duong_dan('khach-hang/khoi-phuc.php')) ?>" method="post">
                                        <input type="hidden" name="id" value="<?= e($khachHang['id']) ?>">
                                        <button class="btn btn-sm btn-outline-success" type="submit">Khôi phục</button>
                                    </form>
                                <?php else: ?>
                                    <a class="btn btn-sm btn-outline-secondary" href="<?= e(duong_dan('khach-hang/sua.php?id=' . $khachHang['id'])) ?>">Sửa</a>
                                    <form action="<?= e(duong_dan('khach-hang/xoa-mem.php')) ?>" method="post">
                                        <input type="hidden" name="id" value="<?= e($khachHang['id']) ?>">
                                        <button class="btn btn-sm btn-outline-danger" data-confirm-message="Xóa mềm khách hàng này khỏi danh sách đang chăm sóc?" type="submit">Xóa mềm</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($danhSachKhachHang === []): ?>
                    <tr><td class="text-center text-muted py-5" colspan="6">Không tìm thấy khách hàng phù hợp.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<nav class="customer-pagination" data-customer-pagination aria-label="Phân trang khách hàng">
    <a class="btn btn-outline-primary <?= $phanTrang['co_trang_truoc'] ? '' : 'disabled' ?>" href="<?= e($taoLienKetTrang($phanTrang['trang'] - 1)) ?>">Trước</a>
    <span>Trang <?= e((string) $phanTrang['trang']) ?>/<?= e((string) $phanTrang['tong_trang']) ?></span>
    <a class="btn btn-outline-primary <?= $phanTrang['co_trang_sau'] ? '' : 'disabled' ?>" href="<?= e($taoLienKetTrang($phanTrang['trang'] + 1)) ?>">Sau</a>
</nav>
<?php require __DIR__ . '/../giao-dien/cuoi-trang.php'; ?>
