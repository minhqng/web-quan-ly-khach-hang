<?php

$tepHienTaiBaoCao = basename(str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? 'index.php'));
$lopBaoCao = static fn (string $tep): string => $tepHienTaiBaoCao === $tep ? ' is-active' : '';
?>
<nav class="report-nav segmented-nav" aria-label="Điều hướng báo cáo">
    <a class="report-nav-link segmented-nav-link<?= e($lopBaoCao('index.php')) ?>" href="<?= e(duong_dan('bao-cao/')) ?>">Tổng quan</a>
    <a class="report-nav-link segmented-nav-link<?= e($lopBaoCao('khach-hang.php')) ?>" href="<?= e(duong_dan('bao-cao/khach-hang.php')) ?>">Khách hàng</a>
    <a class="report-nav-link segmented-nav-link<?= e($lopBaoCao('tuong-tac.php')) ?>" href="<?= e(duong_dan('bao-cao/tuong-tac.php')) ?>">Tương tác</a>
    <a class="report-nav-link segmented-nav-link<?= e($lopBaoCao('cong-viec.php')) ?>" href="<?= e(duong_dan('bao-cao/cong-viec.php')) ?>">Công việc</a>
</nav>
