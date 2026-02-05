<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThreeHourReportManager extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_user_id',
        'report_12_at',
        'report_16_at',
        'report_20_at',
        'keterangan',
    ];

    protected $casts = [
        'report_12_at' => 'datetime',
        'report_16_at' => 'datetime',
        'report_20_at' => 'datetime',
    ];

    // ===============================
    // RELATIONSHIPS
    // ===============================

    public function branchUser()
    {
        return $this->belongsTo(BranchUser::class);
    }

    public function revenues()
    {
        return $this->hasMany(Revenue::class);
    }

    // ===============================
    // ACCESSORS
    // ===============================

    public function getTotalReportsAttribute()
    {
        return collect([
            $this->report_12_at,
            $this->report_16_at,
            $this->report_20_at,
        ])->filter()->count();
    }

    public function getIsCompleteAttribute()
    {
        return $this->total_reports === 3;
    }

    // ===============================
    // SCOPES
    // ===============================

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeByBranchUser($query, $branchUserId)
    {
        return $query->where('branch_user_id', $branchUserId);
    }
}