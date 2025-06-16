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
            // Add course_id for direct course-level exams
            if (!Schema::hasColumn('exams', 'course_id')) {
                $table->foreignId('course_id')->nullable()->after('class_id')->constrained('courses')->onDelete('cascade');
            }

            // Add flags for multi-subject and auto-load functionality
            if (!Schema::hasColumn('exams', 'is_multi_subject')) {
                $table->boolean('is_multi_subject')->default(false)->after('status');
            }

            if (!Schema::hasColumn('exams', 'auto_load_subjects')) {
                $table->boolean('auto_load_subjects')->default(false)->after('is_multi_subject');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $columns = ['course_id', 'is_multi_subject', 'auto_load_subjects'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('exams', $column)) {
                    if ($column === 'course_id') {
                        $table->dropForeign(['course_id']);
                    }
                    $table->dropColumn($column);
                }
            }
        });
    }
};
