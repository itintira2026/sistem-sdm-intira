<?php

// ============================================================
// FILE: app/Models/DailyReportFoValidation.php
// ============================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReportFoValidation extends Model
{
    use HasFactory;

    protected $table = 'daily_report_fo_validations';

    // protected $fillable = [
    //     'daily_report_fo_id',
    //     'manager_id',
    //     'validation_action_id',
    //     'status',
    //     'catatan',
    //     'validated_at',
    // ];
    // Tambahkan ke $fillable (kalau belum ada):
    protected $fillable = [
        'daily_report_fo_id',
        'checked_by',   // BARU
        'checked_at',   // BARU
        'manager_id',
        'status',
        'catatan',
        'validated_at',
    ];

    protected $casts = [
        'validated_at' => 'datetime',
        'checked_at' => 'datetime',
    ];

    // =========================================================
    // RELATIONSHIPS
    // =========================================================

    public function report()
    {
        return $this->belongsTo(DailyReportFo::class, 'daily_report_fo_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    // public function action()
    // {
    //     return $this->belongsTo(ValidationAction::class, 'validation_action_id');
    // }
    public function actions()
    {
        return $this->belongsToMany(
            ValidationAction::class,
            'pivot_validation_actions',
            'daily_report_fo_validation_id',
            'validation_action_id'
        )->withTimestamps();
    }

    // =========================================================
    // SCOPES
    // =========================================================

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // =========================================================
    // ACCESSORS
    // =========================================================

    public function getStatusLabelAttribute()
    {
        $statuses = config('daily_report_fo.validation_statuses');

        return $statuses[$this->status]['label'] ?? ucfirst($this->status);
    }

    public function getIsApprovedAttribute()
    {
        return $this->status === 'approved';
    }

    public function getIsRejectedAttribute()
    {
        return $this->status === 'rejected';
    }

    // Tambahkan relasi baru (di samping relasi manager() yang sudah ada):
    public function checker()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }
}
