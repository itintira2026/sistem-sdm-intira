<?php

namespace App\Http\Controllers;

use App\Models\TaskEntry;
use App\Models\TaskStatusLog;
use App\Services\KpiSnapshotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Part D5 — Form override Superadmin: edit entry manapun + wajib alasan
 * + audit trail (§7: "Hanya Superadmin boleh mengoreksi entry manapun,
 * wajib isi alasan, sistem menandai 'Dikoreksi oleh Superadmin'").
 *
 * §11: kalau assignee-nya sudah pernah di-snapshot (finalized_at != null),
 * koreksi ini otomatis memicu hitung ulang lewat KpiSnapshotService.
 */
class TaskEntryOverrideController extends Controller
{
    public function edit(TaskEntry $entry)
    {
        $this->authorizeSuperadmin();

        $entry->load(['taskAssignee.task', 'taskAssignee.user']);

        return view('task-entries.override', compact('entry'));
    }

    public function update(Request $request, TaskEntry $entry, KpiSnapshotService $snapshotService)
    {
        $this->authorizeSuperadmin();

        $task = $entry->taskAssignee->task;

        $validated = $request->validate([
            'value_number' => 'nullable|numeric|min:0',
            'value_checklist' => 'nullable|boolean',
            'validation_status' => 'nullable|in:pending,checked,approved,rejected',
            'reason' => 'required|string|max:500',
        ], [
            'reason.required' => 'Alasan koreksi wajib diisi.',
        ]);

        $oldValues = $entry->only(['value_number', 'value_checklist', 'validation_status']);

        $entry->update([
            'value_number' => $task->tracking_mode === 'numeric' ? ($validated['value_number'] ?? null) : null,
            'value_checklist' => $task->tracking_mode === 'checklist' ? $request->boolean('value_checklist') : null,
            // Self-record (validation_status null) TETAP null walau di-submit apapun —
            // superadmin cuma boleh koreksi NILAINYA, bukan mengubahnya jadi ikut alur validasi
            'validation_status' => $entry->validation_status !== null
                ? ($validated['validation_status'] ?? $entry->validation_status)
                : null,
        ]);

        TaskStatusLog::record(
            null,
            $entry->id,
            'corrected_by_superadmin',
            json_encode($oldValues),
            json_encode($entry->fresh()->only(['value_number', 'value_checklist', 'validation_status'])),
            Auth::id(),
            $validated['reason']
        );

        $assignee = $entry->taskAssignee;

        if ($assignee->finalized_at !== null) {
            $snapshotService->recalculateAssignee($assignee);
        }

        return redirect()
            ->route('tasks.show', $task->id)
            ->with('success', 'Entry berhasil dikoreksi oleh Superadmin. Tercatat di audit trail.');
    }

    private function authorizeSuperadmin(): void
    {
        if (! Auth::user()->hasRole('superadmin')) {
            abort(403, 'Hanya Superadmin yang bisa melakukan koreksi data.');
        }
    }
}
