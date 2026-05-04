<?php
$duongDanHienTai = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$lienKetDangHoatDong = static function (string $duongDan) use ($duongDanHienTai): string {
    return str_contains($duongDanHienTai, '/' . trim($duongDan, '/')) ? ' is-active' : '';
};
$hienThiLienKet = static function (string $duongDan, string $nhan) use ($lienKetDangHoatDong): void {
    ?>
    <a class="sidebar-link<?= e($lienKetDangHoatDong($duongDan)) ?>" href="<?= e(duong_dan($duongDan)) ?>">
        <span class="sidebar-link-marker" aria-hidden="true"></span>
        <span><?= e($nhan) ?></span>
    </a>
    <?php
};
?>
<aside class="app-sidebar" aria-label="Điều hướng chính">
    <div class="sidebar-section">
        <p class="sidebar-title">Tổng quan</p>
        <?php $hienThiLienKet('bang-dieu-khien.php', 'Bảng điều khiển'); ?>
    </div>
    <div class="sidebar-section">
        <p class="sidebar-title">Chăm sóc khách hàng</p>
        <?php $hienThiLienKet('khach-hang/', 'Khách hàng'); ?>
        <?php $hienThiLienKet('tuong-tac/', 'Tương tác'); ?>
        <?php $hienThiLienKet('cong-viec-theo-doi/', 'Công việc theo dõi'); ?>
        <?php $hienThiLienKet('bao-cao/', 'Báo cáo'); ?>
    </div>
    <?php if (la_admin()): ?>
        <div class="sidebar-section">
            <p class="sidebar-title">Quản trị</p>
            <?php $hienThiLienKet('loai-khach-hang/', 'Loại khách hàng'); ?>
            <?php $hienThiLienKet('nguoi-dung/', 'Người dùng'); ?>
        </div>
    <?php endif; ?>
</aside>
