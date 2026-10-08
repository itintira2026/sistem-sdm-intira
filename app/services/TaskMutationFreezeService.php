<?php

namespace App\Services;

use App\Models\TaskAssignee;
use App\Models\TaskStatusLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Part G4 — Mutasi Jabatan (§10): begitu role/divisi/branch seseorang
 * berubah, semua Tugas KPI aktif orang itu otomatis frozen — rolling %
 * dikunci di angka terakhir, hari setelah tanggal mutasi gak dihitung
 * lagi, gak bisa input entry baru. Terjadi OTOMATIS, tanpa keputusan
 * manual per-tugas.
 *
 * FREEZE DI SINI = per task_assignee (bukan per task) — lihat catatan
 * di TaskProgressService kenapa ini penting untuk kasus multi-assignee.
 */
class TaskMutationFreezeService
{
    public function __construct(
        private TaskProgressService $progressService
    ) {}

    /**
     * Freeze semua Tugas KPI aktif milik $user yang belum pernah
     * di-freeze/finalize sebelumnya. Idempotent — aman dipanggil ulang,
     * gak akan nge-lock ulang assignee yang sudah finalized_at.
     */
    public function freezeActiveTasksFor(User $user, ?string $reason = null): int
    {
        $assignees = TaskAssignee::with('task')
            ->where('user_id', $user->id)
            ->whereNull('finalized_at')
            ->whereHas('task', function ($q) {
                $q->where('has_weight', true)->where('status', 'active');
            })
            ->get();

        $count = 0;

        foreach ($assignees as $assignee) {
            // Hitung % TERAKHIR sebelum dikunci (angka detik ini, sebelum frozen)
            $percentage = $this->progressService->currentPercentage($assignee);

            $assignee->update([
                'final_percentage' => $percentage,
                'finalized_at' => now(),
            ]);

            TaskStatusLog::record(
                $assignee->task_id,
                null,
                'frozen_due_to_mutation',
                null,
                (string) $percentage,
                Auth::id(),
                $reason ?? 'Mutasi jabatan/divisi/cabang',
                $assignee->id // taskAssigneeId, parameter baru di paling belakang
            );

            $count++;
        }

        return $count;
    }
}
