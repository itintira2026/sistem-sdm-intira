<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TaskAssignmentPermissionService;
use App\Services\TaskWeightService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Part C1 (lanjutan) — Halaman "Set Bobot" per orang, dipanggil TaskWeightService.
 * Terpisah dari TaskController biar gak numpuk 1 file gede.
 *
 * DIUBAH: sekarang sekalian bisa koreksi individual_target per tugas, dan
 * menampilkan riwayat assignee yang sudah dikunci (freeze mutasi ATAU
 * dinonaktifkan manual — lihat TaskAssigneeController::remove()).
 */
class TaskWeightController extends Controller
{
    public function __construct(
        private TaskWeightService $weightService,
        private TaskAssignmentPermissionService $permissionService,
    ) {}

    public function edit(User $user)
    {
        $this->authorizeAccess($user);

        $assignments = $this->weightService->activeWeightedAssignments($user);
        $currentTotal = $this->weightService->currentTotalWeight($user);
        $lockedAssignments = $this->weightService->lockedAssignments($user);

        return view('tasks.weight-reinput', [
            'targetUser' => $user,
            'assignments' => $assignments,
            'currentTotal' => $currentTotal,
            'lockedAssignments' => $lockedAssignments,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeAccess($user);

        $validated = $request->validate([
            'weights' => 'required|array',
            'weights.*' => 'required|numeric|min:0|max:100',
            'targets' => 'nullable|array',
            'targets.*' => 'nullable|numeric|min:0',
        ]);

        $result = $this->weightService->validateTotal($user, $validated['weights']);

        if (! $result['valid']) {
            return back()->withInput()->with('error', $result['message']);
        }

        DB::transaction(function () use ($validated, $user) {
            $this->weightService->applyWeights($validated['weights']);
            $this->weightService->applyTargets($user, $validated['targets'] ?? []);
        });

        return redirect()
            ->route('tasks.weight-reinput', $user->id)
            ->with('success', 'Bobot & target KPI berhasil disimpan — total pas 100%.');
    }

    /**
     * Otorisasi: cuma orang yang punya hak assign ke $user ini (via
     * TaskAssignmentPermissionService) atau superadmin yang boleh
     * atur bobotnya. Ini mencegah orang random ubah bobot siapapun.
     */
    private function authorizeAccess(User $targetUser): void
    {
        $currentUser = Auth::user();

        if ($currentUser->hasRole('superadmin')) {
            return;
        }

        if (! $this->permissionService->canAssign($currentUser, $targetUser)) {
            abort(403, 'Anda tidak memiliki hak untuk mengatur bobot KPI user ini.');
        }
    }
}
