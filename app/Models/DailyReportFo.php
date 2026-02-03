<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;

// class DailyReportFo extends Model
// {
//     //
// }

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
    ];

    protected $casts = [
        'tanggal' => 'date',
        'uploaded_at' => 'datetime',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Branch
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Relasi ke Photos
     */
    public function photos()
    {
        return $this->hasMany(DailyReportFOPhoto::class);
    }

    /**
     * Get photos by category
     *
     * @param string $kategori
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPhotosByCategory($kategori)
    {
        return $this->photos()->where('kategori', $kategori)->get();
    }

    /**
     * Get photo count by category
     *
     * @param string $kategori
     * @return int
     */
    public function getPhotoCategoryCount($kategori)
    {
        return $this->photos()->where('kategori', $kategori)->count();
    }

    /**
     * Check if all categories have photos
     *
     * @return bool
     */
    public function hasAllCategories()
    {
        $categories = array_keys(config('daily_report_fo.photo_categories'));

        foreach ($categories as $category) {
            if ($this->getPhotoCategoryCount($category) < 1) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get shift label
     *
     * @return string
     */
    public function getShiftLabelAttribute()
    {
        $shifts = config('daily_report_fo.shifts');
        return $shifts[$this->shift]['label'] ?? ucfirst($this->shift);
    }

    /**
     * Get formatted slot time
     *
     * @return string
     */
    public function getFormattedSlotTimeAttribute()
    {
        return Carbon::parse($this->slot_time)->format('H:i');
    }

    /**
     * Get slot window range
     *
     * @return array
     */
    public function getSlotWindowAttribute()
    {
        $start = Carbon::parse($this->tanggal->format('Y-m-d') . ' ' . $this->slot_time);
        $windowMinutes = config('daily_report_fo.upload_window_minutes', 60);
        $end = $start->copy()->addMinutes($windowMinutes);

        return [
            'start' => $start->format('H:i'),
            'end' => $end->format('H:i'),
        ];
    }

    /**
     * Scope: Filter by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Filter by branch
     */
    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope: Filter by date
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('tanggal', $date);
    }

    /**
     * Scope: Filter by shift
     */
    public function scopeByShift($query, $shift)
    {
        return $query->where('shift', $shift);
    }

    /**
     * Scope: Filter by slot
     */
    public function scopeBySlot($query, $slot)
    {
        return $query->where('slot', $slot);
    }

    /**
     * Boot method - auto delete photos when report deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($report) {
            // Delete all photos from storage
            foreach ($report->photos as $photo) {
                \App\Helpers\ImageHelper::delete($photo->file_path);
            }

            // Delete photo records (cascade will handle this, but explicit is better)
            $report->photos()->delete();
        });
    }
}
