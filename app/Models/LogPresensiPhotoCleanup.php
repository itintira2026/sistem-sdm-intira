<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogPresensiPhotoCleanup extends Model
{
        protected $fillable = [
        'executed_by',
        'deleted_before_date',
        'total_photos_deleted',
        'total_size_freed_bytes',
        'execution_type',
        'executed_at',
    ];

    protected $casts = [
        'deleted_before_date' => 'date',
        'executed_at'         => 'datetime',
    ];

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------

    public function executor()
    {
        return $this->belongsTo(User::class, 'executed_by');
    }

    // -------------------------------------------------------
    // Accessors
    // -------------------------------------------------------

    /**
     * Size dalam format human readable
     * Contoh: 12.50 MB, 1.20 GB
     */
    public function getSizeFreedHumanAttribute(): string
    {
        return self::formatBytes($this->total_size_freed_bytes);
    }

    // -------------------------------------------------------
    // Static Helpers
    // -------------------------------------------------------

    public static function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' B';
    }
}
