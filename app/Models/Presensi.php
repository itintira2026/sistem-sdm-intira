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
    public function hitungPotonganTerlambat($potonganPerMenit = 5000)
    {
        if ($this->status !== 'CHECK_IN') {
            return ['menit_terlambat' => 0, 'potongan' => 0];
        }

        $jamCheckIn = Carbon::parse($this->jam);
        $jamOnly = $jamCheckIn->format('H:i');
        $hour = $jamCheckIn->hour;
        
        $menitTerlambat = 0;
        
        // Shift 1: 08:00 - 12:00
        if ($hour >= 8 && $hour < 13) {
            $jamMasukShift1 = Carbon::parse($this->tanggal->format('Y-m-d') . ' 08:00:00');
            
            if ($jamCheckIn->gt($jamMasukShift1)) {
                $menitTerlambat = $jamCheckIn->diffInMinutes($jamMasukShift1);
            }
        }
        // Shift 2: 13:00 - 21:00
        elseif ($hour >= 13 && $hour < 22) {
            $jamMasukShift2 = Carbon::parse($this->tanggal->format('Y-m-d') . ' 13:00:00');
            
            if ($jamCheckIn->gt($jamMasukShift2)) {
                $menitTerlambat = $jamCheckIn->diffInMinutes($jamMasukShift2);
            }
        }
        
        $potongan = $menitTerlambat > 0 ? $menitTerlambat * $potonganPerMenit : 0;
        
        return [
            'menit_terlambat' => $menitTerlambat,
            'potongan' => $potongan,
            'jam_check_in' => $jamOnly,
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
