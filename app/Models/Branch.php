<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'address',
        'code',
        'timezone',
        'longitude',
        'latitude',
        'is_active',
    ];

    // public function users()
    // {
    //     return $this->belongsToMany(User::class, 'branch_users')
    //         ->using(BranchUser::class)
    //         ->withPivot('is_manager')
    //         ->withTimestamps();
    // }

    public function dailyContents()
    {
        return $this->hasMany(DailyContent::class);
    }

    /**
     * Get all user assignments dengan detail
     */
    // public function userAssignments()
    // {
    //     return $this->hasMany(BranchUser::class);
    // }

    /**
     * Get hanya user yang aktif di cabang ini
     */
    public function activeUsers()
    {
        return $this->belongsToMany(User::class, 'branch_users')
            ->using(BranchUser::class)
            ->where('users.is_active', true)
            ->withPivot('is_manager')
            ->withTimestamps();
    }

    /**
     * Get manager cabang
     */
    // public function managers()
    // {
    //     return $this->belongsToMany(User::class, 'branch_users')
    //         ->using(BranchUser::class)
    //         ->wherePivot('is_manager', true)
    //         ->withTimestamps();
    // }

    /**
     * Get jumlah user di cabang ini
     */
    public function getUserCountAttribute()
    {
        return $this->users()->count();
    }

    /**
     * Get jumlah manager di cabang ini
     */
    public function getManagerCountAttribute()
    {
        return $this->managers()->count();
    }

    /**
     * Assign user ke branch
     */
    // public function assignUser($userId, $isManager = false)
    // {
    //     return BranchUser::firstOrCreate(
    //         [
    //             'branch_id' => $this->id,
    //             'user_id' => $userId,
    //         ],
    //         [
    //             'is_manager' => $isManager,
    //         ]
    //     );
    // }

    /**
     * Remove user dari branch
     */
    // public function removeUser($userId)
    // {
    //     return $this->userAssignments()
    //         ->where('user_id', $userId)
    //         ->delete();
    // }

    /**
     * Check if user exists in this branch
     */
    // public function hasUser($userId)
    // {
    //     return $this->userAssignments()
    //         ->where('user_id', $userId)
    //         ->exists();
    // }

    /**
     * Get all gaji pokok di cabang ini
     */
    // public function gajiPokok()
    // {
    //     return $this->hasManyThrough(
    //         GajihPokok::class,
    //         BranchUser::class,
    //         'branch_id', // Foreign key on branch_user table
    //         'branch_user_id', // Foreign key on gaji_pokok table
    //         'id', // Local key on branches table
    //         'id' // Local key on branch_user table
    //     );
    // }
    /**
     * Ambil semua gaji pokok user yang TERDAFTAR di cabang ini
     * (walaupun gajinya diinput dari cabang lain)
     */
    public function gajiPokoks()
    {
        return GajihPokok::whereIn(
            'user_id',
            $this->userAssignments()->pluck('user_id')
        );
    }


    /**
     * Get total gaji pokok untuk bulan tertentu
     */
    public function getTotalGajiPokokForMonth($bulan, $tahun)
    {
        return $this->gajiPokoks()
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->sum('amount');
    }

    // public function getTotalGajiPokokForMonth($bulan, $tahun)
    // {
    //     return $this->gajiPokok()
    //         ->where('bulan', $bulan)
    //         ->where('tahun', $tahun)
    //         ->sum('amount');
    // }
    public function branchUsers()
    {
        return $this->hasMany(BranchUser::class);
    }

    // Patch untuk Branch model — tambahkan/ganti method berikut:

    /**
     * Get users yang AKTIF di cabang ini (is_active = true di pivot)
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'branch_users')
            ->using(BranchUser::class)
            ->wherePivot('is_active', true) // ← filter aktif
            ->withPivot('is_manager', 'is_active')
            ->withTimestamps();
    }

    /**
     * Get SEMUA user assignments (termasuk inactive) — untuk keperluan audit
     */
    public function allUserAssignments()
    {
        return $this->hasMany(BranchUser::class);
    }

    /**
     * Get hanya assignments yang aktif
     */
    public function userAssignments()
    {
        return $this->hasMany(BranchUser::class)->where('is_active', true);
    }

    /**
     * Get manager cabang (aktif)
     */
    public function managers()
    {
        return $this->belongsToMany(User::class, 'branch_users')
            ->using(BranchUser::class)
            ->wherePivot('is_manager', true)
            ->wherePivot('is_active', true) // ← filter aktif
            ->withTimestamps();
    }

    /**
     * Check if user exists in this branch (dan aktif)
     */
    public function hasUser($userId)
    {
        return $this->userAssignments()
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Assign user ke branch (atau reaktivasi)
     */
    public function assignUser($userId, $isManager = false)
    {
        $existing = BranchUser::where('branch_id', $this->id)
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            // Reaktivasi jika inactive
            $existing->update([
                'is_active' => true,
                'is_manager' => $isManager,
            ]);
            return $existing;
        }

        // Buat baru
        return BranchUser::create([
            'branch_id' => $this->id,
            'user_id' => $userId,
            'is_manager' => $isManager,
            'is_active' => true,
        ]);
    }

    /**
     * Soft delete user dari branch
     */
    public function removeUser($userId)
    {
        return $this->allUserAssignments()
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->update(['is_active' => false]);
    }
}
