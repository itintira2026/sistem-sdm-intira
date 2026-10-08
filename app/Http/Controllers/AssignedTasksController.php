<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskAssignee;
use App\Services\TaskAssignmentPermissionService;
use Illuminate\Support\Facades\Auth;
use App\Services\TaskWeightService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AssignedTasksController extends Controller
{
    public function index(TaskAssignmentPermissionService $permissionService, TaskWeightService $weightService, Request $request)
    {
        $user = Auth::user();

        // A. Bobot per bawahan
        $team = $permissionService->eligibleAssignees($user)
            ->with('division')
            ->orderBy('name')
            ->get();

        $weightTotals = $team->mapWithKeys(function ($member) use ($weightService) {
            $total = $weightService->currentTotalWeight($member);
            $count = $weightService->activeWeightedAssignments($member)->count();

            return [$member->id => (object) ['total' => $total, 'jumlah' => $count]];
        });

        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);
        $startOfMonth = Carbon::create($year, $month, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        // B. Riwayat tugas yang saya buat
        $tasks = Task::where('created_by', $user->id)
            ->where('has_weight', true)
            ->withCount('assignees')
            ->whereBetween('period_end', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->with(['assignees' => fn($q) => $q->whereNull('weight_percent')->with('user')])
            ->latest()
            ->paginate(15);

        $team = $team->sortBy(fn($m) => [
            ($weightTotals->get($m->id)->belum_diatur ?? 0) > 0 ? 0 : 1,
            $m->name,
        ])->values();

        $prevMonth = $startOfMonth->copy()->subMonth();
        $nextMonth = $startOfMonth->copy()->addMonth();

        return view('tasks.assigned', compact('team', 'weightTotals', 'tasks', 'startOfMonth', 'prevMonth', 'nextMonth'));
    }
}
