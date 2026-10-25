<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

/**
 * BARU (Part E3 lanjutan) — nentuin siapa yang boleh DILIHAT (bukan
 * dikelola) oleh siapa di halaman KPI Tim.
 *
 * Beda dari TaskAssignmentPermissionService: service itu jawab
 * "siapa boleh ASSIGN/approve ke siapa" (hak kelola). Service ini jawab
 * "siapa boleh LIHAT KPI siapa" (hak pantau), yang lebih luas —
 * Kadiv & Staff bisa PREVIEW Lapangan meski gak punya hak kelola
 * apapun di sana.
 *
 * Tiap User yang dikembalikan dapat 2 atribut sementara (bukan kolom
 * database, cuma dipasang di collection ini buat kebutuhan tampilan):
 * - visibility_scope: 'managed' (atasan langsung/bertanggung jawab)
 *                      atau 'preview' (cuma boleh lihat)
 * - visibility_group: 'head_office' atau 'lapangan' (buat filter tab)
 */
class TeamVisibilityService
{
    private const TOP_TIER_ROLES = [
        'owner',
        'ketua',
        'direktur_utama',
        'direktur_bisnis',
        'direktur_operasional',
    ];

    public function headOffice(): Collection
    {
        return User::role(['kadiv', 'staff'])->with(['roles', 'division'])->orderBy('name')->get()
            ->each(fn($u) => $u->visibility_group = 'head_office');
    }

    public function lapangan(): Collection
    {
        return User::role(['korwil', 'manager', 'fo'])->with(['roles', 'branches'])->orderBy('name')->get()
            ->each(fn($u) => $u->visibility_group = 'lapangan');
    }

    /**
     * Semua orang yang $viewer boleh LIHAT KPI-nya di halaman KPI Tim.
     */
    public function visibleTo(User $viewer): Collection
    {
        if ($viewer->hasRole('superadmin') || $viewer->hasAnyRole(self::TOP_TIER_ROLES)) {
            return $this->headOffice()->concat($this->lapangan())
                ->each(fn($u) => $u->visibility_scope = 'managed');
        }

        if ($viewer->hasRole('kadiv')) {
            $managed = User::role('staff')
                ->where('division_id', $viewer->division_id)
                ->orderBy('name')->get()
                ->each(function ($u) {
                    $u->visibility_scope = 'managed';
                    $u->visibility_group = 'head_office';
                });

            $preview = $this->lapangan()->each(fn($u) => $u->visibility_scope = 'preview');

            return $managed->concat($preview);
        }

        if ($viewer->hasRole('korwil')) {
            $branchIds = $viewer->managedBranches->pluck('id');

            return User::role(['manager', 'fo'])
                ->whereHas('branches', fn($q) => $q->whereIn('branches.id', $branchIds))
                ->orderBy('name')->get()
                ->each(function ($u) {
                    $u->visibility_scope = 'managed';
                    $u->visibility_group = 'lapangan';
                });
        }

        if ($viewer->hasRole('staff')) {
            return $this->lapangan()->each(fn($u) => $u->visibility_scope = 'preview');
        }

        return collect();
    }

    /**
     * Cek satu orang aja — dipakai buat otorisasi halaman detail.
     */
    public function canView(User $viewer, User $target): bool
    {
        if ($viewer->hasRole('superadmin')) {
            return true;
        }

        return $this->visibleTo($viewer)->contains('id', $target->id);
    }
}
