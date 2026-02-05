<?php

namespace App\Http\Controllers;

use App\Models\ThreeHourReportManager;
use Illuminate\Http\Request;
use App\Models\Revenue;
use App\Models\BranchUser;
use App\Imports\RevenueImport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ThreeHourReportManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('daily-reports.three-hour-manager.index');
    }

    /**
     * Show the form for creating a new resource.
     */
 public function create()
{
    $user = Auth::user();
    $branchUser = BranchUser::where('user_id', $user->id)->first();

    if (!$branchUser) {
        return redirect()->route('daily-reports.3hour-manager.index')
            ->with('error', 'Anda belum terdaftar di cabang manapun');
    }

    $today = Carbon::today();
    $report = ThreeHourReportManager::where('branch_user_id', $branchUser->id)
        ->whereDate('created_at', $today)
        ->first();

    $stats = [
        'total_today' => 0,
        'target' => 3,
        'report_12' => false,
        'report_16' => false,
        'report_20' => false,
    ];

    if ($report) {
        $stats['report_12'] = !is_null($report->report_12_at);
        $stats['report_16'] = !is_null($report->report_16_at);
        $stats['report_20'] = !is_null($report->report_20_at);
        $stats['total_today'] = collect([
            $stats['report_12'],
            $stats['report_16'],
            $stats['report_20']
        ])->filter()->count();
    }


    return view('daily-reports.three-hour-manager.create', compact('stats'));
}

//    public function store(Request $request)
// {
//     $request->validate([
//         'time_slot'       => ['required', 'in:12:00,16:00,20:00'],
//         'keterangan'      => ['nullable', 'string'],
//         'file_revenue'    => ['nullable', 'file', 'mimes:xlsx,xls,csv', 'max:2048'],
//     ]);

//     DB::beginTransaction();
    
//     try {
//         // ===============================
//         // AMBIL BRANCH USER AUTH
//         // ===============================
//         $user = Auth::user();
//         $branchUser = BranchUser::where('user_id', $user->id)
//             ->first();

//         if (!$branchUser) {
//             return redirect()->back()->with('error', 'Anda belum terdaftar di cabang manapun');
//         }

//         // ===============================
//         // CEK LAPORAN HARI INI
//         // ===============================
//         $today = Carbon::today();
//         $report = ThreeHourReportManager::where('branch_user_id', $branchUser->id)
//             ->whereDate('created_at', $today)
//             ->first();

//         // Jika belum ada laporan, buat baru
//         if (!$report) {
//             $report = ThreeHourReportManager::create([
//                 'branch_user_id' => $branchUser->id,
//                 'keterangan'     => $request->keterangan,
//             ]);
//         }

//         // ===============================
//         // UPDATE TIMESTAMP SESUAI TIME SLOT
//         // ===============================
//         $timeSlot = str_replace(':', '_', $request->time_slot);
//         $columnName = 'report_' . explode('_', $timeSlot)[0] . '_at';

//         // Cek apakah slot waktu ini sudah diisi
//         if ($report->$columnName) {
//             return redirect()->back()->with('error', "Laporan untuk jam {$request->time_slot} sudah diinput!");
//         }

//         // Update timestamp
//         $report->update([
//             $columnName => now(),
//             'keterangan' => $request->keterangan ?? $report->keterangan,
//         ]);

//         // ===============================
//         // IMPORT REVENUE PELUNASAN
//         // ===============================
//         $successCount = 0;
        
//         if ($request->hasFile('file_revenue')) {
//             $import = new RevenueImport($report->id);
            
//             Excel::import($import, $request->file('file_revenue'));
            
//             $successCount = $import->getSuccessCount();
            
//             // Cek jika ada error
//             $failures = $import->failures();
//             if ($failures->isNotEmpty()) {
//                 $errorMessages = [];
//                 foreach ($failures as $failure) {
//                     $errorMessages[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
//                 }
                
//                 DB::rollBack();
//                 return redirect()->back()
//                     ->with('error', 'Import gagal: ' . implode(' | ', $errorMessages))
//                     ->withInput();
//             }
//         }

//         DB::commit();

//         $message = "Laporan jam {$request->time_slot} berhasil disimpan!";
//         if ($successCount > 0) {
//             $message .= " Total {$successCount} data revenue pelunasan berhasil diimport.";
//         }

//         return redirect()->route('daily-reports.3hour-manager.index')
//             ->with('success', $message);

//     } catch (\Exception $e) {
//         DB::rollBack();
        
//         return redirect()->back()
//             ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
//             ->withInput();
//     }
// }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     \Log::info('========== STORE METHOD STARTED ==========');
    //     \Log::info('Request data:', $request->all());
    //     \Log::info('Has file revenue:', [$request->hasFile('file_revenue')]);

    //     if ($request->hasFile('file_revenue')) {
    //         \Log::info('File details:', [
    //             'name' => $request->file('file_revenue')->getClientOriginalName(),
    //             'size' => $request->file('file_revenue')->getSize(),
    //             'mime' => $request->file('file_revenue')->getMimeType(),
    //         ]);
    //     }

    //     $request->validate([
    //         'time_slot'       => ['required', 'in:12:00,16:00,20:00'],
    //         'keterangan'      => ['nullable', 'string'],
    //         'file_revenue'    => ['nullable', 'file', 'mimes:xlsx,xls,csv', 'max:2048'],
    //     ]);

    //     \Log::info('Validation passed');

    //     DB::beginTransaction();

    //     try {
    //         // ===============================
    //         // AMBIL BRANCH USER AUTH
    //         // ===============================
    //         $user = Auth::user();
    //         \Log::info('Current user:', ['id' => $user->id, 'email' => $user->email]);

    //         $branchUser = BranchUser::where('user_id', $user->id)->first();

    //         if (!$branchUser) {
    //             \Log::error('BranchUser not found for user_id: ' . $user->id);
    //             return redirect()->back()->with('error', 'Anda belum terdaftar di cabang manapun');
    //         }

    //         \Log::info('BranchUser found:', ['id' => $branchUser->id, 'branch_id' => $branchUser->branch_id]);

    //         // ===============================
    //         // CEK LAPORAN HARI INI
    //         // ===============================
    //         $today = Carbon::today();
    //         \Log::info('Today date:', ['date' => $today->toDateString()]);

    //         $report = ThreeHourReportManager::where('branch_user_id', $branchUser->id)
    //             ->whereDate('created_at', $today)
    //             ->first();

    //         \Log::info('Existing report:', ['found' => $report ? 'yes' : 'no', 'id' => $report->id ?? null]);

    //         // Jika belum ada laporan, buat baru
    //         if (!$report) {
    //             \Log::info('Creating new report...');
    //             $report = ThreeHourReportManager::create([
    //                 'branch_user_id' => $branchUser->id,
    //                 'keterangan'     => $request->keterangan,
    //             ]);
    //             \Log::info('New report created:', ['id' => $report->id]);
    //         }

    //         // ===============================
    //         // UPDATE TIMESTAMP SESUAI TIME SLOT
    //         // ===============================
    //         $timeSlot = str_replace(':', '_', $request->time_slot);
    //         $columnName = 'report_' . explode('_', $timeSlot)[0] . '_at';

    //         \Log::info('Time slot processing:', [
    //             'time_slot' => $request->time_slot,
    //             'column_name' => $columnName,
    //             'current_value' => $report->$columnName,
    //         ]);

    //         // Cek apakah slot waktu ini sudah diisi
    //         if ($report->$columnName) {
    //             \Log::warning('Time slot already filled:', ['column' => $columnName]);
    //             DB::rollBack();
    //             return redirect()->back()->with('error', "Laporan untuk jam {$request->time_slot} sudah diinput!");
    //         }

    //         // Update timestamp
    //         \Log::info('Updating report timestamp...');
    //         $report->update([
    //             $columnName => now(),
    //             'keterangan' => $request->keterangan ?? $report->keterangan,
    //         ]);
    //         \Log::info('Report updated successfully');

    //         // ===============================
    //         // IMPORT REVENUE PELUNASAN
    //         // ===============================
    //         $successCount = 0;
            

    //         if ($request->hasFile('file_revenue')) {
    //             \Log::info('========== STARTING REVENUE IMPORT ==========');
    //             \Log::info('Report ID for import:', ['report_id' => $report->id]);

    //             try {
    //                 $import = new RevenueImport($report->id);
    //                 \Log::info('RevenueImport instance created');

    //                 Excel::import($import, $request->file('file_revenue'));
    //                 \Log::info('Excel::import executed');

    //                 $successCount = $import->getSuccessCount();
    //                 \Log::info('Import success count:', ['count' => $successCount]);

    //                 // Cek jika ada error
    //                 $failures = $import->failures();
    //                 \Log::info('Import failures count:', ['count' => $failures->count()]);

    //                 if ($failures->isNotEmpty()) {
    //                     $errorMessages = [];
    //                     foreach ($failures as $failure) {
    //                         $errorMsg = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
    //                         $errorMessages[] = $errorMsg;
    //                         \Log::error('Import failure:', [
    //                             'row' => $failure->row(),
    //                             'errors' => $failure->errors(),
    //                             'values' => $failure->values(),
    //                         ]);
    //                     }

    //                     DB::rollBack();
    //                     \Log::warning('Transaction rolled back due to import failures');
    //                     return redirect()->back()
    //                         ->with('error', 'Import gagal: ' . implode(' | ', $errorMessages))
    //                         ->withInput();
    //                 }

    //                 \Log::info('========== REVENUE IMPORT COMPLETED ==========');
    //             } catch (\Exception $importException) {
    //                 \Log::error('Import exception caught:', [
    //                     'message' => $importException->getMessage(),
    //                     'file' => $importException->getFile(),
    //                     'line' => $importException->getLine(),
    //                     'trace' => $importException->getTraceAsString(),
    //                 ]);
    //                 throw $importException;
    //             }
    //         } else {
    //             \Log::info('No revenue file uploaded');
    //         }

    //         DB::commit();
    //         \Log::info('Transaction committed successfully');

    //         $message = "Laporan jam {$request->time_slot} berhasil disimpan!";
    //         if ($successCount > 0) {
    //             $message .= " Total {$successCount} data revenue pelunasan berhasil diimport.";
    //         }

    //         \Log::info('Success message:', ['message' => $message]);
    //         \Log::info('========== STORE METHOD COMPLETED ==========');

    //         return redirect()->route('daily-reports.3hour-manager.index')
    //             ->with('success', $message);
    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         \Log::error('========== STORE METHOD FAILED ==========');
    //         \Log::error('Exception details:', [
    //             'message' => $e->getMessage(),
    //             'file' => $e->getFile(),
    //             'line' => $e->getLine(),
    //             'trace' => $e->getTraceAsString(),
    //         ]);

    //         return redirect()->back()
    //             ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
    //             ->withInput();
    //     }
    // }
    public function store(Request $request)
{
    Log::info('========== STORE METHOD STARTED ==========');
    Log::info('Request data:', $request->all());
    Log::info('Has file revenue:', [$request->hasFile('file_revenue')]);
    
    if ($request->hasFile('file_revenue')) {
        Log::info('File details:', [
            'name' => $request->file('file_revenue')->getClientOriginalName(),
            'size' => $request->file('file_revenue')->getSize(),
            'mime' => $request->file('file_revenue')->getMimeType(),
        ]);
    }

    $request->validate([
        'time_slot'       => ['required', 'in:12:00,16:00,20:00'],
        'keterangan'      => ['nullable', 'string'],
        'file_revenue'    => ['nullable', 'file', 'mimes:xlsx,xls,csv', 'max:2048'],
    ]);

    Log::info('Validation passed');

    DB::beginTransaction();
    
    try {
        // ===============================
        // AMBIL BRANCH USER AUTH
        // ===============================
        $user = Auth::user();
        Log::info('Current user:', ['id' => $user->id, 'email' => $user->email]);
        
        $branchUser = BranchUser::where('user_id', $user->id)->first();

        if (!$branchUser) {
            Log::error('BranchUser not found for user_id: ' . $user->id);
            DB::rollBack();
            return redirect()->back()->with('error', 'Anda belum terdaftar di cabang manapun');
        }

        Log::info('BranchUser found:', ['id' => $branchUser->id, 'branch_id' => $branchUser->branch_id]);

        // ===============================
        // CEK LAPORAN HARI INI
        // ===============================
        $today = Carbon::today();
        Log::info('Today date:', ['date' => $today->toDateString()]);
        
        $report = ThreeHourReportManager::where('branch_user_id', $branchUser->id)
            ->whereDate('created_at', $today)
            ->first();

        Log::info('Existing report:', ['found' => $report ? 'yes' : 'no', 'id' => $report->id ?? null]);

        // Jika belum ada laporan, buat baru
        if (!$report) {
            Log::info('Creating new report...');
            $report = ThreeHourReportManager::create([
                'branch_user_id' => $branchUser->id,
                'keterangan'     => $request->keterangan,
            ]);
            Log::info('New report created:', ['id' => $report->id]);
        }

        // ===============================
        // UPDATE TIMESTAMP SESUAI TIME SLOT
        // ===============================
        $timeSlot = str_replace(':', '_', $request->time_slot);
        $columnName = 'report_' . explode('_', $timeSlot)[0] . '_at';
        
        Log::info('Time slot processing:', [
            'time_slot' => $request->time_slot,
            'column_name' => $columnName,
            'current_value' => $report->$columnName,
        ]);

        // Cek apakah slot waktu ini sudah diisi
        if ($report->$columnName) {
            Log::warning('Time slot already filled:', ['column' => $columnName]);
            DB::rollBack();
            return redirect()->back()->with('error', "Laporan untuk jam {$request->time_slot} sudah diinput!");
        }

        // Update timestamp
        Log::info('Updating report timestamp...');
        $report->update([
            $columnName => now(),
            'keterangan' => $request->keterangan ?? $report->keterangan,
        ]);
        Log::info('Report updated successfully');

        // ===============================
        // IMPORT REVENUE PELUNASAN
        // ===============================
        $successCount = 0;
        $updateCount = 0;
        
        if ($request->hasFile('file_revenue')) {
            Log::info('========== STARTING REVENUE IMPORT ==========');
            Log::info('Report ID for import:', ['report_id' => $report->id]);
            
            try {
                $import = new RevenueImport($report->id);
                Log::info('RevenueImport instance created');
                
                Excel::import($import, $request->file('file_revenue'));
                Log::info('Excel::import executed');
                
                $successCount = $import->getSuccessCount();
                $updateCount = $import->getUpdateCount();
                
                Log::info('Import results:', [
                    'inserted' => $successCount,
                    'updated' => $updateCount
                ]);
                
                // Cek jika ada error
                $failures = $import->failures();
                Log::info('Import failures count:', ['count' => $failures->count()]);
                
                if ($failures->isNotEmpty()) {
                    $errorMessages = [];
                    foreach ($failures as $failure) {
                        $errorMsg = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
                        $errorMessages[] = $errorMsg;
                        Log::error('Import failure:', [
                            'row' => $failure->row(),
                            'errors' => $failure->errors(),
                            'values' => $failure->values(),
                        ]);
                    }
                    
                    DB::rollBack();
                    Log::warning('Transaction rolled back due to import failures');
                    return redirect()->back()
                        ->with('error', 'Import gagal: ' . implode(' | ', $errorMessages))
                        ->withInput();
                }
                
                Log::info('========== REVENUE IMPORT COMPLETED ==========');
            } catch (\Exception $importException) {
                Log::error('Import exception caught:', [
                    'message' => $importException->getMessage(),
                    'file' => $importException->getFile(),
                    'line' => $importException->getLine(),
                    'trace' => $importException->getTraceAsString(),
                ]);
                throw $importException;
            }
        } else {
            Log::info('No revenue file uploaded');
        }

        DB::commit();
        Log::info('Transaction committed successfully');

        // ===============================
        // BUAT SUCCESS MESSAGE
        // ===============================
        $message = "Laporan jam {$request->time_slot} berhasil disimpan!";
        
        if ($successCount > 0) {
            $message .= " {$successCount} data baru ditambahkan.";
        }
        
        if ($updateCount > 0) {
            $message .= " {$updateCount} data diupdate.";
        }

        Log::info('Success message:', ['message' => $message]);
        Log::info('========== STORE METHOD COMPLETED ==========');

        return redirect()->route('daily-reports.3hour-manager.index')
            ->with('success', $message);

    } catch (\Exception $e) {
        DB::rollBack();
        
        Log::error('========== STORE METHOD FAILED ==========');
        Log::error('Exception details:', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);
        
        return redirect()->back()
            ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
            ->withInput();
    }
}
    /**
     * Display the specified resource.
     */
    public function show(ThreeHourReportManager $threeHourReportManager)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ThreeHourReportManager $threeHourReportManager)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ThreeHourReportManager $threeHourReportManager)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ThreeHourReportManager $threeHourReportManager)
    {
        //
    }
}
