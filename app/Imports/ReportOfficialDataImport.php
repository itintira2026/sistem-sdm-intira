<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\ReportOfficialData;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Import data resmi dari Excel/CSV yang diupload Kadiv.
 * Pakai ToCollection (bukan ToModel) supaya bisa kontrol penuh —
 * kasih tahu baris mana yang gagal & kenapa, bukan cuma insert diam-diam.
 *
 * Format kolom yang diharapkan (baris pertama = header):
 * username | tanggal | omset | revenue | jumlah_akad | nasabah_baru | branch_code
 *
 * - tanggal: format YYYY-MM-DD (disarankan set kolom Excel sebagai Text
 *   biar gak keubah jadi serial number Excel)
 * - branch_code: opsional, kalau kosong pakai cabang FO saat ini
 */
class ReportOfficialDataImport implements ToCollection, WithHeadingRow
{
    public array $results = [];
    public int $successCount = 0;
    public int $failCount = 0;

    public function __construct(private int $uploadedByUserId) {}

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2: 1 baris header + index mulai dari 0

            $username = trim((string) ($row['username'] ?? ''));
            $tanggal = $row['tanggal'] ?? null;
            $omset = $row['omset'] ?? null;
            $revenue = $row['revenue'] ?? null;
            $jumlahAkad = $row['jumlah_akad'] ?? 0;
            $nasabahBaru = $row['nasabah_baru'] ?? 0;
            $branchCode = trim((string) ($row['branch_code'] ?? ''));

            if (empty($username)) {
                $this->fail($rowNumber, 'Kolom username kosong.');
                continue;
            }

            $user = User::where('username', $username)->first();

            if (! $user) {
                $this->fail($rowNumber, "Username '{$username}' tidak ditemukan.");
                continue;
            }

            if (! $user->hasRole('fo')) {
                $this->fail($rowNumber, "User '{$username}' bukan FO.");
                continue;
            }

            if (empty($tanggal)) {
                $this->fail($rowNumber, 'Kolom tanggal kosong.');
                continue;
            }

            try {
                $parsedDate = Carbon::parse((string) $tanggal)->format('Y-m-d');
            } catch (\Throwable $e) {
                $this->fail($rowNumber, "Format tanggal '{$tanggal}' tidak valid. Gunakan format YYYY-MM-DD.");
                continue;
            }

            if (! is_numeric($omset) || ! is_numeric($revenue)) {
                $this->fail($rowNumber, 'Kolom omset/revenue harus berupa angka.');
                continue;
            }

            $branch = null;

            if ($branchCode !== '') {
                $branch = Branch::where('code', $branchCode)->first();

                if (! $branch) {
                    $this->fail($rowNumber, "Kode cabang '{$branchCode}' tidak ditemukan.");
                    continue;
                }
            } else {
                $branch = $user->branches->first();
            }

            if (! $branch) {
                $this->fail($rowNumber, "Tidak bisa menentukan cabang untuk '{$username}' — isi kolom branch_code atau pastikan user ini terdaftar di sebuah cabang.");
                continue;
            }

            ReportOfficialData::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'tanggal' => $parsedDate,
                ],
                [
                    'branch_id' => $branch->id,
                    'omset' => $omset,
                    'revenue' => $revenue,
                    'jumlah_akad' => $jumlahAkad ?: 0,
                    'nasabah_baru' => $nasabahBaru ?: 0,
                    'uploaded_by' => $this->uploadedByUserId,
                    'uploaded_at' => now(),
                ]
            );

            $this->successCount++;
        }
    }

    private function fail(int $rowNumber, string $message): void
    {
        $this->failCount++;
        $this->results[] = [
            'row' => $rowNumber,
            'status' => 'error',
            'message' => $message,
        ];
    }
}
