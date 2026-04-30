<?php
// app/Services/DailyReportFoPhotoCleanupService.php

namespace App\Services;

use App\Helpers\ImageHelper;
use App\Models\DailyReportFoPhoto;
use App\Models\DailyReportFoPhotoCleanupLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class DailyReportFoPhotoCleanupService
{
    /**
     * Threshold: awal bulan 2 bulan lalu
     *
     * Contoh:
     * - Sekarang April 2026 → threshold = 1 Maret 2026
     * - Foto dari laporan sebelum 1 Maret 2026 = eligible hapus
     * - Maret 2026 dan April 2026 = aman
     */
    public function getThresholdDate(): Carbon
    {
        return now()->subMonthsNoOverflow(1)->startOfMonth();
    }

    //for testing
    // public function getThresholdDate(): Carbon
    // {
    //     return now()->addMonthsNoOverflow(2)->startOfMonth();
    // }

    /**
     * Preview: kalkulasi tanpa menghapus apapun
     * Dipakai di halaman index sebelum user konfirmasi
     */
    public function preview(): array
    {
        $threshold = $this->getThresholdDate();
        $photos    = $this->getEligiblePhotos($threshold);

        $totalSize = 0;
        foreach ($photos as $photo) {
            if (Storage::disk('public')->exists($photo->file_path)) {
                $totalSize += Storage::disk('public')->size($photo->file_path);
            }
        }

        return [
            'threshold_date'   => $threshold,
            'total_photos'     => $photos->count(),
            'total_size_bytes' => $totalSize,
            'total_size_human' => DailyReportFoPhotoCleanupLog::formatBytes($totalSize),
        ];
    }

    /**
     * Eksekusi penghapusan:
     * 1. Ambil foto eligible
     * 2. Hapus file fisik di storage
     * 3. Hapus record DB
     * 4. Catat di audit log
     */
    public function cleanup(int $executedBy, string $executionType = 'manual'): DailyReportFoPhotoCleanupLog
    {
        $threshold      = $this->getThresholdDate();
        $photos         = $this->getEligiblePhotos($threshold);

        $totalDeleted   = 0;
        $totalSizeFreed = 0;

        foreach ($photos as $photo) {
            if (Storage::disk('public')->exists($photo->file_path)) {
                $totalSizeFreed += Storage::disk('public')->size($photo->file_path);
                ImageHelper::delete($photo->file_path);
            }

            $photo->delete();
            $totalDeleted++;
        }

        return DailyReportFoPhotoCleanupLog::create([
            'executed_by'            => $executedBy,
            'deleted_before_date'    => $threshold,
            'total_photos_deleted'   => $totalDeleted,
            'total_size_freed_bytes' => $totalSizeFreed,
            'execution_type'         => $executionType,
            'executed_at'            => now(),
        ]);
    }

    /**
     * Stats storage keseluruhan foto yang masih ada
     * Ditampilkan di halaman index sebagai gambaran kondisi saat ini
     */
    public function storageStats(): array
    {
        $totalPhotos = DailyReportFoPhoto::count();

        $totalSize = 0;
        DailyReportFoPhoto::pluck('file_path')->each(function ($path) use (&$totalSize) {
            if (Storage::disk('public')->exists($path)) {
                $totalSize += Storage::disk('public')->size($path);
            }
        });

        return [
            'total_photos'     => $totalPhotos,
            'total_size_bytes' => $totalSize,
            'total_size_human' => DailyReportFoPhotoCleanupLog::formatBytes($totalSize),
        ];
    }

    // -------------------------------------------------------
    // Private
    // -------------------------------------------------------

    /**
     * Ambil semua foto yang laporannya sebelum threshold
     * Relasi: DailyReportFoPhoto → detail → report (tanggal ada di sini)
     */
    private function getEligiblePhotos(Carbon $threshold)
    {
        return DailyReportFoPhoto::whereHas('detail.report', function ($q) use ($threshold) {
            $q->whereDate('tanggal', '<', $threshold);
        })->get();
    }

    // -------------------------------------------------------
    // BARU: Hapus by Range Tanggal
    // -------------------------------------------------------

    /**
     * Batas proteksi: awal bulan lalu
     * Dipakai untuk deteksi apakah range masuk zona proteksi
     * Bukan untuk block, hanya untuk peringatan
     *
     * Contoh: sekarang April 2026 → batas = 1 Maret 2026
     */
    public function getProtectionBoundary(): Carbon
    {
        return now()->subMonthsNoOverflow(1)->startOfMonth();
    }

    /**
     * Cek apakah range tanggal masuk zona proteksi
     * Return warning message, null = aman
     */
    public function checkProtectionWarning(Carbon $dateFrom, Carbon $dateTo): ?string
    {
        $boundary = $this->getProtectionBoundary();

        if ($dateTo->gte($boundary)) {
            $protectedMonths = [];

            // Cek apakah bulan lalu masuk range
            if ($dateTo->gte($boundary) && $dateFrom->lte($boundary->copy()->endOfMonth())) {
                $protectedMonths[] = $boundary->translatedFormat('F Y');
            }

            // Cek apakah bulan ini masuk range
            if ($dateTo->gte(now()->startOfMonth())) {
                $protectedMonths[] = now()->translatedFormat('F Y');
            }

            if (!empty($protectedMonths)) {
                return 'Range ini mencakup ' . implode(' dan ', $protectedMonths)
                    . ' yang masih dalam periode aktif. '
                    . 'Foto di periode ini akan ikut terhapus. Pastikan ini disengaja.';
            }
        }

        return null;
    }

    /**
     * Validasi range — hanya cek logika dasar
     * Tidak ada block berdasarkan zona proteksi
     */
    public function validateDateRange(Carbon $dateFrom, Carbon $dateTo): array
    {
        $errors = [];

        if ($dateFrom->gt($dateTo)) {
            $errors[] = 'Tanggal Dari tidak boleh lebih baru dari Tanggal Sampai.';
        }

        return $errors;
    }

    /**
     * Preview by range: kalkulasi tanpa menghapus apapun
     */
    public function previewByRange(Carbon $dateFrom, Carbon $dateTo): array
    {
        $photos    = $this->getEligiblePhotosByRange($dateFrom, $dateTo);

        $totalSize = 0;
        foreach ($photos as $photo) {
            if (Storage::disk('public')->exists($photo->file_path)) {
                $totalSize += Storage::disk('public')->size($photo->file_path);
            }
        }

        return [
            'date_from'        => $dateFrom,
            'date_to'          => $dateTo,
            'total_photos'     => $photos->count(),
            'total_size_bytes' => $totalSize,
            'total_size_human' => DailyReportFoPhotoCleanupLog::formatBytes($totalSize),
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
    ): DailyReportFoPhotoCleanupLog {

        $photos         = $this->getEligiblePhotosByRange($dateFrom, $dateTo);
        $totalDeleted   = 0;
        $totalSizeFreed = 0;

        foreach ($photos as $photo) {
            if (Storage::disk('public')->exists($photo->file_path)) {
                $totalSizeFreed += Storage::disk('public')->size($photo->file_path);
                ImageHelper::delete($photo->file_path);
            }

            $photo->delete();
            $totalDeleted++;
        }

        return DailyReportFoPhotoCleanupLog::create([
            'executed_by'            => $executedBy,
            'deleted_before_date'    => $dateTo->copy()->addDay(),
            'total_photos_deleted'   => $totalDeleted,
            'total_size_freed_bytes' => $totalSizeFreed,
            'execution_type'         => $executionType,
            'executed_at'            => now(),
        ]);
    }

    // -------------------------------------------------------
    // Private tambahan
    // -------------------------------------------------------

    private function getEligiblePhotosByRange(Carbon $dateFrom, Carbon $dateTo)
    {
        return DailyReportFoPhoto::whereHas('detail.report', function ($q) use ($dateFrom, $dateTo) {
            $q->whereDate('tanggal', '>=', $dateFrom)
                ->whereDate('tanggal', '<=', $dateTo);
        })->get();
    }
}
