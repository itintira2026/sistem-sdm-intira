<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Services\RankingCacheService;
use App\Services\RankingFoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\RankingFoExport;
use Maatwebsite\Excel\Facades\Excel;

class RankingFoController extends Controller
{
    public function __construct(
        private RankingFoService    $rankingService,
        private RankingCacheService $cacheService
    ) {}

    public function index(Request $request)
    {
        // Gate
        $user = Auth::user();
        if (! $user->hasRole('superadmin') && ! $user->hasRole('marketing')) {
            abort(403);
        }

        $branches = Branch::orderBy('name')->get();

        // Filter params
        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo   = $request->input('date_to',   now()->toDateString());
        $branchId = $request->input('branch_id', 'all');
        $sortBy   = $request->input('sort_by',   'omset');   // omset | revenue | akad
        $mode     = $request->input('mode',      'validated'); // validated | all
        $perPage  = (int) $request->input('per_page', 10);
        $page     = (int) $request->input('page', 1);

        // Validasi sortBy — hindari SQL injection
        if (! in_array($sortBy, ['omset', 'revenue', 'akad'])) {
            $sortBy = 'omset';
        }

        // Validasi mode
        if (! in_array($mode, ['validated', 'all'])) {
            $mode = 'validated';
        }

        // Query via service (dengan cache)
        $rows = $this->rankingService->getRanking(
            $dateFrom,
            $dateTo,
            $branchId,
            $sortBy,
            $mode,
            $page,
            $perPage
        );

        $top3 = $this->rankingService->getTop3(
            $dateFrom,
            $dateTo,
            $branchId,
            $sortBy
        );

        // Load user info
        $allUserIds = $rows->pluck('user_id')
            ->merge($top3->pluck('user_id'))
            ->unique();

        $users = $this->rankingService->loadUsers($allUserIds);

        // Attach user ke top3
        $top3->each(fn($r) => $r->user = $users->get($r->user_id));

        // Cache timestamp
        $lastUpdated = $this->cacheService->getLastUpdated();

        return view('ranking-fo.index', [
            'branches'    => $branches,
            'dateFrom'    => $dateFrom,
            'dateTo'      => $dateTo,
            'branchId'    => $branchId,
            'sortBy'      => $sortBy,
            'mode'        => $mode,
            'perPage'     => $perPage,
            'rows'        => $rows,
            'users'       => $users,
            'top3'        => $top3,
            'lastUpdated' => $lastUpdated,
        ]);
    }

    // -------------------------------------------------------
    // Force refresh cache — dipanggil dari tombol "Perbarui"
    // -------------------------------------------------------
    public function refresh(Request $request)
    {
        $user = Auth::user();
        if (! $user->hasRole('superadmin') && ! $user->hasRole('marketing')) {
            abort(403);
        }

        $this->cacheService->flush();

        return redirect()
            ->route('ranking-fo.index', $request->except('_token'))
            ->with('success', 'Data ranking berhasil diperbarui.');
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        if (! $user->hasRole('superadmin') && ! $user->hasRole('marketing')) {
            abort(403);
        }

        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo   = $request->input('date_to',   now()->toDateString());
        $branchId = $request->input('branch_id', 'all');
        $sortBy   = $request->input('sort_by',   'omset');

        if (! in_array($sortBy, ['omset', 'revenue', 'akad'])) {
            $sortBy = 'omset';
        }

        $filename = 'ranking-fo_'
            . $dateFrom . '_'
            . $dateTo . '_'
            . $sortBy . '_'
            . now()->format('His')
            . '.xlsx';

        return Excel::download(
            new RankingFoExport($dateFrom, $dateTo, $branchId, $sortBy),
            $filename
        );
    }
}
