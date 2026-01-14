<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use App\Models\BranchUser;
use Illuminate\Http\Request;

class BranchUserController extends Controller
{
    /**
     * Show form untuk assign user ke branch
     */
    public function create(Branch $branch)
    {
        // Get users yang belum di-assign ke branch ini dan aktif
        $users = User::where('is_active', true)->whereDoesntHave('branchAssignments', function($query) use ($branch) {
                         $query->where('branch_id', $branch->id);
                     })
                     ->get();
        
        // Get assigned users dengan detail assignment
        $assignedUsers = $branch->users()->with('roles')->get();
        
        return view('management_data.branch_user.create', compact('branch', 'users', 'assignedUsers'));
    }

    /**
     * Assign user ke branch
     */
    public function store(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'is_manager' => 'boolean',
        ]);

        // Cek apakah user sudah di-assign
        if ($branch->hasUser($validated['user_id'])) {
            return back()->with('error', 'User sudah terdaftar di cabang ini!');
        }

        // Assign user menggunakan method dari model
        $branch->assignUser(
            $validated['user_id'], 
            $request->has('is_manager')
        );

        return back()->with('success', 'User berhasil ditambahkan ke cabang!');
    }

    /**
     * Update status manager
     */
    public function toggleManager(Branch $branch, User $user)
    {
        $assignment = BranchUser::where('branch_id', $branch->id)
                                ->where('user_id', $user->id)
                                ->firstOrFail();

        $assignment->toggleManagerStatus();

        $status = $assignment->is_manager ? 'ditambahkan' : 'dihapus';
        
        return back()->with('success', "Status manager berhasil {$status}!");
    }

    /**
     * Remove user dari branch
     */
    public function destroy(Branch $branch, User $user)
    {
        $assignment = BranchUser::where('branch_id', $branch->id)
                                ->where('user_id', $user->id)
                                ->firstOrFail();

        $assignment->delete();

        return back()->with('success', 'User berhasil dihapus dari cabang!');
    }

    /**
     * Bulk assign users
     */
    public function bulkStore(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $count = 0;
        foreach ($validated['user_ids'] as $userId) {
            if (!$branch->hasUser($userId)) {
                $branch->assignUser($userId, false);
                $count++;
            }
        }

        return back()->with('success', "{$count} user berhasil ditambahkan ke cabang!");
    }

    /**
     * Get statistics
     */
    public function statistics(Branch $branch)
    {
        $stats = [
            'total_users' => $branch->userAssignments()->count(),
            'total_managers' => $branch->userAssignments()->managers()->count(),
            'active_users' => $branch->activeUsers()->count(),
            'assignments' => BranchUser::forBranch($branch->id)
                                      ->with(['user', 'branch'])
                                      ->latest()
                                      ->get(),
        ];

        return response()->json($stats);
    }
}