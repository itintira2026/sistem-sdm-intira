<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Part 0.5 (revisi) — Tambah index performa untuk modul Laporan 4 Jam.
 *
 * Setelah cross-check ke migration asli, ternyata sebagian rencana index
 * sudah ada (unique_report_field di daily_report_fo_details, unique
 * constraint di report_fields.code) — jadi TIDAK ditambahkan ulang di sini
 * untuk menghindari index duplikat yang sia-sia.
 *
 * Hanya 2 index yang benar-benar baru:
 * 1. idx_report_filter — untuk kombinasi filter (tanggal, branch_id, validation_status)
 *    yang dipakai berulang di ValidationController::index(). Index existing
 *    (user_id, tanggal), (shift, slot_time), branch_id sendiri TIDAK meng-cover
 *    kombinasi ini secara efisien.
 * 2. idx_report_user_latest — untuk query joinSub "latest upload per user".
 *    Kalau nanti joinSub itu dihapus (lihat evaluasi Part 0.6), index ini
 *    boleh di-drop juga karena tidak akan dipakai lagi.
 *
 * ASUMSI: kolom `validation_status` sudah ada di tabel `daily_report_fo`
 * (ditambahkan via migration terpisah, tidak terlihat di 3 migration yang
 * dicek). Kalau nama kolomnya berbeda, sesuaikan dulu sebelum migrate.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_report_fo', function (Blueprint $table) {
            $table->index(['tanggal', 'branch_id', 'validation_status'], 'idx_report_filter');
            $table->index(['user_id', 'uploaded_at'], 'idx_report_user_latest');
        });
    }

    public function down(): void
    {
        Schema::table('daily_report_fo', function (Blueprint $table) {
            $table->dropIndex('idx_report_filter');
            $table->dropIndex('idx_report_user_latest');
        });
    }
};
