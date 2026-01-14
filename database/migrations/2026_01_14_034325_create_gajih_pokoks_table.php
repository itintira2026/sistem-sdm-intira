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
        Schema::create('gajih_pokoks', function (Blueprint $table) {
            $table->id();
             $table->foreignId('branch_user_id')->constrained('branch_users')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->tinyInteger('bulan'); // 1-12
            $table->year('tahun'); // 2024, 2025
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Prevent duplicate - satu user di satu cabang hanya bisa punya 1 gaji pokok per bulan
            $table->unique(['branch_user_id', 'bulan', 'tahun']);
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gajih_pokoks');
    }
};
