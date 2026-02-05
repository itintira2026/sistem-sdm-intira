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
        Schema::create('three_hour_report_managers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_user_id')
                ->constrained('branch_users')
                ->cascadeOnDelete(); //ini untuk am ny

            $table->timestamp('report_12_at')->nullable();
            $table->timestamp('report_16_at')->nullable();
            $table->timestamp('report_20_at')->nullable();
            $table->text('keterangan')->nullable();



            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('three_hour_report_managers');
    }
};
