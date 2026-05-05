<?php

$boLocBaoCao = $boLocBaoCao ?? lay_bo_loc_bao_cao();
$danhSachNhanVienBaoCao = $danhSachNhanVienBaoCao ?? lay_lua_chon_nhan_vien_bao_cao();
$danhSachLoaiBaoCao = $danhSachLoaiBaoCao ?? lay_lua_chon_loai_khach_hang_bao_cao();
?>
<form class="surface-card report-filter" method="get">
    <div class="row g-3 align-items-end">
        <div class="col-md-6 col-lg-2">
            <label class="form-label" for="tu_ngay">Từ ngày</label>
            <input class="form-control" id="tu_ngay" name="tu_ngay" type="date" value="<?= e($boLocBaoCao['tu_ngay']) ?>">
        </div>
        <div class="col-md-6 col-lg-2">
            <label class="form-label" for="den_ngay">Đến ngày</label>
            <input class="form-control" id="den_ngay" name="den_ngay" type="date" value="<?= e($boLocBaoCao['den_ngay']) ?>">
        </div>
        <div class="col-md-6 col-lg-3">
            <label class="form-label" for="staff_id">Nhân viên phụ trách</label>
            <select class="form-select" id="staff_id" name="staff_id" <?= la_admin() ? '' : 'disabled' ?>>
                <option value="">Tất cả nhân viên</option>
                <?php foreach ($danhSachNhanVienBaoCao as $nhanVien): ?>
                    <option value="<?= e($nhanVien['id']) ?>" <?= (int) $boLocBaoCao['staff_id'] === (int) $nhanVien['id'] ? 'selected' : '' ?>><?= e($nhanVien['full_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6 col-lg-3">
            <label class="form-label" for="customer_type_id">Loại khách hàng</label>
            <select class="form-select" id="customer_type_id" name="customer_type_id">
                <option value="">Tất cả loại</option>
                <?php foreach ($danhSachLoaiBaoCao as $loai): ?>
                    <option value="<?= e($loai['id']) ?>" <?= (int) $boLocBaoCao['customer_type_id'] === (int) $loai['id'] ? 'selected' : '' ?>><?= e($loai['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-lg-2 d-grid">
            <button class="btn btn-primary" type="submit">Lọc báo cáo</button>
        </div>
    </div>
</form>
