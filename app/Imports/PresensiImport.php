<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\{
    ToModel,
    WithHeadingRow,
    WithValidation,
    SkipsOnFailure,
    SkipsFailures,
    SkipsEmptyRows
};

class PresensiImport implements
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
            empty($data['nama']) &&
            empty($data['status']) &&
            empty($data['tanggal'])
        ) {
            return null;
        }

        // ===============================
        // CARI USER BERDASARKAN NAMA
        // ===============================
        $user = User::where('name', $data['nama'])->first();

        if (!$user) {
            throw new \Exception("User dengan nama '{$data['nama']}' tidak ditemukan");
        }

        // ===============================
        // NORMALISASI TANGGAL & JAM
        // ===============================
        try {
            $tanggal = Carbon::parse($data['tanggal'])->format('Y-m-d');
            $jam     = Carbon::parse($data['jam'])->format('H:i:s');
        } catch (\Exception $e) {
            throw new \Exception("Format tanggal/jam tidak valid untuk {$data['nama']}");
        }

        // ===============================
        // CEGAH DUPLIKASI EVENT
        // ===============================
        $exists = Presensi::where('user_id', $user->id)
            ->where('tanggal', $tanggal)
            ->where('status', $data['status'])
            ->exists();

        if ($exists) {
            return null; // skip tanpa error
        }

        // ===============================
        // SIMPAN PRESENSI (RAW EVENT)
        // ===============================
        $this->successCount++;

        return new Presensi([
            'user_id'    => $user->id,
            'tanggal'    => $tanggal,
            'status'     => $data['status'],
            'jam'        => $jam,
            'wilayah'    => $data['wilayah'] ?? null,
            'keterangan' => $data['keterangan'] ?? null,
        ]);
    }

    // ===============================
    // VALIDATION RULES
    // ===============================
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string'],
            'tanggal' => ['required'],
            'status' => [
                'required',
                Rule::in([
                    'CHECK_IN',
                    'ISTIRAHAT_OUT',
                    'ISTIRAHAT_IN',
                    'CHECK_OUT'
                ])
            ],
            'jam' => ['required'],
        ];
    }

    // ===============================
    // CUSTOM ERROR MESSAGE
    // ===============================
    public function customValidationMessages(): array
    {
        return [
            'nama.required' => 'Nama karyawan wajib diisi',
            'status.required' => 'Status presensi wajib diisi',
            'status.in' => 'Status presensi tidak valid',
            'tanggal.required' => 'Tanggal wajib diisi',
            'jam.required' => 'Jam presensi wajib diisi',
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
