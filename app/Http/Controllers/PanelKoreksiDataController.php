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
        $filters = [
            'q'      => trim((string) $request->get('entry_search')),
            'status' => $request->get('status'),
            'from'   => $request->get('date_from'),
            'to'     => $request->get('date_to'),
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
            ->when($filters['from'], fn($q, $d) => $q->whereDate('entry_date', '>=', $d))
            ->when($filters['to'], fn($q, $d) => $q->whereDate('entry_date', '<=', $d))
            ->latest()
            ->paginate(15, ['*'], 'entries_page')
            ->withQueryString();

        $statusOptions = TaskEntry::whereNotNull('validation_status')
            ->distinct()
            ->orderBy('validation_status')
            ->pluck('validation_status');

        $hasFilter = (bool) array_filter($filters);

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
