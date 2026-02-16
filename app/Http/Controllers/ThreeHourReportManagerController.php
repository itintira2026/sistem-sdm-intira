<?php

namespace App\Http\Controllers;

use App\Imports\NasabahImport;
use App\Models\ThreeHourReportManager;
use Illuminate\Http\Request;
use App\Models\BranchUser;
use App\Models\Branch;
use App\Models\Revenue;
use App\Models\Nasabah;
use App\Models\Omzet;
use App\Imports\OmzetImport;
use App\Imports\RevenueImport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ThreeHourReportManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * Dashboard - Single view untuk semua role
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // ===============================
        // DETERMINE VIEW TYPE BASED ON ROLE
        // ===============================
        $viewType = $this->getViewType($user);
        
        // ===============================
        // GET DATA BASED ON ROLE
        // ===============================
        switch ($viewType) {
            case 'manager':
                return $this->managerDashboard($request, $user);
                
            case 'superadmin':
                return $this->superadminDashboard($request, $user);
                
            case 'marketing':
                return $this->marketingDashboard($request, $user);
                
            default:
                abort(403, 'Unauthorized access');
        }
    }
    
    /**
     * Determine view type based on role
     */
    protected function getViewType($user)
    {
        // Priority: superadmin > marketing > manager
        if ($user->hasRole('superadmin')) {
            return 'superadmin';
        } elseif ($user->hasRole('marketing')) {
            return 'marketing';
        } elseif ($user->hasRole('manager')) {
            return 'manager';
        }
        
        return null;
    }
    
    /**
     * Manager Dashboard (AM View)
     */
    protected function managerDashboard(Request $request, $user)
    {
        // Get managed branches (active only)
        $managedBranchUsers = $user->branchAssignments()
            ->where('is_active', true)
            ->with('branch')
            ->get();

        $managedBranches = $managedBranchUsers
            ->pluck('branch')
            ->unique('id')
            ->values();

        if ($managedBranches->isEmpty()) {
            return back()->with('error', 'Anda belum ditugaskan di cabang manapun.');
        }

        // Filter
        $selectedBranchId = $request->input('branch_id', $managedBranches->first()->id);
        $selectedDate = $request->input('date', today()->toDateString());

        // Validate access
        if (!$managedBranches->pluck('id')->contains($selectedBranchId)) {
            abort(403, 'Anda tidak memiliki akses ke cabang ini.');
        }

        $selectedBranch = Branch::find($selectedBranchId);

        // Get branch_user_ids
        $branchUserIds = $managedBranchUsers
            ->where('branch_id', $selectedBranchId)
            ->pluck('id');

        // Stats
        $stats = $this->getStats($branchUserIds, $selectedDate);
        
        // Report status today
        $reportToday = ThreeHourReportManager::whereIn('branch_user_id', $branchUserIds)
            ->whereDate('created_at', $selectedDate)
            ->first();

        // Slot status
        $slots = $this->getSlotStatus($reportToday, $selectedDate, now());

        // History (7 days)
        $reports = ThreeHourReportManager::whereIn('branch_user_id', $branchUserIds)
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
     * Superadmin Dashboard (Global Monitoring)
     */
    protected function superadminDashboard(Request $request, $user)
    {
        // Get all branches
        $allBranches = Branch::orderBy('name')->get();
        
        // Filter
        $selectedBranchId = $request->input('branch_id'); // null = all branches
        $selectedDate = $request->input('date', today()->toDateString());
        
        // Get branch_user_ids
        if ($selectedBranchId) {
            $branchUserIds = BranchUser::where('branch_id', $selectedBranchId)
                ->where('is_active', true)
                ->pluck('id');
            $selectedBranch = Branch::find($selectedBranchId);
        } else {
            $branchUserIds = BranchUser::where('is_active', true)->pluck('id');
            $selectedBranch = null;
        }
        
        // Global Stats
        $stats = $this->getStats($branchUserIds, $selectedDate);
        
        // Monitoring: All branches with their report status
        $branchesMonitoring = $this->getBranchesMonitoring($selectedDate);
        
        // Late reports (alerts)
        $lateReports = $this->getLateReports($selectedDate);
        
        // History
        $reports = ThreeHourReportManager::whereIn('branch_user_id', $branchUserIds)
            ->whereDate('created_at', '>=', Carbon::parse($selectedDate)->subDays(7))
            ->whereDate('created_at', '<=', $selectedDate)
            ->with('branchUser.branch')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('daily-reports.three-hour-manager.index', [
            'viewType' => 'superadmin',
            'allBranches' => $allBranches,
            'selectedBranch' => $selectedBranch,
            'selectedDate' => $selectedDate,
            'stats' => $stats,
            'branchesMonitoring' => $branchesMonitoring,
            'lateReports' => $lateReports,
            'reports' => $reports,
        ]);
    }
    
    /**
     * Marketing Dashboard (Analytics) - UPDATED
     */
    protected function marketingDashboard(Request $request, $user)
    {
        // Get all branches
        $allBranches = Branch::orderBy('name')->get();
        
        // Filter
        $selectedBranchIds = $request->input('branch_ids', []);
        $dateFrom = $request->input('date_from', now()->subDays(30)->toDateString());
        $dateTo = $request->input('date_to', today()->toDateString());
        
        // Get branch_user_ids
        if (!empty($selectedBranchIds)) {
            $branchUserIds = BranchUser::whereIn('branch_id', $selectedBranchIds)
                ->where('is_active', true)
                ->pluck('id');
        } else {
            $branchUserIds = BranchUser::where('is_active', true)->pluck('id');
        }
        
        // Analytics Stats
        $analyticsStats = $this->getAnalyticsStats($branchUserIds, $dateFrom, $dateTo);
        
        // Trends (untuk chart)
        $trends = $this->getTrends($branchUserIds, $dateFrom, $dateTo);
        
        // Top/Bottom branches
        $rankings = $this->getBranchRankings($dateFrom, $dateTo);
        
        // Compliance rate
        $compliance = $this->getComplianceRate($dateFrom, $dateTo);
        
        // Upload heatmap (NEW)
        $heatmap = $this->getUploadHeatmap($branchUserIds, $dateFrom, $dateTo);
        
        return view('daily-reports.three-hour-manager.index', [
            'viewType' => 'marketing',
            'allBranches' => $allBranches,
            'selectedBranchIds' => $selectedBranchIds,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'analyticsStats' => $analyticsStats,
            'trends' => $trends,
            'rankings' => $rankings,
            'compliance' => $compliance,
            'heatmap' => $heatmap, // NEW
        ]);
    }
    // protected function marketingDashboard(Request $request, $user)
    // {
    //     // Get all branches
    //     $allBranches = Branch::orderBy('name')->get();
        
    //     // Filter
    //     $selectedBranchIds = $request->input('branch_ids', []); // multiple selection
    //     $dateFrom = $request->input('date_from', now()->subDays(30)->toDateString());
    //     $dateTo = $request->input('date_to', today()->toDateString());
        
    //     // Get branch_user_ids
    //     if (!empty($selectedBranchIds)) {
    //         $branchUserIds = BranchUser::whereIn('branch_id', $selectedBranchIds)
    //             ->where('is_active', true)
    //             ->pluck('id');
    //     } else {
    //         $branchUserIds = BranchUser::where('is_active', true)->pluck('id');
    //     }
        
    //     // Analytics Stats
    //     $analyticsStats = $this->getAnalyticsStats($branchUserIds, $dateFrom, $dateTo);
        
    //     // Trends (untuk chart)
    //     $trends = $this->getTrends($branchUserIds, $dateFrom, $dateTo);
        
    //     // Top/Bottom branches
    //     $rankings = $this->getBranchRankings($dateFrom, $dateTo);
        
    //     // Compliance rate
    //     $compliance = $this->getComplianceRate($dateFrom, $dateTo);
        
    //     return view('daily-reports.three-hour-manager.index', [
    //         'viewType' => 'marketing',
    //         'allBranches' => $allBranches,
    //         'selectedBranchIds' => $selectedBranchIds,
    //         'dateFrom' => $dateFrom,
    //         'dateTo' => $dateTo,
    //         'analyticsStats' => $analyticsStats,
    //         'trends' => $trends,
    //         'rankings' => $rankings,
    //         'compliance' => $compliance,
    //     ]);
    // }
    
    /**
     * Get stats (Omzet, Nasabah, Revenue)
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
     * Get slot status (waiting/open/done/late)
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
            } elseif (!$isToday || $currentTime->lessThan($slotTime)) {
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

            $data['can_report'] = !$hasReport && ($data['status'] === 'open' || $data['status'] === 'late');
        }

        return $slots;
    }
    
    /**
     * Get branches monitoring (for superadmin)
     */
    protected function getBranchesMonitoring($date)
    {
        $branches = Branch::with(['branchUsers' => function ($q) {
            $q->where('is_active', true)
              ->whereHas('user', function ($q2) {
                  $q2->whereHas('roles', function ($q3) {
                      $q3->where('name', 'manager');
                  });
              });
        }])->get();
        
        $monitoring = [];
        
        foreach ($branches as $branch) {
            foreach ($branch->branchUsers as $branchUser) {
                $report = ThreeHourReportManager::where('branch_user_id', $branchUser->id)
                    ->whereDate('created_at', $date)
                    ->first();
                
                $slots = $this->getSlotStatus($report, $date, now());
                
                $completedSlots = 0;
                if ($report) {
                    if ($report->report_12_at) $completedSlots++;
                    if ($report->report_16_at) $completedSlots++;
                    if ($report->report_20_at) $completedSlots++;
                }
                
                $monitoring[] = [
                    'branch' => $branch,
                    'manager' => $branchUser->user,
                    'slots' => $slots,
                    'completion' => ($completedSlots / 3) * 100,
                    'report' => $report,
                ];
            }
        }
        
        return collect($monitoring)->sortByDesc('completion')->values();
    }
    
    /**
     * Get late reports (for alerts)
     */
    protected function getLateReports($date)
    {
        $currentTime = now();
        $targetDate = Carbon::parse($date);
        
        if (!$targetDate->isToday()) {
            return collect([]);
        }
        
        $lateReports = [];
        $allBranchUsers = BranchUser::where('is_active', true)
            ->whereHas('user', function ($q) {
                $q->whereHas('roles', function ($q2) {
                    $q2->where('name', 'manager');
                });
            })
            ->with(['branch', 'user'])
            ->get();
        
        foreach ($allBranchUsers as $branchUser) {
            $report = ThreeHourReportManager::where('branch_user_id', $branchUser->id)
                ->whereDate('created_at', $date)
                ->first();
            
            // Check slot 12
            if ($currentTime->greaterThan($targetDate->copy()->setTimeFromTimeString('13:00'))) {
                if (!$report || !$report->report_12_at) {
                    $lateReports[] = [
                        'branch' => $branchUser->branch,
                        'manager' => $branchUser->user,
                        'slot' => '12:00',
                        'type' => 'late',
                    ];
                }
            }
            
            // Check slot 16
            if ($currentTime->greaterThan($targetDate->copy()->setTimeFromTimeString('17:00'))) {
                if (!$report || !$report->report_16_at) {
                    $lateReports[] = [
                        'branch' => $branchUser->branch,
                        'manager' => $branchUser->user,
                        'slot' => '16:00',
                        'type' => 'late',
                    ];
                }
            }
            
            // Check slot 20
            if ($currentTime->greaterThan($targetDate->copy()->setTimeFromTimeString('21:00'))) {
                if (!$report || !$report->report_20_at) {
                    $lateReports[] = [
                        'branch' => $branchUser->branch,
                        'manager' => $branchUser->user,
                        'slot' => '20:00',
                        'type' => 'late',
                    ];
                }
            }
        }
        
        return collect($lateReports);
    }
    
    /**
     * Get analytics stats (for marketing)
     */
    protected function getAnalyticsStats($branchUserIds, $dateFrom, $dateTo)
    {
        return [
            'total_omzet' => Omzet::whereIn('branch_user_id', $branchUserIds)
                ->whereBetween('tanggal', [$dateFrom, $dateTo])
                ->sum('rahn'),
                
            'total_nasabah' => Nasabah::whereIn('branch_user_id', $branchUserIds)
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->count(),
                
            'total_revenue' => Revenue::whereIn('branch_user_id', $branchUserIds)
                ->whereBetween('tanggal_transaksi', [$dateFrom, $dateTo])
                ->sum('jumlah_pembayaran'),
                
            'total_reports' => ThreeHourReportManager::whereIn('branch_user_id', $branchUserIds)
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->count(),
        ];
    }

    /**
     * Get trends (for charts) - Daily breakdown
     */
    protected function getTrends($branchUserIds, $dateFrom, $dateTo)
    {
        $dates = [];
        $omzetData = [];
        $revenueData = [];
        $nasabahData = [];
        
        // Generate date range
        $start = Carbon::parse($dateFrom);
        $end = Carbon::parse($dateTo);
        
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $currentDate = $date->toDateString();
            $dates[] = $date->format('d M');
            
            // Omzet per day
            $omzetData[] = Omzet::whereIn('branch_user_id', $branchUserIds)
                ->whereDate('tanggal', $currentDate)
                ->sum('rahn');
            
            // Revenue per day
            $revenueData[] = Revenue::whereIn('branch_user_id', $branchUserIds)
                ->whereDate('tanggal_transaksi', $currentDate)
                ->sum('jumlah_pembayaran');
            
            // Nasabah per day
            $nasabahData[] = Nasabah::whereIn('branch_user_id', $branchUserIds)
                ->whereDate('created_at', $currentDate)
                ->count();
        }
        
        return [
            'labels' => $dates,
            'datasets' => [
                [
                    'label' => 'Omzet',
                    'data' => $omzetData,
                    'borderColor' => 'rgb(59, 130, 246)', // blue
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'yAxisID' => 'y-axis-rupiah',
                ],
                [
                    'label' => 'Revenue',
                    'data' => $revenueData,
                    'borderColor' => 'rgb(168, 85, 247)', // purple
                    'backgroundColor' => 'rgba(168, 85, 247, 0.1)',
                    'yAxisID' => 'y-axis-rupiah',
                ],
                [
                    'label' => 'Nasabah',
                    'data' => $nasabahData,
                    'borderColor' => 'rgb(34, 197, 94)', // green
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'yAxisID' => 'y-axis-count',
                ],
            ],
        ];
    }

    /**
     * Get branch rankings (Top 10 & Bottom 10)
     */
    protected function getBranchRankings($dateFrom, $dateTo)
    {
        // Get all branches with their totals
        $branchStats = Branch::with(['branchUsers' => function ($q) {
            $q->where('is_active', true);
        }])
        ->get()
        ->map(function ($branch) use ($dateFrom, $dateTo) {
            $branchUserIds = $branch->branchUsers->pluck('id');
            
            $totalOmzet = Omzet::whereIn('branch_user_id', $branchUserIds)
                ->whereBetween('tanggal', [$dateFrom, $dateTo])
                ->sum('rahn');
            
            $totalRevenue = Revenue::whereIn('branch_user_id', $branchUserIds)
                ->whereBetween('tanggal_transaksi', [$dateFrom, $dateTo])
                ->sum('jumlah_pembayaran');
            
            $totalNasabah = Nasabah::whereIn('branch_user_id', $branchUserIds)
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->count();
            
            $totalReports = ThreeHourReportManager::whereIn('branch_user_id', $branchUserIds)
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->count();
            
            return [
                'branch' => $branch,
                'total_omzet' => $totalOmzet,
                'total_revenue' => $totalRevenue,
                'total_nasabah' => $totalNasabah,
                'total_reports' => $totalReports,
                'combined_score' => $totalOmzet + $totalRevenue, // Score untuk ranking
            ];
        })
        ->filter(function ($stat) {
            // Filter cabang yang punya aktivitas
            return $stat['total_reports'] > 0;
        });
        
        // Sort by combined score
        $sorted = $branchStats->sortByDesc('combined_score')->values();
        
        return [
            'top_10' => $sorted->take(10),
            'bottom_10' => $sorted->reverse()->take(10)->reverse()->values(),
        ];
    }

    /**
     * Get compliance rate (Kelengkapan laporan per cabang)
     */
    protected function getComplianceRate($dateFrom, $dateTo)
    {
        $start = Carbon::parse($dateFrom);
        $end = Carbon::parse($dateTo);
        $totalDays = $start->diffInDays($end) + 1;
        
        // Expected reports per branch = totalDays * 3 slots
        $expectedReportsPerDay = 3;
        
        $complianceData = Branch::with(['branchUsers' => function ($q) {
            $q->where('is_active', true)
            ->whereHas('user', function ($q2) {
                $q2->whereHas('roles', function ($q3) {
                    $q3->where('name', 'manager');
                });
            });
        }])
        ->get()
        ->map(function ($branch) use ($dateFrom, $dateTo, $totalDays, $expectedReportsPerDay) {
            $branchUserIds = $branch->branchUsers->pluck('id');
            
            if ($branchUserIds->isEmpty()) {
                return null;
            }
            
            // Get all reports
            $reports = ThreeHourReportManager::whereIn('branch_user_id', $branchUserIds)
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->get();
            
            // Count total slots filled
            $totalSlotsFilled = 0;
            foreach ($reports as $report) {
                if ($report->report_12_at) $totalSlotsFilled++;
                if ($report->report_16_at) $totalSlotsFilled++;
                if ($report->report_20_at) $totalSlotsFilled++;
            }
            
            // Expected slots = totalDays * 3 slots * jumlah AM
            $amCount = $branchUserIds->count();
            $expectedSlots = $totalDays * $expectedReportsPerDay * $amCount;
            
            // Compliance rate
            $complianceRate = $expectedSlots > 0 
                ? ($totalSlotsFilled / $expectedSlots) * 100 
                : 0;
            
            return [
                'branch' => $branch,
                'am_count' => $amCount,
                'expected_slots' => $expectedSlots,
                'filled_slots' => $totalSlotsFilled,
                'compliance_rate' => round($complianceRate, 2),
                'status' => $complianceRate >= 90 ? 'excellent' : 
                        ($complianceRate >= 70 ? 'good' : 
                        ($complianceRate >= 50 ? 'warning' : 'critical')),
            ];
        })
        ->filter() // Remove nulls
        ->sortByDesc('compliance_rate')
        ->values();
        
        return $complianceData;
    }

    /**
     * Get heatmap data (Upload time patterns)
     */
    protected function getUploadHeatmap($branchUserIds, $dateFrom, $dateTo)
    {
        $heatmapData = [
            12 => [], // Slot 12:00
            16 => [], // Slot 16:00
            20 => [], // Slot 20:00
        ];
        
        $reports = ThreeHourReportManager::whereIn('branch_user_id', $branchUserIds)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->get();
        
        foreach ($reports as $report) {
            // Slot 12:00
            if ($report->report_12_at) {
                $minute = $report->report_12_at->format('H:i');
                $heatmapData[12][] = $minute;
            }
            
            // Slot 16:00
            if ($report->report_16_at) {
                $minute = $report->report_16_at->format('H:i');
                $heatmapData[16][] = $minute;
            }
            
            // Slot 20:00
            if ($report->report_20_at) {
                $minute = $report->report_20_at->format('H:i');
                $heatmapData[20][] = $minute;
            }
        }
        
        // Group by time ranges
        $processed = [];
        foreach ($heatmapData as $slot => $times) {
            $timeCounts = array_count_values($times);
            arsort($timeCounts);
            
            // Get most common upload time
            $mostCommon = array_slice($timeCounts, 0, 3, true);
            
            $processed[$slot] = [
                'slot_time' => $slot . ':00',
                'total_uploads' => count($times),
                'most_common_times' => $mostCommon,
                'average_delay' => $this->calculateAverageDelay($times, $slot),
            ];
        }
        
        return $processed;
    }

    /**
     * Calculate average delay from slot time
     */
    protected function calculateAverageDelay($times, $slotHour)
    {
        if (empty($times)) return 0;
        
        $delays = [];
        foreach ($times as $time) {
            $uploadTime = Carbon::createFromFormat('H:i', $time);
            $slotTime = Carbon::createFromFormat('H:i', sprintf('%02d:00', $slotHour));
            
            // Calculate minutes difference
            $delay = $uploadTime->diffInMinutes($slotTime, false);
            $delays[] = max(0, $delay); // Only positive delays
        }
        
        return count($delays) > 0 ? round(array_sum($delays) / count($delays), 1) : 0;
    }
    // public function index()
    // {
    //     $user = Auth::user();

    //     // ===============================
    //     // AMBIL SEMUA branch_user_id MILIK AREA MANAGER
    //     // ===============================
    //     $branchUserIds = Auth::user()
    //         ->branchAssignments()
    //         ->pluck('id');

    //     // DEBUG (optional)
    //     // dd($branchUserIds);

    //     // ===============================
    //     // OMZET
    //     // ===============================
    //     $omzetHariIni = Omzet::whereIn('branch_user_id', $branchUserIds)
    //         ->whereDate('tanggal', today())
    //         ->sum('rahn');


    //     $omzetBulanIni = Omzet::whereIn('branch_user_id', $branchUserIds)
    //         ->whereMonth('tanggal', now()->month)
    //         ->whereYear('tanggal', now()->year)
    //         ->sum('rahn');


    //     // ===============================
    //     // NASABAH
    //     // ===============================

    //     $nasabahHariIni = Nasabah::whereIn('branch_user_id', $branchUserIds)
    //         ->whereDate('created_at', today())
    //         ->count();

    //     $nasabahBulanIni = Nasabah::whereIn('branch_user_id', $branchUserIds)
    //         ->whereMonth('created_at', now()->month)
    //         ->whereYear('created_at', now()->year)
    //         ->count();


    //     // ===============================
    //     // REVENUE
    //     // ===============================

    //     $revenueHariIni = Revenue::whereIn('branch_user_id', $branchUserIds)
    //         ->whereDate('tanggal_transaksi', today())
    //         ->sum('jumlah_pembayaran');

    //     $revenueBulanIni = Revenue::whereIn('branch_user_id', $branchUserIds)
    //         ->whereMonth('tanggal_transaksi', now()->month)
    //         ->whereYear('tanggal_transaksi', now()->year)
    //         ->sum('jumlah_pembayaran');


    //     // ===============================
    //     // RETURN VIEW
    //     // ===============================

    //     return view('daily-reports.three-hour-manager.index', compact(
    //         'omzetHariIni',
    //         'omzetBulanIni',
    //         'nasabahHariIni',
    //         'nasabahBulanIni',
    //         'revenueHariIni',
    //         'revenueBulanIni'
    //     ));
    // }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $branchUser = BranchUser::where('user_id', $user->id)->first();

        if (!$branchUser) {
            return redirect()->route('daily-reports.3hour-manager.index')
                ->with('error', 'Anda belum terdaftar di cabang manapun');
        }

        $today = Carbon::today();
        $report = ThreeHourReportManager::where('branch_user_id', $branchUser->id)
            ->whereDate('created_at', $today)
            ->first();

        $stats = [
            'total_today' => 0,
            'target' => 3,
            'report_12' => false,
            'report_16' => false,
            'report_20' => false,
        ];

        if ($report) {
            $stats['report_12'] = !is_null($report->report_12_at);
            $stats['report_16'] = !is_null($report->report_16_at);
            $stats['report_20'] = !is_null($report->report_20_at);
            $stats['total_today'] = collect([
                $stats['report_12'],
                $stats['report_16'],
                $stats['report_20']
            ])->filter()->count();
        }


        return view('daily-reports.three-hour-manager.create', compact('stats'));
    }

//     public function store(Request $request)
// {
//     Log::info('========== STORE METHOD STARTED ==========');
//     Log::info('Request data:', $request->all());
//     Log::info('Has file revenue:', [$request->hasFile('file_revenue')]);
    
//     if ($request->hasFile('file_revenue')) {
//         Log::info('File details:', [
//             'name' => $request->file('file_revenue')->getClientOriginalName(),
//             'size' => $request->file('file_revenue')->getSize(),
//             'mime' => $request->file('file_revenue')->getMimeType(),
//         ]);
//     }

//     $request->validate([
//         'time_slot'       => ['required', 'in:12:00,16:00,20:00'],
//         'keterangan'      => ['nullable', 'string'],
//         'file_revenue'    => ['nullable', 'file', 'mimes:xlsx,xls,csv', 'max:2048'],
//     ]);

//     Log::info('Validation passed');

//     DB::beginTransaction();
    
//     try {
//         // ===============================
//         // AMBIL BRANCH USER AUTH
//         // ===============================
//         $user = Auth::user();
//         Log::info('Current user:', ['id' => $user->id, 'email' => $user->email]);
        
//         $branchUser = BranchUser::where('user_id', $user->id)->first();

//         if (!$branchUser) {
//             Log::error('BranchUser not found for user_id: ' . $user->id);
//             DB::rollBack();
//             return redirect()->back()->with('error', 'Anda belum terdaftar di cabang manapun');
//         }

//         Log::info('BranchUser found:', ['id' => $branchUser->id, 'branch_id' => $branchUser->branch_id]);

//         // ===============================
//         // CEK LAPORAN HARI INI
//         // ===============================
//         $today = Carbon::today();
//         Log::info('Today date:', ['date' => $today->toDateString()]);
        
//         $report = ThreeHourReportManager::where('branch_user_id', $branchUser->id)
//             ->whereDate('created_at', $today)
//             ->first();

//         Log::info('Existing report:', ['found' => $report ? 'yes' : 'no', 'id' => $report->id ?? null]);

//         // Jika belum ada laporan, buat baru
//         if (!$report) {
//             Log::info('Creating new report...');
//             $report = ThreeHourReportManager::create([
//                 'branch_user_id' => $branchUser->id,
//                 'keterangan'     => $request->keterangan,
//             ]);
//             Log::info('New report created:', ['id' => $report->id]);
//         }

//         // ===============================
//         // UPDATE TIMESTAMP SESUAI TIME SLOT
//         // ===============================
//         $timeSlot = str_replace(':', '_', $request->time_slot);
//         $columnName = 'report_' . explode('_', $timeSlot)[0] . '_at';
        
//         Log::info('Time slot processing:', [
//             'time_slot' => $request->time_slot,
//             'column_name' => $columnName,
//             'current_value' => $report->$columnName,
//         ]);

//         // Cek apakah slot waktu ini sudah diisi
//         if ($report->$columnName) {
//             Log::warning('Time slot already filled:', ['column' => $columnName]);
//             DB::rollBack();
//             return redirect()->back()->with('error', "Laporan untuk jam {$request->time_slot} sudah diinput!");
//         }

//         // Update timestamp
//         Log::info('Updating report timestamp...');
//         $report->update([
//             $columnName => now(),
//             'keterangan' => $request->keterangan ?? $report->keterangan,
//         ]);
//         Log::info('Report updated successfully');

//         // ===============================
//         // IMPORT REVENUE PELUNASAN
//         // ===============================
//         $successCount = 0;
//         $updateCount = 0;
        
//         if ($request->hasFile('file_revenue')) {
//             Log::info('========== STARTING REVENUE IMPORT ==========');
//             Log::info('Report ID for import:', ['report_id' => $report->id]);
            
//             try {
//                 $import = new RevenueImport($report->id);
//                 Log::info('RevenueImport instance created');
                
//                 Excel::import($import, $request->file('file_revenue'));
//                 Log::info('Excel::import executed');
                
//                 $successCount = $import->getSuccessCount();
//                 $updateCount = $import->getUpdateCount();
                
//                 Log::info('Import results:', [
//                     'inserted' => $successCount,
//                     'updated' => $updateCount
//                 ]);
                
//                 // Cek jika ada error
//                 $failures = $import->failures();
//                 Log::info('Import failures count:', ['count' => $failures->count()]);
                
//                 if ($failures->isNotEmpty()) {
//                     $errorMessages = [];
//                     foreach ($failures as $failure) {
//                         $errorMsg = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
//                         $errorMessages[] = $errorMsg;
//                         Log::error('Import failure:', [
//                             'row' => $failure->row(),
//                             'errors' => $failure->errors(),
//                             'values' => $failure->values(),
//                         ]);
//                     }
                    
//                     DB::rollBack();
//                     Log::warning('Transaction rolled back due to import failures');
//                     return redirect()->back()
//                         ->with('error', 'Import gagal: ' . implode(' | ', $errorMessages))
//                         ->withInput();
//                 }
                
//                 Log::info('========== REVENUE IMPORT COMPLETED ==========');
//             } catch (\Exception $importException) {
//                 Log::error('Import exception caught:', [
//                     'message' => $importException->getMessage(),
//                     'file' => $importException->getFile(),
//                     'line' => $importException->getLine(),
//                     'trace' => $importException->getTraceAsString(),
//                 ]);
//                 throw $importException;
//             }
//         } else {
//             Log::info('No revenue file uploaded');
//         }

//           if ($request->hasFile('file_nasabah')) {
//             Log::info('========== STARTING NASABAH IMPORT ==========');
//             Log::info('Report ID for import:', ['report_id' => $report->id]);
            
//             try {
//                 $import = new NasabahImport($report->id);
//                 Log::info('NasabahImport instance created');
                
//                 Excel::import($import, $request->file('file_nasabah'));
//                 Log::info('Excel::import executed');
                
//                 $successCount = $import->getSuccessCount();
//                 $updateCount = $import->getUpdateCount();
                
//                 Log::info('Import results:', [
//                     'inserted' => $successCount,
//                     'updated' => $updateCount
//                 ]);
                
//                 // Cek jika ada error
//                 $failures = $import->failures();
//                 Log::info('Import failures count:', ['count' => $failures->count()]);
                
//                 if ($failures->isNotEmpty()) {
//                     $errorMessages = [];
//                     foreach ($failures as $failure) {
//                         $errorMsg = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
//                         $errorMessages[] = $errorMsg;
//                         Log::error('Import failure:', [
//                             'row' => $failure->row(),
//                             'errors' => $failure->errors(),
//                             'values' => $failure->values(),
//                         ]);
//                     }
                    
//                     DB::rollBack();
//                     Log::warning('Transaction rolled back due to import failures');
//                     return redirect()->back()
//                         ->with('error', 'Import gagal: ' . implode(' | ', $errorMessages))
//                         ->withInput();
//                 }
                
//                 Log::info('========== REVENUE IMPORT COMPLETED ==========');
//             } catch (\Exception $importException) {
//                 Log::error('Import exception caught:', [
//                     'message' => $importException->getMessage(),
//                     'file' => $importException->getFile(),
//                     'line' => $importException->getLine(),
//                     'trace' => $importException->getTraceAsString(),
//                 ]);
//                 throw $importException;
//             }
//         } else {
//             Log::info('No revenue file uploaded');
//         }

//         DB::commit();
//         Log::info('Transaction committed successfully');

//         // ===============================
//         // BUAT SUCCESS MESSAGE
//         // ===============================
//         $message = "Laporan jam {$request->time_slot} berhasil disimpan!";
        
//         if ($successCount > 0) {
//             $message .= " {$successCount} data baru ditambahkan.";
//         }
        
//         if ($updateCount > 0) {
//             $message .= " {$updateCount} data diupdate.";
//         }

//         Log::info('Success message:', ['message' => $message]);
//         Log::info('========== STORE METHOD COMPLETED ==========');

//         return redirect()->route('daily-reports.3hour-manager.index')
//             ->with('success', $message);

//     } catch (\Exception $e) {
//         DB::rollBack();
        
//         Log::error('========== STORE METHOD FAILED ==========');
//         Log::error('Exception details:', [
//             'message' => $e->getMessage(),
//             'file' => $e->getFile(),
//             'line' => $e->getLine(),
//             'trace' => $e->getTraceAsString(),
//         ]);
        
//         return redirect()->back()
//             ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
//             ->withInput();
//     }
// }
    /**
     * Display the specified resource.
     */

    public function store(Request $request)
    {
        Log::info('========== STORE METHOD STARTED ==========');

        $request->validate([
            'time_slot'       => ['required', 'in:12:00,16:00,20:00'],
            'keterangan'      => ['nullable', 'string'],
            'file_revenue'    => ['nullable', 'file', 'mimes:xlsx,xls,csv', 'max:2048'],
            'file_nasabah'    => ['nullable', 'file', 'mimes:xlsx,xls,csv', 'max:2048'],
            'file_omzet'      => ['nullable', 'file', 'mimes:xlsx,xls,csv', 'max:2048'],
        ]);

        DB::beginTransaction();

        try {

            // ===============================
            // USER & BRANCH USER
            // ===============================

            $user = Auth::user();

            $branchUser = BranchUser::where('user_id', $user->id)->first();

            if (!$branchUser) {
                DB::rollBack();
                return back()->with('error', 'Anda belum terdaftar di cabang manapun');
            }

            // ===============================
            // CEK / BUAT REPORT HARI INI
            // ===============================

            $today = Carbon::today();

            $report = ThreeHourReportManager::where('branch_user_id', $branchUser->id)
                ->whereDate('created_at', $today)
                ->first();

            if (!$report) {

                $report = ThreeHourReportManager::create([
                    'branch_user_id' => $branchUser->id,
                    'keterangan' => $request->keterangan,
                ]);
            }

            // ===============================
            // CEK SLOT
            // ===============================

            $timeSlot = str_replace(':', '_', $request->time_slot);
            $columnName = 'report_' . explode('_', $timeSlot)[0] . '_at';

            if ($report->$columnName) {

                DB::rollBack();

                return back()->with('error', "Laporan jam {$request->time_slot} sudah diinput!");
            }

            $report->update([
                $columnName => now(),
                'keterangan' => $request->keterangan ?? $report->keterangan,
            ]);

            // ===============================
            // COUNTERS
            // ===============================

            $revenueInsertCount = 0;
            $revenueUpdateCount = 0;

            $nasabahInsertCount = 0;
            $nasabahUpdateCount = 0;

            $omzetInsertCount = 0;
            $omzetUpdateCount = 0;

            // ===============================
            // IMPORT OMZET
            // ===============================

            if ($request->hasFile('file_omzet')) {

                Log::info('STARTING OMZET IMPORT');

                $import = new OmzetImport($report->id);

                Excel::import($import, $request->file('file_omzet'));

                $omzetInsertCount = $import->getSuccessCount();
                $omzetUpdateCount = $import->getUpdateCount();

                $failures = $import->failures();

                if ($failures->isNotEmpty()) {

                    $errors = [];

                    foreach ($failures as $failure) {

                        $errors[] =
                            "Baris {$failure->row()}: " .
                            implode(', ', $failure->errors());
                    }

                    DB::rollBack();

                    return back()
                        ->with('error', implode(' | ', $errors));
                }

                Log::info('OMZET IMPORT DONE', [
                    'insert' => $omzetInsertCount,
                    'update' => $omzetUpdateCount,
                ]);
            }

            // ===============================
            // IMPORT REVENUE
            // ===============================

            if ($request->hasFile('file_revenue')) {

                Log::info('STARTING REVENUE IMPORT');

                $import = new RevenueImport($report->id);

                Excel::import($import, $request->file('file_revenue'));

                $revenueInsertCount = $import->getSuccessCount();
                $revenueUpdateCount = $import->getUpdateCount();

                $failures = $import->failures();

                if ($failures->isNotEmpty()) {

                    $errors = [];

                    foreach ($failures as $failure) {

                        $errors[] =
                            "Baris {$failure->row()}: " .
                            implode(', ', $failure->errors());
                    }

                    DB::rollBack();

                    return back()
                        ->with('error', implode(' | ', $errors));
                }

                Log::info('REVENUE IMPORT DONE', [
                    'insert' => $revenueInsertCount,
                    'update' => $revenueUpdateCount,
                ]);
            }

            // ===============================
            // IMPORT NASABAH
            // ===============================

            if ($request->hasFile('file_nasabah')) {

                Log::info('STARTING NASABAH IMPORT');

                $import = new NasabahImport($report->id);

                Excel::import($import, $request->file('file_nasabah'));

                $nasabahInsertCount = $import->getSuccessCount();
                $nasabahUpdateCount = $import->getUpdateCount();

                $failures = $import->failures();

                if ($failures->isNotEmpty()) {

                    $errors = [];

                    foreach ($failures as $failure) {

                        $errors[] =
                            "Baris {$failure->row()}: " .
                            implode(', ', $failure->errors());
                    }

                    DB::rollBack();

                    return back()
                        ->with('error', implode(' | ', $errors));
                }

                Log::info('NASABAH IMPORT DONE', [
                    'insert' => $nasabahInsertCount,
                    'update' => $nasabahUpdateCount,
                ]);
            }

            DB::commit();

            // ===============================
            // SUCCESS MESSAGE
            // ===============================

            $message = "Laporan jam {$request->time_slot} berhasil disimpan.";

            if ($revenueInsertCount)
                $message .= " Revenue baru: {$revenueInsertCount}.";

            if ($revenueUpdateCount)
                $message .= " Revenue update: {$revenueUpdateCount}.";

            if ($nasabahInsertCount)
                $message .= " Nasabah baru: {$nasabahInsertCount}.";

            if ($nasabahUpdateCount)
                $message .= " Nasabah update: {$nasabahUpdateCount}.";

            if ($omzetInsertCount)
                $message .= " Omzet baru: {$omzetInsertCount}.";

            if ($omzetUpdateCount)
                $message .= " Omzet update: {$omzetUpdateCount}.";


            return redirect()
                ->route('daily-reports.3hour-manager.index')
                ->with('success', $message);
        } catch (\Exception $e) {

            DB::rollBack();

            Log::error($e);

            return back()
                ->with('error', $e->getMessage());
        }
    }


    public function show(ThreeHourReportManager $threeHourReportManager)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ThreeHourReportManager $threeHourReportManager)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ThreeHourReportManager $threeHourReportManager)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ThreeHourReportManager $threeHourReportManager)
    {
        //
    }
}
