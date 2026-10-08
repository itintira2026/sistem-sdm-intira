<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Part A5 — task_status_logs: audit trail (siapa, kapan, alasan).
 *
 * Bisa nempel ke tasks (misal: frozen/unfrozen karena mutasi jabatan)
 * ATAU ke task_entries (misal: checked/approved/rejected/dikoreksi
 * Superadmin) — pola FK ganda-nullable, sama seperti task_attachments.
 *
 * Ini tabel append-only (log), gak pernah di-update setelah dibuat.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_status_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('task_id')->nullable()->constrained('tasks')->cascadeOnDelete();
            $table->foreignId('task_entry_id')->nullable()->constrained('task_entries')->cascadeOnDelete();

            // Contoh isi: 'frozen', 'unfrozen', 'checked', 'approved',
            // 'rejected', 'corrected_by_superadmin', dll — free text,
            // gak dibatasi enum karena daftar aksi bisa terus nambah.
            $table->string('action');

            $table->string('old_value')->nullable();
            $table->string('new_value')->nullable();

            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();

            // Wajib diisi khusus untuk koreksi Superadmin (§7), opsional untuk aksi lain
            $table->text('reason')->nullable();

            $table->timestamps();

            $table->index('task_id');
            $table->index('task_entry_id');
            $table->index('performed_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_status_logs');
    }
};
