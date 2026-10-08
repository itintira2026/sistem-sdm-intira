<?php

namespace App\Http\Controllers;

use App\Models\TaskEntry;
use App\Services\TaskValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Part D2 — Halaman "Perlu Saya Cek" (bulk check/tolak + alasan).
 * Query exclude has_weight=false (self-record gak pernah masuk antrian ini).
 *
 * CATATAN PERFORMA: canCheck() dicek per-baris di PHP (bukan translasi ke
 * SQL WHERE), karena logic-nya (role + cabang/divisi dinamis) susah
 * diterjemahkan jadi query tunggal.
 */
class TaskCheckQueueController extends Controller
{
    public function index(TaskValidationService $validationService)
    {
        $user = Auth::user();

        $candidates = TaskEntry::with(['taskAssignee.task', 'taskAssignee.user', 'attachments'])
            ->where('validation_status', 'pending')
            ->whereHas('taskAssignee.task', fn($q) => $q->where('has_weight', true))
            // BARU: assignee yang sudah dibekukan (mutasi jabatan atau
            // dinonaktifkan manual) gak boleh nongol di antrian lagi.
            ->whereHas('taskAssignee', fn($q) => $q->whereNull('finalized_at'))
            ->get();

        $entries = $candidates->filter(fn($entry) => $validationService->canCheck($user, $entry))->values();

        // Highlight kurang/lebih dari target (logic ada di TaskEntry::compareToTarget)
        $entries = $entries->map(function ($entry) {
            $entry->comparison = $entry->compareToTarget();
            return $entry;
        });

        return view('task-entries.check-queue', compact('entries'));
    }

    public function bulkCheck(Request $request, TaskValidationService $validationService)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'entry_ids' => 'required|array|min:1',
            'entry_ids.*' => 'exists:task_entries,id',
            'result' => 'required|in:checked,rejected',
            'reason' => 'nullable|string|max:500',
        ]);

        if ($validated['result'] === 'rejected' && empty($validated['reason'])) {
            return back()->with('error', 'Alasan wajib diisi kalau menolak.');
        }

        $entries = TaskEntry::with(['taskAssignee.task', 'taskAssignee.user'])
            ->whereIn('id', $validated['entry_ids'])
            ->get();

        $results = $validationService->bulkCheck($user, $entries, $validated['result'], $validated['reason'] ?? null);

        $successCount = collect($results)->where('success', true)->count();
        $failCount = collect($results)->where('success', false)->count();

        return back()->with('success', "{$successCount} entry berhasil diproses" . ($failCount > 0 ? ", {$failCount} gagal." : "."));
    }
}
