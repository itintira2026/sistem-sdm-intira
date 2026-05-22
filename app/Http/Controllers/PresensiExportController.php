<?php

namespace App\Http\Controllers;

use App\Exports\PresensiAllExport;
use App\Exports\PresensiUserExport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class PresensiExportController extends Controller
{
    // ============================================================
    // EXPORT ALL — generate langsung, simpan ke storage, return URL
    // ============================================================

    public function exportAll(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate   = Carbon::parse($request->end_date);

        if ($startDate->diffInDays($endDate) > 7) {
            return response()->json([
                'success' => false,
                'message' => 'Maksimal range export adalah 7 hari.',
            ], 422);
        }

        try {
            $token    = Str::random(16);
            $fileName = 'presensi_all_'
                . $startDate->format('Ymd')
                . '_'
                . $endDate->format('Ymd')
                . '_' . $token . '.xlsx';

            $filePath = 'exports/' . $fileName;

            // Pastikan folder ada
            Storage::disk('local')->makeDirectory('exports');

            // Generate Excel — synchronous
            Excel::store(
                new PresensiAllExport($request->start_date, $request->end_date),
                $filePath,
                'local'
            );

            // Pastikan file benar-benar ada
            if (!Storage::disk('local')->exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File gagal dibuat. Coba lagi.',
                ], 500);
            }

            // Simpan info file ke cache selama 15 menit
            Cache::put('export_file_' . $token, [
                'path'     => $filePath,
                'filename' => 'Rekap_Presensi_'
                    . $startDate->format('d-m-Y')
                    . '_sd_'
                    . $endDate->format('d-m-Y')
                    . '.xlsx',
            ], now()->addMinutes(15));

            return response()->json([
                'success'      => true,
                'download_url' => route('presensi.export.download', $token),
                'message'      => 'Export selesai.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ============================================================
    // DOWNLOAD FILE
    // ============================================================

    public function download($token)
    {
        $fileInfo = Cache::get('export_file_' . $token);

        // DEBUG SEMENTARA - hapus setelah fix
        // dd([
        //     'token'     => $token,
        //     'cache_key' => 'export_file_' . $token,
        //     'cache_val' => Cache::get('export_file_' . $token),
        //     'all_keys'  => Cache::get('export_file_' . $token) ? 'ADA' : 'TIDAK ADA',
        // ]);

        if (!$fileInfo) {
            abort(404, 'File tidak ditemukan atau sudah expired.');
        }

        // ✅ SESUDAH
        if (!Storage::disk('local')->exists($fileInfo['path'])) {
            abort(404, 'File export tidak ditemukan di server.');
        }
        $fullPath = Storage::disk('local')->path($fileInfo['path']);


        return response()->download($fullPath, $fileInfo['filename'])->deleteFileAfterSend(true);
    }

    // ============================================================
    // EXPORT PERORANGAN
    // ============================================================

    public function exportUser(Request $request, $userId)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $user = User::where('is_active', true)
            ->with('branches')
            ->findOrFail($userId);

        $startDate = Carbon::parse($request->start_date);
        $endDate   = Carbon::parse($request->end_date);

        if ($startDate->diffInDays($endDate) > 31) {
            return back()->with('error', 'Maksimal range export perorangan adalah 31 hari.');
        }

        $fileName = 'Presensi_'
            . Str::slug($user->name)
            . '_'
            . $startDate->format('d-m-Y')
            . '_sd_'
            . $endDate->format('d-m-Y')
            . '.xlsx';

        return Excel::download(
            new PresensiUserExport($user, $request->start_date, $request->end_date),
            $fileName
        );
    }
}
