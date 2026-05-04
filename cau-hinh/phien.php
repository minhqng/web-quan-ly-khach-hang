<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');

    $secureCookie = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

    session_name(TEN_SESSION);
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => rtrim(DUONG_DAN_GOC, '/') . '/',
        'domain' => '',
        'secure' => $secureCookie,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();

    $_SESSION['khoi_tao_luc'] ??= time();
    $_SESSION['hoat_dong_cuoi'] ??= time();
}
