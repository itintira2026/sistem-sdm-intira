<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Pembuat laporan
            $table->date('tanggal');
            $table->enum('shift', ['pagi', 'siang']);

            // Pencairan (Barang Masuk)
            $table->integer('pencairan_jumlah_barang')->default(0);
            $table->decimal('pencairan_nominal', 15, 2)->default(0);

            // Pelunasan (Barang Keluar)
            $table->integer('pelunasan_jumlah_barang')->default(0);
            $table->decimal('pelunasan_nominal', 15, 2)->default(0);

            // Additional Info
            $table->text('keterangan')->nullable();

            // Validasi Manager
            $table->boolean('validasi_manager')->default(false);
            $table->foreignId('validated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('validated_at')->nullable();

            $table->timestamps();

            // Unique constraint: 1 laporan per cabang per tanggal per shift
            $table->unique(['branch_id', 'tanggal', 'shift'], 'unique_daily_report');

            // Indexes
            $table->index('tanggal');
            $table->index('shift');
            $table->index('validasi_manager');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};
