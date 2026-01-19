<?php

namespace App\Http\Controllers\Payroll;

use App\Imports\GajihPokokImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class GajihPokokImportController extends Controller
{
    /**
     * Menampilkan form import
     */
    public function create()
    {
        return view('gaji_pokok.import');
    }

    /**
     * Proses import gaji pokok
     */
    public function store(Request $request)
    {
        Log::info('Import gaji pokok started', [
            'file_name' => $request->file('file')
                ? $request->file('file')->getClientOriginalName()
                : 'No file'
        ]);

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240', // 10MB
        ]);

        try {
            $import = new GajihPokokImport();

            Log::info('Starting Excel import (gaji pokok)');

            Excel::import($import, $request->file('file'));

            $successCount = $import->getSuccessCount();
            $failures     = $import->failures();

            Log::info('Import gaji pokok completed', [
                'success_count' => $successCount,
                'failure_count' => count($failures)
            ]);

            // ===============================
            // JIKA ADA ERROR PARSIAL
            // ===============================
            if (count($failures) > 0) {
                $errorMessages = [];

                foreach ($failures as $failure) {
                    $errorMessages[] =
                        "Baris {$failure->row()}: " . implode(', ', $failure->errors());
                }

                return response()->json([
                    'success'   => false,
                    'inserted'  => $successCount,
                    'message'   => implode(' ', $errorMessages),
                ], 200); // 200 biar JS tetap kebaca
            }

            // ===============================
            // FULL SUCCESS
            // ===============================
            return response()->json([
                'success'  => true,
                'message'  => "Berhasil mengimport {$successCount} data gaji pokok.",
                'inserted' => $successCount
            ]);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {

            Log::error('ValidationException import gaji pokok', [
                'errors'   => $e->errors(),
                'failures' => $e->failures()
            ]);

            $errors = [];
            foreach ($e->failures() as $failure) {
                $errors[] =
                    "Baris {$failure->row()}: " . implode(', ', $failure->errors());
            }

            return response()->json([
                'success' => false,
                'message' => implode(' ', $errors)
            ], 422);
        } catch (\Exception $e) {

            Log::error('Import gaji pokok error', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download template Excel
     */
    public function template()
    {
        $templatePath = storage_path('app/templates/template_import_gaji_pokok.xlsx');

        if (!file_exists($templatePath)) {
            return $this->generateTemplate();
        }

        return response()->download(
            $templatePath,
            'template_import_gaji_pokok.xlsx'
        );
    }

    /**
     * Generate template Excel secara dinamis
     */
    private function generateTemplate()
    {
        $data = [
            [
                'email_karyawan'          => 'akramm1782@gmail.com',
                'kode_cabang'             => 'BJM-009',
                'nominal_gaji'            => 1200000,
                'tunjangan_makan'         => 30000,
                'tunjangan_transportasi'  => 25000,
                'tunjangan_jabatan'       => 20000,
                'tunjangan_komunikasi'    => 15000,
                'bulan'                   => 'jan',
                'tahun'                   => 2026,
                'keterangan'              => 'selesai',
            ],
            [
                'email_karyawan'          => 'juhratun.nissa@icloud.com',
                'kode_cabang'             => 'HSU-001',
                'nominal_gaji'            => 1200000,
                'tunjangan_makan'         => 30000,
                'tunjangan_transportasi'  => 25000,
                'tunjangan_jabatan'       => 20000,
                'tunjangan_komunikasi'    => 15000,
                'bulan'                   => 'jan',
                'tahun'                   => 2026,
                'keterangan'              => 'selesai',
            ],
        ];

        return Excel::download(
            new class($data) implements
                \Maatwebsite\Excel\Concerns\FromArray,
                \Maatwebsite\Excel\Concerns\WithHeadings {

                private $data;

                public function __construct($data)
                {
                    $this->data = $data;
                }

                public function array(): array
                {
                    return $this->data;
                }

                public function headings(): array
                {
                    return [
                        'email_karyawan',
                        'kode_cabang',
                        'nominal_gaji',
                        'tunjangan_makan',
                        'tunjangan_transportasi',
                        'tunjangan_jabatan',
                        'tunjangan_komunikasi',
                        'bulan',
                        'tahun',
                        'keterangan',
                    ];
                }
            },
            'template_import_gaji_pokok.xlsx'
        );
    }
}
