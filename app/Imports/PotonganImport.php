<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Branch;
use App\Models\BranchUser;
use App\Models\Potongan;
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
// use Maatwebsite\Excel\Concerns\ToModel;
// use Maatwebsite\Excel\Concerns\WithHeadingRow;
// use Maatwebsite\Excel\Concerns\WithValidation;
// use Maatwebsite\Excel\Concerns\SkipsOnFailure;
// use Maatwebsite\Excel\Concerns\SkipsFailures;
// use Maatwebsite\Excel\Concerns\SkipsEmptyRows;


class PotonganImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    SkipsOnFailure,
    SkipsEmptyRows
{
    use SkipsFailures;

    private int $successCount = 0;

    public function model(array $row)
    {
        // ===============================
        // NORMALISASI HEADER
        // ===============================
        $data = [];
        foreach ($row as $key => $value) {
            $normalizedKey = strtolower(preg_replace('/[^a-z0-9]/', '_', $key));
            $data[$normalizedKey] = is_string($value) ? trim($value) : $value;
        }

        // ===============================
        // CEGAH PHANTOM ROW
        // ===============================
        if (
            empty($data['email_karyawan']) &&
            empty($data['cabang']) &&
            empty($data['nominal'])
        ) {
            return null;
        }

        // ===============================
        // CARI USER
        // ===============================
        $user = User::where('email', $data['email_karyawan'])->first();
        if (!$user) {
            throw new \Exception("User dengan email {$data['email_karyawan']} tidak ditemukan");
        }

        // ===============================
        // CARI CABANG
        // ===============================
        $branch = Branch::where('code', $data['cabang'])->first();
        if (!$branch) {
            throw new \Exception("Cabang {$data['cabang']} tidak ditemukan");
        }

        // ===============================
        // CARI RELASI USER–CABANG
        // ===============================
        $branchUser = BranchUser::where('user_id', $user->id)
            ->where('branch_id', $branch->id)
            ->first();

        if (!$branchUser) {
            throw new \Exception("User belum terdaftar di cabang {$data['cabang']}");
        }

        // ===============================
        // VALIDASI JENIS
        // ===============================
        $jenis = strtolower($data['jenis']);
        if (!in_array($jenis, ['potongan', 'tambahan'])) {
            throw new \Exception("Jenis harus potongan atau tambahan");
        }

        // ===============================
        // SIMPAN POTONGAN
        // ===============================
        $this->successCount++;

        return new Potongan([
            'branch_user_id' => $branchUser->id,
            'bulan'          => (int) $data['bulan'],
            'tahun'          => (int) $data['tahun'],
            'tanggal'        => $data['tanggal'],
            // 'tanggal'        => \Carbon\Carbon::parse($data['tanggal'])->format('Y-m-d'),
            'divisi'         => $data['divisi'],
            'keterangan'     => $data['keterangan'],
            'jenis'          => $jenis,
            'amount'         => (float) $data['nominal'],
        ]);
    }

    // ===============================
    // VALIDATION RULES
    // ===============================
    public function rules(): array
    {
        return [
            'email_karyawan' => ['required', 'email'],
            'cabang'         => ['required', 'string'],
            'bulan'          => ['required', 'numeric', 'between:1,12'],
            'tahun'          => ['required', 'digits:4'],
            'tanggal'        => ['required', 'date'],
            'divisi'         => ['required', 'string'],
            'jenis'          => ['required', 'in:potongan,tambahan'],
            'nominal'        => ['required', 'numeric'],
        ];
    }

    // ===============================
    // CUSTOM ERROR MESSAGE
    // ===============================
    public function customValidationMessages(): array
    {
        return [
            'email_karyawan.required' => 'Email karyawan wajib diisi',
            'email_karyawan.email'    => 'Format email karyawan tidak valid',
            'cabang.required'         => 'Kode cabang wajib diisi',
            'bulan.required'          => 'Bulan wajib diisi',
            'bulan.between'           => 'Bulan harus 1 sampai 12',
            'tahun.required'          => 'Tahun wajib diisi',
            'tanggal.required'        => 'Tanggal wajib diisi',
            'tanggal.date'            => 'Format tanggal tidak valid',
            'divisi.required'         => 'Divisi wajib diisi',
            'jenis.in'                => 'Jenis harus potongan atau tambahan',
            'nominal.required'        => 'Nominal wajib diisi',
            'nominal.numeric'         => 'Nominal harus angka',
        ];
    }

    public function prepareForValidation($data, $index)
    {
        // ===============================
        // NORMALISASI TANGGAL EXCEL
        // ===============================
        if (isset($data['tanggal'])) {
            try {
                // Jika numeric (serial Excel)
                if (is_numeric($data['tanggal'])) {
                    $data['tanggal'] = Carbon::instance(
                        ExcelDate::excelToDateTimeObject($data['tanggal'])
                    )->format('Y-m-d');
                }

                // Jika string (misal 1/1/2026)
                else {
                    $data['tanggal'] = Carbon::parse($data['tanggal'])->format('Y-m-d');
                }
            } catch (\Exception $e) {
                // Biarkan gagal di validation
            }
        }

        return $data;
    }


    public function headingRow(): int
    {
        return 1;
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }
}
