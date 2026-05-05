<?php

$boLoc = $boLoc ?? lay_bo_loc_khach_hang();
$danhSachKhachHang = $danhSachKhachHang ?? [];
$danhSachLoai = $danhSachLoai ?? [];
$danhSachNhanVien = $danhSachNhanVien ?? [];
$tongDong = (int) ($tongDong ?? 0);
?>
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
            <select class="form-select" id="assigned_user_id" name="assigned_user_id" <?= la_admin() ? '' : 'disabled' ?>>
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
                <?php if (la_admin()): ?>
                    <option value="da_xoa" <?= $boLoc['status'] === 'da_xoa' ? 'selected' : '' ?>>Đã xóa mềm</option>
                <?php endif; ?>
            </select>
        </div>
        <div class="col-md-6 col-lg-2 d-grid">
            <button class="btn btn-primary" type="submit">Lọc danh sách</button>
        </div>
    </div>
</form>

<section class="table-card" data-customer-list-region>
    <div class="customer-list-summary table-panel-heading">
        <div>
            <strong data-customer-total><?= e((string) $tongDong) ?></strong>
            <span>hồ sơ phù hợp</span>
        </div>
        <span class="customer-ajax-status" data-customer-ajax-status aria-live="polite"></span>
    </div>
    <div class="table-responsive">
        <table class="table align-middle customer-table">
            <colgroup>
                <col class="customer-table-col-main">
                <col class="customer-table-col-contact">
                <col class="customer-table-col-type">
                <col class="customer-table-col-staff">
                <col class="customer-table-col-status">
                <col class="customer-table-col-actions">
            </colgroup>
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
                    <?php $lichToi = dinh_dang_ngay_gio($khachHang['next_task_due_at']) ?: 'Chưa có lịch'; ?>
                    <tr>
                        <td>
                            <div class="customer-name-cell">
                                <a class="customer-main-link" href="<?= e(duong_dan('khach-hang/chi-tiet.php?id=' . $khachHang['id'])) ?>"><?= e($khachHang['full_name']) ?></a>
                                <span><?= e($khachHang['company_name'] ?: $khachHang['city'] ?: 'Khách hàng cá nhân') ?></span>
                                <small><?= e((string) $khachHang['interaction_count']) ?> tương tác</small>
                                <span class="customer-due-badge"><?= e($lichToi) ?></span>
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
                            <div class="customer-row-actions row-actions justify-content-end">
                                <a class="btn btn-sm btn-outline-secondary" href="<?= e(duong_dan('khach-hang/chi-tiet.php?id=' . $khachHang['id'])) ?>">Xem</a>
                                <?php if ($khachHang['deleted_at'] && la_admin()): ?>
                                    <form action="<?= e(duong_dan('khach-hang/khoi-phuc.php')) ?>" method="post">
                                        <?= csrf_input() ?>
                                        <input type="hidden" name="id" value="<?= e($khachHang['id']) ?>">
                                        <button class="btn btn-sm btn-outline-success" type="submit">Khôi phục</button>
                                    </form>
                                <?php elseif (!$khachHang['deleted_at']): ?>
                                    <a class="btn btn-sm btn-outline-secondary" href="<?= e(duong_dan('khach-hang/sua.php?id=' . $khachHang['id'])) ?>">Sửa</a>
                                    <?php if (la_admin()): ?>
                                        <form action="<?= e(duong_dan('khach-hang/xoa-mem.php')) ?>" method="post">
                                            <?= csrf_input() ?>
                                            <input type="hidden" name="id" value="<?= e($khachHang['id']) ?>">
                                            <button class="btn btn-sm btn-outline-danger" data-confirm-message="Xóa mềm khách hàng này khỏi danh sách đang chăm sóc?" type="submit">Xóa mềm</button>
                                        </form>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($danhSachKhachHang === []): ?>
                    <tr>
                        <td class="table-empty-state" colspan="6">
                            <div class="empty-state-inline mx-auto">
                                <strong>Không tìm thấy khách hàng phù hợp</strong>
                                <p>Thử đổi từ khóa, bộ lọc hoặc tạo hồ sơ mới nếu đây là khách hàng mới.</p>
                                <div class="row-actions justify-content-center">
                                    <a class="btn btn-sm btn-outline-secondary" href="<?= e(duong_dan('khach-hang/')) ?>">Đổi bộ lọc</a>
                                    <a class="btn btn-sm btn-primary" href="<?= e(duong_dan('khach-hang/them.php')) ?>">Thêm khách hàng</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<nav class="customer-pagination" data-customer-pagination aria-label="Phân trang khách hàng">
    <a class="btn btn-outline-primary <?= $phanTrang['co_trang_truoc'] ? '' : 'disabled' ?>" data-page="<?= e((string) ($phanTrang['trang'] - 1)) ?>" href="<?= e($taoLienKetTrang($phanTrang['trang'] - 1)) ?>">Trước</a>
    <span>Trang <?= e((string) $phanTrang['trang']) ?>/<?= e((string) $phanTrang['tong_trang']) ?></span>
    <a class="btn btn-outline-primary <?= $phanTrang['co_trang_sau'] ? '' : 'disabled' ?>" data-page="<?= e((string) ($phanTrang['trang'] + 1)) ?>" href="<?= e($taoLienKetTrang($phanTrang['trang'] + 1)) ?>">Sau</a>
</nav>
