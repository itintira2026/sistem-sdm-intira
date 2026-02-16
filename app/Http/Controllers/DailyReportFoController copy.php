<?php

namespace App\Http\Controllers;

use App\Exports\DailyReportFOExport;
use App\Helpers\ImageHelper;
use App\Helpers\ShiftHelper;
use App\Helpers\TimeHelper;
use App\Models\Branch;
use App\Models\DailyReportFO;
use App\Models\DailyReportFOPhoto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class DailyReportFoController extends Controller
{
    // ==============================
    // FO SECTION
    // ==============================

    /**
     * Dashboard FO - Tampilkan 4 slot dengan status real-time
     */
    public function index()
    {
        $user = Auth::user();

        // Get user's branch
        $userBranches = $user->branches;

        if ($userBranches->isEmpty()) {
            return back()->with('error', 'Anda belum terdaftar di cabang manapun.');
        }

        // Ambil cabang pertama user (asumsi 1 FO = 1 cabang utama)
        $branch = $userBranches->first();
        $branchTime = TimeHelper::getBranchTime($branch->id);
        $today = $branchTime->toDateString();

        // Determine shift (dari session atau dari laporan yang sudah ada)
        $selectedShift = ShiftHelper::determineShift($user->id);

        // Jika belum pilih shift, redirect ke modal pilih shift
        if (! $selectedShift) {
            return view('daily-reports-fo.index', [
                'branch' => $branch,
                'needShiftSelection' => true,
                'branchTime' => $branchTime,
            ]);
        }

        // Get all slots untuk shift yang dipilih
        $slots = TimeHelper::getShiftSlots($selectedShift);

        // Get laporan yang sudah ada hari ini
        $existingReports = DailyReportFO::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->where('shift', $selectedShift)
            ->with('photos')
            ->get()
            ->keyBy('slot'); // Key by slot number untuk easy access

        // Build slot data dengan status
        $slotData = [];
        foreach ($slots as $slot) {
            $slotNumber = $slot['slot_number'];
            $slotTime = $slot['slot_time'];

            $status = TimeHelper::getSlotStatus($slotTime, $branch->id, $today);
            $existingReport = $existingReports->get($slotNumber);

            $slotData[] = [
                'slot_number' => $slotNumber,
                'slot_time' => $slotTime,
                'slot_config' => $slot,
                'status' => $status, // 'waiting', 'open', 'closed'
                'window' => TimeHelper::getSlotWindowRange($slotTime, $branch->id, $today),
                'existing_report' => $existingReport,
                'has_report' => $existingReport !== null,
                'can_upload' => $status === 'open' && ! $existingReport,
                'can_edit' => $status === 'open' && $existingReport,
                'time_until_open' => $status === 'waiting' ? TimeHelper::getTimeUntilSlotOpens($slotTime, $branch->id, $today) : null,
                'time_remaining' => $status === 'open' ? TimeHelper::getRemainingTimeInSlot($slotTime, $branch->id, $today) : null,
            ];
        }

        // Stats hari ini
        $stats = [
            'total_slots' => 4,
            'completed_slots' => $existingReports->count(),
            'progress_percentage' => ($existingReports->count() / 4) * 100,
        ];

        return view('daily-reports-fo.index', [
            'branch' => $branch,
            'branchTime' => $branchTime,
            'selectedShift' => $selectedShift,
            'needShiftSelection' => false,
            'slotData' => $slotData,
            'stats' => $stats,
            'today' => $today,
            'serverTimestamp' => $branchTime->timestamp,
            'branchTimezone' => $branch->timezone,
        ]);
    }

    /**
     * Select Shift (POST)
     */
    public function selectShift(Request $request)
    {
        $user = Auth::user();

        // Validation
        $validated = $request->validate([
            'shift' => 'required|in:pagi,siang',
        ]);

        // Check if user can still change shift (belum ada laporan hari ini)
        if (! ShiftHelper::canChangeShiftToday($user->id)) {
            return back()->with('error', 'Shift sudah terkunci karena Anda sudah membuat laporan hari ini.');
        }

        // Set shift
        ShiftHelper::setTodayShift($validated['shift']);

        return redirect()->route('daily-reports-fo.index')
            ->with('success', 'Shift '.config('daily_report_fo.shifts')[$validated['shift']]['label'].' berhasil dipilih!');
    }

    /**
     * Show Slot Form (untuk upload atau edit)
     */
    public function showSlot($slotNumber)
    {
        $user = Auth::user();
        $branch = $user->branches->first();

        if (! $branch) {
            return back()->with('error', 'Anda belum terdaftar di cabang manapun.');
        }

        $branchTime = TimeHelper::getBranchTime($branch->id);
        $today = $branchTime->toDateString();

        // Get shift
        $selectedShift = ShiftHelper::determineShift($user->id);

        if (! $selectedShift) {
            return redirect()->route('daily-reports-fo.index')
                ->with('error', 'Silakan pilih shift terlebih dahulu.');
        }

        // Get slot config
        $slotConfig = TimeHelper::getSlotConfig($selectedShift, $slotNumber);

        if (! $slotConfig) {
            return back()->with('error', 'Slot tidak valid.');
        }

        $slotTime = $slotConfig['slot_time'];

        // Check if slot is open
        $status = TimeHelper::getSlotStatus($slotTime, $branch->id, $today);

        if ($status !== 'open') {
            return back()->with('error', 'Slot ini belum dibuka atau sudah ditutup.');
        }

        // Check if already has report
        $existingReport = DailyReportFO::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->where('shift', $selectedShift)
            ->where('slot', $slotNumber)
            ->with('photos')
            ->first();

        $window = TimeHelper::getSlotWindowRange($slotTime, $branch->id, $today);
        $categories = config('daily_report_fo.photo_categories');

        return view('daily-reports-fo.slot-form', [
            'branch' => $branch,
            'branchTime' => $branchTime,
            'selectedShift' => $selectedShift,
            'slotNumber' => $slotNumber,
            'slotConfig' => $slotConfig,
            'slotTime' => $slotTime,
            'window' => $window,
            'existingReport' => $existingReport,
            'isEdit' => $existingReport !== null,
            'categories' => $categories,
        ]);
    }

    /**
     * Store Slot Report (POST)
     */
    public function storeSlot(Request $request, $slotNumber)
    {
        $user = Auth::user();
        $branch = $user->branches->first();

        if (! $branch) {
            return back()->with('error', 'Anda belum terdaftar di cabang manapun.');
        }

        $branchTime = TimeHelper::getBranchTime($branch->id);
        $today = $branchTime->toDateString();

        // Get shift
        $selectedShift = ShiftHelper::determineShift($user->id);

        if (! $selectedShift) {
            return redirect()->route('daily-reports-fo.index')
                ->with('error', 'Silakan pilih shift terlebih dahulu.');
        }

        // Get slot config
        $slotConfig = TimeHelper::getSlotConfig($selectedShift, $slotNumber);

        if (! $slotConfig) {
            return back()->with('error', 'Slot tidak valid.');
        }

        $slotTime = $slotConfig['slot_time'];

        // Check if slot is open
        if (! TimeHelper::isSlotOpen($slotTime, $branch->id, $today)) {
            return back()->with('error', 'Window upload untuk slot ini sudah ditutup.');
        }

        // Check if already exists
        $existing = DailyReportFO::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->where('shift', $selectedShift)
            ->where('slot', $slotNumber)
            ->first();

        if ($existing) {
            return back()->with('error', 'Anda sudah melaporkan slot ini.');
        }

        // Build validation rules untuk photos
        $categories = array_keys(config('daily_report_fo.photo_categories'));
        $photoRules = [];

        foreach ($categories as $category) {
            $photoRules["photos_{$category}"] = 'required|array|min:1';
            $photoRules["photos_{$category}.*"] = 'required|image|mimes:jpg,jpeg,png|max:5120';
        }

        // Validation
        $validated = $request->validate(array_merge([
            'keterangan' => 'nullable|string|max:1000',
        ], $photoRules), [
            'photos_*.required' => 'Setiap kategori wajib memiliki minimal 1 foto.',
            'photos_*.min' => 'Setiap kategori wajib memiliki minimal 1 foto.',
        ]);

        // Create report
        $report = DailyReportFO::create([
            'user_id' => $user->id,
            'branch_id' => $branch->id,
            'tanggal' => $today,
            'shift' => $selectedShift,
            'slot' => $slotNumber,
            'slot_time' => $slotTime,
            'uploaded_at' => now(),
            'keterangan' => $validated['keterangan'],
        ]);

        // Upload photos per kategori
        foreach ($categories as $category) {
            $fieldName = "photos_{$category}";

            if ($request->hasFile($fieldName)) {
                foreach ($request->file($fieldName) as $photo) {
                    // Compress & save using ImageHelper
                    $filePath = ImageHelper::compressAndSave(
                        $photo,
                        "daily_reports_fo/{$report->id}/{$category}",
                        config('daily_report_fo.image_compression.max_width', 1920),
                        config('daily_report_fo.image_compression.max_height', 1080),
                        config('daily_report_fo.image_compression.quality', 80)
                    );

                    DailyReportFOPhoto::create([
                        'daily_report_fo_id' => $report->id,
                        'kategori' => $category,
                        'file_path' => $filePath,
                        'file_name' => $photo->getClientOriginalName(),
                    ]);
                }
            }
        }

        return redirect()->route('daily-reports-fo.index')
            ->with('success', "Laporan Slot {$slotNumber} berhasil diupload!");
    }

    /**
     * Edit Slot (GET)
     */
    public function editSlot($slotNumber)
    {
        // Reuse showSlot method (already handles edit mode)
        return $this->showSlot($slotNumber);
    }

    /**
     * Update Slot (PUT)
     */
    public function updateSlot(Request $request, $slotNumber)
    {
        $user = Auth::user();
        $branch = $user->branches->first();

        if (! $branch) {
            return back()->with('error', 'Anda belum terdaftar di cabang manapun.');
        }

        $branchTime = TimeHelper::getBranchTime($branch->id);
        $today = $branchTime->toDateString();

        // Get shift
        $selectedShift = ShiftHelper::determineShift($user->id);

        if (! $selectedShift) {
            return redirect()->route('daily-reports-fo.index')
                ->with('error', 'Silakan pilih shift terlebih dahulu.');
        }

        // Get slot config
        $slotConfig = TimeHelper::getSlotConfig($selectedShift, $slotNumber);

        if (! $slotConfig) {
            return back()->with('error', 'Slot tidak valid.');
        }

        $slotTime = $slotConfig['slot_time'];

        // Check if slot is still open
        if (! TimeHelper::isSlotOpen($slotTime, $branch->id, $today)) {
            return back()->with('error', 'Window upload untuk slot ini sudah ditutup. Tidak bisa edit.');
        }

        // Get existing report
        $report = DailyReportFO::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->where('shift', $selectedShift)
            ->where('slot', $slotNumber)
            ->firstOrFail();

        // Build validation rules
        $categories = array_keys(config('daily_report_fo.photo_categories'));
        $photoRules = [];

        foreach ($categories as $category) {
            // Optional saat edit (bisa tidak upload foto baru)
            $photoRules["photos_{$category}"] = 'nullable|array';
            $photoRules["photos_{$category}.*"] = 'nullable|image|mimes:jpg,jpeg,png|max:5120';

            // Delete photos rules
            $photoRules["delete_photos_{$category}"] = 'nullable|array';
            $photoRules["delete_photos_{$category}.*"] = 'exists:daily_report_fo_photos,id';
        }

        // Validation
        $validated = $request->validate(array_merge([
            'keterangan' => 'nullable|string|max:1000',
        ], $photoRules));

        // Update keterangan
        $report->update([
            'keterangan' => $validated['keterangan'],
        ]);

        // Process each category
        foreach ($categories as $category) {
            $deleteFieldName = "delete_photos_{$category}";
            $uploadFieldName = "photos_{$category}";

            // Delete selected photos
            if ($request->has($deleteFieldName)) {
                $photosToDelete = DailyReportFOPhoto::whereIn('id', $request->input($deleteFieldName))
                    ->where('daily_report_fo_id', $report->id)
                    ->where('kategori', $category)
                    ->get();

                foreach ($photosToDelete as $photo) {
                    ImageHelper::delete($photo->file_path);
                    $photo->delete();
                }
            }

            // Upload new photos
            if ($request->hasFile($uploadFieldName)) {
                foreach ($request->file($uploadFieldName) as $photo) {
                    $filePath = ImageHelper::compressAndSave(
                        $photo,
                        "daily_reports_fo/{$report->id}/{$category}",
                        config('daily_report_fo.image_compression.max_width', 1920),
                        config('daily_report_fo.image_compression.max_height', 1080),
                        config('daily_report_fo.image_compression.quality', 80)
                    );

                    DailyReportFOPhoto::create([
                        'daily_report_fo_id' => $report->id,
                        'kategori' => $category,
                        'file_path' => $filePath,
                        'file_name' => $photo->getClientOriginalName(),
                    ]);
                }
            }
        }

        // Validate: Each category must have at least 1 photo
        $report->refresh();
        foreach ($categories as $category) {
            if ($report->getPhotoCategoryCount($category) < 1) {
                return back()
                    ->withErrors(["photos_{$category}" => "Kategori {$category} harus memiliki minimal 1 foto."])
                    ->withInput();
            }
        }

        return redirect()->route('daily-reports-fo.index')
            ->with('success', "Laporan Slot {$slotNumber} berhasil diperbarui!");
    }

    /**
     * History FO (30 hari terakhir)
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        $branch = $user->branches->first();

        if (! $branch) {
            return back()->with('error', 'Anda belum terdaftar di cabang manapun.');
        }

        $perPage = (int) $request->input('per_page', 10);
        $shiftFilter = $request->input('shift');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $branchTime = TimeHelper::getBranchTime($branch->id);
        $historyDays = config('daily_report_fo.history_days', 30);

        // Query
        $query = DailyReportFO::where('user_id', $user->id)
            ->where('branch_id', $branch->id)
            ->where('tanggal', '>=', $branchTime->copy()->subDays($historyDays)->toDateString())
            ->with('photos');

        // Filter shift
        if ($shiftFilter) {
            $query->where('shift', $shiftFilter);
        }

        // Filter date range
        if ($dateFrom) {
            $query->whereDate('tanggal', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('tanggal', '<=', $dateTo);
        }

        $reports = $query->orderBy('tanggal', 'desc')
            ->orderBy('shift', 'asc')
            ->orderBy('slot', 'asc')
            ->paginate($perPage)
            ->withQueryString();

        // Stats
        $stats = [
            'total_reports' => $query->count(),
            'total_days' => $query->select('tanggal')->distinct()->count(),
        ];

        return view('daily-reports-fo.history', [
            'branch' => $branch,
            'branchTime' => $branchTime,
            'reports' => $reports,
            'stats' => $stats,
        ]);
    }

    // ==============================
    // MANAGER SECTION
    // ==============================

    /**
     * Manager Dashboard - Overview semua FO di cabang
     */
    public function managerDashboard(Request $request)
    {
        $user = Auth::user();
        $tanggal = $request->input('tanggal', now()->toDateString());

        // Get managed branches
        if ($user->hasRole('superadmin')) {
            $managedBranches = Branch::orderBy('name')->get();
        } else {
            $managedBranches = $user->managedBranches;
        }

        if ($managedBranches->isEmpty()) {
            return back()->with('error', 'Anda tidak mengelola cabang manapun.');
        }

        // Selected branch (default to first)
        $selectedBranchId = $request->input('branch_id', $managedBranches->first()->id);
        $selectedBranch = $managedBranches->find($selectedBranchId);

        if (! $selectedBranch) {
            abort(403, 'Anda tidak memiliki akses ke cabang ini.');
        }

        // Get all FO in this branch
        $foUsers = $selectedBranch->users()
            ->whereHas('roles', function ($q) {
                $q->where('name', 'fo');
            })
            ->with(['branches'])
            ->get();

        // Get all reports for today
        $todayReports = DailyReportFO::where('branch_id', $selectedBranchId)
            ->whereDate('tanggal', $tanggal)
            ->with('photos')
            ->get()
            ->groupBy('user_id');

        // Build FO progress data
        $foProgress = [];
        foreach ($foUsers as $fo) {
            $userReports = $todayReports->get($fo->id, collect());
            $shift = $userReports->first()->shift ?? null;

            $slots = [1, 2, 3, 4];
            $slotStatus = [];

            foreach ($slots as $slotNumber) {
                $report = $userReports->firstWhere('slot', $slotNumber);
                $slotStatus[$slotNumber] = [
                    'has_report' => $report !== null,
                    'report' => $report,
                    'photo_count' => $report ? $report->photos->count() : 0,
                ];
            }

            $completedSlots = $userReports->count();
            $progressPercentage = ($completedSlots / 4) * 100;

            $foProgress[] = [
                'user' => $fo,
                'shift' => $shift,
                'shift_label' => $shift ? config('daily_report_fo.shifts')[$shift]['label'] : 'Belum Pilih',
                'total_reports' => $completedSlots,
                'progress_percentage' => $progressPercentage,
                'slot_status' => $slotStatus,
            ];
        }

        // Overall stats
        $stats = [
            'total_fo' => $foUsers->count(),
            'total_reports_today' => DailyReportFO::where('branch_id', $selectedBranchId)
                ->whereDate('tanggal', $tanggal)
                ->count(),
            'target_reports' => $foUsers->count() * 4,
            'fo_complete' => collect($foProgress)->where('total_reports', 4)->count(),
            'fo_in_progress' => collect($foProgress)->whereBetween('total_reports', [1, 3])->count(),
            'fo_not_started' => collect($foProgress)->where('total_reports', 0)->count(),
        ];

        return view('daily-reports-fo.manager.dashboard', [
            'managedBranches' => $managedBranches,
            'selectedBranch' => $selectedBranch,
            'tanggal' => $tanggal,
            'foProgress' => $foProgress,
            'stats' => $stats,
        ]);
    }

    /**
     * Manager - List FO
     */
    public function managerFOList(Request $request)
    {
        $user = Auth::user();

        // Get managed branches
        if ($user->hasRole('superadmin')) {
            $managedBranches = Branch::orderBy('name')->get();
        } else {
            $managedBranches = $user->managedBranches;
        }

        $selectedBranchId = $request->input('branch_id', $managedBranches->first()->id);
        $selectedBranch = $managedBranches->find($selectedBranchId);

        if (! $selectedBranch) {
            abort(403, 'Anda tidak memiliki akses ke cabang ini.');
        }

        // Get FO users
        $foUsers = $selectedBranch->users()
            ->whereHas('roles', function ($q) {
                $q->where('name', 'fo');
            })
            ->with(['branches'])
            ->paginate(20);

        return view('daily-reports-fo.manager.fo-list', [
            'managedBranches' => $managedBranches,
            'selectedBranch' => $selectedBranch,
            'foUsers' => $foUsers,
        ]);
    }

    /**
     * Manager - FO Detail (semua laporan 1 FO)
     */
    public function managerFODetail(Request $request, $userId)
    {
        // dd($userId, $request);
        $user = Auth::user();
        $dateFrom = $request->input('date_from', now()->subDays(7)->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());

        // Get FO user
        $foUser = User::findOrFail($userId);

        // Check permission
        if (! $user->hasRole('superadmin')) {
            $managedBranchIds = $user->managedBranches->pluck('id');
            $foUserBranchIds = $foUser->branches->pluck('id');

            if ($managedBranchIds->intersect($foUserBranchIds)->isEmpty()) {
                abort(403, 'Anda tidak memiliki akses ke FO ini.');
            }
        }

        // Get reports
        $reports = DailyReportFO::where('user_id', $userId)
            ->whereBetween('tanggal', [$dateFrom, $dateTo])
            ->with(['branch', 'photos'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('shift', 'asc')
            ->orderBy('slot', 'asc')
            ->paginate(20)
            ->withQueryString();

        // Stats
        $stats = [
            'total_reports' => DailyReportFO::where('user_id', $userId)
                ->whereBetween('tanggal', [$dateFrom, $dateTo])
                ->count(),
            'total_photos' => DailyReportFOPhoto::whereIn('daily_report_fo_id', function ($query) use ($userId, $dateFrom, $dateTo) {
                $query->select('id')
                    ->from('daily_report_fo')
                    ->where('user_id', $userId)
                    ->whereBetween('tanggal', [$dateFrom, $dateTo]);
            })->count(),
            'total_days' => DailyReportFO::where('user_id', $userId)
                ->whereBetween('tanggal', [$dateFrom, $dateTo])
                ->select('tanggal')
                ->distinct()
                ->count(),
        ];

        return view('daily-reports-fo.manager.fo-detail', [
            'foUser' => $foUser,
            'reports' => $reports,
            'stats' => $stats,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    /**
     * Manager - Reports List (All reports dari semua FO)
     */
    public function managerReports(Request $request)
    {
        $user = Auth::user();

        // Get managed branches
        if ($user->hasRole('superadmin')) {
            $managedBranches = Branch::orderBy('name')->get();
        } else {
            $managedBranches = $user->managedBranches;
        }

        $selectedBranchId = $request->input('branch_id', $managedBranches->first()->id);
        $selectedBranch = $managedBranches->find($selectedBranchId);

        if (! $selectedBranch) {
            abort(403, 'Anda tidak memiliki akses ke cabang ini.');
        }

        $tanggal = $request->input('tanggal', now()->toDateString());
        $shiftFilter = $request->input('shift');
        $perPage = (int) $request->input('per_page', 25);

        // Query
        $query = DailyReportFO::where('branch_id', $selectedBranchId)
            ->whereDate('tanggal', $tanggal)
            ->with(['user', 'branch', 'photos']);

        if ($shiftFilter) {
            $query->where('shift', $shiftFilter);
        }

        $reports = $query->orderBy('shift', 'asc')
            ->orderBy('slot', 'asc')
            ->orderBy('uploaded_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // Stats
        $stats = [
            'total_reports' => DailyReportFO::where('branch_id', $selectedBranchId)
                ->whereDate('tanggal', $tanggal)
                ->count(),
            'total_photos' => DailyReportFOPhoto::whereIn('daily_report_fo_id', function ($q) use ($selectedBranchId, $tanggal) {
                $q->select('id')
                    ->from('daily_report_fo')
                    ->where('branch_id', $selectedBranchId)
                    ->whereDate('tanggal', $tanggal);
            })->count(),
        ];

        return view('daily-reports-fo.manager.reports', [
            'managedBranches' => $managedBranches,
            'selectedBranch' => $selectedBranch,
            'reports' => $reports,
            'stats' => $stats,
            'tanggal' => $tanggal,
        ]);
    }

    /**
     * Manager - Report Detail
     */
    public function managerReportDetail($reportId)
    {
        $user = Auth::user();
        $report = DailyReportFO::with(['user', 'branch', 'photos'])->findOrFail($reportId);

        // Check permission
        if (! $user->hasRole('superadmin')) {
            $managedBranchIds = $user->managedBranches->pluck('id');

            if (! $managedBranchIds->contains($report->branch_id)) {
                abort(403, 'Anda tidak memiliki akses ke laporan ini.');
            }
        }

        // Group photos by category
        $photosByCategory = $report->photos->groupBy('kategori');

        return view('daily-reports-fo.manager.report-detail', [
            'report' => $report,
            'photosByCategory' => $photosByCategory,
        ]);
    }

    /**
     * Manager - Export Excel
     */
    // public function managerExport(Request $request)
    // {
    //     // TODO: Implement Excel export using Laravel Excel
    //     return back()->with('info', 'Fitur export sedang dalam pengembangan.');
    // }

    // ==============================
    // MARKETING SECTION
    // ==============================

    /**
     * Marketing Dashboard - Analytics semua cabang
     */
    public function marketingDashboard(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->subDays(7)->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());

        // Get all branches
        $branches = Branch::orderBy('name')->get();

        // Overall stats
        $stats = [
            'total_reports' => DailyReportFO::whereBetween('tanggal', [$dateFrom, $dateTo])->count(),
            'total_photos' => DailyReportFOPhoto::whereIn('daily_report_fo_id', function ($q) use ($dateFrom, $dateTo) {
                $q->select('id')
                    ->from('daily_report_fo')
                    ->whereBetween('tanggal', [$dateFrom, $dateTo]);
            })->count(),
            'total_fo' => User::whereHas('roles', function ($q) {
                $q->where('name', 'fo');
            })->count(),
            'total_branches' => $branches->count(),
        ];

        // Photos by category
        $photosByCategory = DailyReportFOPhoto::whereIn('daily_report_fo_id', function ($q) use ($dateFrom, $dateTo) {
            $q->select('id')
                ->from('daily_report_fo')
                ->whereBetween('tanggal', [$dateFrom, $dateTo]);
        })
            ->selectRaw('kategori, COUNT(*) as total')
            ->groupBy('kategori')
            ->get()
            ->pluck('total', 'kategori')
            ->toArray();

        // Reports per day (for chart)
        $reportsPerDay = DailyReportFO::whereBetween('tanggal', [$dateFrom, $dateTo])
            ->selectRaw('DATE(tanggal) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->pluck('total', 'date')
            ->toArray();

        // Top performing FO
        $topFO = DailyReportFO::whereBetween('tanggal', [$dateFrom, $dateTo])
            ->selectRaw('user_id, COUNT(*) as total_reports')
            ->groupBy('user_id')
            ->orderBy('total_reports', 'desc')
            ->limit(10)
            ->with('user')
            ->get();

        return view('daily-reports-fo.marketing.dashboard', [
            'stats' => $stats,
            'photosByCategory' => $photosByCategory,
            'reportsPerDay' => $reportsPerDay,
            'topFO' => $topFO,
            'branches' => $branches,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    /**
     * Marketing - Analytics Detail
     */
    public function marketingAnalytics(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->subDays(30)->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());
        $branchId = $request->input('branch_id');

        // Base query
        $query = DailyReportFO::whereBetween('tanggal', [$dateFrom, $dateTo]);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        // Photos by category (detailed)
        $categoryStats = [];
        $categories = config('daily_report_fo.photo_categories');

        foreach ($categories as $key => $label) {
            $total = DailyReportFOPhoto::where('kategori', $key)
                ->whereIn('daily_report_fo_id', function ($q) use ($dateFrom, $dateTo, $branchId) {
                    $q->select('id')
                        ->from('daily_report_fo')
                        ->whereBetween('tanggal', [$dateFrom, $dateTo]);

                    if ($branchId) {
                        $q->where('branch_id', $branchId);
                    }
                })
                ->count();

            $categoryStats[$key] = [
                'label' => $label,
                'total' => $total,
            ];
        }

        // Reports by shift
        $reportsByShift = $query->selectRaw('shift, COUNT(*) as total')
            ->groupBy('shift')
            ->get()
            ->pluck('total', 'shift')
            ->toArray();

        // Reports by branch
        $reportsByBranch = DailyReportFO::whereBetween('tanggal', [$dateFrom, $dateTo])
            ->selectRaw('branch_id, COUNT(*) as total')
            ->groupBy('branch_id')
            ->with('branch')
            ->get();

        $branches = Branch::orderBy('name')->get();

        return view('daily-reports-fo.marketing.analytics', [
            'categoryStats' => $categoryStats,
            'reportsByShift' => $reportsByShift,
            'reportsByBranch' => $reportsByBranch,
            'branches' => $branches,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'selectedBranchId' => $branchId,
        ]);
    }

    /**
     * Marketing - All Reports
     */
    public function marketingReports(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->subDays(7)->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());
        $branchId = $request->input('branch_id');
        $shiftFilter = $request->input('shift');
        $perPage = (int) $request->input('per_page', 25);

        // Query
        $query = DailyReportFO::whereBetween('tanggal', [$dateFrom, $dateTo])
            ->with(['user', 'branch', 'photos']);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($shiftFilter) {
            $query->where('shift', $shiftFilter);
        }

        $reports = $query->orderBy('tanggal', 'desc')
            ->orderBy('shift', 'asc')
            ->orderBy('slot', 'asc')
            ->paginate($perPage)
            ->withQueryString();

        $branches = Branch::orderBy('name')->get();

        return view('daily-reports-fo.marketing.reports', [
            'reports' => $reports,
            'branches' => $branches,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    /**
     * Marketing - Report Detail
     */
    public function marketingReportDetail($reportId)
    {
        $report = DailyReportFO::with(['user', 'branch', 'photos'])->findOrFail($reportId);

        // Group photos by category
        $photosByCategory = $report->photos->groupBy('kategori');

        return view('daily-reports-fo.marketing.report-detail', [
            'report' => $report,
            'photosByCategory' => $photosByCategory,
        ]);
    }

    /**
     * Marketing - Export
     */
    // public function marketingExport(Request $request)
    // {
    //     // TODO: Implement Excel/PDF export
    //     return back()->with('info', 'Fitur export sedang dalam pengembangan.');
    // }

    /**
     * Manager - Export Excel
     */
    public function managerExport(Request $request)
    {
        $user = Auth::user();

        // Get managed branches
        if ($user->hasRole('superadmin')) {
            $managedBranches = Branch::orderBy('name')->get();
        } else {
            $managedBranches = $user->managedBranches;
        }

        $selectedBranchId = $request->input('branch_id', $managedBranches->first()->id);
        $selectedBranch = $managedBranches->find($selectedBranchId);

        if (! $selectedBranch) {
            abort(403, 'Anda tidak memiliki akses ke cabang ini.');
        }

        $dateFrom = $request->input('date_from', now()->subDays(7)->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());
        $shiftFilter = $request->input('shift');

        // Build query
        $query = DailyReportFO::where('branch_id', $selectedBranchId)
            ->whereBetween('tanggal', [$dateFrom, $dateTo]);

        if ($shiftFilter) {
            $query->where('shift', $shiftFilter);
        }

        $query->orderBy('tanggal', 'desc')
            ->orderBy('shift', 'asc')
            ->orderBy('slot', 'asc');

        // Generate filename
        $filename = 'Daily_Report_FO_'.$selectedBranch->name.'_'.$dateFrom.'_to_'.$dateTo.'.xlsx';
        $title = 'Report '.$selectedBranch->name;

        return Excel::download(new DailyReportFOExport($query, $title), $filename);
    }

    /**
     * Marketing - Export Excel
     */
    public function marketingExport(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->subDays(7)->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());
        $branchId = $request->input('branch_id');
        $shiftFilter = $request->input('shift');

        // Build query
        $query = DailyReportFO::whereBetween('tanggal', [$dateFrom, $dateTo]);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($shiftFilter) {
            $query->where('shift', $shiftFilter);
        }

        $query->orderBy('tanggal', 'desc')
            ->orderBy('branch_id', 'asc')
            ->orderBy('shift', 'asc')
            ->orderBy('slot', 'asc');

        // Generate filename
        $branchName = $branchId ? Branch::find($branchId)->name : 'All_Branches';
        $filename = 'Daily_Report_FO_'.$branchName.'_'.$dateFrom.'_to_'.$dateTo.'.xlsx';
        $title = 'Report '.$branchName;

        return Excel::download(new DailyReportFOExport($query, $title), $filename);
    }
}
