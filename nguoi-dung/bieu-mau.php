<?php

$loi = $loi ?? [];
$duLieu = array_merge(du_lieu_mac_dinh_nguoi_dung(), $duLieu ?? []);
$nhanNut = $nhanNut ?? 'Lưu người dùng';
$tieuDeBieuMau = $tieuDeBieuMau ?? 'Thông tin người dùng';
$hienThiMatKhau = $hienThiMatKhau ?? false;
$coLoi = static fn (string $truong): string => isset($loi[$truong]) ? ' is-invalid' : '';
?>
<form method="post" class="surface-card user-form">
    <?= csrf_input() ?>
    <div class="form-section">
        <div class="form-section-title">
            <p class="eyebrow">Tài khoản hệ thống</p>
            <h2 class="card-title mb-1"><?= e($tieuDeBieuMau) ?></h2>
            <p class="text-muted mb-0">Dùng cho đăng nhập, phân quyền và giao khách hàng/công việc.</p>
        </div>
        <div class="row g-3">
            <div class="col-lg-6">
                <label class="form-label" for="full_name">Họ tên <span class="text-danger">*</span></label>
                <input class="form-control<?= e($coLoi('full_name')) ?>" id="full_name" maxlength="120" name="full_name" required value="<?= e($duLieu['full_name']) ?>">
                <?php if (isset($loi['full_name'])): ?><div class="invalid-feedback"><?= e($loi['full_name']) ?></div><?php endif; ?>
            </div>
            <div class="col-lg-6">
                <label class="form-label" for="username">Tên đăng nhập <span class="text-danger">*</span></label>
                <input class="form-control<?= e($coLoi('username')) ?>" id="username" maxlength="50" name="username" required value="<?= e($duLieu['username']) ?>">
                <?php if (isset($loi['username'])): ?><div class="invalid-feedback"><?= e($loi['username']) ?></div><?php endif; ?>
            </div>
            <div class="col-lg-6">
                <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
                <input class="form-control<?= e($coLoi('email')) ?>" id="email" maxlength="191" name="email" required type="email" value="<?= e($duLieu['email']) ?>">
                <?php if (isset($loi['email'])): ?><div class="invalid-feedback"><?= e($loi['email']) ?></div><?php endif; ?>
            </div>
            <div class="col-lg-6">
                <label class="form-label" for="phone">Số điện thoại</label>
                <input class="form-control<?= e($coLoi('phone')) ?>" id="phone" maxlength="32" name="phone" value="<?= e($duLieu['phone']) ?>">
                <?php if (isset($loi['phone'])): ?><div class="invalid-feedback"><?= e($loi['phone']) ?></div><?php endif; ?>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="role">Vai trò</label>
                <select class="form-select<?= e($coLoi('role')) ?>" id="role" name="role">
                    <?php foreach (nhan_vai_tro_nguoi_dung() as $giaTri => $nhan): ?>
                        <option value="<?= e($giaTri) ?>" <?= $duLieu['role'] === $giaTri ? 'selected' : '' ?>><?= e($nhan) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($loi['role'])): ?><div class="invalid-feedback"><?= e($loi['role']) ?></div><?php endif; ?>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="status">Trạng thái</label>
                <select class="form-select<?= e($coLoi('status')) ?>" id="status" name="status">
                    <?php foreach (nhan_trang_thai_nguoi_dung() as $giaTri => $nhan): ?>
                        <option value="<?= e($giaTri) ?>" <?= $duLieu['status'] === $giaTri ? 'selected' : '' ?>><?= e($nhan) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($loi['status'])): ?><div class="invalid-feedback"><?= e($loi['status']) ?></div><?php endif; ?>
            </div>
        </div>
    </div>

    <?php if ($hienThiMatKhau): ?>
        <div class="form-section">
            <div class="form-section-title">
                <h3>Mật khẩu ban đầu</h3>
                <p>Admin có thể đổi lại mật khẩu ở trang chi tiết người dùng.</p>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="password">Mật khẩu <span class="text-danger">*</span></label>
                    <input class="form-control<?= e($coLoi('password')) ?>" id="password" name="password" required type="password" autocomplete="new-password">
                    <?php if (isset($loi['password'])): ?><div class="invalid-feedback"><?= e($loi['password']) ?></div><?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="password_confirm">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                    <input class="form-control<?= e($coLoi('password_confirm')) ?>" id="password_confirm" name="password_confirm" required type="password" autocomplete="new-password">
                    <?php if (isset($loi['password_confirm'])): ?><div class="invalid-feedback"><?= e($loi['password_confirm']) ?></div><?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="form-actions user-form-actions">
        <a class="btn btn-outline-secondary" href="<?= e(duong_dan('nguoi-dung/')) ?>">Hủy</a>
        <button class="btn btn-primary" type="submit"><?= e($nhanNut) ?></button>
    </div>
</form>
