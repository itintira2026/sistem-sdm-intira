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
        Schema::create('contact90s', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nama_nasabah');
            $table->string('akun_or_notelp');
            $table->enum('sosmed', ['DM_IG', 'CHAT_WA', 'INBOX_FB', 'MRKT_PLACE_FB', 'TIKTOK']);
            $table->enum('situasi', ['tdk_merespon', 'merespon', 'tertarik', 'closing']);
            $table->boolean('validasi_am')->default(false);
            $table->string('keterangan')->nullable();
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact90s');
    }
};
