<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Potongan extends Model
{
    use HasFactory;

    protected $table = 'potongans';

    protected $fillable = [
        'branch_user_id',
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
        return $this->belongsTo(
            BranchUser::class,
            'branch_user_id',
            'id'
        );
    }

    /**
     * Akses user langsung
     */
    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            BranchUser::class,
            'id',        // FK di branch_users
            'id',        // FK di users
            'branch_user_id',
            'user_id'
        );
    }

    /**
     * Akses branch langsung
     */
    public function branch()
    {
        return $this->hasOneThrough(
            Branch::class,
            BranchUser::class,
            'id',
            'id',
            'branch_user_id',
            'branch_id'
        );
    }

    /* =====================
     | SCOPES
     ===================== */

    public function scopeForMonth($query, int $bulan, int $tahun)
    {
        return $query
            ->where('bulan', $bulan)
            ->where('tahun', $tahun);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->whereHas('branchUser', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    public function scopeForBranch($query, int $branchId)
    {
        return $query->whereHas('branchUser', function ($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        });
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
}
