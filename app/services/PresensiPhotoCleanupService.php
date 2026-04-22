<?php
// app/Services/PresensiPhotoCleanupService.php

namespace App\Services;

use App\Helpers\ImageHelper;
use App\Models\Presensi;
use App\Models\LogPresensiPhotoCleanup;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class PresensiPhotoCleanupService
{
    /**
     * Threshold: awal bulan 2 bulan lalu
     * Contoh: sekarang April 2026 → threshold = 1 Maret 2026
     * Foto dari presensi sebelum 1 Maret 2026 = eligible hapus
     */
    public function getThresholdDate(): Carbon
    {
        return now()->subMonthsNoOverflow(2)->startOfMonth();
    }

    /**
     * Batas proteksi: awal bulan lalu
     * Contoh: sekarang April 2026 → batas = 1 Maret 2026
     */
    public function getProtectionBoundary(): Carbon
    {
        return now()->subMonthsNoOverflow(1)->startOfMonth();
    }

    // -------------------------------------------------------
    // Preview & Cleanup — Opsi 1 (Threshold Otomatis)
    // -------------------------------------------------------

    /**
     * Preview: kalkulasi tanpa menghapus apapun
     */
    public function preview(): array
    {
        $threshold = $this->getThresholdDate();
        $records   = $this->getEligibleRecords($threshold);

        $totalSize = $this->calculateTotalSize($records);

        return [
            'threshold_date'   => $threshold,
            'total_photos'     => $records->count(),
            'total_size_bytes' => $totalSize,
            'total_size_human' => LogPresensiPhotoCleanup::formatBytes($totalSize),
        ];
    }

    /**
     * Eksekusi penghapusan berdasarkan threshold
     */
    public function cleanup(int $executedBy, string $executionType = 'manual'): LogPresensiPhotoCleanup
    {
        $threshold    = $this->getThresholdDate();
        $records      = $this->getEligibleRecords($threshold);

        [$totalDeleted, $totalSizeFreed] = $this->deletePhotosFromRecords($records);

        return LogPresensiPhotoCleanup::create([
            'executed_by'            => $executedBy,
            'deleted_before_date'    => $threshold,
            'total_photos_deleted'   => $totalDeleted,
            'total_size_freed_bytes' => $totalSizeFreed,
            'execution_type'         => $executionType,
            'executed_at'            => now(),
        ]);
    }

    // -------------------------------------------------------
    // Preview & Cleanup — Opsi 2 (By Range Tanggal)
    // -------------------------------------------------------

    /**
     * Preview by range: kalkulasi tanpa menghapus apapun
     */
    public function previewByRange(Carbon $dateFrom, Carbon $dateTo): array
    {
        $records   = $this->getEligibleRecordsByRange($dateFrom, $dateTo);
        $totalSize = $this->calculateTotalSize($records);

        return [
            'date_from'        => $dateFrom,
            'date_to'          => $dateTo,
            'total_photos'     => $records->count(),
            'total_size_bytes' => $totalSize,
            'total_size_human' => LogPresensiPhotoCleanup::formatBytes($totalSize),
            'warning'          => $this->checkProtectionWarning($dateFrom, $dateTo),
        ];
    }

    /**
     * Eksekusi hapus by range tanggal
     */
    public function cleanupByRange(
        Carbon $dateFrom,
        Carbon $dateTo,
        int $executedBy,
        string $executionType = 'manual'
    ): LogPresensiPhotoCleanup {

        $records = $this->getEligibleRecordsByRange($dateFrom, $dateTo);

        [$totalDeleted, $totalSizeFreed] = $this->deletePhotosFromRecords($records);

        return LogPresensiPhotoCleanup::create([
            'executed_by'            => $executedBy,
            'deleted_before_date'    => $dateTo->copy()->addDay(),
            'total_photos_deleted'   => $totalDeleted,
            'total_size_freed_bytes' => $totalSizeFreed,
            'execution_type'         => $executionType,
            'executed_at'            => now(),
        ]);
    }

    // -------------------------------------------------------
    // Storage Stats
    // -------------------------------------------------------

    /**
     * Stats storage keseluruhan foto yang masih ada
     */
    public function storageStats(): array
    {
        // Ambil semua presensi yang punya foto
        $records = Presensi::whereNotNull('photo')
            ->orWhereNotNull('photo_outfit')
            ->get(['photo', 'photo_outfit']);

        $totalPhotos = 0;
        $totalSize   = 0;

        foreach ($records as $record) {
            if ($record->photo) {
                $totalPhotos++;
                if (Storage::disk('public')->exists($record->photo)) {
                    $totalSize += Storage::disk('public')->size($record->photo);
                }
            }
            if ($record->photo_outfit) {
                $totalPhotos++;
                if (Storage::disk('public')->exists($record->photo_outfit)) {
                    $totalSize += Storage::disk('public')->size($record->photo_outfit);
                }
            }
        }

        return [
            'total_photos'     => $totalPhotos,
            'total_size_bytes' => $totalSize,
            'total_size_human' => LogPresensiPhotoCleanup::formatBytes($totalSize),
        ];
    }

    // -------------------------------------------------------
    // Validasi & Warning
    // -------------------------------------------------------

    /**
     * Cek apakah range tanggal masuk zona proteksi
     * Return warning message, null = aman
     */
    public function checkProtectionWarning(Carbon $dateFrom, Carbon $dateTo): ?string
    {
        $boundary = $this->getProtectionBoundary();

        if ($dateTo->lt($boundary)) {
            return null;
        }

        $protectedMonths = [];

        // Cek bulan lalu
        if (
            $dateTo->gte($boundary) &&
            $dateFrom->lte($boundary->copy()->endOfMonth())
        ) {
            $protectedMonths[] = $boundary->translatedFormat('F Y');
        }

        // Cek bulan ini
        if ($dateTo->gte(now()->startOfMonth())) {
            $protectedMonths[] = now()->translatedFormat('F Y');
        }

        if (!empty($protectedMonths)) {
            return 'Range ini mencakup ' . implode(' dan ', $protectedMonths)
                . ' yang masih dalam periode aktif. '
                . 'Foto di periode ini akan ikut terhapus. Pastikan ini disengaja.';
        }

        return null;
    }

    /**
     * Validasi range — hanya cek logika dasar
     */
    public function validateDateRange(Carbon $dateFrom, Carbon $dateTo): array
    {
        $errors = [];

        if ($dateFrom->gt($dateTo)) {
            $errors[] = 'Tanggal Dari tidak boleh lebih baru dari Tanggal Sampai.';
        }

        return $errors;
    }

    // -------------------------------------------------------
    // Private Helpers
    // -------------------------------------------------------

    /**
     * Ambil presensi dengan foto yang tanggalnya sebelum threshold
     */
    private function getEligibleRecords(Carbon $threshold)
    {
        return Presensi::where('tanggal', '<', $threshold)
            ->where(function ($q) {
                $q->whereNotNull('photo')
                  ->orWhereNotNull('photo_outfit');
            })
            ->get(['id', 'tanggal', 'photo', 'photo_outfit']);
    }

    /**
     * Ambil presensi dengan foto dalam range tanggal tertentu
     */
    private function getEligibleRecordsByRange(Carbon $dateFrom, Carbon $dateTo)
    {
        return Presensi::whereBetween('tanggal', [
                $dateFrom->toDateString(),
                $dateTo->toDateString(),
            ])
            ->where(function ($q) {
                $q->whereNotNull('photo')
                  ->orWhereNotNull('photo_outfit');
            })
            ->get(['id', 'tanggal', 'photo', 'photo_outfit']);
    }

    /**
     * Hitung total ukuran file dari koleksi records
     */
    private function calculateTotalSize($records): int
    {
        $totalSize = 0;

        foreach ($records as $record) {
            foreach (['photo', 'photo_outfit'] as $col) {
                if ($record->$col && Storage::disk('public')->exists($record->$col)) {
                    $totalSize += Storage::disk('public')->size($record->$col);
                }
            }
        }

        return $totalSize;
    }

    /**
     * Hapus file foto dari storage dan null-kan kolom di DB
     * Return: [totalDeleted, totalSizeFreed]
     */
    private function deletePhotosFromRecords($records): array
    {
        $totalDeleted   = 0;
        $totalSizeFreed = 0;

        foreach ($records as $record) {
            $updateData = [];

            foreach (['photo', 'photo_outfit'] as $col) {
                if ($record->$col) {
                    if (Storage::disk('public')->exists($record->$col)) {
                        $totalSizeFreed += Storage::disk('public')->size($record->$col);
                        ImageHelper::delete($record->$col);
                        $totalDeleted++;
                    }
                    $updateData[$col] = null;
                }
            }

            // Null-kan kolom foto di DB, record presensi tetap ada
            if (!empty($updateData)) {
                $record->update($updateData);
            }
        }

        return [$totalDeleted, $totalSizeFreed];
    }
}