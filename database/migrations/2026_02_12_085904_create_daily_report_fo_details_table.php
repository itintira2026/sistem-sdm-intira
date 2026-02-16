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
        Schema::create('daily_report_fo_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_report_fo_id')->constrained('daily_report_fo')->onDelete('cascade');
            $table->foreignId('field_id')->constrained('report_fields')->onDelete('cascade');

            // Multi-purpose value columns
            $table->boolean('value_boolean')->nullable();
            $table->decimal('value_number', 15, 2)->nullable();
            $table->text('value_text')->nullable();
            $table->time('value_time')->nullable();

            $table->timestamps();

            $table->unique(['daily_report_fo_id', 'field_id'], 'unique_report_field');
            $table->index('field_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_report_f_o_details');
    }
};
