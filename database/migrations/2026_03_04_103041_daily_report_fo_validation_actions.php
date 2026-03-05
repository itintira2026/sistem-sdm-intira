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
        // 1. Drop kolom validation_action_id dari daily_report_fo_validations
        Schema::table('daily_report_fo_validations', function (Blueprint $table) {
            $table->dropForeign(['validation_action_id']); // Drop FK dulu
            $table->dropColumn('validation_action_id');
        });

        // 2. Buat tabel pivot untuk many-to-many
        Schema::create('pivot_validation_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_report_fo_validation_id')
                ->constrained('daily_report_fo_validations')
                ->onDelete('cascade');
            $table->foreignId('validation_action_id')
                ->constrained('validation_actions')
                ->onDelete('cascade');
            $table->timestamps();

            // Unique constraint: 1 validation tidak bisa punya action yang sama 2x
            $table->unique(
                ['daily_report_fo_validation_id', 'validation_action_id'],
                'validation_action_unique'
            );

            // Index untuk query performance
            $table->index('daily_report_fo_validation_id', 'idx_validation_id');
            $table->index('validation_action_id', 'idx_action_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback: drop pivot table dulu
        Schema::dropIfExists('pivot_validation_actions');

        // Kembalikan kolom validation_action_id
        Schema::table('daily_report_fo_validations', function (Blueprint $table) {
            $table->foreignId('validation_action_id')
                ->nullable()
                ->after('manager_id')
                ->constrained('validation_actions')
                ->onDelete('set null');
        });
    }
};
