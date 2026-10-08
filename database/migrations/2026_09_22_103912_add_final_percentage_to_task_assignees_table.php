<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Part A7 (revisi) — final_percentage & finalized_at TARUH DI task_assignees,
 * BUKAN di tasks. Alasan: 1 tugas bisa punya banyak assignee dengan
 * capaian akhir beda-beda (konsisten sama alasan weight_percent pindah
 * ke sini juga, dari koreksi desain A3/A4 sebelumnya).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_assignees', function (Blueprint $table) {
            $table->decimal('final_percentage', 5, 2)->nullable()->after('weight_percent');
            $table->timestamp('finalized_at')->nullable()->after('final_percentage');
        });
    }

    public function down(): void
    {
        Schema::table('task_assignees', function (Blueprint $table) {
            $table->dropColumn(['final_percentage', 'finalized_at']);
        });
    }
};
