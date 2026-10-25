<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Part A4 — task_attachments: lampiran bebas format.
 * Bisa nempel ke tugas umum (task_id) ATAU entry harian spesifik
 * (task_entry_id) — sesuai §4. TEPAT SATU dari dua kolom ini yang diisi,
 * dijaga di level aplikasi (bukan DB constraint), sama seperti pola FK
 * ganda-nullable yang lain di codebase ini.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->nullable()->constrained('tasks')->cascadeOnDelete();
            $table->foreignId('task_entry_id')->nullable()->constrained('task_entries')->cascadeOnDelete();

            $table->string('file_path');
            $table->string('file_name')->nullable();

            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index('task_id');
            $table->index('task_entry_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_attachments');
    }
};
