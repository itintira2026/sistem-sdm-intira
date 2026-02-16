<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom validation_status di tabel header laporan
        Schema::table('daily_report_fo', function (Blueprint $table) {
            $table->enum('validation_status', ['pending', 'approved', 'rejected'])
                ->default('pending')
                ->after('keterangan');
        });

        // 2. Buat tabel transaksi validasi
        Schema::create('daily_report_fo_validations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('daily_report_fo_id')
                ->constrained('daily_report_fo')
                ->onDelete('cascade');

            $table->foreignId('manager_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('validation_action_id')
                ->constrained('validation_actions')
                ->onDelete('restrict'); // jangan hapus action kalau masih dipakai

            $table->enum('status', ['approved', 'rejected']);

            $table->text('catatan')->nullable(); // opsional, untuk notes bebas nanti

            $table->timestamp('validated_at');

            $table->timestamps();

            // Satu laporan hanya boleh punya satu record validasi
            $table->unique('daily_report_fo_id', 'unique_report_validation');

            $table->index('manager_id');
            $table->index('validation_action_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_report_fo_validations');

        Schema::table('daily_report_fo', function (Blueprint $table) {
            $table->dropColumn('validation_status');
        });
    }
};
