<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * BARU (hasil keputusan desain 3-tabel) — task_entries: entry aktual +
 * status validasi, PER task_assignee (per orang).
 *
 * - Mode 'daily_split'  -> banyak baris per task_assignee (1 per hari kerja)
 * - Mode 'once'         -> 1 baris per task_assignee, entry_date NULL
 * - Mode 'cumulative'   -> 1 baris per task_assignee, entry_date NULL,
 *                          progress diupdate berkali-kali (kolom di-UPDATE,
 *                          bukan bikin baris baru)
 *
 * CATATAN: untuk mode once/cumulative, "hanya 1 baris per task_assignee"
 * TIDAK dijamin lewat unique constraint DB (karena entry_date nullable,
 * MySQL menganggap tiap NULL berbeda) — ini jadi tanggung jawab
 * TaskProgressService (pakai updateOrCreate keyed di task_assignee_id
 * kalau entry_date null), bukan constraint database.
 *
 * validation_status NULL berarti tugas ini has_weight=false (Tugas
 * Tambahan/self-record) — "tanpa status validasi apapun" sesuai §6,
 * jadi begitu disimpan, entry dianggap final tanpa alur apapun.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_assignee_id')->constrained('task_assignees')->cascadeOnDelete();

            $table->date('entry_date')->nullable(); // null untuk once/cumulative

            // Sesuai tracking_mode induk tugasnya (numeric atau checklist)
            $table->decimal('value_number', 15, 2)->nullable();
            $table->boolean('value_checklist')->nullable();

            // pending -> checked -> approved/rejected. NULL = self-record (§6), gak divalidasi.
            $table->enum('validation_status', ['pending', 'checked', 'approved', 'rejected'])->nullable();

            $table->foreignId('checked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('checked_at')->nullable();

            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->text('rejected_reason')->nullable();

            $table->timestamps();

            $table->index(['task_assignee_id', 'entry_date'], 'idx_entry_assignee_date');
            $table->index('validation_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_entries');
    }
};
