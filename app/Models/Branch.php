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
        'is_active',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'branch_users')
            ->using(BranchUser::class)
            ->withPivot('is_manager')
            ->withTimestamps();
    }

    /**
     * Get all user assignments dengan detail
     */
    public function userAssignments()
    {
        return $this->hasMany(BranchUser::class);
    }

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
    public function managers()
    {
        return $this->belongsToMany(User::class, 'branch_users')
            ->using(BranchUser::class)
            ->wherePivot('is_manager', true)
            ->withTimestamps();
    }

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
    public function assignUser($userId, $isManager = false)
    {
        return BranchUser::firstOrCreate(
            [
                'branch_id' => $this->id,
                'user_id' => $userId,
            ],
            [
                'is_manager' => $isManager,
            ]
        );
    }

    /**
     * Remove user dari branch
     */
    public function removeUser($userId)
    {
        return $this->userAssignments()
            ->where('user_id', $userId)
            ->delete();
    }

    /**
     * Check if user exists in this branch
     */
    public function hasUser($userId)
    {
        return $this->userAssignments()
            ->where('user_id', $userId)
            ->exists();
    }

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
}
