<?php

namespace App\Services;

use App\Models\TaskAssignee;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Part B3 — Paksa total bobot KPI seseorang selalu pas 100% (§5).
 *
 * ALUR PEMAKAIAN YANG DIMAKSUD (di controller):
 * 1. Atasan bikin/nonaktifkan 1 tugas berbobot untuk seseorang.
 * 2. Controller panggil activeWeightedAssignments($user) buat tampilkan
 *    SEMUA tugas aktif berbobot orang itu (termasuk yang baru + yang lama).
 * 3. Atasan isi ulang SEMUA persentasenya di 1 form, dan boleh sekalian
 *    koreksi individual_target kalau ada salah ketik.
 * 4. Controller panggil validateTotal($user, $weights) sebelum simpan.
 * 5. Kalau valid, applyWeights()/applyTargets() dijalankan dalam
 *    DB::transaction() bareng operasi assign/freeze tugasnya.
 *
 * CATATAN: service ini CUMA relevan untuk Tugas KPI (has_weight=true).
 * Tugas Tambahan (self-record) gak pernah masuk hitungan sini sama sekali.
 * Assignee yang finalized_at != null (freeze mutasi ATAU dinonaktifkan
 * manual) TIDAK masuk activeWeightedAssignments() — sudah dikunci,
 * gak boleh diutak-atik bobot/targetnya lagi dari sini.
 */
class TaskWeightService
{
    private const TOLERANCE = 0.01; // toleransi pembulatan desimal

    /**
     * Semua task_assignees AKTIF & BERBOBOT milik user ini.
     * "Aktif" = task.status masih 'active', assignee belum di-freeze
     * (mutasi jabatan ATAU dinonaktifkan manual), DAN task.period_end
     * jatuh di BULAN BERJALAN — bukan sekadar "belum lewat hari ini".
     *
     * KENAPA PER BULAN (bukan rolling >= hari ini): KPI gak ada yang
     * lebih dari 1 bulan, jadi "paket bobot" yang wajib pas 100% itu
     * scope-nya satu bulan. Kalau dipakai rolling >= hari ini, tugas
     * `once` yang cuma 1 hari bakal "ilang" dari hitungan begitu
     * harinya lewat — padahal masih bagian dari bobot bulan itu,
     * cuma hari aktifnya aja yang udah kelar. Definisi bulan ini
     * SAMA PERSIS dengan yang dipakai KpiSnapshotService buat rollup
     * bulanan — satu sumber kebenaran, konsisten di semua halaman.
     */
    public function activeWeightedAssignments(User $user): Collection
    {
        $startOfMonth = now()->startOfMonth()->toDateString();
        $endOfMonth = now()->endOfMonth()->toDateString();

        return TaskAssignee::with('task')
            ->where('user_id', $user->id)
            ->whereNull('finalized_at')
            ->whereHas('task', function ($q) use ($startOfMonth, $endOfMonth) {
                $q->where('has_weight', true)
                    ->where('status', 'active')
                    ->whereBetween('period_end', [$startOfMonth, $endOfMonth]);
            })
            ->get();
    }

    /**
     * BARU: riwayat assignee yang sudah dikunci untuk user ini — dipakai
     * buat ditampilkan read-only di halaman Atur Bobot, biar atasan bisa
     * lihat kenapa suatu tugas gak muncul lagi di daftar aktif.
     */
    public function lockedAssignments(User $user): Collection
    {
        return TaskAssignee::with(['task', 'remover'])
            ->where('user_id', $user->id)
            ->whereNotNull('finalized_at')
            ->whereHas('task', fn($q) => $q->where('has_weight', true))
            ->orderByDesc('finalized_at')
            ->get();
    }

    /**
     * Total bobot SEKARANG (sebelum perubahan) — buat referensi tampilan
     * di UI, misal "Total saat ini: 80% — perlu disesuaikan jadi 100%".
     */
    public function currentTotalWeight(User $user): float
    {
        return (float) $this->activeWeightedAssignments($user)->sum('weight_percent');
    }

    /**
     * Validasi submission form re-input.
     *
     * @param  array<int,float>  $weights  [task_assignee_id => persentase, ...]
     *                                     WAJIB mencakup SEMUA task_assignee
     *                                     aktif berbobot milik user — gak
     *                                     boleh cuma sebagian.
     * @return array{valid: bool, message: ?string, total: ?float}
     */
    public function validateTotal(User $user, array $weights): array
    {
        $requiredIds = $this->activeWeightedAssignments($user)
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->sort()
            ->values();

        $providedIds = collect(array_keys($weights))
            ->map(fn($id) => (int) $id)
            ->sort()
            ->values();

        if ($requiredIds->toArray() !== $providedIds->toArray()) {
            return [
                'valid' => false,
                'message' => 'Semua tugas berbobot aktif harus diisi ulang persentasenya sekaligus, bukan cuma sebagian.',
                'total' => null,
            ];
        }

        $total = round(array_sum($weights), 2);

        if (abs($total - 100) > self::TOLERANCE) {
            return [
                'valid' => false,
                'message' => "Total bobot harus pas 100%, sekarang totalnya {$total}%.",
                'total' => $total,
            ];
        }

        return ['valid' => true, 'message' => null, 'total' => $total];
    }

    /**
     * Simpan bobot baru. HARUS dipanggil setelah validateTotal() lolos.
     *
     * @param  array<int,float>  $weights  [task_assignee_id => persentase, ...]
     */
    public function applyWeights(array $weights): void
    {
        foreach ($weights as $taskAssigneeId => $percent) {
            TaskAssignee::where('id', $taskAssigneeId)->update([
                'weight_percent' => $percent,
            ]);
        }
    }

    /**
     * BARU: simpan koreksi individual_target. Terpisah dari applyWeights()
     * karena target itu opsional (boleh sebagian aja yang dikirim/diubah),
     * beda dari bobot yang wajib lengkap & pas 100%.
     *
     * @param  User  $user  dipakai buat mastiin id yang dikirim memang
     *                      milik user ini (gak bisa nyasar ke assignee orang lain)
     * @param  array<int,float|null>  $targets  [task_assignee_id => target, ...]
     */
    public function applyTargets(User $user, array $targets): void
    {
        $ownedIds = $this->activeWeightedAssignments($user)->pluck('id')->map(fn($id) => (int) $id);

        foreach ($targets as $taskAssigneeId => $target) {
            if (! $ownedIds->contains((int) $taskAssigneeId)) {
                continue; // abaikan id yang bukan milik user ini
            }

            TaskAssignee::where('id', $taskAssigneeId)->update([
                'individual_target' => $target === '' ? null : $target,
            ]);
        }
    }
}
