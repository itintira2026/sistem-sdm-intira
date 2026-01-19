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
        $bulan = $request->input('bulan', Carbon::now()->month);
        $tahun = $request->input('tahun', Carbon::now()->year);
        
        // Get users yang PUNYA gaji pokok di periode ini
        $users = $branch->userAssignments()
                        ->with([
                            'user.roles',
                            'gajihPokok' => function($query) use ($bulan, $tahun) {
                                $query->where('bulan', $bulan)
                                      ->where('tahun', $tahun);
                            }
                        ])
                        ->whereHas('gajihPokok', function($query) use ($bulan, $tahun) {
                            // FILTER: hanya yang punya gaji pokok di periode ini
                            $query->where('bulan', $bulan)
                                  ->where('tahun', $tahun);
                        })
                        ->get()
                        ->map(function($branchUser) use ($bulan, $tahun) {
                            // Attach current gaji pokok
                            $branchUser->current_gaji_pokok = $branchUser->gajihPokok
                                ->where('bulan', $bulan)
                                ->where('tahun', $tahun)
                                ->first();
                            return $branchUser;
                        });
        
        // Statistics
        $totalGajiPokok = $users->sum(function($user) {
            return $user->current_gaji_pokok ? $user->current_gaji_pokok->amount : 0;
        });
        
        $userWithGaji = $users->count(); // Semua user di sini pasti punya gaji
        
        // Total user di cabang (termasuk yang belum ada gaji)
        $totalUserDiCabang = $branch->userAssignments()->count();
        $userWithoutGaji = $totalUserDiCabang - $userWithGaji;
        
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

public function show(GajihPokok $gajihPokok)
    {
         $gajihPokok->load([
        'branchUser.user.roles',
        'branchUser.branch'
    ]);
    
    return view('payroll.gajih_pokok.show', compact('gajihPokok'));

    }

    public function destroy(GajihPokok $gajiPokok)
    {
        $gajiPokok->delete();
        return back()->with('success', 'Gaji pokok berhasil dihapus!');
    }
}
