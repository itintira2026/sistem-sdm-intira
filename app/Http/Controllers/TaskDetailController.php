<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskProgressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Part C4 + C5 — Halaman Detail Tugas, Edit, dan Delete (soft delete).
 *
 * CATATAN OTORISASI (show/view): masih LONGGAR (creator, assignee, atau
 * role manajerial apapun boleh lihat) — filtering presisi per cabang/
 * divisi itu ranahnya Part E3 (drill-down/tim view).
 *
 * CATATAN EDIT: SENGAJA dibatasi cuma field "aman" (title, description,
 * unit, division_scope, reference_task_id). Field struktural
 * (tracking_mode, repeat_mode, period_start/end, has_weight) TIDAK BISA
 * diubah setelah tugas dibuat — karena task_assignees & task_entries
 * yang udah ke-generate bergantung ke nilai-nilai itu. Restore
 * soft-delete itu ranahnya Part G3 (Panel Koreksi Data Superadmin),
 * bukan di sini.
 */
class TaskDetailController extends Controller
{
    private const BROAD_VIEW_ROLES = [
        'owner',
        'ketua',
        'direktur_utama',
        'direktur_bisnis',
        'direktur_operasional',
        'kadiv',
        'korwil',
        'manager',
    ];

    public function show(Task $task, TaskProgressService $progressService)
    {
        $user = Auth::user();
        $this->authorizeView($user, $task);

        // $task->load(['creator', 'division', 'referenceTask', 'assignees.user']);

        // $assigneesData = $task->assignees->map(function ($assignee) use ($progressService) {
        //     return [
        //         'assignee' => $assignee,
        //         'percentage' => $progressService->currentPercentage($assignee),
        //         'entries' => $assignee->entries()->orderBy('entry_date')->get(),
        //     ];
        // });

        $task->load(['creator', 'division', 'referenceTask', 'assignees.user']);

        // Yang boleh lihat SEMUA assignee: superadmin, pembuat tugas, dan atasan.
        // Selain itu (assignee biasa) cuma lihat dirinya sendiri.
        $canSeeAll = $user->hasRole('superadmin')
            || $task->created_by === $user->id
            || $user->hasAnyRole(self::BROAD_VIEW_ROLES);

        $visibleAssignees = $canSeeAll
            ? $task->assignees
            : $task->assignees->where('user_id', $user->id);

        $assigneesData = $visibleAssignees->map(function ($assignee) use ($progressService) {
            return [
                'assignee' => $assignee,
                'percentage' => $progressService->currentPercentage($assignee),
                'entries' => $assignee->entries()->orderBy('entry_date')->get(),
            ];
        })->values();
        // dd($assigneesData);
        return view('tasks.show', [
            'task' => $task,
            'assigneesData' => $assigneesData,
            'canManage' => $user->hasRole('superadmin') || $task->created_by === $user->id,
        ]);
    }

    public function edit(Task $task)
    {
        $this->authorizeManage(Auth::user(), $task);

        return view('tasks.edit', [
            'task' => $task,
            'divisions' => Division::orderBy('name')->get(),
            'previousTasks' => Task::where('id', '!=', $task->id)->latest()->limit(20)->get(),
        ]);
    }

    public function update(Request $request, Task $task)
    {
        $this->authorizeManage(Auth::user(), $task);

        // SENGAJA cuma field "aman" — lihat catatan di docblock class.
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'nullable|string|max:50',
            'division_scope' => 'nullable|exists:divisions,id',
            'reference_task_id' => 'nullable|exists:tasks,id',
        ]);

        $task->update($validated);

        return redirect()->route('tasks.show', $task->id)->with('success', 'Tugas berhasil diperbarui.');
    }

    public function destroy(Task $task)
    {
        $user = Auth::user();

        if (! $user->hasRole('superadmin') && $task->created_by !== $user->id) {
            abort(403, 'Hanya pembuat tugas atau Superadmin yang bisa menghapus tugas ini.');
        }

        $task->delete(); // soft delete

        return redirect()->route('dashboard')->with('success', 'Tugas berhasil dihapus (soft delete).');
    }

    private function authorizeView(User $user, Task $task): void
    {
        if ($user->hasRole('superadmin')) {
            return;
        }

        if ($task->created_by === $user->id) {
            return;
        }

        if ($task->assignees->contains('user_id', $user->id)) {
            return;
        }

        if ($user->hasAnyRole(self::BROAD_VIEW_ROLES)) {
            return;
        }

        abort(403, 'Anda tidak memiliki akses ke tugas ini.');
    }

    private function authorizeManage(User $user, Task $task): void
    {
        if ($user->hasRole('superadmin') || $task->created_by === $user->id) {
            return;
        }

        abort(403, 'Hanya pembuat tugas atau Superadmin yang bisa mengedit tugas ini.');
    }
}
