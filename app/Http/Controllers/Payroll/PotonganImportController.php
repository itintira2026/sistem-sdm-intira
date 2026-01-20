<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Imports\PotonganImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class PotonganImportController extends Controller
{
    /**
     * Proses import potongan / tambahan
     */
    public function store(Request $request)
    {
        Log::info('Import potongan started', [
            'file' => $request->file('file')
                ? $request->file('file')->getClientOriginalName()
                : null
        ]);

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        try {
            $import = new PotonganImport();

            Excel::import($import, $request->file('file'));

            $successCount = $import->getSuccessCount();
            $failures     = $import->failures();

            // ===============================
            // ERROR PARSIAL
            // ===============================
            if (count($failures) > 0) {
                $messages = [];

                foreach ($failures as $failure) {
                    $messages[] =
                        "Baris {$failure->row()}: " . implode(', ', $failure->errors());
                }

                return response()->json([
                    'success'  => false,
                    'inserted' => $successCount,
                    'message'  => implode(' ', $messages),
                ], 200);
            }

            // ===============================
            // FULL SUCCESS
            // ===============================
            return response()->json([
                'success'  => true,
                'inserted' => $successCount,
                'message'  => "Berhasil import {$successCount} data potongan.",
            ]);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {

            $errors = [];
            foreach ($e->failures() as $failure) {
                $errors[] =
                    "Baris {$failure->row()}: " . implode(', ', $failure->errors());
            }

            return response()->json([
                'success' => false,
                'message' => implode(' ', $errors),
            ], 422);
        } catch (\Exception $e) {

            Log::error('Import potongan gagal', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download template Excel
     */
    public function template()
    {
        return Excel::download(
            new class implements
                \Maatwebsite\Excel\Concerns\FromArray,
                \Maatwebsite\Excel\Concerns\WithHeadings {

                public function array(): array
                {
                    return [
                        [
                            'email karyawan' => 'isyevira02@gmail.com',
                            'cabang'         => 'BKL-002',
                            'bulan'          => 1,
                            'tahun'          => 2026,
                            'tanggal'        => '2026-01-01',
                            'divisi'         => 'sdm',
                            'keterangan'     => 'terlambat',
                            'jenis'          => 'potongan',
                            'nominal'        => 15000,
                        ],
                        [
                            'email karyawan' => 'rekhasyavira1@gmail.com',
                            'cabang'         => 'ACH-003',
                            'bulan'          => 1,
                            'tahun'          => 2026,
                            'tanggal'        => '2026-01-02',
                            'divisi'         => 'sdm',
                            'keterangan'     => 'terlambat',
                            'jenis'          => 'potongan',
                            'nominal'        => 15000,
                        ],
                    ];
                }

                public function headings(): array
                {
                    return [
                        'email karyawan',
                        'cabang',
                        'bulan',
                        'tahun',
                        'tanggal',
                        'divisi',
                        'keterangan',
                        'jenis',
                        'nominal',
                    ];
                }
            },
            'template_import_potongan.xlsx'
        );
    }
}
