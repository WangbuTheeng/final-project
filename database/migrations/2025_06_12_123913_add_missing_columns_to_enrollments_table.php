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
        Schema::table('enrollments', function (Blueprint $table) {
            // Add missing grade fields if they don't exist
            if (!Schema::hasColumn('enrollments', 'ca_score')) {
                $table->decimal('ca_score', 5, 2)->nullable()->after('attendance_percentage'); // Continuous Assessment score (out of 30)
            }
            if (!Schema::hasColumn('enrollments', 'exam_score')) {
                $table->decimal('exam_score', 5, 2)->nullable()->after('ca_score'); // Exam score (out of 70)
            }
            if (!Schema::hasColumn('enrollments', 'total_score')) {
                $table->decimal('total_score', 5, 2)->nullable()->after('exam_score'); // Total score (out of 100)
            }
            if (!Schema::hasColumn('enrollments', 'final_grade')) {
                $table->char('final_grade', 1)->nullable()->after('total_score'); // A, B, C, D, E, F
            }

            // Add soft deletes if it doesn't exist
            if (!Schema::hasColumn('enrollments', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }

            // Add missing index if it doesn't exist
            // When using migrate:fresh, indexes are typically recreated, so explicit checks are often not needed.
            // If the index already exists from a previous migration or manual addition,
            // Laravel's Schema builder will handle it or throw a more specific error.
            if (!Schema::hasColumn('enrollments', 'final_grade')) { // Check if the column exists before indexing
                $table->index('final_grade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            // Drop the added columns
            $table->dropColumn(['ca_score', 'exam_score', 'total_score', 'final_grade', 'deleted_at']);

            // Drop the added index
            $table->dropIndex(['final_grade']);
        });
    }
};
