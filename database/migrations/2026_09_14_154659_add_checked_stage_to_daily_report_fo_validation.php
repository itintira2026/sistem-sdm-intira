<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Part F1 — Tambah tahap "checked" ke alur validasi Laporan 4 Jam.
 *
 * Sebelum: pending -> approved/rejected (1 tahap, oleh manager/AM)
 * Sesudah: pending -> checked -> approved/rejected
 *          (checked oleh AM/manager, approved/rejected final oleh Korwil)
 *
 * PENDEKATAN: extend tabel existing (bukan bikin tabel baru), sesuai
 * prinsip perubahan seminimal mungkin ke struktur yang sudah berjalan.
 *
 * CATATAN: kolom `manager_id` & relasi Eloquent `manager()` di model
 * DailyReportFoValidation SENGAJA TIDAK di-rename. Setelah migrasi ini,
 * kolom tersebut merepresentasikan "siapa yang approve tahap akhir"
 * (Korwil), bukan lagi "manager yang validasi 1 tahap". Ini murni
 * perubahan makna, bukan perubahan struktur — supaya kode existing yang
 * memanggil $report->validation->manager tidak perlu ikut diubah.
 *
 * Enum diubah pakai raw SQL (bukan ->change()) supaya tidak bergantung
 * pada package doctrine/dbal yang mungkin belum ter-install.
 */
return new class extends Migration
{
    public function up(): void
    {
        // -----------------------------------------------------------
        // 1. Tambah value 'checked' di enum validation_status (header laporan)
        // -----------------------------------------------------------
        DB::statement("
            ALTER TABLE daily_report_fo
            MODIFY COLUMN validation_status
            ENUM('pending', 'checked', 'approved', 'rejected')
            NOT NULL DEFAULT 'pending'
        ");

        // -----------------------------------------------------------
        // 2. Tambah kolom checked_by & checked_at
        // -----------------------------------------------------------
        Schema::table('daily_report_fo_validations', function (Blueprint $table) {
            $table->foreignId('checked_by')
                ->nullable()
                ->after('daily_report_fo_id')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('checked_at')->nullable()->after('checked_by');
        });

        // -----------------------------------------------------------
        // 3. manager_id & validated_at jadi nullable
        //    (baris validasi sekarang dibuat pas tahap "checked",
        //    approver/waktu approve belum ada sampai Korwil approve)
        // -----------------------------------------------------------
        DB::statement("
            ALTER TABLE daily_report_fo_validations
            MODIFY COLUMN manager_id BIGINT UNSIGNED NULL
        ");

        DB::statement("
            ALTER TABLE daily_report_fo_validations
            MODIFY COLUMN validated_at TIMESTAMP NULL
        ");

        // -----------------------------------------------------------
        // 4. Tambah value 'checked' di enum status (baris validasi)
        // -----------------------------------------------------------
        DB::statement("
            ALTER TABLE daily_report_fo_validations
            MODIFY COLUMN status
            ENUM('checked', 'approved', 'rejected')
            NOT NULL
        ");
    }

    public function down(): void
    {
        // -----------------------------------------------------------
        // Catatan rollback: kalau sudah ada baris berstatus 'checked'
        // (manager_id/validated_at NULL) saat down() dijalankan, langkah
        // mengembalikan NOT NULL di bawah ini akan GAGAL. Bersihkan/isi
        // dulu data yang null sebelum rollback kalau sudah dipakai production.
        // -----------------------------------------------------------

        DB::statement("
            ALTER TABLE daily_report_fo_validations
            MODIFY COLUMN status
            ENUM('approved', 'rejected')
            NOT NULL
        ");

        DB::statement("
            ALTER TABLE daily_report_fo_validations
            MODIFY COLUMN validated_at TIMESTAMP NOT NULL
        ");

        DB::statement("
            ALTER TABLE daily_report_fo_validations
            MODIFY COLUMN manager_id BIGINT UNSIGNED NOT NULL
        ");

        Schema::table('daily_report_fo_validations', function (Blueprint $table) {
            $table->dropForeign(['checked_by']);
            $table->dropColumn(['checked_by', 'checked_at']);
        });

        DB::statement("
            ALTER TABLE daily_report_fo
            MODIFY COLUMN validation_status
            ENUM('pending', 'approved', 'rejected')
            NOT NULL DEFAULT 'pending'
        ");
    }
};
