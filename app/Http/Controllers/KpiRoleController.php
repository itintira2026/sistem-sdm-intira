<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\User;
use App\Services\TaskMutationFreezeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller TERPISAH dari UserController — khusus buat set role & divisi
 * Sistem Tugas & KPI (Part G1). Role dasar (manager/fo/superadmin/dll)
 * TIDAK disentuh sama sekali di sini.
 *
 * DIUBAH (Part G4): update() sekarang deteksi perubahan role/divisi/korwil
 * SEBELUM vs SESUDAH, dan otomatis panggil TaskMutationFreezeService kalau
 * ada yang berubah — ini yang mengimplementasikan "mutasi jabatan" (§10).
 */
class KpiRoleController extends Controller
{
    private const STRUCTURAL_ROLES = [
        'owner',
        'ketua',
        'direktur_utama',
        'direktur_bisnis',
        'direktur_operasional',
        'kadiv',
        'staff',
    ];

    private const DIVISION_ROLES = ['kadiv', 'staff'];

    public function index(Request $request)
    {
        $search = $request->get('search');

        $users = User::with(['roles', 'division'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $users->getCollection()->transform(function ($user) {
            $roleNames = $user->roles->pluck('name');
            $user->current_structural_role = $roleNames->first(
                fn($r) => in_array($r, self::STRUCTURAL_ROLES)
            );
            $user->has_korwil = $roleNames->contains('korwil');
            $user->base_roles = $roleNames->diff(array_merge(self::STRUCTURAL_ROLES, ['korwil']));
            return $user;
        });

        return view('management_data.kpi-role.index', [
            'users' => $users,
            'search' => $search,
            'structuralRoles' => self::STRUCTURAL_ROLES,
            'divisions' => Division::orderBy('name')->get(),
            'divisionRoles' => self::DIVISION_ROLES,
        ]);
    }

    public function update(Request $request, User $user, TaskMutationFreezeService $freezeService)
    {
        $validated = $request->validate([
            'structural_role' => 'nullable|in:' . implode(',', self::STRUCTURAL_ROLES),
            'division_id' => 'nullable|exists:divisions,id|required_if:structural_role,' . implode(',', self::DIVISION_ROLES),
        ], [
            'division_id.required_if' => 'Divisi wajib dipilih untuk role Kadiv/Staff.',
        ]);

        // BARU (G4): rekam kondisi SEBELUM diubah, buat dibandingkan nanti
        $previousStructuralRole = collect(self::STRUCTURAL_ROLES)->first(fn($r) => $user->hasRole($r));
        $previousDivisionId = $user->division_id;
        $previousKorwil = $user->hasRole('korwil');

        DB::transaction(function () use ($validated, $request, $user) {
            foreach (self::STRUCTURAL_ROLES as $role) {
                if ($user->hasRole($role)) {
                    $user->removeRole($role);
                }
            }

            if (! empty($validated['structural_role'])) {
                $user->assignRole($validated['structural_role']);
            }

            $needsDivision = in_array($validated['structural_role'] ?? null, self::DIVISION_ROLES);
            $user->division_id = $needsDivision ? $validated['division_id'] : null;
            $user->save();

            $wantsKorwil = $request->boolean('korwil');

            if ($wantsKorwil && ! $user->hasRole('korwil')) {
                $user->assignRole('korwil');
            } elseif (! $wantsKorwil && $user->hasRole('korwil')) {
                $user->removeRole('korwil');
            }
        });

        // BARU (G4): cek ada mutasi apa enggak, kalau ada -> auto-freeze
        $user->refresh();
        $newStructuralRole = $validated['structural_role'] ?? null;
        $newDivisionId = $user->division_id;
        $newKorwil = $request->boolean('korwil');

        $isMutated = $previousStructuralRole !== $newStructuralRole
            || $previousDivisionId !== $newDivisionId
            || $previousKorwil !== $newKorwil;

        $frozenMessage = '';

        if ($isMutated) {
            $frozenCount = $freezeService->freezeActiveTasksFor($user, 'Perubahan role/divisi/Korwil via halaman Set Role KPI');

            if ($frozenCount > 0) {
                $frozenMessage = " {$frozenCount} Tugas KPI aktif otomatis di-freeze (rolling % dikunci).";
            }
        }

        return back()->with('success', "Role KPI untuk {$user->name} berhasil diperbarui.{$frozenMessage}");
    }
}
