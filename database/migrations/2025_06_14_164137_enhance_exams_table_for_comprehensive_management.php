<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            // Add new exam types to the enum
            DB::statement("ALTER TABLE exams MODIFY COLUMN exam_type ENUM('internal', 'board', 'practical', 'midterm', 'annual', 'quiz', 'test', 'final', 'assignment') DEFAULT 'internal'");

            // Add exam period dates
            if (!Schema::hasColumn('exams', 'start_date')) {
                $table->date('start_date')->nullable()->after('exam_date');
            }
            if (!Schema::hasColumn('exams', 'end_date')) {
                $table->date('end_date')->nullable()->after('start_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            // Revert exam types to original
            DB::statement("ALTER TABLE exams MODIFY COLUMN exam_type ENUM('quiz', 'test', 'midterm', 'final', 'practical', 'assignment') DEFAULT 'test'");

            // Remove new columns
            if (Schema::hasColumn('exams', 'start_date')) {
                $table->dropColumn('start_date');
            }
            if (Schema::hasColumn('exams', 'end_date')) {
                $table->dropColumn('end_date');
            }
        });
    }
};
