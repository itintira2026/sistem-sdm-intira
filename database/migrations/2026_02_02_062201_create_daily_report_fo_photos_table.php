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
        Schema::create('daily_report_fo_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_report_fo_id')->constrained('daily_report_fo')->onDelete('cascade');
            $table->enum('kategori', [
                'like_fb',
                'comment_fb',
                'like_ig',
                'comment_ig',
                'like_tiktok',
                'comment_tiktok'
            ]);
            $table->string('file_path');
            $table->string('file_name');
            $table->timestamps();

            // Indexes
            $table->index(['daily_report_fo_id', 'kategori'], 'idx_report_kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_report_fo_photos');
    }
};
