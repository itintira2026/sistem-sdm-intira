<?php

namespace App\Services;

use App\Models\PeriodWorkingDay;
use App\Models\Task;
use App\Models\TaskAssignee;
use App\Models\TaskEntry;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * Part B4 — Generator entry harian untuk mode daily_split (§4).
 *
 * PENTING soal desain: tidak seperti rencana awal ("generate baris anak
 * di tabel tasks"), sekarang generate baris di task_entries — sesuai
 * keputusan desain 3-tabel (tasks / task_assignees / task_entries).
 *
 * CATATAN EDGE CASE yang BELUM ditangani otomatis di sini (didesain
 * untuk ditangani TaskProgressService nanti, bukan di sini):
 * kalau sebuah tanggal DIHAPUS dari period_working_days SETELAH entry-nya
 * sudah ke-generate (kasus §7.2 — libur mendadak, tanggal di-uncheck di
 * kalender), baris task_entries untuk tanggal itu TETAP ADA di database
 * (gak otomatis kehapus). TaskProgressService yang harus mengabaikan
 * entry semacam ini saat menghitung rolling % (cross-check ulang ke
 * period_working_days, bukan cuma percaya entry_date yang sudah tersimpan).
 */
class TaskSplitService
{
    /**
     * Generate entry harian untuk 1 task_assignee bermode daily_split.
     * Idempotent — aman dipanggil ulang berkali-kali.
     */
    public function generateEntriesFor(TaskAssignee $taskAssignee): Collection
    {
        $task = $taskAssignee->task;

        if ($task->repeat_mode !== 'daily_split') {
            throw new InvalidArgumentException('generateEntriesFor() cuma untuk task dengan repeat_mode = daily_split.');
        }

        if (! $task->period_start || ! $task->period_end) {
            throw new InvalidArgumentException('Task daily_split wajib punya period_start & period_end sebelum entry di-generate.');
        }

        $workingDays = PeriodWorkingDay::whereBetween('tanggal', [
            $task->period_start->toDateString(),
            $task->period_end->toDateString(),
        ])
            ->orderBy('tanggal')
            ->pluck('tanggal');

        // has_weight=false (self-record) -> validation_status NULL, gak divalidasi (§6)
        $initialStatus = $task->has_weight ? 'pending' : null;

        return $workingDays->map(function ($tanggal) use ($taskAssignee, $initialStatus) {
            return TaskEntry::firstOrCreate(
                [
                    'task_assignee_id' => $taskAssignee->id,
                    'entry_date' => $tanggal->toDateString(),
                ],
                [
                    'validation_status' => $initialStatus,
                ]
            );
        });
    }

    /**
     * Generate ulang untuk SEMUA assignee sekaligus — dipakai controller
     * setelah bulk-assign daily_split ke banyak orang.
     */
    public function generateForAllAssignees(Task $task): void
    {
        foreach ($task->assignees as $assignee) {
            $this->generateEntriesFor($assignee);
        }
    }

    /**
     * Total tanggal hari kerja yang ditandai dalam periode task ini
     * (dari kalender, TANPA syarat "sudah lewat") — dipakai buat info
     * "target total N hari kerja periode ini" di UI.
     */
    public function totalWorkingDaysInPeriod(Task $task): int
    {
        if (! $task->period_start || ! $task->period_end) {
            return 0;
        }

        return PeriodWorkingDay::countTotalBetween(
            $task->period_start->toDateString(),
            $task->period_end->toDateString()
        );
    }
}
