<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('validation_actions', function (Blueprint $table) {
            $table->id();
            $table->string('name');         // "Mengarahkan", "Memberikan Solusi", dll
            $table->string('code')->unique(); // "mengarahkan", "memberikan_solusi", dll
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('validation_actions');
    }
};
