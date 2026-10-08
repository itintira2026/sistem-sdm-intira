<?php

namespace App\Http\Controllers;

use App\Services\KpiSnapshotService;
use App\Services\TaskWeightService;
use App\Services\TeamVisibilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * Part E3 — Drill-down/tim view.
 *
 * DIUBAH: sekarang split jadi 2 grup (Head Office & Lapangan) buat
 * viewer yang bisa lihat keduanya (Owner/Direktur/Ketua/Kadiv), dengan
 * ringkasan statistik TERPISAH per grup — bukan digabung jadi satu
 * angka "semua". Viewer yang cuma bisa lihat 1 grup (Korwil/Staff)
 * dapet tampilan 1 kolom dengan panel ringkasan yang lebih besar di
 * sebelahnya, karena gak perlu berbagi ruang dengan grup lain.
 *
 * Paginasi tiap grup INDEPENDEN (page name beda: head_office_page vs
 * lapangan_page), biar geser halaman di satu grup gak ganggu grup lain.
 */
class TeamDrilldownController extends Controller
{
    private const PER_PAGE = 10;
    private const HIGH_THRESHOLD = 80;
    private const LOW_THRESHOLD = 40;

    public function index(
        Request $request,
        TeamVisibilityService $visibility,
        KpiSnapshotService $snapshotService,
        TaskWeightService $weightService
    ) {
        $user = Auth::user();
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);
        $startOfMonth = Carbon::create($year, $month, 1);

        $subordinates = $visibility->visibleTo($user);

        $rows = $subordinates->map(function ($sub) use ($snapshotService, $weightService, $month, $year) {
            $kpi = $snapshotService->getOrCalculateMonthly($sub, $month, $year);
            $totalWeight = $weightService->currentTotalWeight($sub);

            return [
                'user' => $sub,
                'percentage' => (float) ($kpi->total_percentage ?? 0),
                // NOTE: status bobot ini "sekarang", bukan spesifik bulan yang
                // sedang dilihat — cukup akurat buat bulan berjalan, tapi bisa
                // beda kalau sedang lihat riwayat bulan lalu.
                'weight_ok' => abs($totalWeight - 100) < 0.01,
            ];
        });

        $groups = collect();

        foreach (['head_office' => 'Head Office', 'lapangan' => 'Lapangan'] as $key => $label) {
            $groupRows = $rows->filter(fn($r) => $r['user']->visibility_group === $key)
                ->sortByDesc('percentage')
                ->values();

            if ($groupRows->isEmpty()) {
                continue;
            }

            $groups->put($key, [
                'label' => $label,
                'stats' => $this->buildStats($groupRows),
                'paginator' => $this->paginate($groupRows, $request, $key . '_page'),
            ]);
        }

        return view('tasks.team-drilldown', [
            'groups' => $groups,
            'month' => $month,
            'year' => $year,
            'monthLabel' => $startOfMonth->translatedFormat('F Y'),
            'prevMonth' => $startOfMonth->copy()->subMonth(),
            'nextMonth' => $startOfMonth->copy()->addMonth(),
        ]);
    }

    private function buildStats(Collection $groupRows): array
    {
        $percentages = $groupRows->pluck('percentage');

        return [
            'total' => $groupRows->count(),
            'average' => $percentages->isEmpty() ? 0 : round($percentages->avg(), 1),
            'high' => $percentages->filter(fn($p) => $p >= self::HIGH_THRESHOLD)->count(),
            'low' => $percentages->filter(fn($p) => $p < self::LOW_THRESHOLD)->count(),
        ];
    }

    private function paginate(Collection $groupRows, Request $request, string $pageName): LengthAwarePaginator
    {
        $page = (int) $request->query($pageName, 1);

        return new LengthAwarePaginator(
            $groupRows->forPage($page, self::PER_PAGE)->values(),
            $groupRows->count(),
            self::PER_PAGE,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->except($pageName),
                'pageName' => $pageName,
            ]
        );
    }
}
