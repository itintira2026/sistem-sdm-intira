<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DailyReportPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_report_id',
        'file_path',
        'file_name',
        'keterangan',
    ];

    // ===============================
    // RELATIONSHIPS
    // ===============================

    public function dailyReport()
    {
        return $this->belongsTo(DailyReport::class);
    }

    // ===============================
    // ACCESSORS
    // ===============================

    public function getUrlAttribute()
    {
        return Storage::disk('public')->url($this->file_path);
    }

    public function getFileSizeAttribute()
    {
        if (Storage::disk('public')->exists($this->file_path)) {
            return Storage::disk('public')->size($this->file_path);
        }
        return 0;
    }

    public function getFileSizeFormattedAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    // ===============================
    // BOOT
    // ===============================

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($photo) {
            // Auto delete file from storage when record is deleted
            Storage::disk('public')->delete($photo->file_path);
        });
    }
}
