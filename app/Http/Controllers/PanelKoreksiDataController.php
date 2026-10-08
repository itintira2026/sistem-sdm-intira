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

        $deletedTasks = Task::onlyTrashed()
            ->with('creator')
            ->latest('deleted_at')
            ->paginate(15, ['*'], 'deleted_page');

        $entrySearch = $request->get('entry_search');
        $entries = collect();

        if ($entrySearch) {
            $entries = TaskEntry::with(['taskAssignee.task', 'taskAssignee.user'])
                ->where(function ($q) use ($entrySearch) {
                    $q->whereHas('taskAssignee.user', fn($q2) => $q2->where('name', 'like', "%{$entrySearch}%"))
                        ->orWhereHas('taskAssignee.task', fn($q2) => $q2->where('title', 'like', "%{$entrySearch}%"));
                })
                ->latest()
                ->limit(20)
                ->get();
        }

        return view('superadmin.koreksi-data', compact('deletedTasks', 'entries', 'entrySearch'));
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
