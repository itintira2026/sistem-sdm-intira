<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Part G3 — Panel Koreksi Data Superadmin.
 * Isinya 2 hal: restore tugas yang ke-soft-delete, dan pencarian entry
 * yang diarahkan ke halaman override (Part D5, sudah ada — cuma di-link
 * dari sini, bukan dibangun ulang).
 */
class PanelKoreksiDataController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeSuperadmin();

        // --- Tugas terhapus ---
        $deletedSearch = trim((string) $request->get('deleted_search'));

        $deletedTasks = Task::onlyTrashed()
            ->with('creator')
            ->withCount('assignees')
            ->when($deletedSearch, fn($q) => $q->where('title', 'like', "%{$deletedSearch}%"))
            ->latest('deleted_at')
            ->paginate(10, ['*'], 'deleted_page')
            ->withQueryString();

        // --- Entry ---
        $dateMode = $request->get('date_mode');
        $dateMode = in_array($dateMode, ['all', 'with_date', 'without_date'], true)
            ? $dateMode
            : 'all';
        $taskType = $request->get('task_type');
        $taskType = in_array($taskType, ['kpi', 'additional', 'all'], true)
            ? $taskType
            : 'kpi';
        $frozenMode = $request->get('frozen_mode') === 'include' ? 'include' : 'hide';
        $dateBasis = $request->get('date_basis') === 'created_at' ? 'created_at' : 'entry_date';
        $ignoreDateRange = $dateMode === 'without_date' && $dateBasis === 'entry_date';

        $filters = [
            'q'      => trim((string) $request->get('entry_search')),
            'status' => $request->get('status'),
            'task_type' => $taskType,
            'frozen_mode' => $frozenMode,
            'date_basis' => $dateBasis,
            'date_mode' => $dateMode,
            'from'   => $ignoreDateRange ? null : $request->get('date_from'),
            'to'     => $ignoreDateRange ? null : $request->get('date_to'),
        ];

        $entries = TaskEntry::with([
            'taskAssignee.task' => fn($q) => $q->withTrashed(),
            'taskAssignee.user',
        ])
            ->when($filters['q'], function ($query, $term) {
                $query->whereHas('taskAssignee', function ($a) use ($term) {
                    $a->whereHas('user', fn($u) => $u->where('name', 'like', "%{$term}%"))
                        ->orWhereHas('task', fn($t) => $t->withTrashed()->where('title', 'like', "%{$term}%"));
                });
            })
            ->when($filters['status'] === 'self', fn($q) => $q->whereNull('validation_status'))
            ->when(
                $filters['status'] && $filters['status'] !== 'self',
                fn($q) => $q->where('validation_status', $filters['status'])
            )
            ->when($filters['task_type'] !== 'all', function ($q) use ($filters) {
                $q->whereHas('taskAssignee.task', fn($task) => $task
                    ->withTrashed()
                    ->where('has_weight', $filters['task_type'] === 'kpi'));
            })
            ->when($filters['frozen_mode'] === 'hide', fn($q) => $q->whereHas(
                'taskAssignee',
                fn($assignee) => $assignee->whereNull('finalized_at')
            ))
            ->when($filters['date_mode'] === 'with_date', fn($q) => $q->whereNotNull('entry_date'))
            ->when($filters['date_mode'] === 'without_date', fn($q) => $q->whereNull('entry_date'))
            ->when($filters['from'], fn($q, $d) => $q->whereDate($filters['date_basis'], '>=', $d))
            ->when($filters['to'], fn($q, $d) => $q->whereDate($filters['date_basis'], '<=', $d))
            ->latest()
            ->paginate(15, ['*'], 'entries_page')
            ->withQueryString();

        $statusOptions = TaskEntry::whereNotNull('validation_status')
            ->distinct()
            ->orderBy('validation_status')
            ->pluck('validation_status');

        $hasFilter = (bool) array_filter([
            $filters['q'],
            $filters['status'],
            $filters['task_type'] !== 'kpi',
            $filters['frozen_mode'] !== 'hide',
            $filters['date_basis'] !== 'entry_date',
            $filters['from'],
            $filters['to'],
        ]) || $filters['date_mode'] !== 'all';

        return view('superadmin.koreksi-data', compact(
            'deletedTasks',
            'deletedSearch',
            'entries',
            'filters',
            'statusOptions',
            'hasFilter'
        ));
    }

    public function restore($id)
    {
        $this->authorizeSuperadmin();

        $task = Task::onlyTrashed()->findOrFail($id);
        $task->restore();

        return back()->with('success', "Tugas '{$task->title}' berhasil dipulihkan.");
    }

    private function authorizeSuperadmin(): void
    {
        if (! Auth::user()->hasRole('superadmin')) {
            abort(403, 'Hanya Superadmin yang bisa mengakses panel ini.');
        }
    }
}
