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
        Schema::table('presensis', function (Blueprint $table) {
            $table->string('photo')->nullable()->after('keterangan');
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
        $table->integer('jarak')->nullable()->comment('Jarak dari kantor dalam meter');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presensis', function (Blueprint $table) {
             $table->dropForeign(['branch_id']);
            $table->dropColumn(['photo','branch_id', 'jarak']);
        });
    }
};
