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
        Schema::table('exams', function (Blueprint $table) {
            // Add grading system relationship only if it doesn't exist
            if (!Schema::hasColumn('exams', 'grading_system_id')) {
                $table->foreignId('grading_system_id')->nullable()->after('status')->constrained('grading_systems')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            // Only drop if the column exists and was added by this migration
            if (Schema::hasColumn('exams', 'grading_system_id')) {
                $table->dropForeign(['grading_system_id']);
                $table->dropColumn('grading_system_id');
            }
        });
    }
};
