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
            // Add education level distinction
            $table->enum('education_level', ['plus_two', 'bachelors'])->after('academic_year_id')->nullable();
            
            // Add curriculum/stream information
            $table->string('stream')->after('education_level')->nullable(); // Science, Management, Humanities for +2
            $table->string('program_code')->after('stream')->nullable(); // BBS, BSc CSIT, etc. for Bachelor's
            
            // Link to exam type
            $table->foreignId('exam_type_id')->after('program_code')->nullable()->constrained('exam_types')->onDelete('set null');
            
            // Assessment category
            $table->enum('assessment_category', ['internal', 'external', 'both'])->after('exam_type_id')->default('internal');
            
            // Weightage in overall assessment
            $table->decimal('weightage_percentage', 5, 2)->after('assessment_category')->nullable();
            
            // Auto-enrollment flags
            $table->boolean('auto_enroll_students')->after('auto_load_subjects')->default(true);
            $table->boolean('is_published')->after('auto_enroll_students')->default(false);
            
            // Nepal University specific fields
            $table->decimal('minimum_pass_percentage', 5, 2)->after('is_published')->default(32.00); // 32% minimum
            $table->decimal('overall_pass_percentage', 5, 2)->after('minimum_pass_percentage')->default(40.00); // 40% overall
            $table->boolean('requires_attendance')->after('overall_pass_percentage')->default(true);
            $table->decimal('minimum_attendance_percentage', 5, 2)->after('requires_attendance')->default(75.00);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropForeign(['exam_type_id']);
            $table->dropColumn([
                'education_level',
                'stream',
                'program_code',
                'exam_type_id',
                'assessment_category',
                'weightage_percentage',
                'auto_enroll_students',
                'is_published',
                'minimum_pass_percentage',
                'overall_pass_percentage',
                'requires_attendance',
                'minimum_attendance_percentage'
            ]);
        });
    }
};
