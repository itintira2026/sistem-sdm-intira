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
        Schema::create('nasabahs', function (Blueprint $table) {
            $table->id();
          $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('branch_user_id')->constrained('branch_users')->onDelete('cascade');
            $table->foreignId('three_hour_report_manager_id')->nullable()->constrained('three_hour_report_managers')->onDelete('set null');
            
            $table->date('tangal_registrasi'); // atau rename jadi tanggal_registrasi
            $table->string('status_anggota', 50)->default('Aktif'); // Aktif, Nonaktif
            $table->string('no_member', 100)->nullable();
            $table->string('nik', 20)->unique();
            $table->string('nama', 255);
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('provinsi', 100)->nullable();
            $table->string('kab_kota', 100)->nullable();
            $table->string('kecamatan', 100)->nullable();
            $table->string('kelurahan', 100)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('no_telepon', 20)->nullable();
            $table->string('agama', 50)->nullable();
            $table->string('pekerjaan', 100)->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('nik');
            $table->index('branch_id');
            $table->index('status_anggota');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nasabahs');
    }
};
