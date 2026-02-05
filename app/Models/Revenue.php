<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Revenue extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_user_id',
        'branch_id',
        'three_hour_report_manager_id',
        'no_akad',
        'keterangan',
        'jumlah_pembayaran',
        'tanggal_transaksi',
    ];

    protected $casts = [
        'jumlah_pembayaran' => 'decimal:2',
        'tanggal_transaksi' => 'date',
    ];

    // ===============================
    // RELATIONSHIPS
    // ===============================

    public function branchUser()
    {
        return $this->belongsTo(BranchUser::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function threeHourReportManager()
    {
        return $this->belongsTo(ThreeHourReportManager::class);
    }

    // ===============================
    // ACCESSORS
    // ===============================

    public function getJumlahPembayaranFormattedAttribute()
    {
        return 'Rp ' . number_format($this->jumlah_pembayaran, 0, ',', '.');
    }

    // ===============================
    // SCOPES
    // ===============================

    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeByBranchUser($query, $branchUserId)
    {
        return $query->where('branch_user_id', $branchUserId);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('tanggal_transaksi', $date);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('tanggal_transaksi', today());
    }

}
