<?php

namespace App\Imports;

use App\Models\Nasabah;
use App\Models\User;
use App\Models\Branch;
use App\Models\BranchUser;
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

class NasabahImport implements
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
    private array $processedMember = [];

    public function __construct($threeHourReportId = null)
    {
        $this->threeHourReportId = $threeHourReportId;

        Log::info('NasabahImport constructed', [
            'report_id' => $threeHourReportId
        ]);
    }

    public function model(array $row)
    {
        Log::info('========== PROCESSING NASABAH ROW ==========');
        Log::info('Raw row:', $row);

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

                if (preg_match('/^[\d,]+$/', $value)) {
                    $value = str_replace(',', '', $value);
                }
            }

            $data[$normalizedKey] = $value;
        }

        Log::info('Normalized:', $data);

        // ===============================
        // MAPPING KOLOM
        // ===============================

        $adminEmail = $data['admin'] ?? $data['email_admin'] ?? null;
        $branchCode = $data['branch'] ?? $data['cabang'] ?? null;

        $tanggalRegistrasi = $data['tangal_registrasi'] ?? $data['tanggal_registrasi'] ?? null;
        $statusAnggota = $data['status_anggota'] ?? null;
        $noMember = $data['no_member'] ?? null;
        $nik = $data['nik'] ?? null;
        $nama = $data['nama'] ?? null;
        $tanggalLahir = $data['tanggal_lahir'] ?? null;
        $alamat = $data['alamat'] ?? null;
        $provinsi = $data['provinsi'] ?? null;
        $kabKota = $data['kab_kota'] ?? null;
        $kecamatan = $data['kecamatan'] ?? null;
        $kelurahan = $data['kelurahan'] ?? null;
        $email = $data['email'] ?? null;
        $noTelepon = $data['no_telepon'] ?? null;
        $agama = $data['agama'] ?? null;
        $pekerjaan = $data['pekerjaan'] ?? null;

        // ===============================
        // CEGAH PHANTOM ROW
        // ===============================

        if (empty($noMember) && empty($nik) && empty($nama)) {

            Log::info('Skipped empty row');

            return null;
        }

        try {

            // ===============================
            // CARI ADMIN (branch_user)
            // ===============================

            Log::info('Looking admin:', ['email' => $adminEmail]);

            $admin = User::where('email', $adminEmail)->first();

            if (!$admin) {

                throw new \Exception("Admin dengan email {$adminEmail} tidak ditemukan");
            }

            // ===============================
            // CARI CABANG
            // ===============================

            Log::info('Looking branch:', ['code' => $branchCode]);

            $branch = Branch::where('code', $branchCode)->first();

            if (!$branch) {

                throw new \Exception("Cabang {$branchCode} tidak ditemukan");
            }

            // ===============================
            // CARI BRANCH USER
            // ===============================

            $branchUser = BranchUser::where('user_id', $admin->id)
                ->where('branch_id', $branch->id)
                ->first();

            if (!$branchUser) {

                throw new \Exception("Admin {$adminEmail} tidak terdaftar di cabang {$branchCode}");
            }

            // ===============================
            // NORMALISASI TANGGAL
            // ===============================

            $tanggalRegistrasiParsed = $this->parseDate($tanggalRegistrasi);
            $tanggalLahirParsed = $this->parseDate($tanggalLahir);

            // ===============================
            // CEK DUPLICATE DALAM FILE
            // ===============================

            $trackingKey = $branchUser->id . '_' . $email;

            if (in_array($trackingKey, $this->processedMember)) {

                Log::info('Skipped duplicate in same file', ['email' => $email]);

                return null;
            }

            // ===============================
            // CEK DUPLICATE DI DATABASE
            // ===============================

            $existing = Nasabah::where('branch_user_id', $branchUser->id)
                ->where('email', $email)
                ->first();

            if ($existing) {

                Log::info('Updating existing nasabah', ['id' => $existing->id]);

                $existing->update([

                    'three_hour_report_manager_id' => $this->threeHourReportId,
                    'status_anggota' => $statusAnggota,
                    'nik' => $nik,
                    'nama' => $nama,
                    'tanggal_lahir' => $tanggalLahirParsed,
                    'alamat' => $alamat,
                    'provinsi' => $provinsi,
                    'kab_kota' => $kabKota,
                    'kecamatan' => $kecamatan,
                    'kelurahan' => $kelurahan,
                    'email' => $email,
                    'no_telepon' => $noTelepon,
                    'agama' => $agama,
                    'pekerjaan' => $pekerjaan,
                    'tangal_registrasi' => $tanggalRegistrasiParsed,

                ]);

                $this->updateCount++;

                $this->processedMember[] = $trackingKey;

                return null;
            }

            // ===============================
            // INSERT BARU
            // ===============================

            Log::info('Creating new nasabah');

            $this->successCount++;

            $this->processedMember[] = $trackingKey;

            return new Nasabah([

                'branch_id' => $branch->id,
                'branch_user_id' => $branchUser->id,
                'three_hour_report_manager_id' => $this->threeHourReportId,

                'tangal_registrasi' => $tanggalRegistrasiParsed,
                'status_anggota' => $statusAnggota,
                'no_member' => $noMember,
                'nik' => $nik,
                'nama' => $nama,
                'tanggal_lahir' => $tanggalLahirParsed,
                'alamat' => $alamat,
                'provinsi' => $provinsi,
                'kab_kota' => $kabKota,
                'kecamatan' => $kecamatan,
                'kelurahan' => $kelurahan,
                'email' => $email,
                'no_telepon' => $noTelepon,
                'agama' => $agama,
                'pekerjaan' => $pekerjaan,

            ]);
        } catch (\Exception $e) {

            Log::error('Nasabah import failed', [
                'message' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    // ===============================
    // DATE PARSER
    // ===============================

    private function parseDate($value)
    {
        if (!$value) return null;

        try {

            if (is_numeric($value)) {

                return Carbon::instance(
                    ExcelDate::excelToDateTimeObject($value)
                )->format('Y-m-d');
            }

            return Carbon::parse($value)->format('Y-m-d');

        } catch (\Exception $e) {

            Log::error('Date parse failed', [
                'value' => $value
            ]);

            return null;
        }
    }

    // ===============================
    // VALIDATION
    // ===============================

    public function rules(): array
    {
        return [

            '*.admin' => ['required', 'email'],
            '*.branch' => ['required'],
            '*.no_member' => ['required'],
            '*.nama' => ['required'],

        ];
    }

    public function customValidationMessages()
    {
        return [

            '*.admin.required' => 'Email admin wajib diisi',
            '*.admin.email' => 'Format email admin tidak valid',
            '*.branch.required' => 'Kode cabang wajib diisi',
            '*.no_member.required' => 'No member wajib diisi',
            '*.nama.required' => 'Nama wajib diisi',

        ];
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
