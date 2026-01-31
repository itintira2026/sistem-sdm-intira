<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_report_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_report_id')->constrained('daily_reports')->onDelete('cascade');
            $table->string('file_path'); // storage/daily_reports/{id}/photo.jpg
            $table->string('file_name');
            $table->string('keterangan')->nullable();
            $table->timestamps();

            // Index
            $table->index('daily_report_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_report_photos');
    }
};
