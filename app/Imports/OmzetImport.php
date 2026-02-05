<?php

namespace App\Imports;

use App\Models\Omzet;
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

class OmzetImport implements
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
    private array $processedAkad = [];

    public function __construct($threeHourReportId = null)
    {
        $this->threeHourReportId = $threeHourReportId;

        Log::info('OmzetImport constructed', [
            'report_id' => $threeHourReportId
        ]);
    }

    public function model(array $row)
    {
        Log::info('========== PROCESSING OMZET ROW ==========');
        Log::info('Raw row:', $row);

        /*
        |--------------------------------------------------------------------------
        | NORMALISASI HEADER
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | MAPPING
        |--------------------------------------------------------------------------
        */

        $noAkad = $data['no_akad'] ?? null;
        $tanggal = $data['tanggal'] ?? null;

        $branchCode = $data['branch'] ?? null;
        $adminEmail = $data['admin'] ?? null;

        $lokasi = $data['lokasi'] ?? null;
        $status = $data['status'] ?? null;

        $nama = $data['nama'] ?? null;
        $noTelepon = $data['no_telepon'] ?? null;

        $rahn = $data['rahn'] ?? 0;
        $tunggakan = $data['tunggakan'] ?? 0;

        $gradeBarang = $data['grade_barang'] ?? null;
        $jenisBarang = $data['jenis_barang'] ?? null;

        $merk = $data['merk'] ?? null;
        $type = $data['type'] ?? null;

        $keterangan = $data['keterangan'] ?? null;
        $tanggalAngkut = $data['tanggal_angkut'] ?? null;

        /*
        |--------------------------------------------------------------------------
        | SKIP EMPTY
        |--------------------------------------------------------------------------
        */

        if (!$noAkad && !$nama) {

            Log::info('Skipped empty row');

            return null;
        }

        try {

            /*
            |--------------------------------------------------------------------------
            | ADMIN
            |--------------------------------------------------------------------------
            */

            $admin = User::where('email', $adminEmail)->first();

            if (!$admin) {
                throw new \Exception("Admin tidak ditemukan: {$adminEmail}");
            }

            /*
            |--------------------------------------------------------------------------
            | BRANCH
            |--------------------------------------------------------------------------
            */

            $branch = Branch::where('code', $branchCode)->first();

            if (!$branch) {
                throw new \Exception("Branch tidak ditemukan: {$branchCode}");
            }

            /*
            |--------------------------------------------------------------------------
            | BRANCH USER
            |--------------------------------------------------------------------------
            */

            $branchUser = BranchUser::where('user_id', $admin->id)
                ->where('branch_id', $branch->id)
                ->first();

            if (!$branchUser) {

                throw new \Exception(
                    "BranchUser tidak ditemukan untuk {$adminEmail}"
                );
            }

            /*
            |--------------------------------------------------------------------------
            | PARSE DATE
            |--------------------------------------------------------------------------
            */

            $tanggalParsed = $this->parseDate($tanggal);
            $tanggalAngkutParsed = $this->parseDate($tanggalAngkut);

            /*
            |--------------------------------------------------------------------------
            | PREVENT DUPLICATE FILE
            |--------------------------------------------------------------------------
            */

            $trackingKey = $branchUser->id . '_' . $noAkad;

            if (in_array($trackingKey, $this->processedAkad)) {

                Log::info('Skipped duplicate file row');

                return null;
            }

            /*
            |--------------------------------------------------------------------------
            | CHECK EXISTING
            |--------------------------------------------------------------------------
            */

            $existing = Omzet::where('branch_user_id', $branchUser->id)
                ->where('no_akad', $noAkad)
                ->first();

            if ($existing) {

                Log::info('Updating omzet', [
                    'id' => $existing->id
                ]);

                $existing->update([

                    'three_hour_report_manager_id' => $this->threeHourReportId,

                    'tanggal' => $tanggalParsed,
                    'lokasi' => $lokasi,
                    'status' => $status,

                    'nama' => $nama,
                    'no_telepon' => $noTelepon,

                    'rahn' => $rahn,
                    'tunggakan' => $tunggakan,

                    'grade_barang' => $gradeBarang,
                    'jenis_barang' => $jenisBarang,

                    'merk' => $merk,
                    'type' => $type,

                    'keterangan' => $keterangan,
                    'tanggal_angkut' => $tanggalAngkutParsed,

                ]);

                $this->updateCount++;
                $this->processedAkad[] = $trackingKey;

                return null;
            }

            /*
            |--------------------------------------------------------------------------
            | INSERT
            |--------------------------------------------------------------------------
            */

            Log::info('Creating omzet');

            $this->successCount++;
            $this->processedAkad[] = $trackingKey;

            return new Omzet([

                'branch_id' => $branch->id,
                'branch_user_id' => $branchUser->id,
                'three_hour_report_manager_id' => $this->threeHourReportId,

                'no_akad' => $noAkad,
                'tanggal' => $tanggalParsed,

                'lokasi' => $lokasi,
                'status' => $status,

                'nama' => $nama,
                'no_telepon' => $noTelepon,

                'rahn' => $rahn,
                'tunggakan' => $tunggakan,

                'grade_barang' => $gradeBarang,
                'jenis_barang' => $jenisBarang,

                'merk' => $merk,
                'type' => $type,

                'keterangan' => $keterangan,
                'tanggal_angkut' => $tanggalAngkutParsed,
            ]);

        } catch (\Exception $e) {

            Log::error('Omzet import failed', [
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DATE PARSER
    |--------------------------------------------------------------------------
    */

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

            return null;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    public function rules(): array
    {
        return [

            '*.no_akad' => ['required'],
            '*.branch' => ['required'],
            '*.admin' => ['required'],
            '*.nama' => ['required'],

        ];
    }

    public function headingRow(): int
    {
        return 1;
    }

    /*
    |--------------------------------------------------------------------------
    | RESULT
    |--------------------------------------------------------------------------
    */

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getUpdateCount(): int
    {
        return $this->updateCount;
    }
}
