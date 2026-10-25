<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodWorkingDay extends Model
{
    protected $fillable = [
        'tanggal',
        'set_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function setter()
    {
        return $this->belongsTo(User::class, 'set_by');
    }

    /**
     * Helper utama dipakai TaskProgressService (Grup B) buat hitung
     * "hari kerja yang sudah lewat" — jumlah tanggal yang ditandai
     * DAN <= hari ini, dalam rentang [start, end] tertentu.
     */
    public static function countElapsedBetween(string $start, string $end): int
    {
        $today = now()->toDateString();
        $effectiveEnd = $end < $today ? $end : $today;

        if ($effectiveEnd < $start) {
            return 0;
        }

        return self::whereBetween('tanggal', [$start, $effectiveEnd])->count();
    }

    /**
     * Total hari kerja yang ditandai dalam rentang [start, end],
     * TANPA batasan "sudah lewat" — dipakai buat tampilan/estimasi.
     */
    public static function countTotalBetween(string $start, string $end): int
    {
        return self::whereBetween('tanggal', [$start, $end])->count();
    }
}
