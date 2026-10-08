<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Part F5 — Tabel data resmi hasil upload Kadiv.
 * Granularitas: per FO (user_id) per hari (tanggal) — bukan per shift/slot,
 * karena data resmi biasanya rekap harian dari sistem akuntansi.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_official_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // FO
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->date('tanggal');
            $table->decimal('omset', 15, 2)->default(0);
            $table->decimal('revenue', 15, 2)->default(0);
            $table->integer('jumlah_akad')->default(0);
            $table->integer('nasabah_baru')->default(0);
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade'); // Kadiv yang upload
            $table->timestamp('uploaded_at');
            $table->timestamps();

            // 1 FO cuma boleh punya 1 data resmi per tanggal — upload ulang = update, bukan duplikat
            $table->unique(['user_id', 'tanggal'], 'unique_official_user_date');
            $table->index(['branch_id', 'tanggal'], 'idx_official_branch_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_official_data');
    }
};
