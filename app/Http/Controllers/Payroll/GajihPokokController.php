<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\GajihPokok;
use App\Models\BranchUser;
use Carbon\Carbon;
// use Maatwebsite\Excel\Facades\Excel;
// use App\Imports\GajihPokokImport;
// use Throwable;

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
                'gajihPokok' => function ($query) use ($bulan, $tahun) {
                    $query->where('bulan', $bulan)
                        ->where('tahun', $tahun);
                }
            ])
            ->whereHas('gajihPokok', function ($query) use ($bulan, $tahun) {
                // FILTER: hanya yang punya gaji pokok di periode ini
                $query->where('bulan', $bulan)
                    ->where('tahun', $tahun);
            })
            ->get()
            ->map(function ($branchUser) use ($bulan, $tahun) {
                // Attach current gaji pokok
                $branchUser->current_gaji_pokok = $branchUser->gajihPokok
                    ->where('bulan', $bulan)
                    ->where('tahun', $tahun)
                    ->first();
                return $branchUser;
            });

        // Statistics
        $totalGajiPokok = $users->sum(function ($user) {
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

    // public function import(Request $request)
    // {
    //     // ===============================
    //     // VALIDASI FILE
    //     // ===============================
    //     $request->validate([
    //         'file' => 'required|file|mimes:xlsx,xls|max:10240',
    //     ]);

    //     $import = new GajihPokokImport();

    //     try {
    //         Excel::import($import, $request->file('file'));
    //     } catch (Throwable $e) {
    //         // ERROR FATAL (misal file rusak)
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Gagal memproses file: ' . $e->getMessage(),
    //         ], 500);
    //     }

    //     // ===============================
    //     // AMBIL HASIL IMPORT
    //     // ===============================
    //     $successCount = $import->getSuccessCount();
    //     $failures     = $import->failures();
    //     $errorCount   = count($failures);

    //     // ===============================
    //     // JIKA ADA ERROR PARSIAL
    //     // ===============================
    //     if ($errorCount > 0) {
    //         return response()->json([
    //             'success'        => false, // penting → biar modal tidak auto-close
    //             'message'        => 'Import selesai dengan beberapa error',
    //             'inserted'       => $successCount,
    //             'error_count'    => $errorCount,
    //             'errors'         => collect($failures)->map(function ($failure) {
    //                 return [
    //                     'row'     => $failure->row(),
    //                     'column'  => $failure->attribute(),
    //                     'message' => implode(', ', $failure->errors()),
    //                 ];
    //             })->values(),
    //         ], 200);
    //     }

    //     // ===============================
    //     // JIKA SEMUA BERHASIL
    //     // ===============================
    //     return response()->json([
    //         'success'  => true,
    //         'message'  => "Import gaji berhasil ({$successCount} data)",
    //         'inserted' => $successCount,
    //     ], 200);
    // }

    public function destroy(GajihPokok $gajiPokok)
    {
        $gajiPokok->delete();
        return back()->with('success', 'Gaji pokok berhasil dihapus!');
    }
}
