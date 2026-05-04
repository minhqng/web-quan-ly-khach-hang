<?php

declare(strict_types=1);

$tieuDe = $tieuDe ?? TEN_UNG_DUNG;
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($tieuDe) ?> - <?= e(TEN_UNG_DUNG) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= e(duong_dan('tai-nguyen/css/ung-dung.css')) ?>" rel="stylesheet">
</head>
<body class="app-body">
<a class="skip-link" href="#noi-dung-chinh">Bỏ qua menu</a>
<?php require __DIR__ . '/thanh-dieu-huong.php'; ?>
<div class="<?= da_dang_nhap() ? 'app-shell' : 'app-shell app-shell--public' ?>">
    <?php if (da_dang_nhap()) require __DIR__ . '/thanh-ben.php'; ?>
    <main class="app-main" id="noi-dung-chinh">
        <?php require __DIR__ . '/thong-bao.php'; ?>
