<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\GajihPokok;
use App\Models\BranchUser;
use Carbon\Carbon;

class GajihPokokController extends Controller
{
    /**
     * Show detail gaji pokok per cabang
     */

    public function detail(Request $request, Branch $branch)
    {
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        /**
         * Ambil SEMUA user AKTIF di cabang ini
         */
        $users = $branch->userAssignments()
            ->whereHas('user', function ($q) {
                $q->where('is_active', true);
            })
            ->with([
                'user.roles',
                'user.gajihPokoks' => function ($q) use ($bulan, $tahun) {
                    $q->where('bulan', $bulan)
                        ->where('tahun', $tahun);
                }
            ])
            ->get()
            ->map(function ($branchUser) {

                // ambil gaji dari USER (bukan branch_user)
                $gaji = $branchUser->user->gajihPokoks->first();

                // inject supaya VIEW TIDAK PERLU DIUBAH
                $branchUser->current_gaji_pokok = $gaji;

                return $branchUser;
            });

        /**
         * Statistik
         */
        $totalGajiPokok = $users->sum(
            fn($bu) =>
            $bu->current_gaji_pokok?->amount ?? 0
        );

        $userWithGaji = $users->filter(
            fn($bu) =>
            !is_null($bu->current_gaji_pokok)
        )->count();

        $totalUserDiCabang = $branch->userAssignments()
            ->whereHas('user', fn($q) => $q->where('is_active', true))
            ->count();

        $userWithoutGaji = $totalUserDiCabang - $userWithGaji;

        // dd($branch, $users, $bulan, $tahun, $totalGajiPokok, $userWithGaji, $userWithoutGaji);

        return view('payroll.gajih_pokok.detail', compact(
            'branch',
            'users',
            'bulan',
            'tahun',
            'totalGajiPokok',
            'userWithGaji',
            'userWithoutGaji'
        ));
    }


    // public function detail(Request $request, Branch $branch)
    // {
    //     $bulan = (int) $request->input('bulan', Carbon::now()->month);
    //     $tahun = (int) $request->input('tahun', Carbon::now()->year);

    //     /**
    //      * Ambil SEMUA user aktif di cabang ini
    //      */
    //     $users = $branch->userAssignments()
    //         // ->where('is_active', 'true') // penting
    //         ->whereHas('user', fn ($q) => $q->where('is_active', true))
    //         ->with([
    //             'user.roles',
    //             'user.gajihPokoks' => function ($query) use ($bulan, $tahun) {
    //                 $query->where('bulan', $bulan)
    //                     ->where('tahun', $tahun);
    //             }
    //         ])
    //         ->get()
    //         ->map(function ($branchUser) use ($bulan, $tahun) {

    //             // Ambil gaji dari USER (bukan branch_user)
    //             $gaji = optional($branchUser->user)
    //                 ->gajihPokoks
    //                 ->first();

    //             // Inject ke object supaya view tidak berubah
    //             $branchUser->current_gaji_pokok = $gaji;

    //             return $branchUser;
    //         });

    //     /**
    //      * Statistik
    //      */
    //     $totalGajiPokok = $users->sum(function ($branchUser) {
    //         return $branchUser->current_gaji_pokok
    //             ? $branchUser->current_gaji_pokok->amount
    //             : 0;
    //     });

    //     $userWithGaji = $users->filter(function ($branchUser) {
    //         return !is_null($branchUser->current_gaji_pokok);
    //     })->count();

    //     $totalUserDiCabang = $branch->userAssignments()
    //         ->where('is_active', 'true')
    //         ->count();

    //     $userWithoutGaji = $totalUserDiCabang - $userWithGaji;

    //     // $users = $branch->userAssignments()
    //     //     ->with('user')
    //     //     ->get();

    //     // dd($users);

    //     dd($branch, $users, $bulan, $tahun, $totalGajiPokok, $userWithGaji, $userWithoutGaji);

    //     return view('payroll.gajih_pokok.detail', compact(
    //         'branch',
    //         'users',
    //         'bulan',
    //         'tahun',
    //         'totalGajiPokok',
    //         'userWithGaji',
    //         'userWithoutGaji'
    //     ));
    // }


    // public function detail(Request $request, Branch $branch)
    // {
    //     $bulan = $request->input('bulan', Carbon::now()->month);
    //     $tahun = $request->input('tahun', Carbon::now()->year);

    //     // Get users yang PUNYA gaji pokok di periode ini
    //     $users = $branch->userAssignments()
    //         ->with([
    //             'user.roles',
    //             'gajihPokok' => function ($query) use ($bulan, $tahun) {
    //                 $query->where('bulan', $bulan)
    //                     ->where('tahun', $tahun);
    //             }
    //         ])
    //         ->whereHas('gajihPokok', function ($query) use ($bulan, $tahun) {
    //             // FILTER: hanya yang punya gaji pokok di periode ini
    //             $query->where('bulan', $bulan)
    //                 ->where('tahun', $tahun);
    //         })
    //         ->get()
    //         ->map(function ($branchUser) use ($bulan, $tahun) {
    //             // Attach current gaji pokok
    //             $branchUser->current_gaji_pokok = $branchUser->gajihPokok
    //                 ->where('bulan', $bulan)
    //                 ->where('tahun', $tahun)
    //                 ->first();
    //             return $branchUser;
    //         });

    //     // Statistics
    //     $totalGajiPokok = $users->sum(function ($user) {
    //         return $user->current_gaji_pokok ? $user->current_gaji_pokok->amount : 0;
    //     });

    //     $userWithGaji = $users->count(); // Semua user di sini pasti punya gaji

    //     // Total user di cabang (termasuk yang belum ada gaji)
    //     $totalUserDiCabang = $branch->userAssignments()->count();
    //     $userWithoutGaji = $totalUserDiCabang - $userWithGaji;

    //     return view('payroll.gajih_pokok.detail', compact(
    //         'branch',
    //         'users',
    //         'bulan',
    //         'tahun',
    //         'totalGajiPokok',
    //         'userWithGaji',
    //         'userWithoutGaji'
    //     ));
    // }


    public function create(Request $request, Branch $branch)
    {
        // $bulan = $request->input('bulan', Carbon::now()->month);
        // $tahun = $request->input('tahun', Carbon::now()->year);
        // $branchUserId = $request->input('branch_user_id');

        // // Get specific user atau semua user di cabang
        // if ($branchUserId) {
        //     $branchUser = BranchUser::with(['user.roles'])->findOrFail($branchUserId);
        //     $users = collect([$branchUser]);
        // } else {
        //     // Get users yang belum punya gaji pokok di periode ini
        //     $users = $branch->userAssignments()
        //         ->with(['user.roles'])
        //         ->whereDoesntHave('gajihPokok', function ($query) use ($bulan, $tahun) {
        //             $query->where('bulan', $bulan)
        //                 ->where('tahun', $tahun);
        //         })
        //         ->get();
        // }

        return view('payroll.gajih_pokok.create', compact('branch', 'users', 'bulan', 'tahun', 'branchUserId'));
    }

    /**
     * Store gaji pokok
     */
    public function store(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'branch_user_id' => 'required',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020',
            'amount' => 'required|numeric|min:0',
            'tunjangan_makan' => 'nullable|numeric|min:0',
            'tunjangan_transportasi' => 'nullable|numeric|min:0',
            'tunjangan_jabatan' => 'nullable|numeric|min:0',
            'tunjangan_komunikasi' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string|max:500',
        ]);

        // Set default 0 untuk tunjangan yang null
        $validated['tunjangan_makan'] = $validated['tunjangan_makan'] ?? 0;
        $validated['tunjangan_transportasi'] = $validated['tunjangan_transportasi'] ?? 0;
        $validated['tunjangan_jabatan'] = $validated['tunjangan_jabatan'] ?? 0;
        $validated['tunjangan_komunikasi'] = $validated['tunjangan_komunikasi'] ?? 0;

        // Check jika sudah ada gaji pokok untuk periode ini
        $exists = GajihPokok::where('branch_user_id', $validated['branch_user_id'])
            ->where('bulan', $validated['bulan'])
            ->where('tahun', $validated['tahun'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Gaji pokok untuk periode ini sudah ada!')->withInput();
        }

        GajihPokok::create($validated);

        return redirect()->route('gaji-pokok.detail', ['branch' => $branch->id, 'bulan' => $validated['bulan'], 'tahun' => $validated['tahun']])
            ->with('success', 'Gaji pokok berhasil ditambahkan!');
    }


    public function show($id)
    {
        // Find gaji pokok dengan eager loading yang benar
        $gajihPokok = GajihPokok::with([
            'branchUser' => function ($query) {
                $query->with(['user.roles', 'branch']);
            }
        ])->findOrFail($id);

        // Pastikan branchUser ada
        if (!$gajihPokok->branchUser) {
            return redirect()->back()->with('error', 'Data branch user tidak ditemukan!');
        }

        // Get potongan & tambahan
        $potongans = $gajihPokok->branchUser->potongans()
            ->where('bulan', $gajihPokok->bulan)
            ->where('tahun', $gajihPokok->tahun)
            ->orderBy('tanggal', 'asc')
            ->get();

        // Hitung total potongan & tambahan
        $totalPotongan = $potongans->where('jenis', 'potongan')->sum('amount');
        $totalTambahan = $potongans->where('jenis', 'tambahan')->sum('amount');

        // Hitung gaji
        $gajiKotor = $gajihPokok->total_gaji_kotor; // Gaji pokok + semua tunjangan
        $gajiBersih = $gajiKotor + $totalTambahan - $totalPotongan;

        // Get riwayat gaji pokok (3 bulan terakhir)
        $riwayatGaji = GajihPokok::where('branch_user_id', $gajihPokok->branch_user_id)
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->take(3)
            ->get();

        return view('payroll.gajih_pokok.show', compact(
            'gajihPokok',
            'potongans',
            'totalPotongan',
            'totalTambahan',
            'gajiKotor',
            'gajiBersih',
            'riwayatGaji'
        ));
    }

    public function destroy(GajihPokok $gajiPokok)
    {
        $gajiPokok->delete();
        return back()->with('success', 'Gaji pokok berhasil dihapus!');
    }
}
