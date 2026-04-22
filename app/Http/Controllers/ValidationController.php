<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\DailyReportFo;
use App\Models\DailyReportFoValidation;
use App\Models\ValidationAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Exports\DailyReportFoValidationExport;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ValidationController extends Controller
{
    // =========================================================
    // MANAGER — Dashboard Validasi
    // =========================================================

    /**
     * Dashboard validasi manager
     * Tampilkan laporan dengan filter, fokus pada Metrik Bisnis
     */

    public function index(Request $request)
    {
        $user = Auth::user();

        // Tentukan cabang yang bisa diakses
        if ($user->hasRole('superadmin') || $user->hasRole('marketing')) {
            $accessibleBranches = Branch::orderBy('name')->get();
        } else {
            // Manager: hanya cabang yang dikelolanya
            $accessibleBranches = $user->managedBranches()->orderBy('name')->get();
        }

        if ($accessibleBranches->isEmpty()) {
            return back()->with('error', 'Anda tidak memiliki akses ke cabang manapun.');
        }

        // Filter dari request
        $branchIdParam          = $request->input('branch_id', 'all'); // default: semua cabang
        $tanggal                = $request->input('tanggal', now()->toDateString());
        $validationStatusFilter = $request->input('validation_status');
        $shiftFilter            = $request->input('shift');
        $perPage                = (int) $request->input('per_page', 25);

        // "Semua Cabang" sekarang tersedia untuk SEMUA role (termasuk manager)
        // tapi manager hanya melihat cabang yang dia kelola
        $isAllBranches = $branchIdParam === 'all';

        // Resolve selectedBranch (null jika "all")
        $selectedBranch = null;
        if (! $isAllBranches) {
            $selectedBranch = $accessibleBranches->find($branchIdParam);
            if (! $selectedBranch) {
                abort(403, 'Anda tidak memiliki akses ke cabang ini.');
            }
        }

        // -------------------------------------------------------
        // Base query — selalu dibatasi oleh $accessibleBranches
        // sehingga manager tidak bisa lihat cabang orang lain
        // meski manipulasi parameter
        // -------------------------------------------------------
        $baseQuery = DailyReportFo::query()
            ->whereDate('tanggal', $tanggal)
            ->whereIn('branch_id', $accessibleBranches->pluck('id')); // security gate

        if (! $isAllBranches) {
            $baseQuery->where('branch_id', $selectedBranch->id);
        }

        // TAMBAH INI UNTUK FILTER VALIDATION STATUS & SHIFT DI BASE QUERY
        if ($shiftFilter) {
            $baseQuery->where('shift', $shiftFilter);
        }


        // -------------------------------------------------------
        // Stats
        // -------------------------------------------------------
        $metrikTotals = \App\Models\DailyReportFODetail::query()
            ->whereHas('report', function ($q) use ($baseQuery) {
                // Subquery ikut constraint yang sama dengan baseQuery
                $q->whereIn('id', (clone $baseQuery)->where('validation_status', 'approved')->select('id'));
            })
            ->whereHas('field', function ($q) {
                $q->whereIn('code', ['mb_omset', 'mb_revenue', 'mb_jumlah_akad']);
            })
            ->with('field:id,code')
            ->get()
            ->groupBy(fn($d) => $d->field->code)
            ->map(fn($group) => $group->sum('value_number'));

        $stats = [
            'total'         => (clone $baseQuery)->count(),
            'pending'       => (clone $baseQuery)->where('validation_status', 'pending')->count(),
            'approved'      => (clone $baseQuery)->where('validation_status', 'approved')->count(),
            'rejected'      => (clone $baseQuery)->where('validation_status', 'rejected')->count(),
            'total_omset'   => $metrikTotals->get('mb_omset', 0),
            'total_revenue' => $metrikTotals->get('mb_revenue', 0),
            'total_akad'    => $metrikTotals->get('mb_jumlah_akad', 0),
        ];

        // -------------------------------------------------------
        // Query laporan dengan filter tambahan
        // -------------------------------------------------------
        $reportQuery = (clone $baseQuery)->with([
            'user:id,name',
            'branch:id,name,timezone',
            'validation.manager:id,name',
            'validation.actions:id,name',
            'details' => function ($q) {
                $q->whereHas('field.category', function ($q) {
                    $q->where('code', 'metrik_bisnis');
                })->with('field:id,code,name');
            },
        ]);

        if ($validationStatusFilter) {
            $reportQuery->where('validation_status', $validationStatusFilter);
        }

        if ($shiftFilter) {
            $reportQuery->where('shift', $shiftFilter);
        }

        // terdahulu
        // $reports = $reportQuery
        //     ->orderBy('shift', 'asc')
        //     ->orderBy('slot', 'asc')
        //     ->orderBy('uploaded_at', 'asc')
        //     ->paginate($perPage)
        //     ->withQueryString();

        // baru 1
        // $reports = $reportQuery
        //     ->orderBy('uploaded_at', 'desc')
        //     ->paginate($perPage)
        //     ->withQueryString();

        // terberbaru 2 — group by user_id, lalu order by latest uploaded_at per user
        // $reports = $reportQuery
        //     ->orderBy('user_id', 'asc')        // kelompokkan per FO dulu
        //     ->orderBy('uploaded_at', 'desc')   // dalam 1 FO, terbaru di atas
        //     ->paginate($perPage)
        //     ->withQueryString();

        // Ambil dulu max uploaded_at per user dari baseQuery
        $latestPerUser = (clone $baseQuery)
            ->selectRaw('user_id, MAX(uploaded_at) as latest_upload')
            ->groupBy('user_id');

        $reports = $reportQuery
            ->joinSub($latestPerUser, 'latest', function ($join) {
                $join->on('daily_report_fo.user_id', '=', 'latest.user_id');
            })
            ->orderBy('latest.latest_upload', 'desc')
            ->orderBy('daily_report_fo.user_id')
            ->orderBy('daily_report_fo.uploaded_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // return view('daily-reports-fo.validation.index', [
        //     'accessibleBranches' => $accessibleBranches,
        //     'selectedBranch'     => $selectedBranch,
        //     'isAllBranches'      => $isAllBranches,
        //     'branchIdParam'      => $branchIdParam,
        //     'tanggal'            => $tanggal,
        //     'reports'            => $reports,
        //     'stats'              => $stats,
        //     'canValidate'        => ! $user->hasRole('marketing'),
        //     'isSuperadmin'       => $user->hasRole('superadmin'),
        // ]);
        $groupedUserIds = $reports->pluck('user_id')->unique()->values();

        return view('daily-reports-fo.validation.index', [
            'accessibleBranches' => $accessibleBranches,
            'selectedBranch'     => $selectedBranch,
            'isAllBranches'      => $isAllBranches,
            'branchIdParam'      => $branchIdParam,
            'tanggal'            => $tanggal,
            'reports'            => $reports,
            'groupedUserIds'     => $groupedUserIds, // ← BARU: untuk border grouping di blade
            'stats'              => $stats,
            'canValidate'        => ! $user->hasRole('marketing'),
            'isSuperadmin'       => $user->hasRole('superadmin'),
        ]);
    }

    /**
     * Show form validasi untuk satu laporan
     */

    /**
     * Reset validasi ke pending (superadmin only)
     */
    public function reset($reportId)
    {
        $user = Auth::user();
        $report = DailyReportFo::with('validation')->findOrFail($reportId);

        if (! $user->hasRole('superadmin')) {
            abort(403, 'Hanya superadmin yang dapat mereset validasi.');
        }

        DB::transaction(function () use ($report) {
            // Hapus record validasi
            $report->validation()->delete();

            // Reset status ke pending
            $report->update([
                'validation_status' => 'pending',
            ]);
        });

        return back()->with('success', 'Validasi berhasil direset ke pending.');
    }

    // =========================================================
    // PRIVATE HELPERS
    // =========================================================

    /**
     * Cek apakah user boleh akses laporan ini
     */
    private function authorizeAccess($user, DailyReportFo $report): void
    {
        if ($user->hasRole('superadmin') || $user->hasRole('marketing')) {
            return; // Akses penuh
        }

        // Manager: hanya cabang yang dikelolanya
        $managedBranchIds = $user->managedBranches->pluck('id');

        if (! $managedBranchIds->contains($report->branch_id)) {
            abort(403, 'Anda tidak memiliki akses ke laporan ini.');
        }
    }

    /**
     * Cek apakah user bisa memvalidasi laporan ini
     */
    // private function canValidate($user, DailyReportFo $report): bool
    // {
    //     // Marketing tidak bisa validasi
    //     if ($user->hasRole('marketing')) {
    //         return false;
    //     }

    //     // Superadmin selalu bisa (untuk reset/override)
    //     if ($user->hasRole('superadmin')) {
    //         return true;
    //     }

    //     // Manager: hanya bisa jika window open dan laporan masih pending
    //     return $report->manager_window_status === 'open'
    //         && $report->validation_status === 'pending';
    // }

    // new
    // Hanya bagian yang berubah di ValidationController
    // Ganti method canValidate() dan validate() dengan ini:

    /**
     * Proses validasi (approve/reject) oleh manager
     * FIX: window cek tidak butuh timezone parameter lagi —
     * DailyReportFo::isManagerWindowOpen() sudah pakai timezone cabang laporan
     */
    public function validate(Request $request, $reportId)
    {
        $user = Auth::user();
        $report = DailyReportFo::with(['validation.actions', 'branch'])->findOrFail($reportId);
        // ↑ PENTING: eager load 'validation.actions' untuk edit mode

        $this->authorizeAccess($user, $report);

        if ($user->hasRole('marketing')) {
            abort(403, 'Marketing tidak memiliki akses untuk memvalidasi laporan.');
        }

        // Cek window — isManagerWindowOpen() sudah pakai timezone cabang laporan secara otomatis
        if (! $user->hasRole('superadmin') && ! $report->isManagerWindowOpen()) {
            $status = $report->manager_window_status;

            $message = match ($status) {
                'waiting' => 'Window validasi belum dibuka. Tunggu hingga ' . $report->manager_window_start->format('H:i') . ' ' . $report->branch->timezone . '.',
                'expired' => 'Window validasi sudah tutup sejak ' . $report->manager_window_end->format('H:i') . ' ' . $report->branch->timezone . '.',
                default => 'Window validasi tidak tersedia.',
            };

            return back()->with('error', $message);
        }

        if (! $user->hasRole('superadmin') && $report->validation_status !== 'pending') {
            return back()->with('error', 'Laporan ini sudah divalidasi sebelumnya.');
        }

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'validation_action_ids' => 'required|array|min:1', // ← UBAH: array, min 1
            'validation_action_ids.*' => 'exists:validation_actions,id',
            'catatan' => 'nullable|string|max:500',
        ], [
            'status.required' => 'Status validasi wajib dipilih.',
            'validation_action_ids.required' => 'Minimal pilih 1 tindakan.',
            'validation_action_ids.min' => 'Minimal pilih 1 tindakan.',
            'validation_action_ids.*.exists' => 'Tindakan tidak valid.',
        ]);

        DB::transaction(function () use ($validated, $report, $user) {
            // Hapus validasi lama (cascade delete pivot juga)
            $report->validation()->delete();

            // Buat validasi baru
            $validation = DailyReportFoValidation::create([
                'daily_report_fo_id' => $report->id,
                'manager_id' => $user->id,
                'status' => $validated['status'],
                'catatan' => $validated['catatan'] ?? null,
                'validated_at' => now(),
            ]);

            // Attach multiple actions ke pivot table
            $validation->actions()->attach($validated['validation_action_ids']);

            // Update status di report
            $report->update(['validation_status' => $validated['status']]);
        });

        $statusLabel = $validated['status'] === 'approved' ? 'disetujui' : 'ditolak';

        return redirect()
            ->route('validation.index', ['branch_id' => $report->branch_id])
            ->with('success', "Laporan berhasil {$statusLabel}.");
    }

    /**
     * Cek apakah user bisa memvalidasi laporan ini
     * FIX: tidak perlu timezone parameter — model sudah handle
     */
    private function canValidate($user, DailyReportFo $report): bool
    {
        if ($user->hasRole('marketing')) {
            return false;
        }

        if ($user->hasRole('superadmin')) {
            return true;
        }

        // Manager: window harus open (dalam timezone cabang laporan) dan status pending
        return $report->isManagerWindowOpen()
            && $report->validation_status === 'pending';
    }

    /**
     * Show form validasi — pastikan branch di-eager load
     */
    public function show($reportId)
    {
        $user = Auth::user();
        $report = DailyReportFo::with([
            'user',
            'branch',          // ← WAJIB untuk timezone calculation
            'validation.manager',
            'validation.actions',
            'details' => function ($q) {
                $q->with(['field.category', 'photos']);
            },
        ])->findOrFail($reportId);

        $this->authorizeAccess($user, $report);

        $canValidate = $this->canValidate($user, $report);
        $validationActions = ValidationAction::active()->ordered()->get();

        $detailsByCategory = $report->details->groupBy(function ($detail) {
            return $detail->field->category->name ?? 'Lainnya';
        });

        $metrikDetails = $report->details->filter(function ($detail) {
            return $detail->field->category->code === 'metrik_bisnis';
        })->keyBy(fn($d) => $d->field->code);

        return view('daily-reports-fo.validation.show', [
            'report' => $report,
            'detailsByCategory' => $detailsByCategory,
            'metrikDetails' => $metrikDetails,
            'validationActions' => $validationActions,
            'canValidate' => $canValidate,
            'isSuperadmin' => $user->hasRole('superadmin'),
            'managerWindowStatus' => $report->manager_window_status,
            'branchTimezone' => $report->branch->timezone, // untuk ditampilkan di view
        ]);
    }

    /**
     * Export laporan validasi ke Excel
     * Input dari modal: date_from, date_to, branch_id
     * Batasan hari per role:
     * - Manager    → max 7 hari
     * - Superadmin → max 3 hari
     * - Marketing  → max 3 hari
     */
    public function export(Request $request)
    {
        $user = Auth::user();

        // -------------------------------------------------------
        // Tentukan accessible branches (sama dengan index())
        // -------------------------------------------------------
        if ($user->hasRole('superadmin') || $user->hasRole('marketing')) {
            $accessibleBranches = Branch::orderBy('name')->get();
        } else {
            $accessibleBranches = $user->managedBranches()->orderBy('name')->get();
        }

        if ($accessibleBranches->isEmpty()) {
            return back()->with('error', 'Anda tidak memiliki akses ke cabang manapun.');
        }

        // -------------------------------------------------------
        // Validasi input
        // -------------------------------------------------------
        $request->validate([
            'date_from' => 'required|date',
            'date_to'   => 'required|date|after_or_equal:date_from',
            'branch_id' => 'required', // 'all' atau integer ID
        ], [
            'date_from.required'          => 'Tanggal Dari wajib diisi.',
            'date_from.date'              => 'Tanggal Dari tidak valid.',
            'date_to.required'            => 'Tanggal Sampai wajib diisi.',
            'date_to.date'                => 'Tanggal Sampai tidak valid.',
            'date_to.after_or_equal'      => 'Tanggal Sampai tidak boleh sebelum Tanggal Dari.',
            'branch_id.required'          => 'Cabang wajib dipilih.',
        ]);

        $dateFrom = Carbon::parse($request->input('date_from'))->startOfDay();
        $dateTo   = Carbon::parse($request->input('date_to'))->endOfDay();

        // -------------------------------------------------------
        // Validasi batasan hari per role
        // -------------------------------------------------------
        $maxDays     = $user->hasRole('manager') ? 7 : 3;
        // $diffDays    = $dateFrom->diffInDays($dateTo) + 1;
        // Hitung diff dari tanggal saja, bukan datetime
        $diffDays  = Carbon::parse($request->input('date_from'))
            ->diffInDays(Carbon::parse($request->input('date_to'))) + 1;

        if ($diffDays > $maxDays) {
            return back()->with(
                'error',
                "Maksimal export {$maxDays} hari untuk role Anda. "
                    . "Range yang dipilih {$diffDays} hari."
            );
        }

        // -------------------------------------------------------
        // Validasi branch_id — security gate
        // -------------------------------------------------------
        $branchId = null; // null = semua cabang

        if ($request->input('branch_id') !== 'all') {
            $branchId       = (int) $request->input('branch_id');
            $selectedBranch = $accessibleBranches->find($branchId);

            if (! $selectedBranch) {
                abort(403, 'Anda tidak memiliki akses ke cabang ini.');
            }
        }

        // -------------------------------------------------------
        // Buat nama file
        // -------------------------------------------------------
        $branchLabel = $branchId
            ? $accessibleBranches->find($branchId)->name
            : 'SemuaCabang';

        // Bersihkan spasi di nama cabang
        $branchLabel = str_replace(' ', '_', $branchLabel);

        $filename = "Validasi_LaporanFO_{$branchLabel}_"
            . $dateFrom->format('dmY')
            . '_sd_'
            . $dateTo->format('dmY')
            . '.xlsx';

        // -------------------------------------------------------
        // Export
        // -------------------------------------------------------
        return Excel::download(
            new DailyReportFoValidationExport(
                dateFrom: $dateFrom->toDateString(),
                dateTo: $dateTo->toDateString(),
                branchId: $branchId,
                accessibleBranchIds: $accessibleBranches->pluck('id')->toArray(),
                exportedByName: $user->name,
            ),
            $filename
        );
    }
}
