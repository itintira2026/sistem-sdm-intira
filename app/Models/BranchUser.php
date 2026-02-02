<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BranchUser extends Pivot
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'branch_users';

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = true;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'branch_id',
        'user_id',
        'is_manager',
        'is_active'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_manager' => 'boolean',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the branch that owns the assignment.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the user that owns the assignment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to filter only managers.
     */
    public function scopeManagers($query)
    {
        return $query->where('is_manager', true);
    }

    /**
     * Scope to filter by branch.
     */
    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope to filter by user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Check if this assignment is for a manager.
     */
    public function isManager()
    {
        return $this->is_manager;
    }

    /**
     * Set user as manager.
     */
    public function setAsManager()
    {
        $this->update(['is_manager' => true]);
    }

    /**
     * Remove manager status.
     */
    public function removeManagerStatus()
    {
        $this->update(['is_manager' => false]);
    }

    /**
     * Toggle manager status.
     */
    public function toggleManagerStatus()
    {
        $this->update(['is_manager' => !$this->is_manager]);
    }

    /**
     * Relasi ke gaji pokok
     */
    // public function gajihPokok()
    // {
    //     return $this->hasMany(GajihPokok::class);
    // }

    public function gajihPokok()
    {
        return $this->hasMany(
            GajihPokok::class,
            'branch_user_id', // FK di gajih_pokoks
            'id'              // PK di branch_users
        );
    }

    /**
     * Get gaji pokok untuk bulan tertentu
     */
    public function getGajihPokokForMonth($bulan, $tahun)
    {
        return $this->gajiPokok()
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->first();
    }

    /**
     * Get gaji pokok terbaru
     */
    public function getLatestGajihPokok()
    {
        return $this->gajiPokok()->latest()->first();
    }

    /**
     * Set gaji pokok untuk bulan tertentu
     */
    public function setGajihPokok($amount, $bulan, $tahun, $keterangan = null)
    {
        return $this->gajiPokok()->updateOrCreate(
            [
                'bulan' => $bulan,
                'tahun' => $tahun,
            ],
            [
                'amount' => $amount,
                'keterangan' => $keterangan,
            ]
        );
    }

    public function potongans()
    {
        return $this->hasMany(
            Potongan::class,
            'branch_user_id',
            'id'
        );
    }


    // helper
    public function getFormattedAssignmentDateAttribute()
    {
        return $this->created_at->format('d M Y H:i');
    }

    /**
     * Get user name with branch info
     */
    public function getFullInfoAttribute()
    {
        return "{$this->user->name} - {$this->branch->name}" .
            ($this->is_manager ? ' (Manager)' : '');
    }

    /**
     * Boot method untuk event listeners
     */
    protected static function boot()
    {
        parent::boot();

        // Event setelah user di-assign
        static::created(function ($assignment) {
            // Log atau kirim notifikasi
            \Log::info("User {$assignment->user->name} assigned to {$assignment->branch->name}");
        });

        // Event sebelum user di-remove
        static::deleting(function ($assignment) {
            // Log atau kirim notifikasi
            \Log::info("User {$assignment->user->name} removed from {$assignment->branch->name}");
        });
    }
}
