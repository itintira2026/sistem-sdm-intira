<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Task;
use App\Models\TaskAssignee;
use App\Models\TaskEntry;
use App\Models\User;
use App\Services\TaskAssignmentPermissionService;
use App\Services\TaskSplitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Part C1 — Form bikin Tugas KPI (has_weight selalu true di sini).
 * Tugas Tambahan (self-record) punya controller/form SENDIRI (Part C2),
 * TIDAK lewat sini sama sekali.
 */
class TaskController extends Controller
{
    public function __construct(
        private TaskAssignmentPermissionService $permissionService,
        private TaskSplitService $splitService,
    ) {}

    public function create()
    {
        $user = Auth::user();
        $eligibleAssignees = $this->permissionService->eligibleAssignees($user)->orderBy('name')->get();

        if ($eligibleAssignees->isEmpty()) {
            abort(403, 'Role Anda tidak memiliki hak untuk assign Tugas KPI ke siapapun.');
        }

        $previousTasks = Task::where('created_by', $user->id)
            ->where('has_weight', true)
            ->latest()
            ->limit(20)
            ->get(['id', 'title']);

        return view('tasks.create', [
            'eligibleAssignees' => $eligibleAssignees,
            'divisions' => Division::orderBy('name')->get(),
            'previousTasks' => $previousTasks,
        ]);
    }

    public function searchTemplates(Request $request)
    {
        $validated = $request->validate([
            'q' => 'required|string|min:2|max:255',
        ]);

        $tasks = Task::where('created_by', Auth::id())
            ->where('has_weight', true)
            ->where('title', 'like', $validated['q'] . '%')
            ->latest()
            ->limit(20)
            ->get(['id', 'title', 'created_at']);

        return response()->json([
            'data' => $tasks->map(fn($task) => [
                'id' => $task->id,
                'title' => $task->title,
                'created_at' => $task->created_at->format('d M Y'),
            ]),
        ]);
    }

    public function template(Task $task)
    {
        $task = Task::where('created_by', Auth::id())
            ->where('has_weight', true)
            ->findOrFail($task->id);

        return response()->json([
            'data' => $task->only([
                'title',
                'description',
                'tracking_mode',
                'unit',
                'unit_type',
                'repeat_mode',
                'division_scope',
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'tracking_mode' => 'required|in:numeric,checklist',
            'unit' => 'nullable|string|max:50',
            'unit_type' => 'required|in:number,currency,percent', // BARU
            'repeat_mode' => 'required|in:once,daily_split,cumulative',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'reference_task_id' => 'nullable|exists:tasks,id',
            'division_scope' => 'nullable|exists:divisions,id',
            'assignee_ids' => 'required|array|min:1',
            'assignee_ids.*' => 'exists:users,id',
            'individual_targets' => 'nullable|array',
            'individual_targets.*' => 'nullable|numeric|min:0',
        ]);

        // BARU: satuan persen -> target tidak boleh > 100
        if (
            $validated['unit_type'] === 'percent'
            && collect($validated['individual_targets'] ?? [])
            ->contains(fn($t) => $t !== null && (float) $t > 100)
        ) {
            return back()->withInput()->with('error', 'Target dengan satuan persen tidak boleh lebih dari 100.');
        }

        // Cek permission SEMUA assignee dulu SEBELUM nyimpen apapun —
        // kalau ada 1 aja yang gagal, seluruh request ditolak.
        $assignees = User::whereIn('id', $validated['assignee_ids'])->get()->keyBy('id');

        foreach ($validated['assignee_ids'] as $assigneeId) {
            $assignee = $assignees->get((int) $assigneeId);

            if (! $assignee) {
                return back()->withInput()->with('error', 'Assignee tidak ditemukan.');
            }

            if (! $this->permissionService->canAssign($user, $assignee)) {
                $reason = $this->permissionService->denialReason($user, $assignee);

                return back()->withInput()->with('error', "Gagal assign ke {$assignee->name}: {$reason}");
            }
        }

        $task = DB::transaction(function () use ($validated, $user) {
            $task = Task::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'tracking_mode' => $validated['tracking_mode'],
                'unit' => $validated['unit'] ?? null,
                'unit_type' => $validated['unit_type'], // BARU
                'has_weight' => true, // form ini KHUSUS Tugas KPI
                'repeat_mode' => $validated['repeat_mode'],
                'period_start' => $validated['period_start'],
                'period_end' => $validated['period_end'],
                'reference_task_id' => $validated['reference_task_id'] ?? null,
                'created_by' => $user->id,
                'division_scope' => $validated['division_scope'] ?? null,
                'status' => 'active',
            ]);

            foreach ($validated['assignee_ids'] as $assigneeId) {
                $taskAssignee = TaskAssignee::create([
                    'task_id' => $task->id,
                    'user_id' => $assigneeId,
                    'individual_target' => $validated['individual_targets'][$assigneeId] ?? null,
                    'weight_percent' => null, // BELUM diisi — wajib diisi di tahap Set Bobot
                ]);

                if ($task->repeat_mode === 'daily_split') {
                    $this->splitService->generateEntriesFor($taskAssignee);
                } else {
                    // once / cumulative -> 1 entry, entry_date null
                    TaskEntry::create([
                        'task_assignee_id' => $taskAssignee->id,
                        'validation_status' => 'pending',
                    ]);
                }
            }

            return $task;
        });

        // 1 assignee -> langsung ke form Set Bobot orangnya.
        // Banyak assignee (bulk) -> halaman ringkasan, klik 1-1.
        if (count($validated['assignee_ids']) === 1) {
            return redirect()
                ->route('tasks.weight-reinput', $validated['assignee_ids'][0])
                ->with('success', 'Tugas berhasil dibuat. Sekarang atur bobot KPI-nya (wajib pas 100%).');
        }

        return redirect()
            ->route('tasks.assignees-weight-status', $task->id)
            ->with('success', 'Tugas berhasil dibuat untuk ' . count($validated['assignee_ids']) . ' orang. Atur bobot KPI masing-masing di bawah.');
    }

    /**
     * Halaman ringkasan bulk-assign — daftar semua orang yang baru
     * di-assign tugas ini, link ke form Set Bobot masing-masing.
     */
    public function assigneesWeightStatus(Task $task)
    {
        $assignees = $task->assignees()->with('user')->get();

        return view('tasks.assignees-weight-status', compact('task', 'assignees'));
    }
}
