<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskAssignee;
use App\Models\TaskEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Part C2 — Form Tugas Tambahan (self-record).
 *
 * BEDA TOTAL dari TaskController (Tugas KPI):
 * - Siapa saja bisa akses, buat dirinya sendiri (gak butuh TaskAssignmentPermissionService)
 * - has_weight SELALU false, TIDAK ADA field bobot sama sekali
 * - "Begitu disimpan, langsung final" (§6)
 * - validation_status SELALU null -> gak lewat TaskValidationService sama sekali
 *
 * DIUBAH: store() sekarang bisa dipanggil dari modal di halaman Tugas
 * Saya lewat fetch() — kalau request minta JSON (Accept: application/json),
 * balas JSON daripada redirect. Halaman form lama (/tasks/self-record/create)
 * tetap jalan seperti biasa buat akses langsung.
 */
class SelfRecordTaskController extends Controller
{
    public function create()
    {
        return view('tasks.self-record-create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'tracking_mode' => 'required|in:numeric,checklist',
            'unit' => 'nullable|string|max:50',
            'value_number' => 'required_if:tracking_mode,numeric|nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $request, $user) {
            $task = Task::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'tracking_mode' => $validated['tracking_mode'],
                'unit' => $validated['unit'] ?? null,
                'has_weight' => false,
                'repeat_mode' => 'once',
                'period_start' => null,
                'period_end' => null,
                'created_by' => $user->id,
                'status' => 'active',
            ]);

            $assignee = TaskAssignee::create([
                'task_id' => $task->id,
                'user_id' => $user->id,
                'individual_target' => null,
                'weight_percent' => null,
            ]);

            TaskEntry::create([
                'task_assignee_id' => $assignee->id,
                'value_number' => $validated['tracking_mode'] === 'numeric' ? $validated['value_number'] : null,
                'value_checklist' => $validated['tracking_mode'] === 'checklist' ? $request->boolean('value_checklist') : null,
                'validation_status' => null,
            ]);
        });

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Tugas Tambahan berhasil dicatat.']);
        }

        return redirect()
            ->route('tasks.self-record.create')
            ->with('success', 'Tugas Tambahan berhasil dicatat.');
    }
}
