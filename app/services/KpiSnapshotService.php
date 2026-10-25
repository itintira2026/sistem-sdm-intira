<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskAssignee;
use App\Models\User;
use App\Models\UserMonthlyKpi;
use Carbon\Carbon;

/**
 * Part B7 — Lazy snapshot KPI (§11). Tanpa cron job — dihitung PAS
 * halaman dibuka, disimpan, dibaca dari situ untuk pembukaan selanjutnya.
 *
 * ASUMSI yang perlu dicek: rollup bulanan (getOrCalculateMonthly) ngambil
 * task_assignee yang task-nya period_end JATUH DI BULAN itu — jadi kalau
 * ada tugas daily_split yang periodenya lintas bulan (misal 25 Agustus -
 * 24 September), dia dihitung masuk KPI bulan September (bulan
 * period_end-nya), bukan Agustus. Dokumen asli gak nentuin aturan ini
 * secara eksplisit, jadi ini interpretasi saya — kasih tahu kalau
 * maunya beda (misal: dipecah proporsional per bulan).
 *
 * DIKONFIRMASI OK oleh user, DENGAN SYARAT: nanti di halaman "KPI Saya"
 * (Grup E2), setiap baris tugas yang masuk rollup bulanan WAJIB nampilin
 * rentang period_start-period_end aslinya (misal "25 Agu - 24 Sep"),
 * bukan cuma angka persentase doang — biar jelas kenapa tugas lintas
 * bulan itu masuk hitungan bulan tertentu. JANGAN LUPA pas bangun E2.
 */
class KpiSnapshotService
{
    public function __construct(
        private TaskProgressService $progressService
    ) {}

    /**
     * Snapshot per task_assignee. Kalau periode task belum lewat,
     * hitung LIVE tanpa disimpan permanen (biar tetap update real-time
     * selama masih berjalan, sesuai §7).
     */
    // public function getOrCalculateAssignee(TaskAssignee $assignee): float
    // {
    //     if ($assignee->finalized_at !== null) {
    //         return (float) $assignee->final_percentage;
    //     }

    //     $task = $assignee->task;

    //     if (! $this->isPeriodOver($task)) {
    //         return $this->progressService->currentPercentage($assignee);
    //     }

    //     $percentage = $this->progressService->currentPercentage($assignee);

    //     $assignee->update([
    //         'final_percentage' => $percentage,
    //         'finalized_at' => now(),
    //     ]);

    //     return $percentage;
    // }

    /**
     * Rollup bulanan per orang — dari semua task_assignee BERBOBOT
     * miliknya yang period_end jatuh di bulan/tahun tsb.
     */
    // public function getOrCalculateMonthly(User $user, int $month, int $year): UserMonthlyKpi
    // {
    //     $existing = UserMonthlyKpi::where('user_id', $user->id)
    //         ->where('month', $month)
    //         ->where('year', $year)
    //         ->first();

    //     if ($existing && $existing->finalized_at !== null) {
    //         return $existing;
    //     }

    //     $total = $this->calculateMonthlyTotal($user, $month, $year);

    //     return UserMonthlyKpi::updateOrCreate(
    //         ['user_id' => $user->id, 'month' => $month, 'year' => $year],
    //         ['total_percentage' => $total, 'finalized_at' => now()]
    //     );
    // }

    public function getOrCalculateAssignee(TaskAssignee $assignee): float
    {
        if ($assignee->finalized_at !== null) {
            return (float) $assignee->final_percentage; // freeze mutasi, tetap dipertahankan
        }

        return $this->progressService->currentPercentage($assignee);
    }

    public function getOrCalculateMonthly(User $user, int $month, int $year): UserMonthlyKpi
    {
        $total = $this->calculateMonthlyTotal($user, $month, $year);

        return UserMonthlyKpi::updateOrCreate(
            ['user_id' => $user->id, 'month' => $month, 'year' => $year],
            ['total_percentage' => $total, 'finalized_at' => null]
        );
    }

    private function calculateMonthlyTotal(User $user, int $month, int $year): float
    {
        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $assignees = TaskAssignee::with('task')
            ->where('user_id', $user->id)
            ->whereNull('finalized_at')   // ⬅️ tambahan: exclude yang sudah di-lock/superseded
            ->whereHas('task', function ($q) use ($startOfMonth, $endOfMonth) {
                $q->where('has_weight', true)
                    ->where('status', 'active')   // ⬅️ tambahan: samakan dengan TaskWeightService
                    ->whereBetween('period_end', [
                        $startOfMonth->toDateString(),
                        $endOfMonth->toDateString(),
                    ]);
            })
            ->get();

        if ($assignees->isEmpty()) {
            return 0.0;
        }

        $totalWeighted = 0.0;

        foreach ($assignees as $assignee) {
            $percentage = $this->getOrCalculateAssignee($assignee);
            $weight = (float) ($assignee->weight_percent ?? 0);
            $totalWeighted += ($percentage * $weight / 100);
        }

        return round($totalWeighted, 2);
    }

    /**
     * Superadmin mengoreksi data yang sudah ke-snapshot -> hitung ulang
     * paksa (§11: "Superadmin mengoreksi data yang sudah kesnapshot ->
     * otomatis hitung ulang").
     */
    public function recalculateAssignee(TaskAssignee $assignee): float
    {
        $assignee->update(['final_percentage' => null, 'finalized_at' => null]);

        return $this->getOrCalculateAssignee($assignee->fresh());
    }

    public function recalculateMonthly(User $user, int $month, int $year): UserMonthlyKpi
    {
        UserMonthlyKpi::where('user_id', $user->id)
            ->where('month', $month)
            ->where('year', $year)
            ->update(['finalized_at' => null]);

        return $this->getOrCalculateMonthly($user, $month, $year);
    }

    /**
     * Tombol "Hitung Ulang Sekarang" Superadmin (§11, opsional) —
     * pre-warm cache untuk SEMUA orang yang punya tugas di bulan tsb.
     */
    public function prewarmMonth(int $month, int $year): int
    {
        $userIds = TaskAssignee::whereHas('task', function ($q) use ($month, $year) {
            $q->where('has_weight', true)
                ->whereMonth('period_end', $month)
                ->whereYear('period_end', $year);
        })->distinct()->pluck('user_id');

        foreach ($userIds as $userId) {
            $user = User::find($userId);

            if ($user) {
                $this->recalculateMonthly($user, $month, $year);
            }
        }

        return $userIds->count();
    }

    private function isPeriodOver(Task $task): bool
    {
        if (! $task->period_end) {
            return false;
        }

        return $task->period_end->lt(now()->startOfDay());
    }
}
