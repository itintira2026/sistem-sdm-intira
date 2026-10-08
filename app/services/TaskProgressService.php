<?php

namespace App\Services;

use App\Models\PeriodWorkingDay;
use App\Models\Task;
use App\Models\TaskAssignee;
use App\Models\TaskEntry;

/**
 * Part B5 — Rumus progress/rolling % (§7), mode-aware:
 * - daily_split : hari tercapai / hari kerja yang SUDAH LEWAT (rumus inti §7)
 * - cumulative  : progress / target * 100, di-cap 100%
 * - once        : 100% kalau tercapai, 0% kalau belum
 *
 * FROZEN (§10, mutasi jabatan): kalau task->status = 'frozen' DAN
 * final_percentage sudah di-set (oleh service lain nanti, di luar file
 * ini — bagian dari alur mutasi jabatan G4), angka itu DIKUNCI, tidak
 * dihitung ulang oleh service ini sama sekali.
 */
class TaskProgressService
{
    public function currentPercentage(TaskAssignee $assignee): float
    {
        $task = $assignee->task;

        // DIUBAH (Part G4): cek finalized_at di ASSIGNEE, bukan status di
        // TASK. Task bisa punya banyak assignee — freeze harus per-orang,
        // bukan ikut membekukan assignee lain di tugas yang sama. Ini juga
        // menyatukan logic dengan KpiSnapshotService yang dari awal sudah
        // pakai pola cek finalized_at doang.
        if ($assignee->finalized_at !== null) {
            return (float) $assignee->final_percentage;
        }

        return match ($task->repeat_mode) {
            'daily_split' => $this->dailySplitPercentage($assignee),
            'cumulative' => $this->cumulativePercentage($assignee),
            'once' => $this->oncePercentage($assignee),
            default => 0.0,
        };
    }

    /**
     * Rumus inti §7. "Hari yang sudah lewat" diambil LANGSUNG dari
     * period_working_days (bukan dari entry_date yang tersimpan di
     * task_entries) — supaya tanggal yang sudah dihapus dari kalender
     * (libur mendadak, §7.2) otomatis dikeluarkan dari pembilang MAUPUN
     * penyebut, tanpa perlu logic tambahan.
     */
    private function dailySplitPercentage(TaskAssignee $assignee): float
    {
        $task = $assignee->task;

        if (! $task->period_start || ! $task->period_end) {
            return 0.0;
        }

        $effectiveEnd = min($task->period_end->toDateString(), now()->toDateString());

        $elapsedWorkingDays = PeriodWorkingDay::whereBetween('tanggal', [
            $task->period_start->toDateString(),
            $effectiveEnd,
        ])
            ->pluck('tanggal')
            ->map(fn($d) => $d->format('Y-m-d'));

        if ($elapsedWorkingDays->isEmpty()) {
            return 0.0;
        }

        $entries = TaskEntry::where('task_assignee_id', $assignee->id)
            ->whereIn('entry_date', $elapsedWorkingDays)
            ->get()
            ->keyBy(fn($e) => $e->entry_date->format('Y-m-d'));

        $achievedCount = 0;

        foreach ($elapsedWorkingDays as $tanggal) {
            $entry = $entries->get($tanggal);

            // Entry gak ada ATAU belum tercapai -> otomatis gagal (0),
            // gak nambah $achievedCount. Tidak ada fitur "izin" (§7).
            if ($entry && $this->isEntryAchieved($entry, $assignee, $task)) {
                $achievedCount++;
            }
        }

        return round(($achievedCount / $elapsedWorkingDays->count()) * 100, 2);
    }

    private function cumulativePercentage(TaskAssignee $assignee): float
    {
        $entry = TaskEntry::where('task_assignee_id', $assignee->id)
            ->whereNull('entry_date')
            ->first();

        if (! $entry || $entry->value_number === null) {
            return 0.0;
        }

        // FIX URGENT: sama seperti isEntryAchieved(), gak dihitung kalau
        // belum di-approve.
        if ($entry->validation_status !== 'approved') {
            return 0.0;
        }

        if (! $assignee->individual_target || (float) $assignee->individual_target <= 0) {
            return 0.0;
        }

        $percentage = ((float) $entry->value_number / (float) $assignee->individual_target) * 100;

        return round(min(100, $percentage), 2);
    }

    private function oncePercentage(TaskAssignee $assignee): float
    {
        $entry = TaskEntry::where('task_assignee_id', $assignee->id)
            ->whereNull('entry_date')
            ->first();

        if (! $entry) {
            return 0.0;
        }

        return $this->isEntryAchieved($entry, $assignee, $assignee->task) ? 100.0 : 0.0;
    }

    /**
     * "Tercapai" itu boolean (0 atau 1) per entry — ini yang otomatis
     * memenuhi aturan "overachieve tetap dicap 100% hari itu saja, gak
     * menabung ke hari lain" (§7): gak ada nilai > 1 yang bisa numpuk.
     */
    private function isEntryAchieved(TaskEntry $entry, TaskAssignee $assignee, Task $task): bool
    {
        // FIX URGENT: entry HARUS berstatus 'approved' dulu baru dianggap
        // tercapai. Sebelumnya cuma ngecek nilai vs target, jadi entry yang
        // ditolak atau bahkan belum sempat dicek sama sekali tetap kehitung
        // 100% — padahal validasi itu gerbang kualitas, bukan formalitas.
        if ($entry->validation_status !== 'approved') {
            return false;
        }

        if ($task->tracking_mode === 'checklist') {
            return $entry->value_checklist === true;
        }

        // numeric
        if ($entry->value_number === null) {
            return false;
        }

        $target = $assignee->individual_target;

        // Gak ada target ditentukan -> anggap tercapai selama ada input > 0
        if ($target === null || (float) $target <= 0) {
            return (float) $entry->value_number > 0;
        }

        return (float) $entry->value_number >= (float) $target;
    }
}
