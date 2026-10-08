<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_assignees', function (Blueprint $table) {
            // Non-null hanya kalau assignee dinonaktifkan manual oleh atasan
            // (beda dari freeze karena mutasi jabatan, yang removed_reason-nya tetap null).
            // Freeze-nya sendiri tetap pakai finalized_at/final_percentage yang sudah ada.
            $table->string('removed_reason')->nullable()->after('finalized_at');
            $table->foreignId('removed_by')->nullable()->after('removed_reason')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('task_assignees', function (Blueprint $table) {
            $table->dropConstrainedForeignId('removed_by');
            $table->dropColumn('removed_reason');
        });
    }
};
