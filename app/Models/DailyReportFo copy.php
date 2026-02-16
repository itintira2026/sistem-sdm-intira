<?php

// namespace App\Models;

// use Carbon\Carbon;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class DailyReportFo extends Model
// {
//     use HasFactory;

//     protected $table = 'daily_report_fo';

//     protected $fillable = [
//         'user_id',
//         'branch_id',
//         'tanggal',
//         'shift',
//         'slot',
//         'slot_time',
//         'uploaded_at',
//         'keterangan',
//     ];

//     protected $casts = [
//         'tanggal' => 'date',
//         'uploaded_at' => 'datetime',
//     ];

//     // =========================================================
//     // RELATIONSHIPS
//     // =========================================================

//     public function user()
//     {
//         return $this->belongsTo(User::class);
//     }

//     public function branch()
//     {
//         return $this->belongsTo(Branch::class);
//     }

//     /**
//      * Relasi ke Details
//      * Satu report punya banyak detail (satu per field master)
//      */
//     public function details()
//     {
//         return $this->hasMany(DailyReportFODetail::class, 'daily_report_fo_id');
//     }

//     // =========================================================
//     // HELPER METHODS
//     // =========================================================

//     /**
//      * Ambil detail berdasarkan category code
//      */
//     public function getDetailsByCategory($categoryCode)
//     {
//         return $this->details()
//             ->whereHas('field.category', function ($q) use ($categoryCode) {
//                 $q->where('code', $categoryCode);
//             })
//             ->with('field', 'photos')
//             ->get();
//     }

//     /**
//      * Ambil value satu field berdasarkan field code
//      */
//     public function getFieldValue($fieldCode)
//     {
//         $detail = $this->details()
//             ->whereHas('field', function ($q) use ($fieldCode) {
//                 $q->where('code', $fieldCode);
//             })
//             ->first();

//         return $detail ? $detail->getValue() : null;
//     }

//     // =========================================================
//     // ACCESSORS
//     // =========================================================

//     public function getShiftLabelAttribute()
//     {
//         $shifts = config('daily_report_fo.shifts');

//         return $shifts[$this->shift]['label'] ?? ucfirst($this->shift);
//     }

//     public function getFormattedSlotTimeAttribute()
//     {
//         return Carbon::parse($this->slot_time)->format('H:i');
//     }

//     public function getSlotWindowAttribute()
//     {
//         $start = Carbon::parse($this->tanggal->format('Y-m-d').' '.$this->slot_time);
//         $windowMinutes = config('daily_report_fo.upload_window_minutes', 60);
//         $end = $start->copy()->addMinutes($windowMinutes);

//         return [
//             'start' => $start->format('H:i'),
//             'end' => $end->format('H:i'),
//         ];
//     }

//     // =========================================================
//     // SCOPES
//     // =========================================================

//     public function scopeByUser($query, $userId)
//     {
//         return $query->where('user_id', $userId);
//     }

//     public function scopeByBranch($query, $branchId)
//     {
//         return $query->where('branch_id', $branchId);
//     }

//     public function scopeByDate($query, $date)
//     {
//         return $query->whereDate('tanggal', $date);
//     }

//     public function scopeByShift($query, $shift)
//     {
//         return $query->where('shift', $shift);
//     }

//     public function scopeBySlot($query, $slot)
//     {
//         return $query->where('slot', $slot);
//     }

//     // =========================================================
//     // BOOT — cascade delete foto via details
//     // =========================================================

//     protected static function boot()
//     {
//         parent::boot();

//         static::deleting(function ($report) {
//             // Load details beserta photos
//             $report->load('details.photos');

//             foreach ($report->details as $detail) {
//                 foreach ($detail->photos as $photo) {
//                     // Hapus file fisik dari storage
//                     \App\Helpers\ImageHelper::delete($photo->file_path);
//                 }
//                 // Hapus photo records (cascade DB juga handle ini,
//                 // tapi explicit agar file storage ikut terhapus)
//                 $detail->photos()->delete();
//             }

//             // Hapus detail records
//             $report->details()->delete();
//         });
//     }
// }

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReportFo extends Model
{
    use HasFactory;

    protected $table = 'daily_report_fo';

    protected $fillable = [
        'user_id',
        'branch_id',
        'tanggal',
        'shift',
        'slot',
        'slot_time',
        'uploaded_at',
        'keterangan',
        'validation_status', // pending | approved | rejected
    ];

    protected $casts = [
        'tanggal' => 'date',
        'uploaded_at' => 'datetime',
    ];

    // =========================================================
    // RELATIONSHIPS
    // =========================================================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function details()
    {
        return $this->hasMany(DailyReportFODetail::class, 'daily_report_fo_id');
    }

    /**
     * Relasi ke validasi (one-to-one, satu laporan satu validasi)
     */
    public function validation()
    {
        return $this->hasOne(DailyReportFoValidation::class, 'daily_report_fo_id');
    }

    // =========================================================
    // HELPERS — Field Values
    // =========================================================

    public function getDetailsByCategory($categoryCode)
    {
        return $this->details()
            ->whereHas('field.category', function ($q) use ($categoryCode) {
                $q->where('code', $categoryCode);
            })
            ->with('field', 'photos')
            ->get();
    }

    public function getFieldValue($fieldCode)
    {
        $detail = $this->details()
            ->whereHas('field', function ($q) use ($fieldCode) {
                $q->where('code', $fieldCode);
            })
            ->first();

        return $detail ? $detail->getValue() : null;
    }

    // =========================================================
    // HELPERS — Validation Window
    // =========================================================

    /**
     * Waktu mulai window FO (= slot_time)
     */
    public function getFoWindowStartAttribute(): Carbon
    {
        return Carbon::parse($this->tanggal->format('Y-m-d').' '.$this->slot_time);
    }

    /**
     * Waktu selesai window FO (slot_time + upload_window_minutes)
     */
    public function getFoWindowEndAttribute(): Carbon
    {
        return $this->fo_window_start->copy()
            ->addMinutes(config('daily_report_fo.upload_window_minutes', 15));
    }

    /**
     * Waktu mulai window manager (= setelah window FO tutup)
     */
    public function getManagerWindowStartAttribute(): Carbon
    {
        return $this->fo_window_end->copy();
    }

    /**
     * Waktu selesai window manager
     */
    public function getManagerWindowEndAttribute(): Carbon
    {
        return $this->manager_window_start->copy()
            ->addMinutes(config('daily_report_fo.validation_window_minutes', 15));
    }

    /**
     * Apakah window manager sedang buka sekarang?
     */
    public function isManagerWindowOpen(?string $timezone = null): bool
    {
        $now = $timezone
            ? Carbon::now($timezone)
            : Carbon::now();

        return $now->between($this->manager_window_start, $this->manager_window_end);
    }

    /**
     * Apakah window manager sudah expired?
     */
    public function isManagerWindowExpired(?string $timezone = null): bool
    {
        $now = $timezone
            ? Carbon::now($timezone)
            : Carbon::now();

        return $now->isAfter($this->manager_window_end);
    }

    /**
     * Status window manager: 'waiting' | 'open' | 'expired'
     */
    public function getManagerWindowStatusAttribute(): string
    {
        $now = Carbon::now();

        if ($now->isBefore($this->manager_window_start)) {
            return 'waiting'; // window FO belum selesai
        }

        if ($now->between($this->manager_window_start, $this->manager_window_end)) {
            return 'open';
        }

        return 'expired';
    }

    // =========================================================
    // ACCESSORS
    // =========================================================

    public function getShiftLabelAttribute()
    {
        $shifts = config('daily_report_fo.shifts');

        return $shifts[$this->shift]['label'] ?? ucfirst($this->shift);
    }

    public function getFormattedSlotTimeAttribute()
    {
        return Carbon::parse($this->slot_time)->format('H:i');
    }

    public function getSlotWindowAttribute()
    {
        return [
            'start' => $this->fo_window_start->format('H:i'),
            'end' => $this->fo_window_end->format('H:i'),
        ];
    }

    public function getValidationStatusLabelAttribute()
    {
        $statuses = config('daily_report_fo.validation_statuses');

        return $statuses[$this->validation_status]['label'] ?? ucfirst($this->validation_status);
    }

    public function getValidationStatusColorAttribute()
    {
        $statuses = config('daily_report_fo.validation_statuses');

        return $statuses[$this->validation_status]['color'] ?? 'gray';
    }

    // =========================================================
    // SCOPES
    // =========================================================

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('tanggal', $date);
    }

    public function scopeByShift($query, $shift)
    {
        return $query->where('shift', $shift);
    }

    public function scopeBySlot($query, $slot)
    {
        return $query->where('slot', $slot);
    }

    public function scopePendingValidation($query)
    {
        return $query->where('validation_status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('validation_status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('validation_status', 'rejected');
    }

    // =========================================================
    // BOOT
    // =========================================================

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($report) {
            $report->load('details.photos');

            foreach ($report->details as $detail) {
                foreach ($detail->photos as $photo) {
                    \App\Helpers\ImageHelper::delete($photo->file_path);
                }
                $detail->photos()->delete();
            }

            $report->details()->delete();
            $report->validation()->delete();
        });
    }
}
