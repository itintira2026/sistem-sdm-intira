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
            empty($data['email']) &&
            empty($data['gaji_pokok'])
        ) {
            return null;
        }
    
        $user = User::where('email', $data['email'])->first();
        if (!$user) {
            throw new \Exception("User dengan email {$data['email']} tidak ditemukan");
        }

        //
        $bonus_revenue = $data['total_revenue'] * $data['persentase_revenue'] / 100;

        

        // ===============================
        // SIMPAN GAJI
        // ===============================
        $this->successCount++;

        return new GajihPokok([
            // 'branchuser_id'         => $branchUser->id,
            'user_id'         => $user->id,
            'golongan'        => $data['golongan'],
            'amount'                 => (int) $data['gaji_pokok'],
            'tunjangan_makan'        => (int) ($data['tunjangan_makan'] ?? 0),
            'tunjangan_transportasi' => (int) ($data['tunjangan_transportasi'] ?? 0),
            'tunjangan_jabatan'      => (int) ($data['tunjangan_jabatan'] ?? 0),
            'tunjangan_komunikasi'   => (int) ($data['tunjangan_komunikasi'] ?? 0),
            'ptg_bpjs_ketenagakerjaan'   => (int) ($data['potongan_bpjs_ketenagakerjaan'] ?? 0),
            'ptg_bpjs_kesehatan'   => (int) ($data['potongan_bpjs_kesehatan'] ?? 0),
            'total_revenue'   => (int) ($data['total_revenue'] ?? 0),
            'persentase_revenue'     => (int) $data['persentase_revenue'],
            'bonus_revenue'   => (int) $bonus_revenue,
            'total_kpi'   => (int) ($data['total_kpi'] ?? 0),
            'persentase_kpi'     => (int) $data['persentase_kpi'],
            'bonus_kpi'   => (int) ($data['bonus_kpi'] ?? 0),
            'simpanan'   => (int) ($data['simpanan'] ?? 0),
            'bulan'                  => (int) $data['bulan'],
            'tahun'                  => (int) $data['tahun'],
            'hari_kerja'                  => (int) $data['hari_kerja'],
            'keterangan'             => $data['keterangan'] ?? null,
        ]);
    }

    // ===============================
    // VALIDATION RULES
    // ===============================
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            // 'cabang'         => ['required', 'string'],
            'gaji_pokok'   => ['required', 'numeric', 'min:0'],
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
            'email.required' => 'Email karyawan wajib diisi',
            'email.email'    => 'Format email karyawan tidak valid',
            'cabang.required'         => 'Kode cabang wajib diisi',
            'gaji_pokok.required'   => 'Gaji pokok wajib diisi',
            'gaji_pokok.numeric'    => 'Gaji pokok harus angka',
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
