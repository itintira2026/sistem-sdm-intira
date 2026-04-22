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
        Schema::create('log_presensi_photo_cleanups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('executed_by')
                ->constrained('users')
                ->onDelete('restrict');
            $table->date('deleted_before_date')
                ->comment('Foto dari laporan sebelum tanggal ini yang dihapus');
            $table->unsignedInteger('total_photos_deleted')->default(0);
            $table->unsignedBigInteger('total_size_freed_bytes')->default(0);
            $table->enum('execution_type', ['manual', 'auto'])->default('manual');
            $table->timestamp('executed_at');
            $table->timestamps();

            $table->index('executed_by');
            $table->index('executed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_presensi_photo_cleanups');
    }
};
