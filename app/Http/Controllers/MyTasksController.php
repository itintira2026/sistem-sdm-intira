<?php

namespace App\Http\Controllers;

use App\Models\TaskAssignee;
use App\Services\TaskProgressService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

/**
 * Part E1 — "Tugas Saya".
 *
 * DIUBAH TOTAL — sekarang dipecah jadi 3 bagian, bukan 1 list panjang:
 *
 * 1. activeAssignees ("Perlu Dikerjakan Hari Ini"): semua TaskAssignee
 *    KPI yang task.period_end >= hari ini (masih berjalan). Diurutkan
 *    biar yang BUTUH aksi (rejected / belum diisi) muncul duluan,
 *    daripada yang udah diisi tinggal nunggu validasi. GAK difilter
 *    bulan — ini selalu "sekarang", apapun bulan yang lagi dibuka di
 *    bagian Tugas Selesai.
 *
 * 2. finishedPaginated ("Tugas Selesai"): task.period_end < hari ini,
 *    DIFILTER per bulan (month/year query param, default bulan
 *    sekarang) — rule period_end-per-bulan yang sama kayak kpi-saya &
 *    halaman detail tim, biar satu mental model. Dipaginasi, buat
 *    riwayat/bukti ke atasan tanpa perlu nambah halaman baru lagi
 *    nanti kalau datanya numpuk.
 *
 * 3. selfRecordAssignees: dipaginasi 5/halaman (sebelumnya limit 20
 *    tanpa paginasi).
 *
 * Assignee yang finalized_at != null (freeze mutasi ATAU dinonaktifkan
 * manual) TETAP masuk activeAssignees kalau period_end-nya belum lewat
 * (ditandai badge 🧊, gak ada tombol isi — lihat task-daily-detail &
 * badge di blade), biar user tau tugasnya kenapa berhenti kehitung.
 */
class MyTasksController extends Controller
{
    private const SELF_RECORD_PER_PAGE = 5;
    private const FINISHED_PER_PAGE = 8;

    public function index(Request $request, TaskProgressService $progressService)
    {
        $user = Auth::user();
        $today = now()->toDateString();

        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);
        $startOfMonth = Carbon::create($year, $month, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        // =========================================================
        // 1. PERLU DIKERJAKAN HARI INI (+ portofolio aktif)
        // =========================================================
        $activeAssignees = TaskAssignee::with(['task', 'entries.attachments'])
            ->where('user_id', $user->id)
            ->whereHas('task', function ($q) use ($today) {
                $q->where('has_weight', true)
                    ->where('status', 'active')
                    ->whereDate('period_end', '>=', $today);
            })
            ->get()
            ->map(function ($assignee) use ($progressService) {
                $entries = $assignee->entries;

                $actionableEntry = $assignee->isFinalized()
                    ? null
                    : ($entries->firstWhere('validation_status', 'rejected')
                        ?? $entries->first(fn($e) => $e->entry_date?->isToday())
                        ?? $entries->first(fn($e) => $e->entry_date === null));

                return [
                    'assignee' => $assignee,
                    'task' => $assignee->task,
                    'percentage' => $progressService->currentPercentage($assignee),
                    'actionableEntry' => $actionableEntry,
                ];
            })
            ->sortBy(function ($row) {
                $e = $row['actionableEntry'];

                if (! $e) {
                    return 2;
                }

                if ($e->validation_status === 'rejected' || ! $e->hasSubmittedValue()) {
                    return 0; // butuh aksi -> paling atas
                }

                return 1; // sudah diisi, tinggal nunggu validasi
            })
            ->values();

        // =========================================================
        // 2. TUGAS SELESAI (per bulan, dipaginasi)
        // =========================================================
        $finishedAll = TaskAssignee::with(['task', 'entries.attachments'])
            ->where('user_id', $user->id)
            ->whereHas('task', function ($q) use ($startOfMonth, $endOfMonth) {
                $q->where('has_weight', true)
                    ->whereBetween('period_end', [$startOfMonth->toDateString(), $endOfMonth->toDateString()]);
            })
            ->get()
            ->filter(fn($a) => $a->task->period_end->lt(now()->startOfDay()))
            ->map(function ($assignee) use ($progressService) {
                return [
                    'assignee' => $assignee,
                    'task' => $assignee->task,
                    'percentage' => $progressService->currentPercentage($assignee),
                ];
            })
            ->sortByDesc(fn($row) => $row['task']->period_end)
            ->values();

        $finishedStats = [
            'total' => $finishedAll->count(),
            'average' => $finishedAll->isEmpty() ? 0 : round($finishedAll->pluck('percentage')->avg(), 1),
        ];

        $finishedOpenDefault = $request->has('selesai_page');
        $finishedPage = (int) $request->query('selesai_page', 1);

        $finishedPaginated = new LengthAwarePaginator(
            $finishedAll->forPage($finishedPage, self::FINISHED_PER_PAGE)->values(),
            $finishedAll->count(),
            self::FINISHED_PER_PAGE,
            $finishedPage,
            [
                'path' => $request->url(),
                'query' => $request->except('selesai_page'),
                'pageName' => 'selesai_page',
            ]
        );

        // =========================================================
        // 3. TUGAS TAMBAHAN (self-record), dipaginasi
        // =========================================================
        $selfRecordAssignees = TaskAssignee::with(['task', 'entries'])
            ->where('user_id', $user->id)
            ->whereHas('task', fn($q) => $q->where('has_weight', false))
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth->endOfDay()])
            ->latest('id')
            ->paginate(self::SELF_RECORD_PER_PAGE, ['*'], 'self_record_page');

        return view('tasks.my-tasks', [
            'activeAssignees' => $activeAssignees,
            'finishedPaginated' => $finishedPaginated,
            'finishedStats' => $finishedStats,
            'finishedOpenDefault' => $finishedOpenDefault,
            'selfRecordAssignees' => $selfRecordAssignees,
            'month' => $month,
            'year' => $year,
            'startOfMonth' => $startOfMonth,
            'prevMonth' => $startOfMonth->copy()->subMonth(),
            'nextMonth' => $startOfMonth->copy()->addMonth(),
        ]);
    }
}
