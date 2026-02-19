<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('gajih_pokoks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('golongan');

            $table->decimal('amount', 15, 2);
            $table->decimal('tunjangan_makan', 15, 2);
            $table->decimal('tunjangan_transportasi', 15, 2);
            $table->decimal('tunjangan_jabatan', 15, 2);
            $table->decimal('tunjangan_komunikasi', 15, 2);
            $table->decimal('ptg_bpjs_ketenagakerjaan', 15, 2);
            $table->decimal('ptg_bpjs_kesehatan', 15, 2);
            $table->decimal('total_revenue', 15, 2);
            $table->tinyInteger('persentase_revenue'); // 1-12
            $table->decimal('bonus_revenue', 15, 2);
            $table->decimal('total_kpi', 15, 2);
            $table->tinyInteger('persentase_kpi'); // 1-12
            $table->decimal('bonus_kpi', 15, 2);
            $table->decimal('simpanan', 15, 2);
            $table->tinyInteger('bulan'); // 1-12
            $table->year('tahun'); // 2024, 2025
            $table->tinyInteger('hari_kerja');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Prevent duplicate - satu user di satu cabang hanya bisa punya 1 gaji pokok per bulan
            $table->unique(['user_id', 'bulan', 'tahun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gajih_pokoks');
    }
};
