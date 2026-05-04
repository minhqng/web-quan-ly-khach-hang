<?php $thongBao = lay_thong_bao(); ?>
<?php if ($thongBao): ?>
    <div class="app-flash-region" aria-live="polite">
        <div class="alert alert-<?= e($thongBao['loai']) ?> alert-dismissible fade show" role="alert">
            <?= e($thongBao['noi_dung']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    </div>
<?php endif; ?>
