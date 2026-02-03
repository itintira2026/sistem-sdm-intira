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
        Schema::create('daily_report_fo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->date('tanggal');
            $table->enum('shift', ['pagi', 'siang']);
            $table->tinyInteger('slot')->comment('1, 2, 3, 4');
            $table->time('slot_time')->comment('10:00, 12:00, 14:00, etc');
            $table->timestamp('uploaded_at');
            $table->text('keterangan')->nullable()->comment('Keterangan untuk keseluruhan slot');
            $table->timestamps();

            // Indexes
            $table->unique(['user_id', 'tanggal', 'shift', 'slot'], 'unique_user_slot');
            $table->index(['user_id', 'tanggal'], 'idx_user_date');
            $table->index(['shift', 'slot_time'], 'idx_shift_slot');
            $table->index('branch_id', 'idx_branch');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_report_fo');
    }
};
