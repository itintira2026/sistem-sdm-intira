<?php

namespace App\Imports;

use App\Models\User;
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
    $data = [];
    foreach ($row as $key => $value) {
        $normalizedKey        = strtolower(preg_replace('/[^a-z0-9]/', '_', $key));
        $data[$normalizedKey] = is_string($value) ? trim($value) : $value;
    }

    if (empty($data['email_karyawan']) && empty($data['nominal'])) {
        return null;
    }

    $user = User::where('email', $data['email_karyawan'])->first();
    if (!$user) {
        throw new \Exception("User dengan email {$data['email_karyawan']} tidak ditemukan");
    }

    $jenis = strtolower($data['jenis']);
    if (!in_array($jenis, ['potongan', 'tambahan'])) {
        throw new \Exception("Jenis harus potongan atau tambahan");
    }

    // Ambil bulan & tahun otomatis dari tanggal
    $tanggal = Carbon::parse($data['tanggal']);

    $this->successCount++;

    return new Potongan([
        'user_id'    => $user->id,
        'bulan'      => $tanggal->month,  // ← otomatis dari tanggal
        'tahun'      => $tanggal->year,   // ← otomatis dari tanggal
        'tanggal'    => $tanggal->format('Y-m-d'),
        'divisi'     => $data['divisi'],
        'keterangan' => $data['keterangan'],
        'jenis'      => $jenis,
        'amount'     => (float) $data['nominal'],
    ]);
}

public function rules(): array
{
    return [
        'email_karyawan' => ['required', 'email', 'exists:users,email'],
        // bulan & tahun dihapus dari validasi
        'tanggal'        => ['required', 'date'],
        'divisi'         => ['required', 'string'],
        'jenis'          => ['required', 'in:potongan,tambahan'],
        'nominal'        => ['required', 'numeric', 'min:0'],
    ];
}

public function customValidationMessages(): array
{
    return [
        'email_karyawan.required' => 'Email karyawan wajib diisi',
        'email_karyawan.email'    => 'Format email karyawan tidak valid',
        'email_karyawan.exists'   => 'Email karyawan tidak ditemukan di sistem',
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
        if (isset($data['tanggal'])) {
            try {
                if (is_numeric($data['tanggal'])) {
                    $data['tanggal'] = Carbon::instance(
                        ExcelDate::excelToDateTimeObject($data['tanggal'])
                    )->format('Y-m-d');
                } else {
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