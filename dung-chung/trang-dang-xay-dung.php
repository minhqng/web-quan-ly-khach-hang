<?php

declare(strict_types=1);

function hien_thi_trang_dang_xay_dung(string $tieuDe, string $moTa): void
{
    require __DIR__ . '/../giao-dien/dau-trang.php';
    ?>
    <section class="app-content">
        <div class="page-header">
            <div>
                <p class="eyebrow">Khung chức năng</p>
                <h1 class="page-title"><?= e($tieuDe) ?></h1>
                <p class="page-subtitle"><?= e($moTa) ?></p>
            </div>
        </div>
        <section class="placeholder-panel empty-state">
            <p class="eyebrow">Khung chức năng</p>
            <h2 class="card-title">Sẵn sàng triển khai nghiệp vụ</h2>
            <p class="text-muted mb-3">Giao diện dùng chung đã sẵn sàng. Module này sẽ được nối dữ liệu và xử lý nghiệp vụ ở giai đoạn tiếp theo.</p>
            <span class="badge text-bg-secondary">Đang xây dựng logic</span>
        </section>
    </section>
    <?php
    require __DIR__ . '/../giao-dien/cuoi-trang.php';
}
