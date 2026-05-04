<?php

declare(strict_types=1);

$phanTrang = $phanTrang ?? null;
?>
<?php if (is_array($phanTrang)): ?>
    <nav class="d-flex align-items-center justify-content-between gap-3" aria-label="Phân trang">
        <span class="text-muted">Trang <?= e((string) $phanTrang['trang']) ?>/<?= e((string) $phanTrang['tong_trang']) ?></span>
        <div class="btn-group btn-group-sm" role="group" aria-label="Chuyển trang">
            <button class="btn btn-outline-primary" type="button" disabled>Trước</button>
            <button class="btn btn-outline-primary" type="button" disabled>Sau</button>
        </div>
    </nav>
<?php endif; ?>
