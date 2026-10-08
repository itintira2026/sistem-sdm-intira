<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Part A4 — task_assignees: siapa di-assign ke tugas apa,
 * PLUS weight_percent per orang (dipindah dari tasks sesuai keputusan
 * desain 3-tabel), PLUS individual_target per orang (§4).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_assignees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Target manual per orang, gak ada auto-distribute (§4)
            $table->decimal('individual_target', 15, 2)->nullable();

            // Bobot per ORANG untuk tugas ini — null kalau tasks.has_weight = false
            $table->decimal('weight_percent', 5, 2)->nullable();

            $table->timestamps();

            $table->unique(['task_id', 'user_id'], 'unique_task_assignee');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_assignees');
    }
};
