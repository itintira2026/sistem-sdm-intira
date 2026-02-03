<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Presensi extends Model
{
    protected $table = 'presensis';

    protected $fillable = [
        'user_id',
        'tanggal',
        'status',
        'jam',
        'wilayah',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Hitung potongan keterlambatan
     * 
     * @param int $potonganPerMenit Potongan per menit keterlambatan (default: 5000)
     * @return array ['menit_terlambat' => int, 'potongan' => int]
     */
    
// public function hitungPotonganTerlambat($potonganPerMenit = 5000)
// {
//     // Guard: kalau bukan check-in atau jam kosong
//     if ($this->status !== 'CHECK_IN' || !$this->jam) {
//         return [
//             'menit_terlambat' => 0,
//             'potongan' => 0,
//             'jam_check_in' => '-',
//         ];
//     }

//     $jamCheckIn = Carbon::parse($this->jam);
//     $hour = $jamCheckIn->hour;

//     $menitTerlambat = 0;

//     // Shift 1: 08:00 - 12:59
//     if ($hour >= 8 && $hour < 13) {
//         $jamMasuk = Carbon::parse($this->tanggal->format('Y-m-d') . ' 08:00:00');

//         if ($jamCheckIn->gt($jamMasuk)) {
//             // HITUNG DARI JAM MASUK KE JAM CHECK-IN (arah yang benar)
//             $menitTerlambat = $jamMasuk->diffInMinutes($jamCheckIn);
//         }
//     }
//     // Shift 2: 13:00 - 21:00
//     elseif ($hour >= 13 && $hour <= 21) {
//         $jamMasuk = Carbon::parse($this->tanggal->format('Y-m-d') . ' 13:00:00');

//         if ($jamCheckIn->gt($jamMasuk)) {
//             // HITUNG DARI JAM MASUK KE JAM CHECK-IN (arah yang benar)
//             $menitTerlambat = $jamMasuk->diffInMinutes($jamCheckIn);
//         }
//     }

//     $potongan = $menitTerlambat > 0 ? $menitTerlambat * $potonganPerMenit : 0;

//     return [
//         'menit_terlambat' => $menitTerlambat,
//         'potongan' => $potongan,
//         'jam_check_in' => $jamCheckIn->format('H:i'),
//     ];
// }

// public function hitungPotonganTerlambat($potonganFlat = 15000)
// {
//     // Guard: kalau bukan check-in atau jam kosong
//     if ($this->status !== 'CHECK_IN' || !$this->jam) {
//         return [
//             'menit_terlambat' => 0,
//             'potongan' => 0,
//             'jam_check_in' => '-',
//         ];
//     }

//     $jamCheckIn = Carbon::parse($this->jam);
//     $hour = $jamCheckIn->hour;

//     $menitTerlambat = 0;

//     // Shift 1: 08:00 - 12:59
//     if ($hour >= 8 && $hour < 13) {
//         $jamMasuk = Carbon::parse($this->tanggal->format('Y-m-d') . ' 08:00:00');

//         if ($jamCheckIn->gt($jamMasuk)) {
//             $menitTerlambat = $jamMasuk->diffInMinutes($jamCheckIn);
//         }
//     }
//     // Shift 2: 13:00 - 21:00
//     elseif ($hour >= 13 && $hour <= 21) {
//         $jamMasuk = Carbon::parse($this->tanggal->format('Y-m-d') . ' 13:00:00');

//         if ($jamCheckIn->gt($jamMasuk)) {
//             $menitTerlambat = $jamMasuk->diffInMinutes($jamCheckIn);
//         }
//     }

//     // ✅ POTONGAN FLAT: kalau telat minimal 1 menit → potong 15.000
//     $potongan = $menitTerlambat > 0 ? $potonganFlat : 0;

//     return [
//         'menit_terlambat' => $menitTerlambat,
//         'potongan' => $potongan,
//         'jam_check_in' => $jamCheckIn->format('H:i'),
//     ];
// }

public function hitungPotonganTerlambat($potonganTerlambat = 15000, $potonganIzinSakit = 30000)
{
    // Guard: kalau bukan check-in atau jam kosong
    if ($this->status !== 'CHECK_IN' || !$this->jam) {
        return [
            'menit_terlambat' => 0,
            'potongan' => 0,
            'jam_check_in' => '-',
        ];
    }

    $jamCheckIn = \Carbon\Carbon::parse($this->jam);
    $hour = $jamCheckIn->hour;

    $menitTerlambat = 0;

    // =========================
    // 1️⃣ CEK TERLAMBAT
    // =========================

    // Shift 1: 08:00 - 12:59
    if ($hour >= 8 && $hour < 13) {
        $jamMasuk = \Carbon\Carbon::parse($this->tanggal->format('Y-m-d') . ' 08:00:00');

        if ($jamCheckIn->gt($jamMasuk)) {
            $menitTerlambat = $jamMasuk->diffInMinutes($jamCheckIn);
        }
    }
    // Shift 2: 13:00 - 21:00
    elseif ($hour >= 13 && $hour <= 21) {
        $jamMasuk = \Carbon\Carbon::parse($this->tanggal->format('Y-m-d') . ' 13:00:00');

        if ($jamCheckIn->gt($jamMasuk)) {
            $menitTerlambat = $jamMasuk->diffInMinutes($jamCheckIn);
        }
    }

    // =========================
    // 2️⃣ CEK IZIN / SAKIT
    // =========================

    $potongan = 0;

    if ($this->keterangan) {
        $ket = strtolower($this->keterangan);

        if (str_contains($ket, 'sakit') || str_contains($ket, 'izin')) {
            // ✅ Kalau sakit / izin → potong 30.000 flat
            $potongan = $potonganIzinSakit;
        }
    }

    // =========================
    // 3️⃣ JIKA BUKAN IZIN/SAKIT, CEK TERLAMBAT
    // =========================

    if ($potongan === 0 && $menitTerlambat > 0) {
        // ✅ Kalau telat minimal 1 menit → potong 15.000 flat
        $potongan = $potonganTerlambat;
    }

    return [
        'menit_terlambat' => $menitTerlambat,
        'potongan' => $potongan,
        'jam_check_in' => $jamCheckIn->format('H:i'),
    ];
}


    /**
     * Scope untuk filter berdasarkan bulan dan tahun
     */
    public function scopeForMonth($query, $bulan, $tahun)
    {
        return $query->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan);
    }

    /**
     * Scope untuk filter hanya CHECK_IN
     */
    public function scopeCheckIn($query)
    {
        return $query->where('status', 'CHECK_IN');
    }

    /**
     * Scope untuk filter user tertentu
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
