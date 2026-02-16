<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\GajihPokok;
use App\Models\Potongan;
use App\Models\Presensi;
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


    public function show()
    {
        // Ambil user yang sedang login
        $currentUser = auth()->user();

        // Cek apakah ada parameter bulan dan tahun dari request
        $requestedBulan = request('bulan');
        $requestedTahun = request('tahun');

        // Jika ada parameter bulan dan tahun, ambil data untuk periode tersebut
        if ($requestedBulan && $requestedTahun) {
            $gajihPokok = GajihPokok::with([
                'branchUser' => function ($query) {
                    $query->with(['user.roles', 'branch']);
                }
            ])
                ->where('user_id', $currentUser->id)
                ->where('bulan', $requestedBulan)
                ->where('tahun', $requestedTahun)
                ->first();

            // Jika tidak ditemukan untuk periode tersebut
            if (!$gajihPokok) {
                return redirect()->back()->with('error', 'Data gaji untuk periode tersebut tidak ditemukan!');
            }
        } else {
            // Jika tidak ada parameter, ambil data gaji terbaru
            $gajihPokok = GajihPokok::with([
                'branchUser' => function ($query) {
                    $query->with(['user.roles', 'branch']);
                }
            ])
                ->where('user_id', $currentUser->id)
                ->orderBy('tahun', 'desc')
                ->orderBy('bulan', 'desc')
                ->first();

            // Jika tidak ada data gaji sama sekali
            if (!$gajihPokok) {
                return redirect()->back()->with('error', 'Data gaji Anda belum tersedia!');
            }
        }

        // Pastikan branchUser ada
        if (!$gajihPokok->branchUser) {
            return redirect()->back()->with('error', 'Data branch user tidak ditemukan!');
        }

        // Ambil data presensi
        $presensis = Presensi::forUser($gajihPokok->user_id)
            ->forMonth($gajihPokok->bulan, $gajihPokok->tahun)
            ->checkIn()
            ->orderBy('tanggal', 'asc')
            ->get();

        // Hitung potongan keterlambatan
        $potonganFlat = 15000;
        $dataPotonganTerlambat = [];
        $totalPotonganTerlambat = 0;

        foreach ($presensis as $presensi) {
            $hitungan = $presensi->hitungPotonganTerlambat($potonganFlat);

            if ($hitungan['potongan'] > 0) {
                $dataPotonganTerlambat[] = [
                    'tanggal' => $presensi->tanggal,
                    'jam_check_in' => $hitungan['jam_check_in'],
                    'menit_terlambat' => $hitungan['menit_terlambat'],
                    'potongan' => $hitungan['potongan'],
                    'keterangan' => $presensi->keterangan
                        ?? 'Terlambat ' . $hitungan['menit_terlambat'] . ' menit (Potongan Flat)',
                ];

                $totalPotonganTerlambat += $hitungan['potongan'];
            }
        }

        // Ambil data potongan & tambahan dari model Potongan
        $potongans = Potongan::where('branch_user_id', $gajihPokok->branchUser->id)
            ->where('bulan', $gajihPokok->bulan)
            ->where('tahun', $gajihPokok->tahun)
            ->orderBy('tanggal', 'asc')
            ->get();

        // Hitung total potongan & tambahan
        $totalPotonganLain = $potongans->where('jenis', 'potongan')->sum('amount');
        $totalTambahan = $potongans->where('jenis', 'tambahan')->sum('amount');
        $totalPotongan = $totalPotonganTerlambat + $totalPotonganLain;

        // Hitung gaji
        $gajiKotor = $gajihPokok->total_gaji_kotor;
        $gajiBersih = $gajiKotor + $totalTambahan - $totalPotongan;

        // Get riwayat gaji pokok (6 bulan terakhir) user yang login
        $riwayatGaji = GajihPokok::where('user_id', $currentUser->id)
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->take(6)
            ->get();

        return view('payroll.show', compact(
            'gajihPokok',
            'dataPotonganTerlambat',
            'totalPotonganTerlambat',
            'potongans',
            'totalPotonganLain',
            'totalPotongan',
            'totalTambahan',
            'gajiKotor',
            'gajiBersih',
            'riwayatGaji'
        ));
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
