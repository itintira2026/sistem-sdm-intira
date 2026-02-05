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
        Schema::create('omzets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('three_hour_report_manager_id')->nullable()->constrained('three_hour_report_managers')->onDelete('set null');


            $table->string('no_akad')->index();
            $table->date('tanggal')->index();

            $table->string('lokasi')->nullable();
            $table->string('status')->nullable();

            $table->string('nama')->nullable();
            $table->string('no_telepon')->nullable();

            $table->decimal('rahn', 15, 2)->nullable();
            $table->decimal('tunggakan', 15, 2)->nullable();

            $table->string('grade_barang')->nullable();
            $table->string('jenis_barang')->nullable();

            $table->string('merk')->nullable();
            $table->string('type')->nullable();

            $table->string('keterangan')->nullable();

            $table->date('tanggal_angkut')->nullable();

            $table->timestamps();

            $table->unique(['branch_user_id', 'no_akad']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('omzets');
    }
};
