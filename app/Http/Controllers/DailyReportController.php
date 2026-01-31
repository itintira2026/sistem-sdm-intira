<?php

namespace App\Http\Controllers;

use App\Helpers\ImageHelper;
use App\Models\Branch;
use App\Models\BranchUser;
use App\Models\DailyReport;
use App\Models\DailyReportPhoto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class DailyReportController extends Controller
{
    // ==============================
    // FO/STAFF SECTION
    // ==============================

    /**
     * Dashboard & List Laporan FO/Staff
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tanggal = $request->input('tanggal', now()->toDateString());
        $shiftFilter = $request->input('shift');
        $validasiFilter = $request->input('validasi');
        $perPage = (int) $request->input('per_page', 10);

        // 🔥 TENTUKAN BRANCH_ID BERDASARKAN ROLE
        if ($user->hasRole('superadmin')) {
            // Superadmin: bisa pilih cabang
            $selectedBranchId = $request->input('branch_id');

            if ($selectedBranchId) {
                $query = DailyReport::where('branch_id', $selectedBranchId);
            } else {
                // Jika tidak pilih cabang, tampilkan semua
                $query = DailyReport::query();
            }
        } elseif ($user->hasRole('manager')) {
            // Manager: bisa pilih dari cabang yang dia kelola
            $managedBranchIds = $user->managedBranches->pluck('id');
            $selectedBranchId = $request->input('branch_id', $managedBranchIds->first());

            // Validasi: branch yang dipilih harus dari yang dia kelola
            if (!$managedBranchIds->contains($selectedBranchId)) {
                abort(403, 'Anda tidak memiliki akses ke cabang ini.');
            }

            $query = DailyReport::where('branch_id', $selectedBranchId);
        } else {
            // FO/Staff: hanya cabang sendiri
            $userBranches = $user->branches->pluck('id');

            if ($userBranches->isEmpty()) {
                abort(403, 'Anda belum terdaftar di cabang manapun.');
            }

            // Ambil cabang pertama user (asumsi 1 FO = 1 cabang utama)
            $selectedBranchId = $userBranches->first();
            $query = DailyReport::where('branch_id', $selectedBranchId);
        }

        // Filter tanggal
        $query->whereDate('tanggal', $tanggal);

        // Filter shift
        if ($shiftFilter) {
            $query->where('shift', $shiftFilter);
        }

        // Filter validasi
        if ($validasiFilter !== null) {
            $query->where('validasi_manager', $validasiFilter === '1');
        }

        $reports = $query->with(['branch', 'user', 'photos'])
            ->orderBy('shift')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // 🔥 STATISTIK DASHBOARD
        // $stats = [
        //     'total_hari_ini' => DailyReport::where('branch_id', $selectedBranchId)
        //         ->whereDate('tanggal', $tanggal)
        //         ->count(),
        //     'target' => 2, // 2 shift per hari
        //     'shift_pagi' => DailyReport::where('branch_id', $selectedBranchId)
        //         ->whereDate('tanggal', $tanggal)
        //         ->where('shift', 'pagi')
        //         ->exists(),
        //     'shift_siang' => DailyReport::where('branch_id', $selectedBranchId)
        //         ->whereDate('tanggal', $tanggal)
        //         ->where('shift', 'siang')
        //         ->exists(),
        //     'validated' => DailyReport::where('branch_id', $selectedBranchId)
        //         ->whereDate('tanggal', $tanggal)
        //         ->where('validasi_manager', true)
        //         ->count(),
        //     'pending' => DailyReport::where('branch_id', $selectedBranchId)
        //         ->whereDate('tanggal', $tanggal)
        //         ->where('validasi_manager', false)
        //         ->count(),
        // ];
        // 🔥 STATISTIK DASHBOARD
        $baseQuery = DailyReport::where('branch_id', $selectedBranchId)
            ->whereDate('tanggal', $tanggal);

        $stats = [
            'total_hari_ini' => (clone $baseQuery)->count(),
            'target' => 2, // 2 shift per hari
            'shift_pagi' => (clone $baseQuery)->where('shift', 'pagi')->exists(),
            'shift_siang' => (clone $baseQuery)->where('shift', 'siang')->exists(),
            'validated' => (clone $baseQuery)->where('validasi_manager', true)->count(),
            'pending' => (clone $baseQuery)->where('validasi_manager', false)->count(),

            // 🔥 TOTAL PENCAIRAN
            'total_pencairan_barang' => (clone $baseQuery)->sum('pencairan_jumlah_barang'),
            'total_pencairan_nominal' => (clone $baseQuery)->sum('pencairan_nominal'),

            // 🔥 TOTAL PELUNASAN
            'total_pelunasan_barang' => (clone $baseQuery)->sum('pelunasan_jumlah_barang'),
            'total_pelunasan_nominal' => (clone $baseQuery)->sum('pelunasan_nominal'),
        ];

        // 🔥 LIST CABANG untuk dropdown (Superadmin & Manager)
        $branchList = null;
        if ($user->hasRole('superadmin')) {
            $branchList = Branch::orderBy('name')->get();
        } elseif ($user->hasRole('manager')) {
            $branchList = $user->managedBranches;
        }

        // 🔥 STOK AKHIR (dari laporan terakhir hari ini atau kemarin)
        $laporanTerakhir = DailyReport::where('branch_id', $selectedBranchId)
            ->where(function ($q) use ($tanggal) {
                $q->whereDate('tanggal', $tanggal)
                    ->orWhereDate('tanggal', '<', $tanggal);
            })
            ->orderBy('tanggal', 'desc')
            ->orderBy('shift', 'desc')
            ->first();

        $stokAkhir = [
            'jumlah_barang' => $laporanTerakhir->final_jumlah_barang ?? 0,
            'nominal' => $laporanTerakhir->final_nominal ?? 0,
        ];

        return view('daily-reports.index', compact(
            'reports',
            'tanggal',
            'stats',
            'branchList',
            'selectedBranchId',
            'stokAkhir'
        ));
    }

    /**
     * Form Create Laporan
     */
    public function create()
    {
        $user = Auth::user();

        // 🔥 LIST CABANG untuk dropdown
        $branchList = null;
        if ($user->hasRole('superadmin')) {
            $branchList = Branch::orderBy('name')->get();
        } elseif ($user->hasRole('manager')) {
            $branchList = $user->managedBranches;
        } else {
            // FO: ambil cabang sendiri
            $branchList = $user->branches;
        }

        if ($branchList->isEmpty()) {
            return back()->with('error', 'Anda belum terdaftar di cabang manapun.');
        }

        return view('daily-reports.create', compact('branchList'));
    }

    /**
     * Store Laporan Baru
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // 🔥 VALIDASI
        $validated = $request->validate([
            'branch_id' => [
                'required',
                'exists:branches,id',
                // Custom validation: cabang harus sesuai dengan akses user
                function ($attribute, $value, $fail) use ($user) {
                    if ($user->hasRole('superadmin')) {
                        return; // Superadmin bebas pilih cabang
                    } elseif ($user->hasRole('manager')) {
                        if (!$user->managedBranches->pluck('id')->contains($value)) {
                            $fail('Anda tidak memiliki akses ke cabang ini.');
                        }
                    } else {
                        if (!$user->branches->pluck('id')->contains($value)) {
                            $fail('Anda tidak memiliki akses ke cabang ini.');
                        }
                    }
                },
            ],
            'tanggal' => ($user->hasRole('superadmin') || $user->hasRole('manager')) ? 'required|date' : 'nullable',
            'shift' => [
                'required',
                'in:pagi,siang',
                // UNIQUE: branch_id + tanggal + shift
                Rule::unique('daily_reports')
                    ->where('branch_id', $request->branch_id)
                    ->where('tanggal', $request->input('tanggal', now()->toDateString()))
            ],
            'pencairan_jumlah_barang' => 'required|integer|min:0',
            'pencairan_nominal' => 'required|numeric|min:0',
            'pelunasan_jumlah_barang' => 'required|integer|min:0',
            'pelunasan_nominal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:1000',
            'photos' => 'required|array|min:1|max:10',
            'photos.*' => 'required|image|mimes:jpg,jpeg,png|max:5120', // 5MB
            'photo_keterangan' => 'nullable|array',
            'photo_keterangan.*' => 'nullable|string|max:255',
        ], [
            'shift.unique' => 'Laporan untuk shift ' . $request->shift . ' di tanggal ' . Carbon::parse($request->input('tanggal', now()->toDateString()))->format('d M Y') . ' sudah ada. Tidak boleh duplikat.',
            'photos.required' => 'Minimal 1 foto wajib diupload.',
            'photos.min' => 'Minimal 1 foto wajib diupload.',
            'photos.max' => 'Maksimal 10 foto.',
            'photos.*.max' => 'Ukuran foto maksimal 5MB.',
        ]);

        // Tentukan tanggal
        $tanggal = ($user->hasRole('superadmin') || $user->hasRole('manager'))
            ? $request->tanggal
            : now()->toDateString();

        // Create laporan
        $report = DailyReport::create([
            'branch_id' => $validated['branch_id'],
            'user_id' => $user->id,
            'tanggal' => $tanggal,
            'shift' => $validated['shift'],
            'pencairan_jumlah_barang' => $validated['pencairan_jumlah_barang'],
            'pencairan_nominal' => $validated['pencairan_nominal'],
            'pelunasan_jumlah_barang' => $validated['pelunasan_jumlah_barang'],
            'pelunasan_nominal' => $validated['pelunasan_nominal'],
            'keterangan' => $validated['keterangan'],
        ]);
        // 🔥 HITUNG & SIMPAN STOK AKHIR
        $report->hitungStokAkhir();
        $report->save();

        // Upload photos
        // if ($request->hasFile('photos')) {
        //     foreach ($request->file('photos') as $index => $photo) {
        //         $fileName = time() . '_' . $index . '.' . $photo->getClientOriginalExtension();
        //         $filePath = $photo->storeAs('daily_reports/' . $report->id, $fileName, 'public');

        //         DailyReportPhoto::create([
        //             'daily_report_id' => $report->id,
        //             'file_path' => $filePath,
        //             'file_name' => $photo->getClientOriginalName(),
        //             'keterangan' => $request->input('photo_keterangan.' . $index),
        //         ]);
        //     }
        // }
        // Upload photos
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $photo) {
                // 🔥 COMPRESS & CONVERT TO WEBP
                $filePath = ImageHelper::compressAndSave(
                    $photo,
                    'daily_reports/' . $report->id,
                    1920, // max width
                    1080, // max height
                    80    // quality
                );

                DailyReportPhoto::create([
                    'daily_report_id' => $report->id,
                    'file_path' => $filePath,
                    'file_name' => $photo->getClientOriginalName(),
                    'keterangan' => $request->input('photo_keterangan.' . $index),
                ]);
            }
        }

        return redirect()->route('daily-reports.index', ['tanggal' => $tanggal, 'branch_id' => $validated['branch_id']])
            ->with('success', 'Laporan berhasil ditambahkan!');
    }

    /**
     * Form Edit Laporan
     */
    public function edit(DailyReport $dailyReport)
    {
        $user = Auth::user();

        // 🔥 CEK PERMISSION
        if (!$user->hasRole('superadmin')) {
            // Cek apakah user punya akses ke cabang laporan ini
            if ($user->hasRole('manager')) {
                if (!$user->managedBranches->pluck('id')->contains($dailyReport->branch_id)) {
                    abort(403, 'Anda tidak memiliki akses ke laporan cabang ini.');
                }
            } else {
                // FO: harus cabang sendiri & belum divalidasi
                if (!$user->branches->pluck('id')->contains($dailyReport->branch_id) || $dailyReport->validasi_manager) {
                    abort(403, 'Anda tidak memiliki akses untuk edit laporan ini.');
                }
            }
        }

        return view('daily-reports.edit', compact('dailyReport'));
    }

    /**
     * Update Laporan
     */
    public function update(Request $request, DailyReport $dailyReport)
    {
        $user = Auth::user();

        // 🔥 CEK PERMISSION
        if (!$user->hasRole('superadmin')) {
            if ($user->hasRole('manager')) {
                if (!$user->managedBranches->pluck('id')->contains($dailyReport->branch_id)) {
                    abort(403, 'Anda tidak memiliki akses ke laporan cabang ini.');
                }
            } else {
                if (!$user->branches->pluck('id')->contains($dailyReport->branch_id) || $dailyReport->validasi_manager) {
                    abort(403, 'Anda tidak memiliki akses untuk edit laporan ini.');
                }
            }
        }

        // 🔥 VALIDASI
        $validated = $request->validate([
            'pencairan_jumlah_barang' => 'required|integer|min:0',
            'pencairan_nominal' => 'required|numeric|min:0',
            'pelunasan_jumlah_barang' => 'required|integer|min:0',
            'pelunasan_nominal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:1000',
            'photos' => 'nullable|array|max:10',
            'photos.*' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'photo_keterangan' => 'nullable|array',
            'photo_keterangan.*' => 'nullable|string|max:255',
            'delete_photos' => 'nullable|array',
            'delete_photos.*' => 'exists:daily_report_photos,id',
        ]);

        // Update data utama
        $dailyReport->update([
            'pencairan_jumlah_barang' => $validated['pencairan_jumlah_barang'],
            'pencairan_nominal' => $validated['pencairan_nominal'],
            'pelunasan_jumlah_barang' => $validated['pelunasan_jumlah_barang'],
            'pelunasan_nominal' => $validated['pelunasan_nominal'],
            'keterangan' => $validated['keterangan'],
        ]);

        // 🔥 RE-CALCULATE STOK AKHIR
        $dailyReport->hitungStokAkhir();
        $dailyReport->save();

        // Hapus foto yang dipilih untuk dihapus
        if ($request->has('delete_photos')) {
            $photosToDelete = DailyReportPhoto::whereIn('id', $request->delete_photos)
                ->where('daily_report_id', $dailyReport->id)
                ->get();

            foreach ($photosToDelete as $photo) {
                Storage::disk('public')->delete($photo->file_path);
                $photo->delete();
            }
        }

        // Upload foto baru
        // if ($request->hasFile('photos')) {
        //     foreach ($request->file('photos') as $index => $photo) {
        //         $fileName = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
        //         $filePath = $photo->storeAs('daily_reports/' . $dailyReport->id, $fileName, 'public');

        //         DailyReportPhoto::create([
        //             'daily_report_id' => $dailyReport->id,
        //             'file_path' => $filePath,
        //             'file_name' => $photo->getClientOriginalName(),
        //             'keterangan' => $request->input('photo_keterangan.' . $index),
        //         ]);
        //     }
        // }
        // Upload foto baru
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $photo) {
                // 🔥 COMPRESS & CONVERT TO WEBP
                $filePath = ImageHelper::compressAndSave(
                    $photo,
                    'daily_reports/' . $dailyReport->id,
                    1920, // max width
                    1080, // max height
                    80    // quality
                );

                DailyReportPhoto::create([
                    'daily_report_id' => $dailyReport->id,
                    'file_path' => $filePath,
                    'file_name' => $photo->getClientOriginalName(),
                    'keterangan' => $request->input('photo_keterangan.' . $index),
                ]);
            }
        }

        // Validasi minimal 1 foto
        if ($dailyReport->photos()->count() === 0) {
            return back()->withErrors(['photos' => 'Laporan harus memiliki minimal 1 foto.'])->withInput();
        }

        return redirect()->route('daily-reports.index', [
            'tanggal' => $dailyReport->tanggal,
            'branch_id' => $dailyReport->branch_id
        ])->with('success', 'Laporan berhasil diperbarui!');
    }

    /**
     * Delete Laporan
     */
    public function destroy(DailyReport $dailyReport)
    {
        $user = Auth::user();

        // 🔥 CEK PERMISSION
        if (!$user->hasRole('superadmin')) {
            if ($user->hasRole('manager')) {
                if (!$user->managedBranches->pluck('id')->contains($dailyReport->branch_id)) {
                    abort(403, 'Anda tidak memiliki akses ke laporan cabang ini.');
                }
            } else {
                if (!$user->branches->pluck('id')->contains($dailyReport->branch_id) || $dailyReport->validasi_manager) {
                    abort(403, 'Anda tidak memiliki akses untuk hapus laporan ini.');
                }
            }
        }

        $tanggal = $dailyReport->tanggal;
        $branchId = $dailyReport->branch_id;

        // Delete akan trigger event di model yang auto-delete photos
        $dailyReport->delete();

        return redirect()->route('daily-reports.index', [
            'tanggal' => $tanggal,
            'branch_id' => $branchId
        ])->with('success', 'Laporan berhasil dihapus!');
    }

    // ==============================
    // MANAGER SECTION
    // ==============================

    /**
     * Dashboard Manager
     */
    public function managerDashboard(Request $request)
    {
        $user = Auth::user();
        $tanggal = $request->input('tanggal', now()->toDateString());

        // 🔥 SUPERADMIN: Lihat semua cabang
        // 🔥 MANAGER: Hanya cabang yang dia manage
        if ($user->hasRole('superadmin')) {
            $managedBranches = Branch::all();
        } else {
            $managedBranches = $user->managedBranches;
        }

        $managedBranchIds = $managedBranches->pluck('id');

        // 🔥 STATISTIK
        $totalCabang = $managedBranches->count();

        $totalLaporan = DailyReport::whereIn('branch_id', $managedBranchIds)
            ->whereDate('tanggal', $tanggal)
            ->count();

        $targetLaporan = $totalCabang * 2; // 2 shift per cabang

        $cabangLengkap = DailyReport::whereIn('branch_id', $managedBranchIds)
            ->whereDate('tanggal', $tanggal)
            ->select('branch_id')
            ->groupBy('branch_id')
            ->havingRaw('COUNT(*) >= 2')
            ->get()
            ->count();

        $cabangBelumLengkap = $totalCabang - $cabangLengkap;

        $belumValidasi = DailyReport::whereIn('branch_id', $managedBranchIds)
            ->whereDate('tanggal', $tanggal)
            ->where('validasi_manager', false)
            ->count();

        $sudahValidasi = DailyReport::whereIn('branch_id', $managedBranchIds)
            ->whereDate('tanggal', $tanggal)
            ->where('validasi_manager', true)
            ->count();

        // Total Pencairan & Pelunasan
        $totals = DailyReport::whereIn('branch_id', $managedBranchIds)
            ->whereDate('tanggal', $tanggal)
            ->selectRaw('
                SUM(pencairan_jumlah_barang) as total_pencairan_barang,
                SUM(pencairan_nominal) as total_pencairan_nominal,
                SUM(pelunasan_jumlah_barang) as total_pelunasan_barang,
                SUM(pelunasan_nominal) as total_pelunasan_nominal
            ')
            ->first();

        $stokAkhirPerCabang = [];
        foreach ($managedBranches as $branch) {
            $lastReport = DailyReport::where('branch_id', $branch->id)
                ->whereDate('tanggal', '<=', $tanggal)
                ->orderBy('tanggal', 'desc')
                ->orderBy('shift', 'desc')
                ->first();

            $stokAkhirPerCabang[$branch->id] = [
                'jumlah_barang' => $lastReport->final_jumlah_barang ?? 0,
                'nominal' => $lastReport->final_nominal ?? 0,
            ];
        }

        return view('daily-reports.manager.dashboard', compact(
            'tanggal',
            'totalCabang',
            'totalLaporan',
            'targetLaporan',
            'cabangLengkap',
            'cabangBelumLengkap',
            'belumValidasi',
            'sudahValidasi',
            'totals',
            'managedBranches',
            'stokAkhirPerCabang'
        ));
    }

    /**
     * List Laporan untuk Manager (untuk validasi)
     */
    public function managerReportList(Request $request)
    {
        $user = Auth::user();
        $tanggal = $request->input('tanggal', now()->toDateString());
        $branchFilter = $request->input('branch_id');
        $shiftFilter = $request->input('shift');
        $validasiFilter = $request->input('validasi');
        $perPage = (int) $request->input('per_page', 25);

        // 🔥 SUPERADMIN: Lihat semua cabang
        // 🔥 MANAGER: Hanya cabang yang dia manage
        if ($user->hasRole('superadmin')) {
            $managedBranches = Branch::all();
        } else {
            $managedBranches = $user->managedBranches;
        }

        $managedBranchIds = $managedBranches->pluck('id');

        // Query laporan
        $query = DailyReport::whereIn('branch_id', $managedBranchIds)
            ->whereDate('tanggal', $tanggal);

        // Filter cabang
        if ($branchFilter) {
            $query->where('branch_id', $branchFilter);
        }

        // Filter shift
        if ($shiftFilter) {
            $query->where('shift', $shiftFilter);
        }

        // Filter validasi
        if ($validasiFilter !== null) {
            $query->where('validasi_manager', $validasiFilter === '1');
        }

        $reports = $query->with(['branch', 'user', 'photos'])
            ->orderBy('validasi_manager') // Belum validasi di atas
            ->orderBy('branch_id')
            ->orderBy('shift')
            ->paginate($perPage)
            ->withQueryString();

        // 🔥 STATISTIK BERDASARKAN FILTER
        $statsQuery = DailyReport::whereIn('branch_id', $managedBranchIds)
            ->whereDate('tanggal', $tanggal);

        // Apply same filters as main query
        if ($branchFilter) {
            $statsQuery->where('branch_id', $branchFilter);
        }
        if ($shiftFilter) {
            $statsQuery->where('shift', $shiftFilter);
        }
        if ($validasiFilter !== null) {
            $statsQuery->where('validasi_manager', $validasiFilter === '1');
        }

        $stats = [
            'total_laporan' => (clone $statsQuery)->count(),
            'sudah_validasi' => (clone $statsQuery)->where('validasi_manager', true)->count(),
            'belum_validasi' => (clone $statsQuery)->where('validasi_manager', false)->count(),

            // 🔥 TOTAL PENCAIRAN
            'total_pencairan_barang' => (clone $statsQuery)->sum('pencairan_jumlah_barang'),
            'total_pencairan_nominal' => (clone $statsQuery)->sum('pencairan_nominal'),

            // 🔥 TOTAL PELUNASAN
            'total_pelunasan_barang' => (clone $statsQuery)->sum('pelunasan_jumlah_barang'),
            'total_pelunasan_nominal' => (clone $statsQuery)->sum('pelunasan_nominal'),
        ];
        $stokAkhirPerCabang = [];
        foreach ($managedBranches as $branch) {
            $lastReport = DailyReport::where('branch_id', $branch->id)
                ->whereDate('tanggal', '<=', $tanggal)
                ->orderBy('tanggal', 'desc')
                ->orderBy('shift', 'desc')
                ->first();

            $stokAkhirPerCabang[$branch->id] = [
                'jumlah_barang' => $lastReport->final_jumlah_barang ?? 0,
                'nominal' => $lastReport->final_nominal ?? 0,
            ];
        }

        return view('daily-reports.manager.reports', compact(
            'reports',
            'tanggal',
            'managedBranches',
            'stats',
            'stokAkhirPerCabang'
        ));
    }

    /**
     * Validasi Laporan
     * 🔥 Manager hanya bisa validasi laporan dari cabang yang dia kelola
     * 🔥 Superadmin bisa validasi semua
     */
    public function validate(DailyReport $dailyReport)
    {
        $user = Auth::user();

        // 🔥 CEK PERMISSION: Manager hanya bisa validasi cabang yang dia kelola
        if (!$user->hasRole('superadmin')) {
            $managedBranchIds = $user->managedBranches->pluck('id');

            if (!$managedBranchIds->contains($dailyReport->branch_id)) {
                abort(403, 'Anda tidak memiliki akses untuk validasi laporan cabang ini.');
            }
        }

        $dailyReport->update([
            'validasi_manager' => true,
            'validated_by' => $user->id,
            'validated_at' => now(),
        ]);

        return back()->with('success', 'Laporan berhasil divalidasi!');
    }
}
