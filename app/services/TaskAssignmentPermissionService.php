<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Part B2 — Aturan siapa-boleh-assign-Tugas-KPI-ke-siapa (§3).
 *
 * PENTING: service ini CUMA dipakai untuk Tugas KPI (has_weight=true).
 * Tugas Tambahan (self-record, §6) TIDAK lewat sini — assignee-nya
 * selalu = pembuatnya sendiri, gak butuh cek hierarki apapun. Controller
 * yang bikin Tugas Tambahan harus skip pemanggilan service ini sama sekali.
 */
class TaskAssignmentPermissionService
{
    private const TOP_TIER_ROLES = [
        'ketua',
        'direktur_utama',
        'direktur_bisnis',
        'direktur_operasional',
    ];

    /**
     * Cek apakah $assigner boleh assign Tugas KPI ke $assignee.
     */
    public function canAssign(User $assigner, User $assignee): bool
    {
        if ($assigner->hasRole('superadmin')) {
            return true;
        }

        // Ketua/Direktur (siapapun, setara) -> Kadiv atau Korwil
        if ($assigner->hasAnyRole(self::TOP_TIER_ROLES)) {
            return $assignee->hasRole('kadiv') || $assignee->hasRole('korwil');
        }

        // Kadiv -> Staff, WAJIB divisi sama
        if ($assigner->hasRole('kadiv')) {
            return $assignee->hasRole('staff')
                && $assigner->division_id !== null
                && $assignee->division_id !== null
                && $assigner->division_id === $assignee->division_id;
        }

        // Korwil -> AM atau FO, WAJIB dalam cabang yang dia naungi
        if ($assigner->hasRole('korwil')) {
            if (! ($assignee->hasRole('manager') || $assignee->hasRole('fo'))) {
                return false;
            }

            $korwilBranchIds = $assigner->managedBranches->pluck('id');
            $assigneeBranchIds = $assignee->branches->pluck('id');

            return $korwilBranchIds->intersect($assigneeBranchIds)->isNotEmpty();
        }

        // AM (manager biasa, bukan Korwil), Staff, FO, dan role lainnya:
        // TIDAK BOLEH assign Tugas KPI ke siapapun.
        return false;
    }

    /**
     * Alasan penolakan yang informatif, buat ditampilkan di UI kalau
     * canAssign() balikin false.
     */
    public function denialReason(User $assigner, User $assignee): ?string
    {
        if ($this->canAssign($assigner, $assignee)) {
            return null;
        }

        if ($assigner->hasRole('kadiv')) {
            if (! $assignee->hasRole('staff')) {
                return 'Kadiv hanya boleh assign Tugas KPI ke Staff.';
            }

            return 'Staff ini berbeda divisi — Kadiv hanya boleh assign ke Staff di divisi yang sama.';
        }

        if ($assigner->hasRole('korwil')) {
            if (! ($assignee->hasRole('manager') || $assignee->hasRole('fo'))) {
                return 'Korwil hanya boleh assign Tugas KPI ke AM atau FO.';
            }

            return 'AM/FO ini berada di luar cabang yang Anda naungi.';
        }

        if ($assigner->hasAnyRole(self::TOP_TIER_ROLES)) {
            return 'Ketua/Direktur hanya boleh assign Tugas KPI ke Kadiv atau Korwil.';
        }

        return 'Role Anda tidak memiliki izin untuk assign Tugas KPI ke siapapun.';
    }

    /**
     * Query dasar buat dropdown/checklist "pilih assignee" di form bikin
     * tugas — sudah difilter sesuai role $assigner. UI tinggal tambah
     * ->whereNotIn('id', $excludedIds) sendiri buat fitur "exclude" (§5).
     */
    public function eligibleAssignees(User $assigner): Builder
    {
        if ($assigner->hasRole('superadmin')) {
            return User::query();
        }

        if ($assigner->hasAnyRole(self::TOP_TIER_ROLES)) {
            return User::role(['kadiv', 'korwil']);
        }

        if ($assigner->hasRole('kadiv')) {
            return User::role('staff')->where('division_id', $assigner->division_id);
        }

        if ($assigner->hasRole('korwil')) {
            $branchIds = $assigner->managedBranches->pluck('id');

            return User::role(['manager', 'fo'])
                ->whereHas('branches', function ($q) use ($branchIds) {
                    $q->whereIn('branches.id', $branchIds);
                });
        }

        // Role tanpa hak assign sama sekali -> query kosong
        return User::whereRaw('1 = 0');
    }
}
