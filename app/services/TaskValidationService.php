<?php

namespace App\Services;

use App\Models\TaskEntry;
use App\Models\TaskStatusLog;
use App\Models\User;
use RuntimeException;

/**
 * Part B6 — Alur validasi checked -> approved/rejected (§8).
 *
 * GUARD UTAMA: kalau entry->validation_status === null (self-record,
 * has_weight=false), service ini SELALU menolak — sesuai catatan asli
 * "kalau has_weight=false, langsung skip, gak perlu proses validasi".
 *
 * Cuma pengerja FO yang punya tahap cek TERPISAH dari approve (oleh AM,
 * lalu Korwil). Semua pengerja lain (AM/Staff/Kadiv/Korwil) digabung
 * jadi 1 aksi oleh 1 validator — check() otomatis redirect ke approve()
 * untuk kasus ini.
 */
class TaskValidationService
{
    private const TOP_TIER_ROLES = [
        'ketua',
        'direktur_utama',
        'direktur_bisnis',
        'direktur_operasional',
    ];

    // =========================================================
    // OTORISASI
    // =========================================================

    public function canCheck(User $checker, TaskEntry $entry): bool
    {
        if (! $this->isValidatable($entry) || $entry->validation_status !== 'pending') {
            return false;
        }
        if (! $this->isValidatable($entry)) {
            return false;
        }

        if ($checker->hasRole('superadmin')) {
            return true;
        }

        // FIX BUG: entry buat tanggal yang belum sampai, atau yang belum
        // diisi nilainya sama sekali, TIDAK BOLEH dicek — sebelumnya
        // semua entry pending (termasuk yang tanggalnya masih di masa
        // depan & kosong) ikut nongol di antrian cek/approve.
        if ($this->isFutureEntry($entry) || ! $this->hasSubmittedValue($entry)) {
            return false;
        }

        $pengerja = $this->pengerja($entry);

        // Cuma FO yang punya tahap cek terpisah
        if ($pengerja->hasRole('fo')) {
            if (! $checker->hasRole('manager')) {
                return false;
            }

            if ($entry->validation_status !== 'pending') {
                return false;
            }

            return $this->sameBranch($checker, $pengerja);
        }

        // Selain FO -> cek = approve (1 aksi gabungan)
        return $this->canApprove($checker, $entry);
    }

    public function canApprove(User $approver, TaskEntry $entry): bool
    {
        if (
            ! $this->isValidatable($entry)
            || ! in_array($entry->validation_status, ['pending', 'checked'], true)
        ) {
            return false;
        }
        if (! $this->isValidatable($entry)) {
            return false;
        }

        if ($approver->hasRole('superadmin')) {
            return true;
        }

        // FIX BUG: sama seperti canCheck() — gak boleh approve entry yang
        // tanggalnya belum sampai atau belum ada nilainya sama sekali.
        if ($this->isFutureEntry($entry) || ! $this->hasSubmittedValue($entry)) {
            return false;
        }

        $pengerja = $this->pengerja($entry);

        // Pengerja = FO -> approver = Korwil, cabang sama, bebas override kapan saja
        if ($pengerja->hasRole('fo')) {
            return $approver->hasRole('korwil') && $this->sameBranch($approver, $pengerja);
        }

        // Pengerja = AM (manager TANPA korwil) -> approver = Korwil, cabang sama
        if ($pengerja->hasRole('manager') && ! $pengerja->hasRole('korwil')) {
            return $approver->hasRole('korwil') && $this->sameBranch($approver, $pengerja);
        }

        // Pengerja = Staff -> approver = Kadiv, divisi sama
        if ($pengerja->hasRole('staff')) {
            return $approver->hasRole('kadiv')
                && $approver->division_id !== null
                && $approver->division_id === $pengerja->division_id;
        }

        // Pengerja = Kadiv atau Korwil -> approver = Ketua/Direktur (siapapun)
        if ($pengerja->hasRole('kadiv') || $pengerja->hasRole('korwil')) {
            return $approver->hasAnyRole(self::TOP_TIER_ROLES);
        }

        return false;
    }

    // =========================================================
    // AKSI
    // =========================================================

    /**
     * @param  string  $result  'checked' | 'rejected'
     */
    public function check(User $checker, TaskEntry $entry, string $result, ?string $reason = null): void
    {
        if (! $this->canCheck($checker, $entry)) {
            throw new RuntimeException('User ini tidak berwenang melakukan cek untuk entry ini.');
        }

        $pengerja = $this->pengerja($entry);

        // Selain FO -> gabung langsung ke approve()
        if (! $pengerja->hasRole('fo')) {
            $this->approve($checker, $entry, $result === 'checked' ? 'approved' : 'rejected', $reason);

            return;
        }

        $isRejected = $result === 'rejected';
        $oldStatus = $entry->validation_status;

        $entry->update([
            'checked_by' => $checker->id,
            'checked_at' => now(),
            'validation_status' => $result,
            'rejected_reason' => $isRejected ? $reason : null,
            // AM langsung tolak = keputusan final, gak perlu nunggu Korwil
            'approved_by' => $isRejected ? $checker->id : null,
            'approved_at' => $isRejected ? now() : null,
        ]);

        TaskStatusLog::record(
            null,
            $entry->id,
            $isRejected ? 'rejected_at_check' : 'checked',
            $oldStatus,
            $result,
            $checker->id,
            $reason
        );
    }

    /**
     * @param  string  $result  'approved' | 'rejected'
     */
    public function approve(User $approver, TaskEntry $entry, string $result, ?string $reason = null): void
    {
        if (! $this->canApprove($approver, $entry)) {
            throw new RuntimeException('User ini tidak berwenang melakukan approve untuk entry ini.');
        }

        $oldStatus = $entry->validation_status;

        $entry->update([
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'validation_status' => $result,
            'rejected_reason' => $result === 'rejected' ? $reason : null,
        ]);

        TaskStatusLog::record(
            null,
            $entry->id,
            $result,
            $oldStatus,
            $result,
            $approver->id,
            $reason
        );
    }

    /**
     * Bulk check/approve (§8: "Fitur bulk check & approve tersedia di
     * semua level"). Gagal di 1 entry TIDAK menghentikan proses entry
     * lainnya — hasil per-entry dikembalikan buat ditampilkan di UI.
     *
     * @param  iterable<TaskEntry>  $entries
     */
    public function bulkCheck(User $checker, iterable $entries, string $result, ?string $reason = null): array
    {
        return $this->bulkRun($entries, fn($entry) => $this->check($checker, $entry, $result, $reason));
    }

    public function bulkApprove(User $approver, iterable $entries, string $result, ?string $reason = null): array
    {
        return $this->bulkRun($entries, fn($entry) => $this->approve($approver, $entry, $result, $reason));
    }

    private function bulkRun(iterable $entries, callable $action): array
    {
        $results = [];

        foreach ($entries as $entry) {
            try {
                $action($entry);
                $results[] = ['id' => $entry->id, 'success' => true, 'message' => null];
            } catch (\Throwable $e) {
                $results[] = ['id' => $entry->id, 'success' => false, 'message' => $e->getMessage()];
            }
        }

        return $results;
    }

    // =========================================================
    // HELPERS
    // =========================================================

    private function isValidatable(TaskEntry $entry): bool
    {
        return $entry->validation_status !== null;
    }

    /**
     * BARU: entry buat tanggal yang belum sampai (daily_split, tanggal
     * di masa depan).
     */
    private function isFutureEntry(TaskEntry $entry): bool
    {
        return $entry->entry_date !== null && $entry->entry_date->isFuture();
    }

    /**
     * BARU: apakah assignee sudah submit nilai apapun (numeric atau
     * checklist)? Entry kosong (baru digenerate, belum diisi) gak boleh
     * ikut masuk antrian cek/approve.
     */
    private function hasSubmittedValue(TaskEntry $entry): bool
    {
        return $entry->value_number !== null || $entry->value_checklist !== null;
    }

    private function pengerja(TaskEntry $entry): User
    {
        return $entry->taskAssignee->user;
    }

    private function sameBranch(User $a, User $b): bool
    {
        $aBranchIds = $a->managedBranches->pluck('id');
        $bBranchIds = $b->branches->pluck('id');

        return $aBranchIds->intersect($bBranchIds)->isNotEmpty();
    }
    public function canViewEntry(User $viewer, TaskEntry $entry): bool
    {
        $assignee = $entry->taskAssignee;
        $pengerja = $assignee->user;

        if (
            $viewer->hasRole('superadmin')
            || $pengerja->id === $viewer->id
            || $assignee->task->created_by === $viewer->id
        ) {
            return true;
        }

        // AM yang mengecek FO di cabangnya
        if ($pengerja->hasRole('fo') && $viewer->hasRole('manager') && $this->sameBranch($viewer, $pengerja)) {
            return true;
        }

        // Atasan yang berhak assign ke pengerja ini juga berhak melihat buktinya
        return app(TaskAssignmentPermissionService::class)->canAssign($viewer, $pengerja)
            || $this->canApprove($viewer, $entry);
    }
}
