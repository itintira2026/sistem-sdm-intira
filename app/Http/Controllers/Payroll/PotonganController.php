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

    // Ambil user_id semua karyawan di cabang ini dulu
    $userIds = $branch->userAssignments()->pluck('user_id');

    // Query potongan berdasarkan user_id yang ada di cabang
    $potongans = Potongan::where('bulan', $bulan)
                        ->where('tahun', $tahun)
                        ->whereIn('user_id', $userIds)  // ← lebih aman & sesuai model
                        ->with(['branchUser.user.roles'])
                        ->orderBy('tanggal', 'desc')
                        ->get();

    // Statistics
    $totalPotongan       = $potongans->where('jenis', 'potongan')->sum('amount');
    $totalTambahan       = $potongans->where('jenis', 'tambahan')->sum('amount');
    $totalItems          = $potongans->count();
    $userWithPotongan    = $potongans->pluck('user_id')->unique()->count();
    $totalUserDiCabang   = $branch->userAssignments()->count();
    $userWithoutPotongan = $totalUserDiCabang - $userWithPotongan;

    return view('payroll.potongan.index', compact(
        'branch',
        'potongans',
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
    $bulan        = $request->input('bulan', Carbon::now()->month);
    $tahun        = $request->input('tahun', Carbon::now()->year);
    $branchUserId = $request->input('branch_user_id'); // ini adalah BranchUser->id dari index

    if ($branchUserId) {
        $branchUser = BranchUser::with(['user.roles', 'gajihPokok'])
                        ->findOrFail($branchUserId); // cari by BranchUser->id (PK), benar
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
        'user_id'    => 'required|exists:users,id',  // ← sesuai name di blade
        'bulan'      => 'required|integer|min:1|max:12',
        'tahun'      => 'required|integer|min:2020',
        'tanggal'    => 'required|date',
        'divisi'     => 'required|string|max:255',
        'keterangan' => 'required|string|max:500',
        'jenis'      => 'required|in:potongan,tambahan',
        'amount'     => 'required|numeric|min:0',
    ]);

    // Langsung create, user_id sudah ada di $validated sesuai $fillable model
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
   
 public function destroy(Branch $branch, Potongan $potongan)
{
    if ($potongan->created_at->diffInDays(now()) > 7) {
        return redirect()->back()
                        ->with('error', 'Data tidak dapat dihapus karena sudah lebih dari 7 hari!');
    }

    $bulan = $potongan->bulan;
    $tahun = $potongan->tahun;

    $potongan->delete();

    return redirect()->route('potongan.index', [
                            'branch' => $branch->id,
                            'bulan'  => $bulan,
                            'tahun'  => $tahun,
                        ])
                    ->with('success', 'Data berhasil dihapus!');
}
}