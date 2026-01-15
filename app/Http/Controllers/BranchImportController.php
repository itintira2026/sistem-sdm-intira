<?php

namespace App\Http\Controllers;

use App\Imports\BranchImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class BranchImportController extends Controller
{
    /**
     * Menampilkan form import
     */
    public function create()
    {
        return view('branches.import');
    }

    /**
     * Proses import file Excel
     */
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'file' => 'required|file|mimes:xlsx,xls,csv|max:5120', // 5MB max
    //     ]);

    //     try {
    //         // Import data
    //         $import = new BranchImport;

    //         // Import dengan logging
    //         Excel::import($import, $request->file('file'));

    //         // Hitung jumlah yang berhasil dan gagal
    //         $totalRows = $import->getRowCount();
    //         $successCount = $import->getSuccessCount();
    //         $failures = $import->failures();
    //         $errors = $import->errors();

    //         // Log hasil import
    //         Log::info('Import selesai', [
    //             'total' => $totalRows,
    //             'success' => $successCount,
    //             'failures' => count($failures),
    //             'errors' => count($errors)
    //         ]);

    //         // Redirect dengan pesan
    //         return redirect()->route('branches.index')
    //             ->with('success', "Import berhasil!
    //                 {$successCount} data berhasil diimport.
    //                 " . (count($failures) > 0 ? count($failures) . ' data gagal.' : ''));
    //     } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
    //         $failures = $e->failures();

    //         $errorMessages = [];
    //         foreach ($failures as $failure) {
    //             $errorMessages[] = "Baris {$failure->row()}: {$failure->errors()[0]}";
    //         }

    //         return redirect()->back()
    //             ->with('error', 'Validasi gagal: ' . implode(', ', $errorMessages))
    //             ->with('failures', $failures);
    //     } catch (\Exception $e) {
    //         Log::error('Import error: ' . $e->getMessage(), [
    //             'trace' => $e->getTraceAsString()
    //         ]);

    //         return redirect()->back()
    //             ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    //     }
    // }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'file' => 'required|file|mimes:xlsx,xls|max:10240', // 10MB max
    //     ]);

    //     try {
    //         $import = new BranchImport();

    //         // Import data
    //         Excel::import($import, $request->file('file'));

    //         // Hitung jumlah yang berhasil diimport
    //         $successCount = $import->getSuccessCount();

    //         return response()->json([
    //             'success' => true,
    //             'message' => "Berhasil mengimport {$successCount} data cabang."
    //         ]);
    //     } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
    //         $failures = $e->failures();
    //         $errors = [];

    //         foreach ($failures as $failure) {
    //             $errors[] = "Baris {$failure->row()}: {$failure->errors()[0]}";
    //         }

    //         return response()->json([
    //             'success' => false,
    //             'message' => implode(' ', $errors)
    //         ], 422);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Terjadi kesalahan: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function store(Request $request)
    {
        Log::info('Import started', ['file_name' => $request->file('file') ? $request->file('file')->getClientOriginalName() : 'No file']);

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240', // 10MB max
        ]);

        try {
            $import = new BranchImport();

            Log::info('Starting Excel import');

            // Import data
            Excel::import($import, $request->file('file'));

            // Hitung jumlah yang berhasil diimport
            $successCount = $import->getSuccessCount();
            $failures = $import->failures();

            Log::info('Import completed', [
                'success_count' => $successCount,
                'failure_count' => count($failures)
            ]);

            if (count($failures) > 0) {
                $errorMessages = [];
                foreach ($failures as $failure) {
                    $errorMessages[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
                }

                return response()->json([
                    'success' => false,
                    'message' => implode(' ', $errorMessages)
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil mengimport {$successCount} data cabang."
            ]);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            Log::error('ValidationException in import', [
                'errors' => $e->errors(),
                'failures' => $e->failures()
            ]);

            $failures = $e->failures();
            $errors = [];

            foreach ($failures as $failure) {
                $errors[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
            }

            return response()->json([
                'success' => false,
                'message' => implode(' ', $errors)
            ], 422);
        } catch (\Exception $e) {
            Log::error('Import error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
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
        // Path ke template file
        $templatePath = storage_path('app/templates/template_import_cabang.xlsx');

        // Jika template tidak ada, buat secara dinamis
        if (!file_exists($templatePath)) {
            return $this->generateTemplate();
        }

        return response()->download($templatePath, 'template_import_cabang.xlsx');
    }

    /**
     * Generate template Excel secara dinamis
     */
    private function generateTemplate()
    {
        // Buat data template
        $data = [
            [
                'kode_cabang' => 'CAB001',
                'nama_cabang' => 'Cabang Pusat',
                'telepon' => '02112345678',
                'alamat' => 'Jl. Contoh No. 123, Jakarta'
            ],
            [
                'kode_cabang' => 'CAB002',
                'nama_cabang' => 'Cabang Bandung',
                'telepon' => '02287654321',
                'alamat' => 'Jl. Contoh No. 456, Bandung'
            ]
        ];

        // Buat file Excel
        return Excel::download(new class($data) implements \Maatwebsite\Excel\Concerns\FromArray {
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
                return ['kode_cabang', 'nama_cabang', 'telepon', 'alamat'];
            }
        }, 'template_import_cabang.xlsx');
    }
}
