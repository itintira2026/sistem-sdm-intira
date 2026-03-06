<?php

// ============================================================
// FILE: app/Models/ValidationAction.php
// ============================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValidationAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'is_active', 'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // public function validations()
    // {
    //     return $this->hasMany(DailyReportFoValidation::class, 'validation_action_id');
    // }
    public function validations()
    {
        return $this->belongsToMany(
            DailyReportFoValidation::class,
            'pivot_validation_actions',
            'validation_action_id',
            'daily_report_fo_validation_id'
        )->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }
}