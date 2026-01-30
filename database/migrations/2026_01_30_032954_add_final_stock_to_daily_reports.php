<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            // Stok Akhir (Final Stock)
            $table->integer('final_jumlah_barang')->default(0)->after('pelunasan_nominal');
            $table->decimal('final_nominal', 15, 2)->default(0)->after('final_jumlah_barang');

            // Index untuk query cepat
            $table->index(['branch_id', 'tanggal', 'shift']);
        });
    }

    public function down(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->dropColumn(['final_jumlah_barang', 'final_nominal']);
        });
    }
};
