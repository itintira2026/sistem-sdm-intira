<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Part A6/A8 — period_working_days: tanggal spesifik yang ditandai
 * sebagai hari kerja oleh Direktur (via kalender, klik-toggle).
 * Menggantikan pendekatan "angka total hari kerja" sesuai keputusan §7.1.
 *
 * Company-wide (gak per-cabang/divisi) — 1 baris = 1 tanggal.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('period_working_days', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->unique();
            $table->foreignId('set_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('period_working_days');
    }
};
