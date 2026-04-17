<?php

namespace App\Helpers;

class FormatHelper
{
    public static function rupiah(int|float $n): string
    {
        if ($n >= 1_000_000_000) {
            return 'Rp ' . number_format($n / 1_000_000_000, 1, ',', '.') . ' M';
        }
        if ($n >= 1_000_000) {
            return 'Rp ' . number_format($n / 1_000_000, 1, ',', '.') . ' Jt';
        }
        if ($n >= 1_000) {
            return 'Rp ' . number_format($n / 1_000, 0, ',', '.') . ' Rb';
        }
        return 'Rp ' . number_format($n, 0, ',', '.');
    }

    public static function rupiahFull(int|float $n): string
    {
        return 'Rp ' . number_format($n, 0, ',', '.');
    }
}
