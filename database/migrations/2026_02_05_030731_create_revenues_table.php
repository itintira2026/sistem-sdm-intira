<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('revenues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_user_id')
                ->constrained('branch_users')
                ->cascadeOnDelete();
            // Denormalisasi untuk laporan
            $table->foreignId('branch_id')
                ->constrained()
                ->cascadeOnDelete();

            // User pemilik akad / FO
            // $table->foreignId('user_id')
            //     ->constrained()
            //     ->cascadeOnDelete();



            // AM laporan 3 jam (BENAR)
            $table->foreignId('three_hour_report_manager_id')
                ->nullable()
                ->constrained('three_hour_report_managers')
                ->nullOnDelete();

            // Data transaksi
            $table->string('no_akad')->index();
            $table->text('keterangan')->nullable();
            $table->decimal('jumlah_pembayaran', 15, 2);
            $table->date('tanggal_transaksi');

            $table->timestamps();

            // Index laporan
            $table->index(['branch_id', 'tanggal_transaksi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revenues');
    }
};
