<?php

namespace App\Http\Controllers;

use App\Models\TaskAssignee;
use App\Models\User;
use App\Services\KpiSnapshotService;
use App\Services\TeamVisibilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * BARU — Halaman detail KPI 1 orang, dilihat dari sudut pandang atasan.
 * Read-only total: gak ada tombol Isi/Atur Bobot/Nonaktifkan di sini,
 * itu semua tetap lewat halaman masing-masing (task-entries.edit,
 * tasks.weight-reinput). Halaman ini murni buat memantau.
 *
 * Isinya gabungan breakdown KPI (seperti MyKpiController) + daftar
 * Tugas Tambahan (self-record) orang itu, biar atasan juga lihat
 * aktivitas di luar KPI.
 *
 * Otorisasi: target harus ada di TeamVisibilityService::visibleTo($viewer)
 * — sama persis dengan siapa yang muncul di halaman list KPI Tim.
 */
class TeamMemberDetailController extends Controller
{
    public function index(
        Request $request,
        User $user,
        TeamVisibilityService $visibility,
        KpiSnapshotService $snapshotService
    ) {
        $viewer = Auth::user();

        abort_unless($visibility->canView($viewer, $user), 403, 'Anda tidak berwenang melihat KPI orang ini.');

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

        $selfRecordAssignees = TaskAssignee::with(['task', 'entries'])
            ->where('user_id', $user->id)
            ->whereHas('task', fn($q) => $q->where('has_weight', false))
            ->latest('id')
            ->limit(20)
            ->get();

        return view('tasks.team-member-detail', [
            'targetUser' => $user,
            'monthlyKpi' => $monthlyKpi,
            'breakdown' => $breakdown,
            'selfRecordAssignees' => $selfRecordAssignees,
            'month' => $month,
            'year' => $year,
            'monthLabel' => $startOfMonth->translatedFormat('F Y'),
            'prevMonth' => $startOfMonth->copy()->subMonth(),
            'nextMonth' => $startOfMonth->copy()->addMonth(),
        ]);
    }
}
