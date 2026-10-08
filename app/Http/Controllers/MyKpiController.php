<?php

namespace App\Http\Controllers;

use App\Models\TaskAssignee;
use App\Services\KpiSnapshotService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Part E2 — Halaman "KPI Saya". Rollup bulanan + breakdown per tugas.
 *
 * DIUBAH: navigasi bulan sekarang pakai query param month/year (named),
 * bukan positional — route('tasks.my-kpi', $prevMonth) yang lama salah
 * generate query string (ke-detect sebagai bug sebelumnya).
 */
class MyKpiController extends Controller
{
    public function index(Request $request, KpiSnapshotService $snapshotService)
    {
        $user = Auth::user();
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);
        $startOfMonth = Carbon::create($year, $month, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $monthlyKpi = $snapshotService->getOrCalculateMonthly($user, $month, $year);

        $assignees = TaskAssignee::with(['task', 'entries.attachments'])
            ->where('user_id', $user->id)
            ->whereHas('task', function ($q) use ($startOfMonth, $endOfMonth) {
                $q->where('has_weight', true)
                    ->whereBetween('period_end', [$startOfMonth->toDateString(), $endOfMonth->toDateString()]);
            })
            ->get();

        $breakdown = $assignees->map(function ($assignee) use ($snapshotService) {
            $percentage = $snapshotService->getOrCalculateAssignee($assignee);
            $weight = (float) ($assignee->weight_percent ?? 0);

            return [
                'assignee' => $assignee,
                'task' => $assignee->task,
                'weight' => $weight,
                'percentage' => $percentage,
                'contribution' => round($percentage * $weight / 100, 2),
            ];
        });

        return view('tasks.my-kpi', [
            'monthlyKpi' => $monthlyKpi,
            'breakdown' => $breakdown,
            'startOfMonth' => $startOfMonth,
            'month' => $month,
            'year' => $year,
            'prevMonth' => $startOfMonth->copy()->subMonth(),
            'nextMonth' => $startOfMonth->copy()->addMonth(),
        ]);
    }
}
