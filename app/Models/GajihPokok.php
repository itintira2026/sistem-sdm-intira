<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GajihPokok extends Model
{
    protected $table = 'gajih_pokoks';

     protected $fillable = [
        'branch_user_id',
        'amount',
        'bulan',
        'tahun',
        'keterangan',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'bulan' => 'integer',
        'tahun' => 'integer',
    ];

    /**
     * Relasi ke BranchUser
     */
    public function branchUser()
    {
        return $this->belongsTo(BranchUser::class);
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
     * Get branch via branch_user
     */
    public function branch()
    {
        return $this->hasOneThrough(
            Branch::class,
            BranchUser::class,
            'id', // Foreign key on branch_user table
            'id', // Foreign key on branches table
            'branch_user_id', // Local key on gaji_pokok table
            'branch_id' // Local key on branch_user table
        );
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    /**
     * Get nama bulan
     */
    public function getNamaBulanAttribute()
    {
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $bulan[$this->bulan] ?? '';
    }

    /**
     * Get periode (Januari 2024)
     */
    public function getPeriodeAttribute()
    {
        return $this->nama_bulan . ' ' . $this->tahun;
    }

    /**
     * Scope untuk bulan tertentu
     */
    public function scopeForMonth($query, $bulan, $tahun)
    {
        return $query->where('bulan', $bulan)->where('tahun', $tahun);
    }

    /**
     * Scope untuk tahun tertentu
     */
    public function scopeForYear($query, $tahun)
    {
        return $query->where('tahun', $tahun);
    }

    /**
     * Scope untuk branch tertentu
     */
    public function scopeForBranch($query, $branchId)
    {
        return $query->whereHas('branchUser', function($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        });
    }

    /**
     * Scope untuk user tertentu
     */
    public function scopeForUser($query, $userId)
    {
        return $query->whereHas('branchUser', function($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }
}
