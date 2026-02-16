<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'description', 'order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationship
    public function fields()
    {
        return $this->hasMany(ReportField::class, 'category_id');
    }

    // Scope: hanya kategori aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope: ordered
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }
}
