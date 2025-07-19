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
            // Drop the unique constraint that includes semester
            $table->dropUnique(['student_id', 'class_id', 'academic_year_id', 'semester']);
            
            // Drop the semester column
            $table->dropColumn('semester');
            
            // Add new unique constraint without semester
            $table->unique(['student_id', 'class_id', 'academic_year_id'], 'unique_enrollment_without_semester');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            // Drop the new unique constraint
            $table->dropUnique('unique_enrollment_without_semester');
            
            // Add semester column back
            $table->enum('semester', ['first', 'second'])->after('academic_year_id');
            
            // Add original unique constraint
            $table->unique(['student_id', 'class_id', 'academic_year_id', 'semester']);
        });
    }
};