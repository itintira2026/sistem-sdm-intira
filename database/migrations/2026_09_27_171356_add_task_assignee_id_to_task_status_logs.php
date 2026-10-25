<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Part G4 (prasyarat) — tambah task_assignee_id ke task_status_logs,
 * supaya log "frozen karena mutasi jabatan" bisa presisi nunjuk ke
 * assignee mana (bukan cuma task_id, yang gak cukup kalau 1 tugas
 * punya banyak assignee).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_status_logs', function (Blueprint $table) {
            $table->foreignId('task_assignee_id')->nullable()->after('task_entry_id')
                ->constrained('task_assignees')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('task_status_logs', function (Blueprint $table) {
            $table->dropForeign(['task_assignee_id']);
            $table->dropColumn('task_assignee_id');
        });
    }
};
