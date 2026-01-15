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
    public function index(Request $request)
    {
        $bulan = $request->input('bulan', Carbon::now()->month);
        $tahun = $request->input('tahun', Carbon::now()->year);
        $branchId = $request->input('branch_id');
        
        $query = GajihPokok::with(['branchUser.user', 'branchUser.branch'])
                          ->where('bulan', $bulan)
                          ->where('tahun', $tahun);
        
        if ($branchId) {
            $query->whereHas('branchUser', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }
        
        $gajiPokoks = $query->latest()->paginate(20);
        $branches = Branch::all();
        
        // Statistics
        $totalGaji = $query->sum('amount');
        $totalKaryawan = $query->count();
        
        return view('payroll.gajih_pokok.index', compact('gajiPokoks', 'branches', 'bulan', 'tahun', 'totalGaji', 'totalKaryawan'));
    }

    public function create(Branch $branch)
    {
        $bulanSekarang = Carbon::now()->month;
        $tahunSekarang = Carbon::now()->year;
        
        // Get users di cabang dengan gaji pokok bulan ini (jika ada)
        $users = $branch->userAssignments()
                        ->with(['user', 'gajihPokok' => function($query) use ($bulanSekarang, $tahunSekarang) {
                            $query->where('bulan', $bulanSekarang)
                                  ->where('tahun', $tahunSekarang);
                        }])
                        ->get();
        
        return view('payroll.gajih_pokok.create', compact('branch', 'users', 'bulanSekarang', 'tahunSekarang'));
    }

    /**
     * Store gaji pokok
     */
    public function store(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'branch_user_id' => 'required|exists:branch_user,id',
            'amount' => 'required|numeric|min:0',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $branchUser = BranchUser::findOrFail($validated['branch_user_id']);
        
        // Set gaji pokok (akan update jika sudah ada untuk bulan & tahun yang sama)
        $gajiPokok = $branchUser->setGajiPokok(
            $validated['amount'],
            $validated['bulan'],
            $validated['tahun'],
            $validated['keterangan']
        );

        return back()->with('success', 'Gaji pokok berhasil disimpan!');
    }

    /**
     * Bulk input gaji pokok untuk semua user di cabang
     */
    public function bulkStore(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020',
            'gaji_pokok' => 'required|array',
            'gaji_pokok.*.branch_user_id' => 'required|exists:branch_user,id',
            'gaji_pokok.*.amount' => 'required|numeric|min:0',
        ]);

        $count = 0;
        foreach ($validated['gaji_pokok'] as $data) {
            $branchUser = BranchUser::find($data['branch_user_id']);
            if ($branchUser) {
                $branchUser->setGajiPokok(
                    $data['amount'],
                    $validated['bulan'],
                    $validated['tahun']
                );
                $count++;
            }
        }

        return back()->with('success', "{$count} gaji pokok berhasil disimpan!");
    }

    /**
     * Lihat history gaji pokok
     */
    public function history(Branch $branch, BranchUser $branchUser)
    {
        $history = $branchUser->gajiPokok()
                              ->orderBy('tahun', 'desc')
                              ->orderBy('bulan', 'desc')
                              ->get();
        
        return view('gaji-pokok.history', compact('branch', 'branchUser', 'history'));
    }

    /**
     * Update gaji pokok
     */
    public function update(Request $request, GajiPokok $gajiPokok)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $gajiPokok->update($validated);

        return back()->with('success', 'Gaji pokok berhasil diupdate!');
    }

    /**
     * Delete gaji pokok
     */
    public function destroy(GajiPokok $gajiPokok)
    {
        $gajiPokok->delete();

        return back()->with('success', 'Gaji pokok berhasil dihapus!');
    }

    /**
     * Laporan gaji pokok per cabang per bulan
     */
    public function report(Request $request)
    {
        $bulan = $request->input('bulan', Carbon::now()->month);
        $tahun = $request->input('tahun', Carbon::now()->year);
        
        $branches = Branch::with(['gajiPokok' => function($query) use ($bulan, $tahun) {
            $query->where('bulan', $bulan)->where('tahun', $tahun);
        }])->get();
        
        return view('gaji-pokok.report', compact('branches', 'bulan', 'tahun'));
    }
}
