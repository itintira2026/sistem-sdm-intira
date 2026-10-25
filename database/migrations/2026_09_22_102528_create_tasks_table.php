<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Part A3 (revisi) — Tabel tasks: DEFINISI tugas doang.
 * Entry harian/progress per orang ada di tabel terpisah (task_entries),
 * bukan di sini — lihat catatan desain 3-tabel yang sudah disepakati.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();

            // Cara tracking: numeric atau checklist (§4)
            $table->enum('tracking_mode', ['numeric', 'checklist']);
            $table->string('unit')->nullable(); // label bebas, gak masuk logic (§4)

            // false = Tugas Tambahan (self-record, §6) — gak pernah divalidasi
            $table->boolean('has_weight')->default(true);

            // once / daily_split / cumulative (§4)
            $table->enum('repeat_mode', ['once', 'daily_split', 'cumulative']);

            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();

            // Link referensi manual doang, TIDAK memengaruhi perhitungan (§3)
            $table->foreignId('reference_task_id')->nullable()->constrained('tasks')->nullOnDelete();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            // Buat aturan "Kadiv -> Staff wajib divisi sama" (§3)
            $table->foreignId('division_scope')->nullable()->constrained('divisions')->nullOnDelete();

            // Lifecycle tugas (BUKAN status validasi) — active / frozen (Part 10, mutasi jabatan)
            $table->enum('status', ['active', 'frozen'])->default('active');

            $table->softDeletes();
            $table->timestamps();

            $table->index(['created_by', 'status']);
            $table->index('division_scope');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
