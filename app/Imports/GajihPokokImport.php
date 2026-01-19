<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Branch;
use App\Models\BranchUser;
use App\Models\GajihPokok;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\{
    ToModel,
    WithHeadingRow,
    WithValidation,
    SkipsOnFailure,
    SkipsFailures,
    SkipsEmptyRows
};

class GajihPokokImport implements
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
            $data[$normalizedKey] = trim($value);
        }

        // ===============================
        // CEGAH PHANTOM ROW
        // ===============================
        if (
            empty($data['email_karyawan']) &&
            empty($data['cabang']) &&
            empty($data['nominal_gaji'])
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
        // CARI RELASI USER-CABANG
        // ===============================
        $branchUser = BranchUser::where('user_id', $user->id)
            ->where('branch_id', $branch->id)
            ->first();

        if (!$branchUser) {
            throw new \Exception("User belum terdaftar di cabang {$data['cabang']}");
        }

        // ===============================
        // SIMPAN GAJI
        // ===============================
        $this->successCount++;

        return new GajihPokok([
            'branch_user_id'         => $branchUser->id,
            'amount'                 => (int) $data['nominal_gaji'],
            'tunjangan_makan'        => (int) ($data['tunjangan_makan'] ?? 0),
            'tunjangan_transportasi' => (int) ($data['tunjangan_transportasi'] ?? 0),
            'tunjangan_jabatan'      => (int) ($data['tunjangan_jabatan'] ?? 0),
            'tunjangan_komunikasi'   => (int) ($data['tunjangan_komunikasi'] ?? 0),
            'bulan'                  => (int) $data['bulan'],
            'tahun'                  => (int) $data['tahun'],
            'keterangan'             => $data['keterangan'] ?? null,
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
            'nominal_gaji'   => ['required', 'numeric', 'min:0'],
            'bulan'          => ['required', 'numeric', 'between:1,12'],
            'tahun'          => ['required', 'digits:4'],
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
            'nominal_gaji.required'   => 'Nominal gaji wajib diisi',
            'nominal_gaji.numeric'    => 'Nominal gaji harus angka',
            'bulan.numeric'          => 'Bulan wajib diisi',
            'tahun.required'          => 'Tahun wajib diisi',
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
}
