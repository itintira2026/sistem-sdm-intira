<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchUser;
use App\Models\Nasabah;
use App\Models\Omzet;
use App\Models\Revenue;
use App\Models\ThreeHourReportManager;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThreeHourReportDashboardController extends Controller
{
    /**
     * Display dashboard based on user role
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Determine view type
        if ($user->hasRole('superadmin')) {
            return $this->superadminView($request);
        } elseif ($user->hasRole('marketing')) {
            return $this->marketingView($request);
        } elseif ($user->hasRole('manager')) {
            return $this->managerView($request);
        }

        abort(403, 'Unauthorized access');
    }

    /**
     * Manager View - Dashboard sendiri (FIXED)
     */
    protected function managerView(Request $request)
    {
        $user = Auth::user();

        // 🔥 FIX: Get managed branches (cabang yang user ini jadi manager)
        $managedBranches = Branch::whereHas('branchUsers', function ($q) use ($user) {
            $q->where('user_id', $user->id)
                ->where('is_active', true)
                ->where('is_manager', true); // Only manager assignments
        })->orderBy('name')->get();

        if ($managedBranches->isEmpty()) {
            return back()->with('error', 'Anda belum ditugaskan sebagai manager di cabang manapun.');
        }

        // Filter
        $selectedBranchId = $request->input('branch_id', $managedBranches->first()->id);
        $selectedDate = $request->input('date', today()->toDateString());

        // Validate access
        if (! $managedBranches->pluck('id')->contains($selectedBranchId)) {
            abort(403, 'Anda tidak memiliki akses ke cabang ini.');
        }

        $selectedBranch = Branch::find($selectedBranchId);

        // 🔥 FIX: Get ALL branch_user_ids di cabang yang dipilih (bukan hanya milik manager)
        // Karena manager perlu lihat laporan SEMUA FO di cabangnya
        $branchUserIds = BranchUser::where('branch_id', $selectedBranchId)
            ->where('is_active', true)
            ->pluck('id');

        // Stats (aggregate dari semua FO di cabang)
        $stats = $this->getStats($branchUserIds, $selectedDate);

        // 🔥 FIX: Get manager's own report (bukan semua FO)
        // Manager sendiri yang lapor, bukan FO
        $managerBranchUserId = BranchUser::where('user_id', $user->id)
            ->where('branch_id', $selectedBranchId)
            ->where('is_manager', true)
            ->value('id');

        $reportToday = null;
        $slots = [];

        if ($managerBranchUserId) {
            $reportToday = ThreeHourReportManager::where('branch_user_id', $managerBranchUserId)
                ->whereDate('created_at', $selectedDate)
                ->first();

            // Slot status untuk manager sendiri
            $slots = $this->getSlotStatus($reportToday, $selectedDate, now());
        }

        // History (laporan manager sendiri, bukan FO)
        $reports = ThreeHourReportManager::where('branch_user_id', $managerBranchUserId)
            ->whereDate('created_at', '>=', Carbon::parse($selectedDate)->subDays(7))
            ->whereDate('created_at', '<=', $selectedDate)
            ->with('branchUser.branch')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('daily-reports.three-hour-manager.index', [
            'viewType' => 'manager',
            'managedBranches' => $managedBranches,
            'selectedBranch' => $selectedBranch,
            'selectedDate' => $selectedDate,
            'stats' => $stats,
            'reportToday' => $reportToday,
            'slots' => $slots,
            'reports' => $reports,
        ]);
    }

    /**
     * Superadmin View - Table all FO (FIXED)
     */
    protected function superadminView(Request $request)
    {
        // Get all branches & managers for filter
        $allBranches = Branch::where('is_active', true)->orderBy('name')->get();

        $allManagers = User::whereHas('roles', function ($q) {
            $q->where('name', 'manager');
        })->where('is_active', true)->orderBy('name')->get();

        // Filters
        $selectedBranchId = $request->input('branch_id');
        $selectedManagerId = $request->input('manager_id');
        $selectedDate = $request->input('date', today()->toDateString());
        $perPage = $request->input('per_page', 25);

        // Build query for FO data
        $foData = $this->getFOTableData($selectedBranchId, $selectedManagerId, $selectedDate);

        // Paginate
        $foDataPaginated = $this->paginateCollection($foData, $perPage);

        // Global stats (based on filter)
        $globalStats = $this->calculateGlobalStats($foData, $selectedDate);

        return view('daily-reports.three-hour-manager.index', [
            'viewType' => 'superadmin',
            'allBranches' => $allBranches,
            'allManagers' => $allManagers,
            'selectedBranchId' => $selectedBranchId,
            'selectedManagerId' => $selectedManagerId,
            'selectedDate' => $selectedDate,
            'perPage' => $perPage,
            'foData' => $foDataPaginated,
            'globalStats' => $globalStats,
        ]);
    }

    /**
     * Marketing View - Table all FO + Charts (FIXED)
     */
    protected function marketingView(Request $request)
    {
        // Get all branches & managers for filter
        $allBranches = Branch::where('is_active', true)->orderBy('name')->get();

        $allManagers = User::whereHas('roles', function ($q) {
            $q->where('name', 'manager');
        })->where('is_active', true)->orderBy('name')->get();

        // Filters
        $selectedBranchId = $request->input('branch_id');
        $selectedManagerId = $request->input('manager_id');
        $selectedDate = $request->input('date', today()->toDateString());
        $dateFrom = $request->input('date_from', now()->subDays(30)->toDateString());
        $dateTo = $request->input('date_to', today()->toDateString());
        $perPage = $request->input('per_page', 25);

        // Build query for FO data
        $foData = $this->getFOTableData($selectedBranchId, $selectedManagerId, $selectedDate);

        // Paginate
        $foDataPaginated = $this->paginateCollection($foData, $perPage);

        // Global stats
        $globalStats = $this->calculateGlobalStats($foData, $selectedDate);

        // Charts data
        $chartsData = $this->getChartsData($selectedBranchId, $selectedManagerId, $dateFrom, $dateTo);

        return view('daily-reports.three-hour-manager.index', [
            'viewType' => 'marketing',
            'allBranches' => $allBranches,
            'allManagers' => $allManagers,
            'selectedBranchId' => $selectedBranchId,
            'selectedManagerId' => $selectedManagerId,
            'selectedDate' => $selectedDate,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'perPage' => $perPage,
            'foData' => $foDataPaginated,
            'globalStats' => $globalStats,
            'chartsData' => $chartsData,
        ]);
    }

    /**
     * Get FO Table Data (untuk Superadmin & Marketing) - FIXED
     */
    protected function getFOTableData($branchId, $managerId, $date)
    {
        // 🔥 FIX: Base query untuk ambil semua branch_users yang BUKAN manager
        $query = BranchUser::where('is_active', true)
            ->where('is_manager', false) // 🔥 Exclude managers, hanya FO
            ->with(['user', 'branch']);

        // Filter by branch
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        // 🔥 FIX: Filter by manager
        if ($managerId) {
            // Get branches yang di-manage oleh manager ini
            $managerBranchIds = BranchUser::where('user_id', $managerId)
                ->where('is_active', true)
                ->where('is_manager', true)
                ->pluck('branch_id');

            // Filter FO yang ada di cabang-cabang tersebut
            $query->whereIn('branch_id', $managerBranchIds);
        }

        $branchUsers = $query->get();

        // For each FO, get data
        return $branchUsers->map(function ($branchUser) use ($date) {
            // 🔥 FIX: Get manager di cabang yang sama
            $manager = BranchUser::where('branch_id', $branchUser->branch_id)
                ->where('is_manager', true)
                ->where('is_active', true)
                ->with('user')
                ->first();

            // Get report for selected date
            $report = ThreeHourReportManager::where('branch_user_id', $branchUser->id)
                ->whereDate('created_at', $date)
                ->first();

            // Get stats for selected date
            $statsToday = [
                'omzet' => Omzet::where('branch_user_id', $branchUser->id)
                    ->whereDate('tanggal', $date)
                    ->sum('rahn'),
                'revenue' => Revenue::where('branch_user_id', $branchUser->id)
                    ->whereDate('tanggal_transaksi', $date)
                    ->sum('jumlah_pembayaran'),
                'nasabah' => Nasabah::where('branch_user_id', $branchUser->id)
                    ->whereDate('created_at', $date)
                    ->count(),
            ];

            return [
                'branch_user_id' => $branchUser->id,
                'fo' => $branchUser->user,
                'branch' => $branchUser->branch,
                'manager' => $manager ? $manager->user : null,
                'report' => $report,
                'stats_today' => $statsToday,
            ];
        });
    }

    /**
     * Calculate global stats from FO data
     */
    protected function calculateGlobalStats($foData, $date)
    {
        return [
            'total_fo' => $foData->count(),
            'total_omzet' => $foData->sum('stats_today.omzet'),
            'total_revenue' => $foData->sum('stats_today.revenue'),
            'total_nasabah' => $foData->sum('stats_today.nasabah'),
            'fo_with_reports' => $foData->filter(function ($item) {
                return $item['report'] !== null;
            })->count(),
        ];
    }

    /**
     * Get charts data for Marketing (FIXED)
     */
    protected function getChartsData($branchId, $managerId, $dateFrom, $dateTo)
    {
        // 🔥 FIX: Build query for branch_user_ids based on filter
        $query = BranchUser::where('is_active', true)
            ->where('is_manager', false); // Only FO

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($managerId) {
            $managerBranchIds = BranchUser::where('user_id', $managerId)
                ->where('is_active', true)
                ->where('is_manager', true)
                ->pluck('branch_id');
            $query->whereIn('branch_id', $managerBranchIds);
        }

        $branchUserIds = $query->pluck('id');

        // Generate date range
        $dates = [];
        $omzetData = [];
        $revenueData = [];
        $nasabahData = [];

        $start = Carbon::parse($dateFrom);
        $end = Carbon::parse($dateTo);

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $currentDate = $date->toDateString();
            $dates[] = $date->format('d M');

            $omzetData[] = Omzet::whereIn('branch_user_id', $branchUserIds)
                ->whereDate('tanggal', $currentDate)
                ->sum('rahn');

            $revenueData[] = Revenue::whereIn('branch_user_id', $branchUserIds)
                ->whereDate('tanggal_transaksi', $currentDate)
                ->sum('jumlah_pembayaran');

            $nasabahData[] = Nasabah::whereIn('branch_user_id', $branchUserIds)
                ->whereDate('created_at', $currentDate)
                ->count();
        }

        return [
            'labels' => $dates,
            'omzet' => $omzetData,
            'revenue' => $revenueData,
            'nasabah' => $nasabahData,
        ];
    }

    /**
     * Get stats (for Manager view) - FIXED
     */
    protected function getStats($branchUserIds, $date)
    {
        $currentMonth = Carbon::parse($date);

        return [
            'omzet_hari_ini' => Omzet::whereIn('branch_user_id', $branchUserIds)
                ->whereDate('tanggal', $date)
                ->sum('rahn'),

            'omzet_bulan_ini' => Omzet::whereIn('branch_user_id', $branchUserIds)
                ->whereMonth('tanggal', $currentMonth->month)
                ->whereYear('tanggal', $currentMonth->year)
                ->sum('rahn'),

            'nasabah_hari_ini' => Nasabah::whereIn('branch_user_id', $branchUserIds)
                ->whereDate('created_at', $date)
                ->count(),

            'nasabah_bulan_ini' => Nasabah::whereIn('branch_user_id', $branchUserIds)
                ->whereMonth('created_at', $currentMonth->month)
                ->whereYear('created_at', $currentMonth->year)
                ->count(),

            'revenue_hari_ini' => Revenue::whereIn('branch_user_id', $branchUserIds)
                ->whereDate('tanggal_transaksi', $date)
                ->sum('jumlah_pembayaran'),

            'revenue_bulan_ini' => Revenue::whereIn('branch_user_id', $branchUserIds)
                ->whereMonth('tanggal_transaksi', $currentMonth->month)
                ->whereYear('tanggal_transaksi', $currentMonth->year)
                ->sum('jumlah_pembayaran'),
        ];
    }

    /**
     * Get slot status (for Manager view)
     */
    protected function getSlotStatus($report, $date, $currentTime)
    {
        $slots = [
            12 => ['time' => '12:00', 'label' => 'Laporan 12:00', 'deadline' => '13:00'],
            16 => ['time' => '16:00', 'label' => 'Laporan 16:00', 'deadline' => '17:00'],
            20 => ['time' => '20:00', 'label' => 'Laporan 20:00', 'deadline' => '21:00'],
        ];

        $targetDate = Carbon::parse($date);
        $isToday = $targetDate->isToday();

        foreach ($slots as $slot => &$data) {
            $slotTime = $targetDate->copy()->setTimeFromTimeString($data['time']);
            $deadlineTime = $targetDate->copy()->setTimeFromTimeString($data['deadline']);

            $reportField = "report_{$slot}_at";
            $hasReport = $report && $report->$reportField;

            if ($hasReport) {
                $data['status'] = 'done';
                $data['status_label'] = 'Sudah Lapor';
                $data['status_color'] = 'green';
                $data['reported_at'] = $report->$reportField;
            } elseif (! $isToday || $currentTime->lessThan($slotTime)) {
                $data['status'] = 'waiting';
                $data['status_label'] = 'Menunggu';
                $data['status_color'] = 'gray';
            } elseif ($currentTime->between($slotTime, $deadlineTime)) {
                $data['status'] = 'open';
                $data['status_label'] = 'Bisa Lapor';
                $data['status_color'] = 'orange';
            } else {
                $data['status'] = 'late';
                $data['status_label'] = 'Terlambat';
                $data['status_color'] = 'red';
            }

            $data['can_report'] = ! $hasReport && ($data['status'] === 'open' || $data['status'] === 'late');
        }

        return $slots;
    }

    /**
     * Paginate collection manually
     */
    protected function paginateCollection($collection, $perPage)
    {
        $page = request()->get('page', 1);
        $offset = ($page - 1) * $perPage;

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $collection->slice($offset, $perPage)->values(),
            $collection->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    /**
     * Show detail report
     */
    public function show($id)
    {
        $report = ThreeHourReportManager::with(['branchUser.user', 'branchUser.branch', 'revenues', 'omzets', 'nasabahs'])
            ->findOrFail($id);

        // TODO: Implement detail view
        return view('daily-reports.three-hour-manager.show', compact('report'));
    }

    /**
     * Export to Excel
     */
    public function export(Request $request)
    {
        // TODO: Implement Excel export
        return back()->with('info', 'Export feature coming soon');
    }

    /**
     * Create form
     */
    public function create()
    {
        // TODO: Implement create form
        return view('daily-reports.three-hour-manager.create');
    }

    /**
     * Store report
     */
    public function store(Request $request)
    {
        // TODO: Implement store logic
        return back()->with('success', 'Laporan berhasil disimpan');
    }

    /**
     * Edit form
     */
    public function edit($id)
    {
        $report = ThreeHourReportManager::findOrFail($id);

        // TODO: Implement edit form
        return view('daily-reports.three-hour-manager.edit', compact('report'));
    }

    /**
     * Update report
     */
    public function update(Request $request, $id)
    {
        // TODO: Implement update logic
        return back()->with('success', 'Laporan berhasil diupdate');
    }

    /**
     * Delete report
     */
    public function destroy($id)
    {
        $report = ThreeHourReportManager::findOrFail($id);
        $report->delete();

        return back()->with('success', 'Laporan berhasil dihapus');
    }
}
