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
                'kode_cabang' => 'CAB-001',
                'nama_cabang' => 'Cabang Pusat',
                'telepon' => '02112345678',
                'alamat' => 'Jl. Contoh No. 123, Jakarta',
                'timezone' => 'Asia/Jakarta'
            ],
            [
                'kode_cabang' => 'CAB-002',
                'nama_cabang' => 'Cabang Bandung',
                'telepon' => '02287654321',
                'alamat' => 'Jl. Contoh No. 456, Bandung',
                'timezone' => 'Asia/Jakarta'
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
                return ['kode_cabang', 'nama_cabang', 'telepon', 'alamat', 'timezone'];
            }
        }, 'template_import_cabang.xlsx');
    }
}
