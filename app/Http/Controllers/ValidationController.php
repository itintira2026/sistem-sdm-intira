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
    // DASHBOARD — index() (Part F2 + Part 0.6: stats & metrik dioptimasi)
    // =========================================================

    public function index(Request $request)
    {
        $user = Auth::user();

        if ($this->hasFullBranchAccess($user)) {
            $accessibleBranches = Branch::orderBy('name')->get();
        } else {
            // Manager & Korwil (dual-role manager): hanya cabang yang dikelolanya
            $accessibleBranches = $user->managedBranches()->orderBy('name')->get();
        }

        if ($accessibleBranches->isEmpty()) {
            return back()->with('error', 'Anda tidak memiliki akses ke cabang manapun.');
        }

        $branchIdParam          = $request->input('branch_id', 'all');
        $tanggal                = $request->input('tanggal', now()->toDateString());
        $validationStatusFilter = $request->input('validation_status');
        $shiftFilter            = $request->input('shift');
        $perPage                = (int) $request->input('per_page', 25);

        $isAllBranches = $branchIdParam === 'all';

        $selectedBranch = null;
        if (! $isAllBranches) {
            $selectedBranch = $accessibleBranches->find($branchIdParam);
            if (! $selectedBranch) {
                abort(403, 'Anda tidak memiliki akses ke cabang ini.');
            }
        }

        $baseQuery = DailyReportFo::query()
            ->whereDate('tanggal', $tanggal)
            ->whereIn('branch_id', $accessibleBranches->pluck('id'));

        if (! $isAllBranches) {
            $baseQuery->where('branch_id', $selectedBranch->id);
        }

        if ($shiftFilter) {
            $baseQuery->where('shift', $shiftFilter);
        }

        // -------------------------------------------------------
        // Stats — DIOPTIMASI (Part 0.6): 1 query conditional aggregation
        // menggantikan 5x count() terpisah (total/pending/checked/approved/rejected)
        // -------------------------------------------------------
        $statusCounts = (clone $baseQuery)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN validation_status = 'pending'  THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN validation_status = 'checked'  THEN 1 ELSE 0 END) as checked,
                SUM(CASE WHEN validation_status = 'approved' THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN validation_status = 'rejected' THEN 1 ELSE 0 END) as rejected
            ")
            ->first();

        // -------------------------------------------------------
        // Metrik bisnis — DIOPTIMASI (Part 0.6): SUM...GROUP BY di database,
        // menggantikan ->get()->groupBy()->map() yang narik semua baris ke PHP
        // -------------------------------------------------------
        $metrikTotals = DB::table('daily_report_fo_details as d')
            ->join('daily_report_fo as r', 'r.id', '=', 'd.daily_report_fo_id')
            ->join('report_fields as f', 'f.id', '=', 'd.field_id')
            ->whereDate('r.tanggal', $tanggal)
            ->whereIn('r.branch_id', $accessibleBranches->pluck('id'))
            ->where('r.validation_status', 'approved')
            ->when(! $isAllBranches, fn($q) => $q->where('r.branch_id', $selectedBranch->id))
            ->when($shiftFilter, fn($q) => $q->where('r.shift', $shiftFilter))
            ->whereIn('f.code', ['mb_omset', 'mb_revenue', 'mb_jumlah_akad', 'mb_nasabah_baru'])
            ->selectRaw('f.code, SUM(d.value_number) as total')
            ->groupBy('f.code')
            ->pluck('total', 'code');

        $stats = [
            'total'              => (int) $statusCounts->total,
            'pending'            => (int) $statusCounts->pending,
            'checked'            => (int) $statusCounts->checked,
            'approved'           => (int) $statusCounts->approved,
            'rejected'           => (int) $statusCounts->rejected,
            'total_omset'        => $metrikTotals->get('mb_omset', 0),
            'total_revenue'      => $metrikTotals->get('mb_revenue', 0),
            'total_akad'         => $metrikTotals->get('mb_jumlah_akad', 0),
            'total_nasabah_baru' => $metrikTotals->get('mb_nasabah_baru', 0),
        ];

        $reportQuery = (clone $baseQuery)->with([
            'user:id,name',
            'branch:id,name,timezone',
            'validation.manager:id,name',
            'validation.checker:id,name', // ← BARU: siapa yang cek
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

        // NOTE: joinSub "latest upload per user" dipertahankan apa adanya
        // (belum dievaluasi/dihapus — lihat Part 0.6 di dokumen konsep).
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

        $groupedUserIds = $reports->pluck('user_id')->unique()->values();

        return view('daily-reports-fo.validation.index', [
            'accessibleBranches' => $accessibleBranches,
            'selectedBranch'     => $selectedBranch,
            'isAllBranches'      => $isAllBranches,
            'branchIdParam'      => $branchIdParam,
            'tanggal'            => $tanggal,
            'reports'            => $reports,
            'groupedUserIds'     => $groupedUserIds,
            'stats'              => $stats,
            'canCheck'           => $user->hasRole('manager') || $user->hasRole('superadmin'),
            'canApprove'         => $user->hasRole('korwil') || $user->hasRole('superadmin'),
            'isSuperadmin'       => $user->hasRole('superadmin'),
        ]);
    }

    /**
     * Detail laporan + form cek/approve
     */
    public function show($reportId)
    {
        $user = Auth::user();
        $report = DailyReportFo::with([
            'user',
            'branch',
            'validation.manager',
            'validation.checker', // ← BARU
            'validation.actions',
            'details' => function ($q) {
                $q->with(['field.category', 'photos']);
            },
        ])->findOrFail($reportId);

        $this->authorizeAccess($user, $report);

        $canCheck = $this->canCheck($user, $report);
        $canApprove = $this->canApprove($user, $report);
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
            'canCheck' => $canCheck,
            'canApprove' => $canApprove,
            'isSuperadmin' => $user->hasRole('superadmin'),
            'managerWindowStatus' => $report->manager_window_status,
            'branchTimezone' => $report->branch->timezone,
        ]);
    }

    // =========================================================
    // TAHAP 1 — CEK (oleh AM / role `manager`)
    // =========================================================

    public function check(Request $request, $reportId)
    {
        $user = Auth::user();
        $report = DailyReportFo::with(['validation', 'branch'])->findOrFail($reportId);

        $this->authorizeAccess($user, $report);

        if (! $this->canCheck($user, $report)) {
            if ($user->hasRole('korwil') && ! $user->hasRole('manager') && ! $user->hasRole('superadmin')) {
                abort(403, 'Korwil melakukan approve final, bukan tahap cek.');
            }

            if (! $user->hasRole('superadmin') && ! $report->isManagerWindowOpen()) {
                $status = $report->manager_window_status;
                $message = match ($status) {
                    'waiting' => 'Window cek belum dibuka. Tunggu hingga ' . $report->manager_window_start->format('H:i') . ' ' . $report->branch->timezone . '.',
                    'expired' => 'Window cek sudah tutup sejak ' . $report->manager_window_end->format('H:i') . ' ' . $report->branch->timezone . '.',
                    default => 'Window cek tidak tersedia.',
                };
                return back()->with('error', $message);
            }

            return back()->with('error', 'Laporan ini sudah melewati tahap cek.');
        }

        $validated = $request->validate([
            'check_status' => 'required|in:checked,rejected',
            'validation_action_ids' => 'required|array|min:1',
            'validation_action_ids.*' => 'exists:validation_actions,id',
            'catatan' => 'nullable|string|max:500',
        ], [
            'check_status.required' => 'Status cek wajib dipilih.',
            'validation_action_ids.required' => 'Minimal pilih 1 tindakan.',
            'validation_action_ids.min' => 'Minimal pilih 1 tindakan.',
        ]);

        DB::transaction(function () use ($validated, $report, $user) {
            $isRejected = $validated['check_status'] === 'rejected';

            // Kalau AM langsung menolak, itu jadi keputusan final —
            // gak perlu nunggu Korwil approve apa-apa lagi.
            $validation = DailyReportFoValidation::updateOrCreate(
                ['daily_report_fo_id' => $report->id],
                [
                    'checked_by' => $user->id,
                    'checked_at' => now(),
                    'manager_id' => $isRejected ? $user->id : null,
                    'validated_at' => $isRejected ? now() : null,
                    'status' => $validated['check_status'],
                    'catatan' => $validated['catatan'] ?? null,
                ]
            );

            $validation->actions()->sync($validated['validation_action_ids']);

            $report->update(['validation_status' => $validated['check_status']]);
        });

        $label = $validated['check_status'] === 'checked' ? 'dicek, menunggu approve Korwil' : 'ditolak';

        return redirect()
            ->route('validation.index', ['branch_id' => $report->branch_id])
            ->with('success', "Laporan berhasil {$label}.");
    }

    // =========================================================
    // TAHAP 2 — APPROVE FINAL (oleh Korwil)
    // =========================================================

    public function validate(Request $request, $reportId)
    {
        $user = Auth::user();
        $report = DailyReportFo::with(['validation.actions', 'branch'])->findOrFail($reportId);

        $this->authorizeAccess($user, $report);

        if (! $this->canApprove($user, $report)) {
            abort(403, 'Hanya Korwil atau Superadmin yang dapat melakukan approve final.');
        }

        // Korwil bebas override kapan saja — tidak ada gate status/window di sini.

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'validation_action_ids' => 'required|array|min:1',
            'validation_action_ids.*' => 'exists:validation_actions,id',
            'catatan' => 'nullable|string|max:500',
        ], [
            'status.required' => 'Status validasi wajib dipilih.',
            'validation_action_ids.required' => 'Minimal pilih 1 tindakan.',
            'validation_action_ids.min' => 'Minimal pilih 1 tindakan.',
        ]);

        DB::transaction(function () use ($validated, $report, $user) {
            // updateOrCreate (BUKAN delete+create) — supaya checked_by/checked_at
            // dari tahap cek AM tetap kesimpan meski Korwil belakangan yang approve.
            $validation = DailyReportFoValidation::updateOrCreate(
                ['daily_report_fo_id' => $report->id],
                [
                    'manager_id' => $user->id,
                    'validated_at' => now(),
                    'status' => $validated['status'],
                    'catatan' => $validated['catatan'] ?? null,
                ]
            );

            $validation->actions()->sync($validated['validation_action_ids']);

            $report->update(['validation_status' => $validated['status']]);
        });

        $statusLabel = $validated['status'] === 'approved' ? 'disetujui' : 'ditolak';

        return redirect()
            ->route('validation.index', ['branch_id' => $report->branch_id])
            ->with('success', "Laporan berhasil {$statusLabel}.");
    }

    /**
     * Reset ke pending — hapus SELURUH riwayat validasi (cek + approve)
     * (logic tidak berubah dari versi sebelumnya)
     */
    public function reset($reportId)
    {
        $user = Auth::user();
        $report = DailyReportFo::with('validation')->findOrFail($reportId);

        if (! $user->hasRole('superadmin')) {
            abort(403, 'Hanya superadmin yang dapat mereset validasi.');
        }

        DB::transaction(function () use ($report) {
            $report->validation()->delete();
            $report->update(['validation_status' => 'pending']);
        });

        return back()->with('success', 'Validasi berhasil direset ke pending.');
    }

    // =========================================================
    // PRIVATE HELPERS
    // =========================================================

    private function authorizeAccess($user, DailyReportFo $report): void
    {
        // DIUBAH: pakai hasFullBranchAccess() — sekarang termasuk role HO baru
        if ($this->hasFullBranchAccess($user)) {
            return;
        }

        $managedBranchIds = $user->managedBranches->pluck('id');

        if (! $managedBranchIds->contains($report->branch_id)) {
            abort(403, 'Anda tidak memiliki akses ke laporan ini.');
        }
    }

    /**
     * Bisa melakukan tahap CEK?
     * AM (manager) & superadmin, status harus pending, window cek masih buka
     * (superadmin bebas window)
     */
    private function canCheck($user, DailyReportFo $report): bool
    {
        if ($user->hasRole('marketing')) {
            return false;
        }

        if ($user->hasRole('superadmin')) {
            return true;
        }

        if (! $user->hasRole('manager')) {
            return false;
        }

        return $report->validation_status === 'pending'
            && $report->isManagerWindowOpen();
    }

    /**
     * Bisa melakukan APPROVE FINAL?
     * Korwil & superadmin — tanpa syarat status/window (bebas override kapan saja)
     */
    private function canApprove($user, DailyReportFo $report): bool
    {
        if ($user->hasRole('marketing')) {
            return false;
        }

        return $user->hasRole('korwil') || $user->hasRole('superadmin');
    }

    /**
     * Export laporan validasi ke Excel (tidak berubah dari versi sebelumnya)
     */
    public function export(Request $request)
    {
        $user = Auth::user();

        if ($this->hasFullBranchAccess($user)) {
            $accessibleBranches = Branch::orderBy('name')->get();
        } else {
            $accessibleBranches = $user->managedBranches()->orderBy('name')->get();
        }

        if ($accessibleBranches->isEmpty()) {
            return back()->with('error', 'Anda tidak memiliki akses ke cabang manapun.');
        }

        $request->validate([
            'date_from' => 'required|date',
            'date_to'   => 'required|date|after_or_equal:date_from',
            'branch_id' => 'required',
        ], [
            'date_from.required'     => 'Tanggal Dari wajib diisi.',
            'date_from.date'         => 'Tanggal Dari tidak valid.',
            'date_to.required'       => 'Tanggal Sampai wajib diisi.',
            'date_to.date'           => 'Tanggal Sampai tidak valid.',
            'date_to.after_or_equal' => 'Tanggal Sampai tidak boleh sebelum Tanggal Dari.',
            'branch_id.required'     => 'Cabang wajib dipilih.',
        ]);

        $dateFrom = Carbon::parse($request->input('date_from'))->startOfDay();
        $dateTo   = Carbon::parse($request->input('date_to'))->endOfDay();

        $maxDays  = ($user->hasRole('manager') || $user->hasRole('korwil')) ? 7 : 3;
        $diffDays = Carbon::parse($request->input('date_from'))
            ->diffInDays(Carbon::parse($request->input('date_to'))) + 1;

        if ($diffDays > $maxDays) {
            return back()->with(
                'error',
                "Maksimal export {$maxDays} hari untuk role Anda. Range yang dipilih {$diffDays} hari."
            );
        }

        $branchId = null;

        if ($request->input('branch_id') !== 'all') {
            $branchId       = (int) $request->input('branch_id');
            $selectedBranch = $accessibleBranches->find($branchId);

            if (! $selectedBranch) {
                abort(403, 'Anda tidak memiliki akses ke cabang ini.');
            }
        }

        $branchLabel = $branchId
            ? $accessibleBranches->find($branchId)->name
            : 'SemuaCabang';

        $branchLabel = str_replace(' ', '_', $branchLabel);

        $filename = "Validasi_LaporanFO_{$branchLabel}_"
            . $dateFrom->format('dmY')
            . '_sd_'
            . $dateTo->format('dmY')
            . '.xlsx';

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

    private function hasFullBranchAccess($user): bool
    {
        return $user->hasAnyRole([
            'superadmin',
            'marketing',
            'owner',
            'ketua',
            'direktur_utama',
            'direktur_bisnis',
            'direktur_operasional',
            'kadiv',
            'staff',
        ]);
    }
}
