<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\GajihPokok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayrollController extends Controller
{
    // public function index()
    // {
    //     $branches = Branch::latest()->paginate(10);

    //     return view('payroll.index', compact('branches'));
    // }
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $status  = $request->get('status');
        $search  = $request->get('search');

        $branches = Branch::query()
            ->when($status !== null && $status !== '', function ($query) use ($status) {
                $query->where('is_active', $status);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            })
            ->orderBy('code')
            ->paginate($perPage)
            ->withQueryString();

        return view('payroll.index', compact(
            'branches',
            'perPage',
            'status',
            'search'
        ));
    }

    public function show(GajihPokok $gajihPokok)
    {
        $gajihPokok->load([
            'branchUser.user.roles',
            'branchUser.branch'
        ]);

        return view('payroll.gajih_pokok.show', compact('gajihPokok'));
    }

    public function payrollsFo()
    {
        $user = Auth::user();

        $branchUser = $user->branchAssignments()->first();

        if (!$branchUser) {
            return view('dashboard-no-salary')->with('message', 'Anda belum terdaftar di cabang manapun.');
        }

        // Get bulan dan tahun dari request, default ke bulan ini
        $bulan = request('bulan', now()->month);
        $tahun = request('tahun', now()->year);

        $gajihPokok = \App\Models\GajihPokok::with([
            'branchUser' => function ($query) {
                $query->with(['user.roles', 'branch']);
            }
        ])
            ->where('branch_user_id', $branchUser->id)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->first();

        // Jika tidak ada data untuk bulan yang dipilih, ambil data terakhir
        if (!$gajihPokok) {
            $gajihPokok = \App\Models\GajihPokok::with([
                'branchUser' => function ($query) {
                    $query->with(['user.roles', 'branch']);
                }
            ])
                ->where('branch_user_id', $branchUser->id)
                ->orderBy('tahun', 'desc')
                ->orderBy('bulan', 'desc')
                ->first();
        }

        // Jika masih tidak ada data sama sekali
        if (!$gajihPokok) {
            return view('dashboard-no-salary')->with('message', 'Belum ada data gaji untuk Anda.');
        }

        // Get potongan & tambahan
        $potongans = $branchUser->potongans()
            ->where('bulan', $gajihPokok->bulan)
            ->where('tahun', $gajihPokok->tahun)
            ->orderBy('tanggal', 'asc')
            ->get();

        // Hitung total potongan & tambahan
        $totalPotongan = $potongans->where('jenis', 'potongan')->sum('amount');
        $totalTambahan = $potongans->where('jenis', 'tambahan')->sum('amount');

        // Hitung gaji
        $gajiKotor = $gajihPokok->total_gaji_kotor;
        $gajiBersih = $gajiKotor + $totalTambahan - $totalPotongan;

        // Get riwayat gaji pokok (6 bulan terakhir untuk lebih banyak pilihan)
        $riwayatGaji = \App\Models\GajihPokok::where('branch_user_id', $branchUser->id)
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->take(6)
            ->get();

        return view('slip-gajih-fo', compact(
            'gajihPokok',
            'potongans',
            'totalPotongan',
            'totalTambahan',
            'gajiKotor',
            'gajiBersih',
            'riwayatGaji'
        ));
    }
}
