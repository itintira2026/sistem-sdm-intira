<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportField extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'code', 'input_type',
        'is_required', 'is_active', 'order',
        'validation_rules', 'placeholder', 'help_text',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'validation_rules' => 'array',
    ];

    // Relationship
    public function category()
    {
        return $this->belongsTo(ReportCategory::class, 'category_id');
    }

    public function details()
    {
        return $this->hasMany(DailyReportFODetail::class, 'field_id');
    }

    // Scope: hanya field aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope: ordered
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    // Check if field requires photo
    public function requiresPhoto()
    {
        return in_array($this->input_type, ['photo', 'photo_number']);
    }
}
