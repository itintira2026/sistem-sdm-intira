<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Potongan extends Model
{
    use HasFactory;

    protected $table = 'potongans';

    protected $fillable = [
        'user_id',
        'bulan',
        'tahun',
        'tanggal',
        'divisi',
        'keterangan',
        'jenis',
        'amount',
    ];

    protected $casts = [
        'bulan'    => 'integer',
        'tahun'    => 'integer',
        'tanggal'  => 'date',
        'amount'   => 'decimal:2',
    ];

    /* =====================
     | RELATIONS
     ===================== */

     public function branchUser()
    {


        return $this->belongsTo(BranchUser::class, 'user_id', 'user_id')
            ->whereHas('user', function ($query) {
                $query->where('is_active', true);
            });
    }

    /**
     * Get user via branch_user
     */
    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            BranchUser::class,
            'id', // Foreign key on branch_user table
            'id', // Foreign key on users table
            'branch_user_id', // Local key on gaji_pokok table
            'user_id' // Local key on branch_user table
        );
    }

    /**
     * Akses branch langsung
     */


    /* =====================
     | SCOPES
     ===================== */

    public function scopeForMonth($query, int $bulan, int $tahun)
    {
        return $query
            ->where('bulan', $bulan)
            ->where('tahun', $tahun);
    }

  

    public function scopePotongan($query)
    {
        return $query->where('jenis', 'potongan');
    }

    public function scopeTambahan($query)
    {
        return $query->where('jenis', 'tambahan');
    }

    /* =====================
     | ACCESSORS
     ===================== */

    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getNamaBulanAttribute()
    {
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return $bulan[$this->bulan] ?? '';
    }

    public function getPeriodeAttribute()
    {
        return "{$this->nama_bulan} {$this->tahun}";
    }
/**
     * Accessor total tambahan (per record)
     */
    public function getTotalTambahanAttribute()
    {
        return $this->jenis === 'tambahan' ? $this->amount : 0;
    }

    /**
     * Accessor total potongan (per record)
     */
    public function getTotalPotonganAttribute()
    {
        return $this->jenis === 'potongan' ? $this->amount : 0;
    }
    /**
     * Total tambahan per user per bulan
     */
    public static function totalTambahan($userId, $bulan, $tahun)
    {
        return self::where('user_id', $userId)
            ->forMonth($bulan, $tahun)
            ->tambahan()
            ->sum('amount');
    }

    /**
     * Total potongan per user per bulan
     */
    public static function totalPotongan($userId, $bulan, $tahun)
    {
        return self::where('user_id', $userId)
            ->forMonth($bulan, $tahun)
            ->potongan()
            ->sum('amount');
    }
}