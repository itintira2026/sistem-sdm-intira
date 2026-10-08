<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Part A7 — user_monthly_kpi: rollup KPI per orang per bulan.
 * Diisi lewat lazy snapshot (KpiSnapshotService, Grup B) — bukan cron.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_monthly_kpi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('month'); // 1-12
            $table->unsignedSmallInteger('year');

            // Rollup tertimbang dari semua task_assignees.final_percentage
            // milik user ini di bulan tsb (yang has_weight = true)
            $table->decimal('total_percentage', 5, 2)->nullable();

            $table->timestamp('finalized_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'month', 'year'], 'unique_user_month_kpi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_monthly_kpi');
    }
};
