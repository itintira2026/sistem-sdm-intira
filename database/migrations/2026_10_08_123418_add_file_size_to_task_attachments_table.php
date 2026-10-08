<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_attachments', function (Blueprint $table) {
            // Ukuran file dalam BYTES, dicatat sekali pas upload — biar
            // nanti fitur hapus manual (filter rentang tanggal + lihat
            // total ukuran) tinggal SUM() kolom ini, gak perlu
            // Storage::size() satu-satu per file tiap halaman dibuka.
            $table->unsignedBigInteger('file_size')->nullable()->after('file_name');
        });
    }

    public function down(): void
    {
        Schema::table('task_attachments', function (Blueprint $table) {
            $table->dropColumn('file_size');
        });
    }
};
