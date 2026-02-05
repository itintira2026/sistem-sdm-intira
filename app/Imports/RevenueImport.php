<?php

namespace App\Imports;

use App\Models\Revenue;
use App\Models\BranchUser;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\{
    ToModel,
    WithHeadingRow,
    WithValidation,
    SkipsOnFailure,
    SkipsFailures,
    SkipsEmptyRows
};
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class RevenueImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    SkipsOnFailure,
    SkipsEmptyRows
{
    use SkipsFailures;

    private int $successCount = 0;
    private int $updateCount = 0;
    private $threeHourReportId;
    private array $processedNoAkad = []; // Track yang sudah diproses

    public function __construct($threeHourReportId = null)
    {
        $this->threeHourReportId = $threeHourReportId;
        Log::info('RevenueImport constructed', ['report_id' => $threeHourReportId]);
    }

    public function model(array $row)
    {
        Log::info('========== PROCESSING ROW ==========');
        Log::info('Raw row data:', $row);

        // ===============================
        // NORMALISASI HEADER & CLEAN DATA
        // ===============================
        $data = [];
        foreach ($row as $key => $value) {
            $normalizedKey = strtolower(preg_replace('/[^a-z0-9]/', '_', $key));
            $normalizedKey = preg_replace('/_+/', '_', $normalizedKey);
            $normalizedKey = trim($normalizedKey, '_');
            
            if (is_string($value)) {
                $value = trim($value);
                // Hapus koma dari angka (900,000 -> 900000)
                if (preg_match('/^[\d,]+$/', $value)) {
                    $value = str_replace(',', '', $value);
                }
            }
            
            $data[$normalizedKey] = $value;
        }

        Log::info('Normalized data:', $data);

        // ===============================
        // MAPPING KOLOM
        // ===============================
        $email = $data['email'] ?? null;
        $branchCode = $data['branch'] ?? $data['cabang'] ?? null;
        $noAkad = $data['no_akad'] ?? $data['no_akad'] ?? $data['noakad'] ?? null;
        $jumlahPembayaran = $data['jumlah_pembayaran'] ?? $data['jumlahpembayaran'] ?? null;
        $tanggalTransaksi = $data['tanggal_transaksi'] ?? $data['tanggaltransaksi'] ?? null;
        $keterangan = $data['keterangan'] ?? $data['ket'] ?? null;

        // ===============================
        // CEGAH PHANTOM ROW
        // ===============================
        if (empty($email) && empty($branchCode) && empty($noAkad) && empty($jumlahPembayaran)) {
            Log::info('Skipped: Empty row');
            return null;
        }

        try {
            // ===============================
            // CARI USER BERDASARKAN EMAIL
            // ===============================
            Log::info('Looking for user:', ['email' => $email]);
            $user = User::where('email', $email)->first();
            if (!$user) {
                throw new \Exception("User dengan email {$email} tidak ditemukan");
            }

            // ===============================
            // CARI CABANG BERDASARKAN CODE
            // ===============================
            Log::info('Looking for branch:', ['code' => $branchCode]);
            $branch = Branch::where('code', $branchCode)->first();
            if (!$branch) {
                throw new \Exception("Cabang dengan kode {$branchCode} tidak ditemukan");
            }

            // ===============================
            // CARI RELASI USER–CABANG
            // ===============================
            Log::info('Looking for BranchUser:', [
                'user_id' => $user->id,
                'branch_id' => $branch->id
            ]);
            $branchUser = BranchUser::where('user_id', $user->id)
                ->where('branch_id', $branch->id)
                ->first();

            if (!$branchUser) {
                throw new \Exception("User {$email} belum terdaftar di cabang {$branchCode}");
            }

            // ===============================
            // NORMALISASI TANGGAL
            // ===============================
            $parsedDate = null;
            if (!empty($tanggalTransaksi)) {
                try {
                    if (is_numeric($tanggalTransaksi)) {
                        $parsedDate = Carbon::instance(
                            ExcelDate::excelToDateTimeObject($tanggalTransaksi)
                        )->format('Y-m-d');
                    } else {
                        $parsedDate = Carbon::parse($tanggalTransaksi)->format('Y-m-d');
                    }
                } catch (\Exception $e) {
                    Log::error('Date parsing failed:', ['error' => $e->getMessage()]);
                }
            }

            // ===============================
            // CEK APAKAH NO_AKAD SUDAH DIPROSES DI IMPORT INI
            // ===============================
            $trackingKey = $branchUser->id . '_' . $branch->id . '_' . $noAkad . '_' . $parsedDate;
            
            if (in_array($trackingKey, $this->processedNoAkad)) {
                Log::info('Skipped: Already processed in this import', ['no_akad' => $noAkad]);
                return null;
            }

            // ===============================
            // CEK APAKAH SUDAH ADA DATA HARI INI DI DATABASE
            // ===============================
            $existingRevenue = Revenue::where('branch_user_id', $branchUser->id)
                ->where('branch_id', $branch->id)
                ->where('no_akad', (string) $noAkad)
                ->whereDate('tanggal_transaksi', $parsedDate)
                ->first();

            if ($existingRevenue) {
                // ===============================
                // UPDATE DATA YANG SUDAH ADA
                // ===============================
                Log::info('Existing revenue found, updating...', ['id' => $existingRevenue->id]);
                
                $existingRevenue->update([
                    'three_hour_report_manager_id' => $this->threeHourReportId,
                    'keterangan'                   => $keterangan,
                    'jumlah_pembayaran'            => (float) $jumlahPembayaran,
                ]);

                $this->updateCount++;
                $this->processedNoAkad[] = $trackingKey; // Tandai sudah diproses
                
                Log::info('Revenue updated', [
                    'id' => $existingRevenue->id,
                    'update_count' => $this->updateCount
                ]);
                
                return null; // PENTING: Return null agar tidak insert lagi
                
            } else {
                // ===============================
                // INSERT DATA BARU
                // ===============================
                Log::info('Creating new revenue...');
                
                $this->successCount++;
                $this->processedNoAkad[] = $trackingKey; // Tandai sudah diproses
                
                Log::info('New revenue will be created', ['success_count' => $this->successCount]);
                
                return new Revenue([
                    'branch_user_id'               => $branchUser->id,
                    'branch_id'                    => $branch->id,
                    'three_hour_report_manager_id' => $this->threeHourReportId,
                    'no_akad'                      => (string) $noAkad,
                    'keterangan'                   => $keterangan,
                    'jumlah_pembayaran'            => (float) $jumlahPembayaran,
                    'tanggal_transaksi'            => $parsedDate,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Row processing failed:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    // ===============================
    // VALIDATION RULES
    // ===============================
    public function rules(): array
    {
        return [
            '*.email'              => ['required', 'email'],
            '*.branch'             => ['required'],
            '*.no_akad'            => ['required'],
            '*.jumlah_pembayaran'  => ['required', 'numeric', 'min:0'],
            '*.tanggal_transaksi'  => ['required'],
            '*.keterangan'         => ['nullable'],
        ];
    }

    // ===============================
    // CUSTOM ERROR MESSAGE
    // ===============================
    public function customValidationMessages(): array
    {
        return [
            '*.email.required'             => 'Email wajib diisi',
            '*.email.email'                => 'Format email tidak valid',
            '*.branch.required'            => 'Kode cabang wajib diisi',
            '*.no_akad.required'           => 'No Akad wajib diisi',
            '*.jumlah_pembayaran.required' => 'Jumlah pembayaran wajib diisi',
            '*.jumlah_pembayaran.numeric'  => 'Jumlah pembayaran harus berupa angka',
            '*.jumlah_pembayaran.min'      => 'Jumlah pembayaran minimal 0',
            '*.tanggal_transaksi.required' => 'Tanggal transaksi wajib diisi',
        ];
    }

    // ===============================
    // PREPARE DATA BEFORE VALIDATION
    // ===============================
    public function prepareForValidation($data, $index)
    {
        // Normalisasi keys
        $normalized = [];
        foreach ($data as $key => $value) {
            $normalizedKey = strtolower(preg_replace('/[^a-z0-9]/', '_', $key));
            $normalizedKey = preg_replace('/_+/', '_', $normalizedKey);
            $normalizedKey = trim($normalizedKey, '_');
            
            if (is_string($value)) {
                $value = trim($value);
                // Hapus koma dari angka
                if (preg_match('/^[\d,]+$/', $value)) {
                    $value = str_replace(',', '', $value);
                }
            }
            
            $normalized[$normalizedKey] = $value;
        }

        // Konversi no_akad ke string
        if (isset($normalized['no_akad'])) {
            $normalized['no_akad'] = (string) $normalized['no_akad'];
        }

        // Normalisasi tanggal
        if (isset($normalized['tanggal_transaksi'])) {
            try {
                if (is_numeric($normalized['tanggal_transaksi'])) {
                    $normalized['tanggal_transaksi'] = Carbon::instance(
                        ExcelDate::excelToDateTimeObject($normalized['tanggal_transaksi'])
                    )->format('Y-m-d');
                } else {
                    $normalized['tanggal_transaksi'] = Carbon::parse($normalized['tanggal_transaksi'])->format('Y-m-d');
                }
            } catch (\Exception $e) {
                Log::error('Date normalization failed', ['error' => $e->getMessage()]);
            }
        }

        return $normalized;
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getUpdateCount(): int
    {
        return $this->updateCount;
    }
}