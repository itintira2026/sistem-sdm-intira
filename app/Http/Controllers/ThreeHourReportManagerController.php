<?php

namespace App\Http\Controllers;

use App\Imports\NasabahImport;
use App\Models\ThreeHourReportManager;
use Illuminate\Http\Request;
use App\Models\BranchUser;
use App\Models\Revenue;
use App\Models\Nasabah;
use App\Models\Omzet;
use App\Imports\OmzetImport;
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
        $user = Auth::user();

        // ===============================
        // AMBIL SEMUA branch_user_id MILIK AREA MANAGER
        // ===============================
        $branchUserIds = Auth::user()
            ->branchAssignments()
            ->pluck('id');

        // DEBUG (optional)
        // dd($branchUserIds);

        // ===============================
        // OMZET
        // ===============================
        $omzetHariIni = Omzet::whereIn('branch_user_id', $branchUserIds)
            ->whereDate('tanggal', today())
            ->sum('rahn');


        $omzetBulanIni = Omzet::whereIn('branch_user_id', $branchUserIds)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('rahn');


        // ===============================
        // NASABAH
        // ===============================

        $nasabahHariIni = Nasabah::whereIn('branch_user_id', $branchUserIds)
            ->whereDate('created_at', today())
            ->count();

        $nasabahBulanIni = Nasabah::whereIn('branch_user_id', $branchUserIds)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();


        // ===============================
        // REVENUE
        // ===============================

        $revenueHariIni = Revenue::whereIn('branch_user_id', $branchUserIds)
            ->whereDate('tanggal_transaksi', today())
            ->sum('jumlah_pembayaran');

        $revenueBulanIni = Revenue::whereIn('branch_user_id', $branchUserIds)
            ->whereMonth('tanggal_transaksi', now()->month)
            ->whereYear('tanggal_transaksi', now()->year)
            ->sum('jumlah_pembayaran');


        // ===============================
        // RETURN VIEW
        // ===============================

        return view('daily-reports.three-hour-manager.index', compact(
            'omzetHariIni',
            'omzetBulanIni',
            'nasabahHariIni',
            'nasabahBulanIni',
            'revenueHariIni',
            'revenueBulanIni'
        ));
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

//     public function store(Request $request)
// {
//     Log::info('========== STORE METHOD STARTED ==========');
//     Log::info('Request data:', $request->all());
//     Log::info('Has file revenue:', [$request->hasFile('file_revenue')]);
    
//     if ($request->hasFile('file_revenue')) {
//         Log::info('File details:', [
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

//     Log::info('Validation passed');

//     DB::beginTransaction();
    
//     try {
//         // ===============================
//         // AMBIL BRANCH USER AUTH
//         // ===============================
//         $user = Auth::user();
//         Log::info('Current user:', ['id' => $user->id, 'email' => $user->email]);
        
//         $branchUser = BranchUser::where('user_id', $user->id)->first();

//         if (!$branchUser) {
//             Log::error('BranchUser not found for user_id: ' . $user->id);
//             DB::rollBack();
//             return redirect()->back()->with('error', 'Anda belum terdaftar di cabang manapun');
//         }

//         Log::info('BranchUser found:', ['id' => $branchUser->id, 'branch_id' => $branchUser->branch_id]);

//         // ===============================
//         // CEK LAPORAN HARI INI
//         // ===============================
//         $today = Carbon::today();
//         Log::info('Today date:', ['date' => $today->toDateString()]);
        
//         $report = ThreeHourReportManager::where('branch_user_id', $branchUser->id)
//             ->whereDate('created_at', $today)
//             ->first();

//         Log::info('Existing report:', ['found' => $report ? 'yes' : 'no', 'id' => $report->id ?? null]);

//         // Jika belum ada laporan, buat baru
//         if (!$report) {
//             Log::info('Creating new report...');
//             $report = ThreeHourReportManager::create([
//                 'branch_user_id' => $branchUser->id,
//                 'keterangan'     => $request->keterangan,
//             ]);
//             Log::info('New report created:', ['id' => $report->id]);
//         }

//         // ===============================
//         // UPDATE TIMESTAMP SESUAI TIME SLOT
//         // ===============================
//         $timeSlot = str_replace(':', '_', $request->time_slot);
//         $columnName = 'report_' . explode('_', $timeSlot)[0] . '_at';
        
//         Log::info('Time slot processing:', [
//             'time_slot' => $request->time_slot,
//             'column_name' => $columnName,
//             'current_value' => $report->$columnName,
//         ]);

//         // Cek apakah slot waktu ini sudah diisi
//         if ($report->$columnName) {
//             Log::warning('Time slot already filled:', ['column' => $columnName]);
//             DB::rollBack();
//             return redirect()->back()->with('error', "Laporan untuk jam {$request->time_slot} sudah diinput!");
//         }

//         // Update timestamp
//         Log::info('Updating report timestamp...');
//         $report->update([
//             $columnName => now(),
//             'keterangan' => $request->keterangan ?? $report->keterangan,
//         ]);
//         Log::info('Report updated successfully');

//         // ===============================
//         // IMPORT REVENUE PELUNASAN
//         // ===============================
//         $successCount = 0;
//         $updateCount = 0;
        
//         if ($request->hasFile('file_revenue')) {
//             Log::info('========== STARTING REVENUE IMPORT ==========');
//             Log::info('Report ID for import:', ['report_id' => $report->id]);
            
//             try {
//                 $import = new RevenueImport($report->id);
//                 Log::info('RevenueImport instance created');
                
//                 Excel::import($import, $request->file('file_revenue'));
//                 Log::info('Excel::import executed');
                
//                 $successCount = $import->getSuccessCount();
//                 $updateCount = $import->getUpdateCount();
                
//                 Log::info('Import results:', [
//                     'inserted' => $successCount,
//                     'updated' => $updateCount
//                 ]);
                
//                 // Cek jika ada error
//                 $failures = $import->failures();
//                 Log::info('Import failures count:', ['count' => $failures->count()]);
                
//                 if ($failures->isNotEmpty()) {
//                     $errorMessages = [];
//                     foreach ($failures as $failure) {
//                         $errorMsg = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
//                         $errorMessages[] = $errorMsg;
//                         Log::error('Import failure:', [
//                             'row' => $failure->row(),
//                             'errors' => $failure->errors(),
//                             'values' => $failure->values(),
//                         ]);
//                     }
                    
//                     DB::rollBack();
//                     Log::warning('Transaction rolled back due to import failures');
//                     return redirect()->back()
//                         ->with('error', 'Import gagal: ' . implode(' | ', $errorMessages))
//                         ->withInput();
//                 }
                
//                 Log::info('========== REVENUE IMPORT COMPLETED ==========');
//             } catch (\Exception $importException) {
//                 Log::error('Import exception caught:', [
//                     'message' => $importException->getMessage(),
//                     'file' => $importException->getFile(),
//                     'line' => $importException->getLine(),
//                     'trace' => $importException->getTraceAsString(),
//                 ]);
//                 throw $importException;
//             }
//         } else {
//             Log::info('No revenue file uploaded');
//         }

//           if ($request->hasFile('file_nasabah')) {
//             Log::info('========== STARTING NASABAH IMPORT ==========');
//             Log::info('Report ID for import:', ['report_id' => $report->id]);
            
//             try {
//                 $import = new NasabahImport($report->id);
//                 Log::info('NasabahImport instance created');
                
//                 Excel::import($import, $request->file('file_nasabah'));
//                 Log::info('Excel::import executed');
                
//                 $successCount = $import->getSuccessCount();
//                 $updateCount = $import->getUpdateCount();
                
//                 Log::info('Import results:', [
//                     'inserted' => $successCount,
//                     'updated' => $updateCount
//                 ]);
                
//                 // Cek jika ada error
//                 $failures = $import->failures();
//                 Log::info('Import failures count:', ['count' => $failures->count()]);
                
//                 if ($failures->isNotEmpty()) {
//                     $errorMessages = [];
//                     foreach ($failures as $failure) {
//                         $errorMsg = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
//                         $errorMessages[] = $errorMsg;
//                         Log::error('Import failure:', [
//                             'row' => $failure->row(),
//                             'errors' => $failure->errors(),
//                             'values' => $failure->values(),
//                         ]);
//                     }
                    
//                     DB::rollBack();
//                     Log::warning('Transaction rolled back due to import failures');
//                     return redirect()->back()
//                         ->with('error', 'Import gagal: ' . implode(' | ', $errorMessages))
//                         ->withInput();
//                 }
                
//                 Log::info('========== REVENUE IMPORT COMPLETED ==========');
//             } catch (\Exception $importException) {
//                 Log::error('Import exception caught:', [
//                     'message' => $importException->getMessage(),
//                     'file' => $importException->getFile(),
//                     'line' => $importException->getLine(),
//                     'trace' => $importException->getTraceAsString(),
//                 ]);
//                 throw $importException;
//             }
//         } else {
//             Log::info('No revenue file uploaded');
//         }

//         DB::commit();
//         Log::info('Transaction committed successfully');

//         // ===============================
//         // BUAT SUCCESS MESSAGE
//         // ===============================
//         $message = "Laporan jam {$request->time_slot} berhasil disimpan!";
        
//         if ($successCount > 0) {
//             $message .= " {$successCount} data baru ditambahkan.";
//         }
        
//         if ($updateCount > 0) {
//             $message .= " {$updateCount} data diupdate.";
//         }

//         Log::info('Success message:', ['message' => $message]);
//         Log::info('========== STORE METHOD COMPLETED ==========');

//         return redirect()->route('daily-reports.3hour-manager.index')
//             ->with('success', $message);

//     } catch (\Exception $e) {
//         DB::rollBack();
        
//         Log::error('========== STORE METHOD FAILED ==========');
//         Log::error('Exception details:', [
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
    /**
     * Display the specified resource.
     */

    public function store(Request $request)
    {
        Log::info('========== STORE METHOD STARTED ==========');

        $request->validate([
            'time_slot'       => ['required', 'in:12:00,16:00,20:00'],
            'keterangan'      => ['nullable', 'string'],
            'file_revenue'    => ['nullable', 'file', 'mimes:xlsx,xls,csv', 'max:2048'],
            'file_nasabah'    => ['nullable', 'file', 'mimes:xlsx,xls,csv', 'max:2048'],
            'file_omzet'      => ['nullable', 'file', 'mimes:xlsx,xls,csv', 'max:2048'],
        ]);

        DB::beginTransaction();

        try {

            // ===============================
            // USER & BRANCH USER
            // ===============================

            $user = Auth::user();

            $branchUser = BranchUser::where('user_id', $user->id)->first();

            if (!$branchUser) {
                DB::rollBack();
                return back()->with('error', 'Anda belum terdaftar di cabang manapun');
            }

            // ===============================
            // CEK / BUAT REPORT HARI INI
            // ===============================

            $today = Carbon::today();

            $report = ThreeHourReportManager::where('branch_user_id', $branchUser->id)
                ->whereDate('created_at', $today)
                ->first();

            if (!$report) {

                $report = ThreeHourReportManager::create([
                    'branch_user_id' => $branchUser->id,
                    'keterangan' => $request->keterangan,
                ]);
            }

            // ===============================
            // CEK SLOT
            // ===============================

            $timeSlot = str_replace(':', '_', $request->time_slot);
            $columnName = 'report_' . explode('_', $timeSlot)[0] . '_at';

            if ($report->$columnName) {

                DB::rollBack();

                return back()->with('error', "Laporan jam {$request->time_slot} sudah diinput!");
            }

            $report->update([
                $columnName => now(),
                'keterangan' => $request->keterangan ?? $report->keterangan,
            ]);

            // ===============================
            // COUNTERS
            // ===============================

            $revenueInsertCount = 0;
            $revenueUpdateCount = 0;

            $nasabahInsertCount = 0;
            $nasabahUpdateCount = 0;

            $omzetInsertCount = 0;
            $omzetUpdateCount = 0;

            // ===============================
            // IMPORT OMZET
            // ===============================

            if ($request->hasFile('file_omzet')) {

                Log::info('STARTING OMZET IMPORT');

                $import = new OmzetImport($report->id);

                Excel::import($import, $request->file('file_omzet'));

                $omzetInsertCount = $import->getSuccessCount();
                $omzetUpdateCount = $import->getUpdateCount();

                $failures = $import->failures();

                if ($failures->isNotEmpty()) {

                    $errors = [];

                    foreach ($failures as $failure) {

                        $errors[] =
                            "Baris {$failure->row()}: " .
                            implode(', ', $failure->errors());
                    }

                    DB::rollBack();

                    return back()
                        ->with('error', implode(' | ', $errors));
                }

                Log::info('OMZET IMPORT DONE', [
                    'insert' => $omzetInsertCount,
                    'update' => $omzetUpdateCount,
                ]);
            }

            // ===============================
            // IMPORT REVENUE
            // ===============================

            if ($request->hasFile('file_revenue')) {

                Log::info('STARTING REVENUE IMPORT');

                $import = new RevenueImport($report->id);

                Excel::import($import, $request->file('file_revenue'));

                $revenueInsertCount = $import->getSuccessCount();
                $revenueUpdateCount = $import->getUpdateCount();

                $failures = $import->failures();

                if ($failures->isNotEmpty()) {

                    $errors = [];

                    foreach ($failures as $failure) {

                        $errors[] =
                            "Baris {$failure->row()}: " .
                            implode(', ', $failure->errors());
                    }

                    DB::rollBack();

                    return back()
                        ->with('error', implode(' | ', $errors));
                }

                Log::info('REVENUE IMPORT DONE', [
                    'insert' => $revenueInsertCount,
                    'update' => $revenueUpdateCount,
                ]);
            }

            // ===============================
            // IMPORT NASABAH
            // ===============================

            if ($request->hasFile('file_nasabah')) {

                Log::info('STARTING NASABAH IMPORT');

                $import = new NasabahImport($report->id);

                Excel::import($import, $request->file('file_nasabah'));

                $nasabahInsertCount = $import->getSuccessCount();
                $nasabahUpdateCount = $import->getUpdateCount();

                $failures = $import->failures();

                if ($failures->isNotEmpty()) {

                    $errors = [];

                    foreach ($failures as $failure) {

                        $errors[] =
                            "Baris {$failure->row()}: " .
                            implode(', ', $failure->errors());
                    }

                    DB::rollBack();

                    return back()
                        ->with('error', implode(' | ', $errors));
                }

                Log::info('NASABAH IMPORT DONE', [
                    'insert' => $nasabahInsertCount,
                    'update' => $nasabahUpdateCount,
                ]);
            }

            DB::commit();

            // ===============================
            // SUCCESS MESSAGE
            // ===============================

            $message = "Laporan jam {$request->time_slot} berhasil disimpan.";

            if ($revenueInsertCount)
                $message .= " Revenue baru: {$revenueInsertCount}.";

            if ($revenueUpdateCount)
                $message .= " Revenue update: {$revenueUpdateCount}.";

            if ($nasabahInsertCount)
                $message .= " Nasabah baru: {$nasabahInsertCount}.";

            if ($nasabahUpdateCount)
                $message .= " Nasabah update: {$nasabahUpdateCount}.";

            if ($omzetInsertCount)
                $message .= " Omzet baru: {$omzetInsertCount}.";

            if ($omzetUpdateCount)
                $message .= " Omzet update: {$omzetUpdateCount}.";


            return redirect()
                ->route('daily-reports.3hour-manager.index')
                ->with('success', $message);
        } catch (\Exception $e) {

            DB::rollBack();

            Log::error($e);

            return back()
                ->with('error', $e->getMessage());
        }
    }


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
