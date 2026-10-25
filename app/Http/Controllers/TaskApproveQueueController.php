<?php

namespace App\Http\Controllers;

use App\Models\TaskEntry;
use App\Services\TaskValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Part D3 — Halaman "Perlu Saya Approve" (bulk approve, termasuk
 * override Korwil). Query exclude has_weight=false.
 *
 * Status yang di-query mencakup 'pending' JUGA (bukan cuma 'checked') —
 * soalnya Korwil "bebas override kapan saja", termasuk approve entry FO
 * yang belum sempat dicek AM sama sekali.
 */
class TaskApproveQueueController extends Controller
{
    public function index(TaskValidationService $validationService)
    {
        $user = Auth::user();

        $candidates = TaskEntry::with(['taskAssignee.task', 'taskAssignee.user', 'attachments'])
            ->whereIn('validation_status', ['pending', 'checked'])
            ->whereHas('taskAssignee.task', fn($q) => $q->where('has_weight', true))
            // BARU: assignee yang sudah dibekukan (mutasi jabatan atau
            // dinonaktifkan manual) gak boleh nongol di antrian lagi.
            ->whereHas('taskAssignee', fn($q) => $q->whereNull('finalized_at'))
            ->get();

        $entries = $candidates->filter(fn($entry) => $validationService->canApprove($user, $entry))->values();

        // Highlight kurang/lebih dari target (logic ada di TaskEntry::compareToTarget)
        $entries = $entries->map(function ($entry) {
            $entry->comparison = $entry->compareToTarget();
            return $entry;
        });

        return view('task-entries.approve-queue', compact('entries'));
    }

    public function bulkApprove(Request $request, TaskValidationService $validationService)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'entry_ids' => 'required|array|min:1',
            'entry_ids.*' => 'exists:task_entries,id',
            'result' => 'required|in:approved,rejected',
            'reason' => 'nullable|string|max:500',
        ]);

        if ($validated['result'] === 'rejected' && empty($validated['reason'])) {
            return back()->with('error', 'Alasan wajib diisi kalau menolak.');
        }

        $entries = TaskEntry::with(['taskAssignee.task', 'taskAssignee.user'])
            ->whereIn('id', $validated['entry_ids'])
            ->get();

        $results = $validationService->bulkApprove($user, $entries, $validated['result'], $validated['reason'] ?? null);

        $successCount = collect($results)->where('success', true)->count();
        $failCount = collect($results)->where('success', false)->count();

        return back()->with('success', "{$successCount} entry berhasil diproses" . ($failCount > 0 ? ", {$failCount} gagal." : "."));
    }
}
