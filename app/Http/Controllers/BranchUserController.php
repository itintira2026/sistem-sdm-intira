<?php

// namespace App\Http\Controllers;

// use App\Models\Branch;
// use App\Models\User;
// use App\Models\BranchUser;
// use Illuminate\Http\Request;

// class BranchUserController extends Controller
// {
//     /**
//      * Show form untuk assign user ke branch
//      */
//     public function create(Branch $branch)
//     {
//         // Get users yang belum di-assign ke branch ini dan aktif
//         $users = User::where('is_active', true)->whereDoesntHave('branchAssignments', function($query) use ($branch) {
//                          $query->where('branch_id', $branch->id);
//                      })
//                      ->get();

//         // Get assigned users dengan detail assignment
//         $assignedUsers = $branch->users()->with('roles')->get();

//         return view('management_data.branch_user.create', compact('branch', 'users', 'assignedUsers'));
//     }

//     /**
//      * Assign user ke branch
//      */
//     public function store(Request $request, Branch $branch)
//     {
//         $validated = $request->validate([
//             'user_id' => 'required|exists:users,id',
//             'is_manager' => 'boolean',
//         ]);

//         // Cek apakah user sudah di-assign
//         if ($branch->hasUser($validated['user_id'])) {
//             return back()->with('error', 'User sudah terdaftar di cabang ini!');
//         }

//         // Assign user menggunakan method dari model
//         $branch->assignUser(
//             $validated['user_id'],
//             $request->has('is_manager')
//         );

//         return back()->with('success', 'User berhasil ditambahkan ke cabang!');
//     }

//     /**
//      * Update status manager
//      */
//     public function toggleManager(Branch $branch, User $user)
//     {
//         $assignment = BranchUser::where('branch_id', $branch->id)
//                                 ->where('user_id', $user->id)
//                                 ->firstOrFail();

//         $assignment->toggleManagerStatus();

//         $status = $assignment->is_manager ? 'ditambahkan' : 'dihapus';

//         return back()->with('success', "Status manager berhasil {$status}!");
//     }

//     /**
//      * Remove user dari branch
//      */
//     public function destroy(Branch $branch, User $user)
//     {
//         $assignment = BranchUser::where('branch_id', $branch->id)
//                                 ->where('user_id', $user->id)
//                                 ->firstOrFail();

//         $assignment->delete();

//         return back()->with('success', 'User berhasil dihapus dari cabang!');
//     }

//     /**
//      * Bulk assign users
//      */
//     public function bulkStore(Request $request, Branch $branch)
//     {
//         $validated = $request->validate([
//             'user_ids' => 'required|array',
//             'user_ids.*' => 'exists:users,id',
//         ]);

//         $count = 0;
//         foreach ($validated['user_ids'] as $userId) {
//             if (!$branch->hasUser($userId)) {
//                 $branch->assignUser($userId, false);
//                 $count++;
//             }
//         }

//         return back()->with('success', "{$count} user berhasil ditambahkan ke cabang!");
//     }

//     /**
//      * Get statistics
//      */
//     public function statistics(Branch $branch)
//     {
//         $stats = [
//             'total_users' => $branch->userAssignments()->count(),
//             'total_managers' => $branch->userAssignments()->managers()->count(),
//             'active_users' => $branch->activeUsers()->count(),
//             'assignments' => BranchUser::forBranch($branch->id)
//                                       ->with(['user', 'branch'])
//                                       ->latest()
//                                       ->get(),
//         ];

//         return response()->json($stats);
//     }
// }

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
        // Get users yang:
        // 1. Aktif (is_active = true)
        // 2. Role FO
        // 3. TIDAK punya branch_users yang aktif (belum ditempatkan di cabang manapun)
        // ATAU sudah pernah di cabang ini tapi sekarang inactive
        $users = User::where('is_active', true)
            ->role('fo') // ← Spatie whereHas('roles')
            ->where(function ($q) use ($branch) {
                // TIDAK punya assignment aktif di cabang manapun
                $q->whereDoesntHave('branchAssignments', function ($q2) {
                    $q2->where('is_active', true);
                })
                    // ATAU pernah di cabang ini tapi sekarang inactive (bisa di-reactivate)
                    ->orWhereHas('branchAssignments', function ($q2) use ($branch) {
                        $q2->where('branch_id', $branch->id)
                            ->where('is_active', false);
                    });
            })
            ->orderBy('name')
            ->get();

        // Get assigned users yang AKTIF di cabang ini
        $assignedUsers = $branch->users()
            ->wherePivot('is_active', true)
            ->with('roles')
            ->get();

        return view('management_data.branch_user.create', compact('branch', 'users', 'assignedUsers'));
    }

    /**
     * Assign user ke branch (atau reaktivasi jika sudah ada)
     */
    public function store(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'is_manager' => 'boolean',
        ]);

        // Cek apakah user sudah pernah di-assign (record lama exists, bisa aktif atau tidak)
        $existingAssignment = BranchUser::where('branch_id', $branch->id)
            ->where('user_id', $validated['user_id'])
            ->first();

        if ($existingAssignment) {
            // Jika record lama ada dan masih aktif → error
            if ($existingAssignment->is_active) {
                return back()->with('error', 'User sudah terdaftar di cabang ini!');
            }

            // Jika record lama inactive → reaktivasi
            $existingAssignment->update([
                'is_active' => true,
                'is_manager' => $request->has('is_manager'),
            ]);

            return back()->with('success', 'User berhasil diaktifkan kembali di cabang ini!');
        }

        // Jika belum pernah di-assign → buat record baru
        BranchUser::create([
            'branch_id' => $branch->id,
            'user_id' => $validated['user_id'],
            'is_manager' => $request->has('is_manager'),
            'is_active' => true,
        ]);

        return back()->with('success', 'User berhasil ditambahkan ke cabang!');
    }

    /**
     * Update status manager
     */
    public function toggleManager(Branch $branch, User $user)
    {
        $assignment = BranchUser::where('branch_id', $branch->id)
            ->where('user_id', $user->id)
            ->where('is_active', true) // ← hanya yang aktif
            ->firstOrFail();

        $assignment->toggleManagerStatus();

        $status = $assignment->is_manager ? 'ditambahkan' : 'dihapus';

        return back()->with('success', "Status manager berhasil {$status}!");
    }

    /**
     * Soft delete — Set is_active = false
     * (TIDAK hapus record, agar historical data tetap utuh)
     */
    public function destroy(Branch $branch, User $user)
    {
        $assignment = BranchUser::where('branch_id', $branch->id)
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->firstOrFail();

        // Soft delete — set is_active = false
        $assignment->update(['is_active' => false]);

        // Log event (opsional, bisa dipakai untuk audit trail)
        \Log::info("User {$user->name} (ID: {$user->id}) removed from Branch {$branch->name} (ID: {$branch->id})");

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
            $existing = BranchUser::where('branch_id', $branch->id)
                ->where('user_id', $userId)
                ->first();

            if ($existing) {
                // Reaktivasi jika inactive
                if (!$existing->is_active) {
                    $existing->update(['is_active' => true]);
                    $count++;
                }
            } else {
                // Buat baru
                BranchUser::create([
                    'branch_id' => $branch->id,
                    'user_id' => $userId,
                    'is_manager' => false,
                    'is_active' => true,
                ]);
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
            'total_users' => $branch->userAssignments()->where('is_active', true)->count(),
            'total_managers' => $branch->userAssignments()->managers()->where('is_active', true)->count(),
            'active_users' => $branch->activeUsers()->count(),
            'inactive_assignments' => $branch->userAssignments()->where('is_active', false)->count(),
            'assignments' => BranchUser::forBranch($branch->id)
                ->where('is_active', true)
                ->with(['user', 'branch'])
                ->latest()
                ->get(),
        ];

        return response()->json($stats);
    }
}
