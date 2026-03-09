<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\Branch;

class BranchHelper
{
    /**
     * Get user's active branch
     *
     * @param \App\Models\User|null $user
     * @return \App\Models\Branch|null
     */
    public static function getUserActiveBranch($user = null)
    {
        $user = $user ?? auth()->user();
        
        if (!$user) {
            return null;
        }
        
        // Coba ambil dari session dulu (untuk performa)
        $sessionKey = 'user_active_branch_' . $user->id;
        if (session()->has($sessionKey)) {
            return session($sessionKey);
        }
        
        // Ambil dari database
        $branch = $user->branches()->first();
        
        if ($branch) {
            session([$sessionKey => $branch]);
        }
        
        return $branch;
    }

    /**
     * Get all user branches
     *
     * @param \App\Models\User|null $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getUserBranches($user = null)
    {
        $user = $user ?? auth()->user();
        
        if (!$user) {
            return collect();
        }
        
        return $user->branches()->get();
    }

    /**
     * Check if user is manager at branch
     *
     * @param int $branchId
     * @param \App\Models\User|null $user
     * @return bool
     */
    public static function isUserManagerAt($branchId, $user = null)
    {
        $user = $user ?? auth()->user();
        
        if (!$user) {
            return false;
        }
        
        return $user->branchAssignments()
            ->where('branch_id', $branchId)
            ->where('is_manager', true)
            ->exists();
    }
}

// Global helper functions (optional)
if (!function_exists('getUserActiveBranch')) {
    function getUserActiveBranch($user = null)
    {
        return BranchHelper::getUserActiveBranch($user);
    }
}

if (!function_exists('getUserBranches')) {
    function getUserBranches($user = null)
    {
        return BranchHelper::getUserBranches($user);
    }
}

if (!function_exists('isUserManagerAt')) {
    function isUserManagerAt($branchId, $user = null)
    {
        return BranchHelper::isUserManagerAt($branchId, $user);
    }
}