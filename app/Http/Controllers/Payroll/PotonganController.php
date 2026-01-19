<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Potongan;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\BranchUser;
use Carbon\Carbon;

class PotonganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
  public function index(Request $request, Branch $branch)
    {
        $bulan = $request->input('bulan', Carbon::now()->month);
        $tahun = $request->input('tahun', Carbon::now()->year);
        
        // Get users yang punya potongan di periode ini
        $users = $branch->userAssignments()
                        ->with([
                            'user.roles',
                            'potongans' => function($query) use ($bulan, $tahun) {
                                $query->where('bulan', $bulan)
                                      ->where('tahun', $tahun);
                            }
                        ])
                        ->whereHas('potongans', function($query) use ($bulan, $tahun) {
                            // FILTER: hanya yang punya potongan di periode ini
                            $query->where('bulan', $bulan)
                                  ->where('tahun', $tahun);
                        })
                        ->get()
                        ->map(function($branchUser) use ($bulan, $tahun) {
                            // Hitung total potongan & tambahan
                            $potongans = $branchUser->potongans
                                ->where('bulan', $bulan)
                                ->where('tahun', $tahun);
                            
                            $branchUser->total_potongan = $potongans->where('jenis', 'potongan')->sum('amount');
                            $branchUser->total_tambahan = $potongans->where('jenis', 'tambahan')->sum('amount');
                            $branchUser->jumlah_item_potongan = $potongans->count();
                            
                            return $branchUser;
                        });
        
        // Statistics
        $totalPotongan = $users->sum('total_potongan');
        $totalTambahan = $users->sum('total_tambahan');
        $totalItems = $users->sum('jumlah_item_potongan');
        
        $userWithPotongan = $users->count();
        $totalUserDiCabang = $branch->userAssignments()->count();
        $userWithoutPotongan = $totalUserDiCabang - $userWithPotongan;
        
        return view('payroll.potongan.index', compact(
            'branch', 
            'users', 
            'bulan', 
            'tahun', 
            'totalPotongan',
            'totalTambahan',
            'totalItems',
            'userWithPotongan',
            'userWithoutPotongan'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, Branch $branch)
    {
        $bulan = $request->input('bulan', Carbon::now()->month);
        $tahun = $request->input('tahun', Carbon::now()->year);
        $branchUserId = $request->input('branch_user_id');
        
        // Get specific user atau semua user di cabang
        if ($branchUserId) {
            $branchUser = BranchUser::with(['user.roles', 'gajihPokok'])->findOrFail($branchUserId);
            $users = collect([$branchUser]);
        } else {
            $users = $branch->userAssignments()
                           ->with(['user.roles', 'gajihPokok'])
                           ->get();
        }
        
        return view('payroll.potongan.create', compact('branch', 'users', 'bulan', 'tahun', 'branchUserId'));
    }

    public function store(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'branch_user_id' => 'required',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020',
            'tanggal' => 'required|date',
            'divisi' => 'required|string|max:255',
            'keterangan' => 'required|string|max:500',
            'jenis' => 'required|in:potongan,tambahan',
            'amount' => 'required|numeric|min:0',
        ]);

        Potongan::create($validated);

        return redirect()->route('potongan.index', $branch)
                        ->with('success', 'Data berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Potongan $potongan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Potongan $potongan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Potongan $potongan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
   
    public function destroy(Potongan $potongan)
    {
        $branchId = $potongan->branchUser->branch_id;
        $bulan = $potongan->bulan;
        $tahun = $potongan->tahun;
        
        $potongan->delete();
        
        return redirect()->route('potongan.index', ['branch' => $branchId, 'bulan' => $bulan, 'tahun' => $tahun])
                        ->with('success', 'Data berhasil dihapus!');
    }
}
