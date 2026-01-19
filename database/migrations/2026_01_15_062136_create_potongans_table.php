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
        Schema::create('potongans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_user_id')->constrained('branch_users')->onDelete('cascade');
            $table->tinyInteger('bulan'); // 1-12
            $table->year('tahun'); // 2024, 2025
            $table->date('tanggal');
            $table->string('divisi');
            $table->string('keterangan');
            $table->enum('jenis', ['potongan', 'tambahan']);
            $table->decimal('amount', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('potongans');
    }
};
