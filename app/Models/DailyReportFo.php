<?php

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
        'validation_status',
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

    public function validation()
    {
        return $this->hasOne(DailyReportFoValidation::class, 'daily_report_fo_id');
    }

    // =========================================================
    // TIMEZONE HELPER
    // =========================================================

    /**
     * Ambil timezone cabang laporan ini.
     * Prioritas: dari relasi branch yang sudah di-load → fallback ke query → fallback ke WIB
     *
     * Ini adalah SATU-SATUNYA sumber timezone yang dipakai untuk window calculation.
     * Manager di manapun berada, window selalu dihitung dari timezone cabang FO.
     */
    public function getBranchTimezone(): string
    {
        // Gunakan relasi jika sudah di-eager load (hindari N+1)
        if ($this->relationLoaded('branch') && $this->branch) {
            return $this->resolveTzString($this->branch->timezone);
        }

        // Fallback: query langsung (kalau branch belum di-load)
        $branch = Branch::find($this->branch_id);

        return $this->resolveTzString($branch?->timezone);
    }

    /**
     * Konversi kode timezone Indonesia (WIB/WITA/WIT) ke string PHP
     */
    private function resolveTzString(?string $tz): string
    {
        $map = config('daily_report_fo.timezones', [
            'WIB' => 'Asia/Jakarta',
            'WITA' => 'Asia/Makassar',
            'WIT' => 'Asia/Jayapura',
        ]);

        // Kalau sudah berupa full timezone string (e.g. 'Asia/Jakarta'), pakai langsung
        if ($tz && str_contains($tz, '/')) {
            return $tz;
        }

        return $map[$tz] ?? 'Asia/Jakarta';
    }

    // =========================================================
    // WINDOW CALCULATIONS — semua anchor ke timezone cabang
    // =========================================================

    /**
     * Waktu mulai window FO dalam timezone cabang laporan
     */
    public function getFoWindowStartAttribute(): Carbon
    {
        return Carbon::parse(
            $this->tanggal->format('Y-m-d').' '.$this->slot_time,
            $this->getBranchTimezone()
        );
    }

    /**
     * Waktu selesai window FO
     */
    public function getFoWindowEndAttribute(): Carbon
    {
        return $this->fo_window_start->copy()
            ->addMinutes(config('daily_report_fo.upload_window_minutes', 15));
    }

    /**
     * Waktu mulai window manager (langsung setelah FO window tutup)
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
     * Status window manager: 'waiting' | 'open' | 'expired'
     *
     * now() dikonversi ke timezone cabang agar perbandingan apple-to-apple.
     * Manager dari Jakarta yang validasi laporan Makassar tetap dibandingkan
     * dengan waktu Makassar — tidak bisa fraud dengan pindah ke cabang lain.
     */
    public function getManagerWindowStatusAttribute(): string
    {
        $branchTz = $this->getBranchTimezone();

        // Ambil waktu sekarang dalam timezone cabang laporan
        $now = Carbon::now($branchTz);

        // Window start/end sudah pakai timezone cabang (dari fo_window_start)
        // Konversi ke timezone yang sama untuk perbandingan konsisten
        $windowStart = $this->manager_window_start->copy()->setTimezone($branchTz);
        $windowEnd = $this->manager_window_end->copy()->setTimezone($branchTz);

        if ($now->lt($windowStart)) {
            return 'waiting';
        }

        if ($now->lte($windowEnd)) {
            return 'open';
        }

        return 'expired';
    }

    /**
     * Apakah window manager sedang buka?
     * Tidak perlu parameter timezone — selalu pakai timezone cabang laporan.
     */
    public function isManagerWindowOpen(): bool
    {
        return $this->manager_window_status === 'open';
    }

    /**
     * Apakah window manager sudah expired?
     */
    public function isManagerWindowExpired(): bool
    {
        return $this->manager_window_status === 'expired';
    }

    /**
     * Sisa detik dalam window manager (untuk countdown di view)
     */
    public function getManagerWindowRemainingSecondsAttribute(): int
    {
        if ($this->manager_window_status !== 'open') {
            return 0;
        }

        $branchTz = $this->getBranchTimezone();
        $now = Carbon::now($branchTz);
        $windowEnd = $this->manager_window_end->copy()->setTimezone($branchTz);

        return max(0, $now->diffInSeconds($windowEnd, false));
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
    // ACCESSORS
    // =========================================================

    public function getShiftLabelAttribute()
    {
        return config('daily_report_fo.shifts')[$this->shift]['label'] ?? ucfirst($this->shift);
    }

    public function getFormattedSlotTimeAttribute()
    {
        return Carbon::parse($this->slot_time)->format('H:i');
    }

    /**
     * Slot window untuk ditampilkan di view (format H:i)
     */
    public function getSlotWindowAttribute()
    {
        return [
            'start' => $this->fo_window_start->format('H:i'),
            'end' => $this->fo_window_end->format('H:i'),
        ];
    }

    public function getValidationStatusLabelAttribute()
    {
        return config('daily_report_fo.validation_statuses')[$this->validation_status]['label']
            ?? ucfirst($this->validation_status);
    }

    public function getValidationStatusColorAttribute()
    {
        return config('daily_report_fo.validation_statuses')[$this->validation_status]['color']
            ?? 'gray';
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
