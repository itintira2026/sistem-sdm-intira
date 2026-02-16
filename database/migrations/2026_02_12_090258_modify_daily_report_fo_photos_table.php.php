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
        Schema::table('daily_report_fo_photos', function (Blueprint $table) {
            // Drop foreign key lama
            $table->dropForeign(['daily_report_fo_id']);

            // Rename column
            $table->renameColumn('daily_report_fo_id', 'daily_report_fo_detail_id');

            // Drop column kategori (tidak perlu lagi, sudah ada di master)
            $table->dropColumn('kategori');
        });

        // Add new foreign key
        Schema::table('daily_report_fo_photos', function (Blueprint $table) {
            $table->foreign('daily_report_fo_detail_id')
                ->references('id')
                ->on('daily_report_fo_details')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
