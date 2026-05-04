<nav class="navbar navbar-expand-lg app-navbar">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= e(duong_dan('bang-dieu-khien.php')) ?>">
            <span class="brand-mark">CRM</span>
            <span><?= e(TEN_UNG_DUNG) ?></span>
        </a>
        <div class="navbar-actions">
            <?php if (da_dang_nhap()): ?>
                <?php $nguoiDung = nguoi_dung_hien_tai(); ?>
                <?php $vaiTro = ($nguoiDung['vai_tro'] ?? '') === VAI_TRO_ADMIN ? 'Quản trị' : 'Nhân viên'; ?>
                <span class="navbar-user"><?= e($nguoiDung['ho_ten'] ?: $nguoiDung['ten_dang_nhap']) ?></span>
                <span class="navbar-role"><?= e($vaiTro) ?></span>
                <form class="d-inline" method="post" action="<?= e(duong_dan('dang-xuat.php')) ?>">
                    <button class="btn btn-outline-light btn-sm" type="submit">Đăng xuất</button>
                </form>
            <?php else: ?>
                <a class="btn btn-outline-light btn-sm" href="<?= e(duong_dan('dang-nhap.php')) ?>">Đăng nhập</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
