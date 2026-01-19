<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'password',
        'profile_photo',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi many-to-many dengan Branch menggunakan BranchUser model
     */
    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'branch_users')
            ->using(BranchUser::class)
            ->withPivot('is_manager')
            ->withTimestamps();
    }

    /**
     * Get all branch assignments dengan detail
     */
    public function branchAssignments()
    {
        return $this->hasMany(BranchUser::class);
    }

    public function displayBranchName(): string
    {
        $branches = $this->branches;

        if ($branches->isEmpty()) {
            return 'Tidak ada cabang';
        }

        // Jika manager (punya is_manager = 1)
        $isManager = $this->branchAssignments()
            ->where('is_manager', 1)
            ->exists();

        if ($isManager && $branches->count() > 1) {
            return $branches->first()->name . ' +' . ($branches->count() - 1);
        }

        // FO / non manager
        return $branches->first()->name;
    }


    /**
     * Cek apakah user adalah manager di cabang tertentu
     */
    public function isManagerAt($branchId)
    {
        return $this->branchAssignments()
            ->where('branch_id', $branchId)
            ->where('is_manager', true)
            ->exists();
    }

    /**
     * Get cabang dimana user adalah manager
     */
    public function managedBranches()
    {
        return $this->belongsToMany(Branch::class, 'branch_users')
            ->using(BranchUser::class)
            ->wherePivot('is_manager', true)
            ->withTimestamps();
    }

    /**
     * Get assignment untuk cabang tertentu
     */
    public function getAssignmentFor($branchId)
    {
        return $this->branchAssignments()
            ->where('branch_id', $branchId)
            ->first();
    }

    /**
     * Assign user ke branch
     */
    public function assignToBranch($branchId, $isManager = false)
    {
        return BranchUser::firstOrCreate(
            [
                'user_id' => $this->id,
                'branch_id' => $branchId,
            ],
            [
                'is_manager' => $isManager,
            ]
        );
    }

    /**
     * Remove user dari branch
     */
    public function removeFromBranch($branchId)
    {
        return $this->branchAssignments()
            ->where('branch_id', $branchId)
            ->delete();
    }

    /**
     * Get all gaji pokok history
     */
    public function gajiPokokHistory()
    {
        return $this->hasManyThrough(
            GajihPokok::class,
            BranchUser::class,
            'user_id', // Foreign key on branch_user table
            'branch_user_id', // Foreign key on gaji_pokok table
            'id', // Local key on users table
            'id' // Local key on branch_user table
        );
    }

    /**
     * Get gaji pokok di cabang tertentu untuk bulan tertentu
     */
    public function getGajiPokokAt($branchId, $bulan, $tahun)
    {
        $assignment = $this->branchAssignments()
            ->where('branch_id', $branchId)
            ->first();

        if (!$assignment) return null;

        return $assignment->getGajiPokokForMonth($bulan, $tahun);
    }

    /**
     * Get gaji pokok terbaru di cabang
     */
    public function getLatestGajiPokokAt($branchId)
    {
        $assignment = $this->branchAssignments()
            ->where('branch_id', $branchId)
            ->first();

        if (!$assignment) return null;

        return $assignment->getLatestGajiPokok();
    }
}
