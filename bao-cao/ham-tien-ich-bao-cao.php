<?php

declare(strict_types=1);

function ti_le_bao_cao(int|float $giaTri, int|float $tong): int
{
    return $tong > 0 ? (int) round(($giaTri / $tong) * 100) : 0;
}

function tong_cot_bao_cao(array $dong, string $cot = 'total'): int
{
    return array_reduce($dong, static fn (int $tong, array $muc): int => $tong + (int) ($muc[$cot] ?? 0), 0);
}

function mau_bao_cao_an_toan(?string $mau): string
{
    return preg_match('/^#[0-9a-fA-F]{6}$/', (string) $mau) ? (string) $mau : '#64748b';
}
