<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\DailyReportFo;
use App\Models\DailyReportFoValidation;
use App\Models\ValidationAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ValidationController extends Controller
{
    // =========================================================
    // MANAGER — Dashboard Validasi
    // =========================================================

    /**
     * Dashboard validasi manager
     * Tampilkan laporan dengan filter, fokus pada Metrik Bisnis
     */
    // public function index(Request $request)
    // {
    //     $user = Auth::user();

    //     // Tentukan cabang yang bisa diakses
    //     if ($user->hasRole('superadmin')) {
    //         $accessibleBranches = Branch::orderBy('name')->get();
    //     } elseif ($user->hasRole('marketing')) {
    //         $accessibleBranches = Branch::orderBy('name')->get();
    //         // Marketing: read-only, tidak bisa validasi
    //     } else {
    //         // Manager: hanya cabang yang dikelolanya
    //         $accessibleBranches = $user->managedBranches;
    //     }

    //     if ($accessibleBranches->isEmpty()) {
    //         return back()->with('error', 'Anda tidak memiliki akses ke cabang manapun.');
    //     }

    //     // Filter dari request
    //     $selectedBranchId = $request->input('branch_id', $accessibleBranches->first()->id);
    //     $selectedBranch = $accessibleBranches->find($selectedBranchId);
    //     $tanggal = $request->input('tanggal', now()->toDateString());
    //     $validationStatusFilter = $request->input('validation_status'); // pending|approved|rejected
    //     $shiftFilter = $request->input('shift');
    //     $perPage = (int) $request->input('per_page', 25);

    //     if (! $selectedBranch) {
    //         abort(403, 'Anda tidak memiliki akses ke cabang ini.');
    //     }

    //     // Query laporan
    //     $query = DailyReportFo::where('branch_id', $selectedBranchId)
    //         ->whereDate('tanggal', $tanggal)
    //         ->with([
    //             'user',
    //             'validation.manager',
    //             'validation.action',
    //             // Hanya load detail Metrik Bisnis untuk dashboard
    //             'details' => function ($q) {
    //                 $q->whereHas('field.category', function ($q) {
    //                     $q->where('code', 'metrik_bisnis');
    //                 })->with('field');
    //             },
    //         ]);

    //     if ($validationStatusFilter) {
    //         $query->where('validation_status', $validationStatusFilter);
    //     }

    //     if ($shiftFilter) {
    //         $query->where('shift', $shiftFilter);
    //     }

    //     $reports = $query->orderBy('shift', 'asc')
    //         ->orderBy('slot', 'asc')
    //         ->orderBy('uploaded_at', 'asc')
    //         ->paginate($perPage)
    //         ->withQueryString();

    //     // Stats
    //     $statsQuery = DailyReportFo::where('branch_id', $selectedBranchId)
    //         ->whereDate('tanggal', $tanggal);

    //     $stats = [
    //         'total' => (clone $statsQuery)->count(),
    //         'pending' => (clone $statsQuery)->where('validation_status', 'pending')->count(),
    //         'approved' => (clone $statsQuery)->where('validation_status', 'approved')->count(),
    //         'rejected' => (clone $statsQuery)->where('validation_status', 'rejected')->count(),
    //     ];

    //     return view('daily-reports-fo.validation.index', [
    //         'accessibleBranches' => $accessibleBranches,
    //         'selectedBranch' => $selectedBranch,
    //         'tanggal' => $tanggal,
    //         'reports' => $reports,
    //         'stats' => $stats,
    //         'canValidate' => ! $user->hasRole('marketing'),
    //         'isSuperadmin' => $user->hasRole('superadmin'),
    //     ]);
    // }
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
        // branch_id = 'all' untuk superadmin/marketing yang mau lihat semua cabang
        $branchIdParam = $request->input('branch_id', $accessibleBranches->first()->id);
        $tanggal = $request->input('tanggal', now()->toDateString());
        $validationStatusFilter = $request->input('validation_status');
        $shiftFilter = $request->input('shift');
        $perPage = (int) $request->input('per_page', 25);

        $isAllBranches = $branchIdParam === 'all'
            && ($user->hasRole('superadmin') || $user->hasRole('marketing'));

        // Resolve selectedBranch (null jika "all")
        $selectedBranch = null;
        if (! $isAllBranches) {
            $selectedBranch = $accessibleBranches->find($branchIdParam);
            if (! $selectedBranch) {
                abort(403, 'Anda tidak memiliki akses ke cabang ini.');
            }
        }

        // -------------------------------------------------------
        // Base query
        // -------------------------------------------------------
        $baseQuery = DailyReportFo::query()
            ->whereDate('tanggal', $tanggal);

        if ($isAllBranches) {
            // Superadmin/marketing: semua cabang
            $baseQuery->whereIn('branch_id', $accessibleBranches->pluck('id'));
        } else {
            $baseQuery->where('branch_id', $selectedBranch->id);
        }

        // -------------------------------------------------------
        // Stats — hitung sebelum filter status/shift
        // -------------------------------------------------------
        $statsBase = (clone $baseQuery);

        // Hitung total metrik bisnis dari detail
        $metrikFieldCodes = ['mb_omset', 'mb_revenue', 'mb_jumlah_akad'];

        $metrikTotals = \App\Models\DailyReportFODetail::query()
            ->whereHas('report', function ($q) use ($tanggal, $isAllBranches, $selectedBranch, $accessibleBranches) {
                $q->whereDate('tanggal', $tanggal);
                if ($isAllBranches) {
                    $q->whereIn('branch_id', $accessibleBranches->pluck('id'));
                } else {
                    $q->where('branch_id', $selectedBranch->id);
                }
            })
            ->whereHas('field', function ($q) use ($metrikFieldCodes) {
                $q->whereIn('code', $metrikFieldCodes);
            })
            ->with('field')
            ->get()
            ->groupBy(fn ($d) => $d->field->code)
            ->map(fn ($group) => $group->sum('value_number'));

        $stats = [
            'total' => (clone $statsBase)->count(),
            'pending' => (clone $statsBase)->where('validation_status', 'pending')->count(),
            'approved' => (clone $statsBase)->where('validation_status', 'approved')->count(),
            'rejected' => (clone $statsBase)->where('validation_status', 'rejected')->count(),
            'total_omset' => $metrikTotals->get('mb_omset', 0),
            'total_revenue' => $metrikTotals->get('mb_revenue', 0),
            'total_akad' => $metrikTotals->get('mb_jumlah_akad', 0),
        ];

        // -------------------------------------------------------
        // Query laporan dengan filter tambahan
        // -------------------------------------------------------
        $reportQuery = (clone $baseQuery)
            ->with([
                'user',
                'branch',
                'validation.manager',
                'validation.action',
                'details' => function ($q) {
                    $q->whereHas('field.category', function ($q) {
                        $q->where('code', 'metrik_bisnis');
                    })->with('field');
                },
            ]);

        if ($validationStatusFilter) {
            $reportQuery->where('validation_status', $validationStatusFilter);
        }

        if ($shiftFilter) {
            $reportQuery->where('shift', $shiftFilter);
        }

        $reports = $reportQuery
            ->orderBy('shift', 'asc')
            ->orderBy('slot', 'asc')
            ->orderBy('uploaded_at', 'asc')
            ->paginate($perPage)
            ->withQueryString();

        return view('daily-reports-fo.validation.index', [
            'accessibleBranches' => $accessibleBranches,
            'selectedBranch' => $selectedBranch,       // null jika "all"
            'isAllBranches' => $isAllBranches,
            'branchIdParam' => $branchIdParam,
            'tanggal' => $tanggal,
            'reports' => $reports,
            'stats' => $stats,
            'canValidate' => ! $user->hasRole('marketing'),
            'isSuperadmin' => $user->hasRole('superadmin'),
            'canViewAll' => $user->hasRole('superadmin') || $user->hasRole('marketing'),
        ]);
    }

    /**
     * Show form validasi untuk satu laporan
     */
    // public function show($reportId)
    // {
    //     $user = Auth::user();
    //     $report = DailyReportFo::with([
    //         'user',
    //         'branch',
    //         'validation.manager',
    //         'validation.action',
    //         // Load semua detail untuk ditampilkan
    //         'details' => function ($q) {
    //             $q->with(['field.category', 'photos']);
    //         },
    //     ])->findOrFail($reportId);

    //     // Cek permission akses
    //     $this->authorizeAccess($user, $report);

    //     // Cek apakah bisa divalidasi (window manager harus open)
    //     $canValidate = $this->canValidate($user, $report);

    //     // Load opsi tindakan dari master data
    //     $validationActions = ValidationAction::active()->ordered()->get();

    //     // Group details by category untuk tampilan
    //     $detailsByCategory = $report->details->groupBy(function ($detail) {
    //         return $detail->field->category->name ?? 'Lainnya';
    //     });

    //     // Ambil detail metrik bisnis
    //     $metrikDetails = $report->details->filter(function ($detail) {
    //         return $detail->field->category->code === 'metrik_bisnis';
    //     })->keyBy(fn ($d) => $d->field->code);

    //     return view('daily-reports-fo.validation.show', [
    //         'report' => $report,
    //         'detailsByCategory' => $detailsByCategory,
    //         'metrikDetails' => $metrikDetails,
    //         'validationActions' => $validationActions,
    //         'canValidate' => $canValidate,
    //         'isSuperadmin' => $user->hasRole('superadmin'),
    //         'managerWindowStatus' => $report->manager_window_status,
    //     ]);
    // }

    /**
     * Proses validasi (approve/reject) oleh manager
     */
    // public function validate(Request $request, $reportId)
    // {
    //     $user = Auth::user();
    //     $report = DailyReportFo::with('validation')->findOrFail($reportId);

    //     // Cek permission
    //     $this->authorizeAccess($user, $report);

    //     // Marketing tidak bisa validasi
    //     if ($user->hasRole('marketing')) {
    //         abort(403, 'Marketing tidak memiliki akses untuk memvalidasi laporan.');
    //     }

    //     // Cek window manager masih buka (kecuali superadmin)
    //     if (! $user->hasRole('superadmin') && ! $report->isManagerWindowOpen()) {
    //         return back()->with('error', 'Window validasi sudah ditutup.');
    //     }

    //     // Cek laporan belum divalidasi (kecuali superadmin)
    //     if (! $user->hasRole('superadmin') && $report->validation_status !== 'pending') {
    //         return back()->with('error', 'Laporan ini sudah divalidasi.');
    //     }

    //     // Validasi input
    //     $validated = $request->validate([
    //         'status' => 'required|in:approved,rejected',
    //         'validation_action_id' => 'required|exists:validation_actions,id',
    //         'catatan' => 'nullable|string|max:500',
    //     ], [
    //         'status.required' => 'Status validasi wajib dipilih.',
    //         'validation_action_id.required' => 'Opsi tindakan wajib dipilih.',
    //         'validation_action_id.exists' => 'Opsi tindakan tidak valid.',
    //     ]);

    //     DB::transaction(function () use ($validated, $report, $user) {
    //         // Hapus validasi lama jika ada (untuk superadmin yang re-validate)
    //         $report->validation()->delete();

    //         // Buat record validasi baru
    //         DailyReportFoValidation::create([
    //             'daily_report_fo_id' => $report->id,
    //             'manager_id' => $user->id,
    //             'validation_action_id' => $validated['validation_action_id'],
    //             'status' => $validated['status'],
    //             'catatan' => $validated['catatan'] ?? null,
    //             'validated_at' => now(),
    //         ]);

    //         // Update status di header laporan
    //         $report->update([
    //             'validation_status' => $validated['status'],
    //         ]);
    //     });

    //     $statusLabel = $validated['status'] === 'approved' ? 'disetujui' : 'ditolak';

    //     return redirect()
    //         ->route('validation.index', ['branch_id' => $report->branch_id])
    //         ->with('success', "Laporan berhasil {$statusLabel}.");
    // }

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
        $report = DailyReportFo::with(['validation', 'branch'])->findOrFail($reportId);
        // ↑ PENTING: eager load 'branch' agar getBranchTimezone() tidak N+1 query

        $this->authorizeAccess($user, $report);

        if ($user->hasRole('marketing')) {
            abort(403, 'Marketing tidak memiliki akses untuk memvalidasi laporan.');
        }

        // Cek window — isManagerWindowOpen() sudah pakai timezone cabang laporan secara otomatis
        if (! $user->hasRole('superadmin') && ! $report->isManagerWindowOpen()) {
            $status = $report->manager_window_status;

            $message = match ($status) {
                'waiting' => 'Window validasi belum dibuka. Tunggu hingga '.$report->manager_window_start->format('H:i').' '.$report->branch->timezone.'.',
                'expired' => 'Window validasi sudah tutup sejak '.$report->manager_window_end->format('H:i').' '.$report->branch->timezone.'.',
                default => 'Window validasi tidak tersedia.',
            };

            return back()->with('error', $message);
        }

        if (! $user->hasRole('superadmin') && $report->validation_status !== 'pending') {
            return back()->with('error', 'Laporan ini sudah divalidasi sebelumnya.');
        }

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'validation_action_id' => 'required|exists:validation_actions,id',
            'catatan' => 'nullable|string|max:500',
        ], [
            'status.required' => 'Status validasi wajib dipilih.',
            'validation_action_id.required' => 'Opsi tindakan wajib dipilih.',
            'validation_action_id.exists' => 'Opsi tindakan tidak valid.',
        ]);

        DB::transaction(function () use ($validated, $report, $user) {
            $report->validation()->delete();

            DailyReportFoValidation::create([
                'daily_report_fo_id' => $report->id,
                'manager_id' => $user->id,
                'validation_action_id' => $validated['validation_action_id'],
                'status' => $validated['status'],
                'catatan' => $validated['catatan'] ?? null,
                'validated_at' => now(),
            ]);

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
            'validation.action',
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
        })->keyBy(fn ($d) => $d->field->code);

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
}
