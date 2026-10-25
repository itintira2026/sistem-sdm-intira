<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Part A1 — Kolom division_id di users.
 * NULLABLE — bukan semua role butuh divisi (cuma Kadiv & Staff, sesuai
 * dokumen konsep §2 "Dikombinasikan dengan division_id"). FO/AM/Korwil/
 * Ketua/Direktur/Owner/Superadmin dibiarkan null.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('division_id')
                ->nullable()
                ->after('id')
                ->constrained('divisions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['division_id']);
            $table->dropColumn('division_id');
        });
    }
};
