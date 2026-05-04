<?php

declare(strict_types=1);

require_once __DIR__ . '/../cau-hinh/ket-noi-csdl.php';

function chay_truy_van(string $sql, array $thamSo = []): PDOStatement
{
    $stmt = lay_ket_noi_csdl()->prepare($sql);
    $stmt->execute($thamSo);

    return $stmt;
}

function lay_mot_dong(string $sql, array $thamSo = []): ?array
{
    $dong = chay_truy_van($sql, $thamSo)->fetch();

    return $dong === false ? null : $dong;
}

function lay_nhieu_dong(string $sql, array $thamSo = []): array
{
    return chay_truy_van($sql, $thamSo)->fetchAll();
}

function lay_mot_gia_tri(string $sql, array $thamSo = []): mixed
{
    $giaTri = chay_truy_van($sql, $thamSo)->fetchColumn();

    return $giaTri === false ? null : $giaTri;
}

function thuc_thi_lenh(string $sql, array $thamSo = []): int
{
    return chay_truy_van($sql, $thamSo)->rowCount();
}

function lay_id_vua_tao(): string
{
    return lay_ket_noi_csdl()->lastInsertId();
}
