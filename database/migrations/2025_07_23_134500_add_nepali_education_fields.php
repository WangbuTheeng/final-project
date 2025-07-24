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
        // Add Nepali education system fields to subjects (check if they don't exist)
        Schema::table('subjects', function (Blueprint $table) {
            if (!Schema::hasColumn('subjects', 'education_level')) {
                $table->enum('education_level', ['plus_two', 'bachelors', 'both'])->default('both')->after('is_active');
            }
            if (!Schema::hasColumn('subjects', 'applicable_streams')) {
                $table->json('applicable_streams')->nullable()->after('education_level'); // For +2: Science, Management, Humanities
            }
            if (!Schema::hasColumn('subjects', 'applicable_programs')) {
                $table->json('applicable_programs')->nullable()->after('applicable_streams'); // For Bachelor's: BBS, BCA, BSc CSIT
            }
            if (!Schema::hasColumn('subjects', 'theory_marks')) {
                $table->integer('theory_marks')->default(80)->after('pass_marks_practical');
            }
            if (!Schema::hasColumn('subjects', 'practical_marks')) {
                $table->integer('practical_marks')->default(20)->after('theory_marks');
            }
            if (!Schema::hasColumn('subjects', 'has_practical')) {
                $table->boolean('has_practical')->default(true)->after('practical_marks');
            }
            if (!Schema::hasColumn('subjects', 'subject_category')) {
                $table->enum('subject_category', ['compulsory', 'elective', 'optional'])->default('compulsory')->after('has_practical');
            }
        });

        // Add Nepali education system fields to exams (check if they don't exist)
        Schema::table('exams', function (Blueprint $table) {
            if (!Schema::hasColumn('exams', 'education_level')) {
                $table->enum('education_level', ['plus_two', 'bachelors'])->nullable()->after('grading_system_id');
            }
            if (!Schema::hasColumn('exams', 'stream')) {
                $table->string('stream')->nullable()->after('education_level'); // Science, Management, Humanities for +2
            }
            if (!Schema::hasColumn('exams', 'program_code')) {
                $table->string('program_code')->nullable()->after('stream'); // BBS, BCA, BSc CSIT for Bachelor's
            }
            if (!Schema::hasColumn('exams', 'assessment_category')) {
                $table->enum('assessment_category', ['internal', 'external', 'both'])->default('internal')->after('program_code');
            }
            if (!Schema::hasColumn('exams', 'weightage_percentage')) {
                $table->decimal('weightage_percentage', 5, 2)->default(100.00)->after('assessment_category');
            }
            if (!Schema::hasColumn('exams', 'auto_enroll_students')) {
                $table->boolean('auto_enroll_students')->default(true)->after('weightage_percentage');
            }
            if (!Schema::hasColumn('exams', 'is_published')) {
                $table->boolean('is_published')->default(false)->after('auto_enroll_students');
            }
            if (!Schema::hasColumn('exams', 'minimum_pass_percentage')) {
                $table->decimal('minimum_pass_percentage', 5, 2)->default(35.00)->after('is_published');
            }
            if (!Schema::hasColumn('exams', 'overall_pass_percentage')) {
                $table->decimal('overall_pass_percentage', 5, 2)->default(35.00)->after('minimum_pass_percentage');
            }
            if (!Schema::hasColumn('exams', 'requires_attendance')) {
                $table->boolean('requires_attendance')->default(false)->after('overall_pass_percentage');
            }
            if (!Schema::hasColumn('exams', 'minimum_attendance_percentage')) {
                $table->decimal('minimum_attendance_percentage', 5, 2)->default(75.00)->after('requires_attendance');
            }
        });

        // Add Nepali streams and programs to exam_types (check if they don't exist)
        if (Schema::hasTable('exam_types')) {
            Schema::table('exam_types', function (Blueprint $table) {
                if (!Schema::hasColumn('exam_types', 'applicable_streams')) {
                    $table->json('applicable_streams')->nullable()->after('order_sequence');
                }
                if (!Schema::hasColumn('exam_types', 'applicable_programs')) {
                    $table->json('applicable_programs')->nullable()->after('applicable_streams');
                }
            });
        }

        // Update exam_type enum to include Nepali-specific exam types
        try {
            DB::statement("ALTER TABLE exams MODIFY COLUMN exam_type ENUM(
                'internal', 'board', 'practical', 'midterm', 'annual', 'quiz', 'test', 'final', 'assignment',
                'first_term', 'mid_term', 'pre_board', 'internal_assessment', 'viva_voce', 're_sit', 'chance_exam'
            ) DEFAULT 'internal'");
        } catch (\Exception $e) {
            // If enum modification fails, log it but continue
            \Log::warning('Exam type enum modification failed: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn([
                'education_level', 'applicable_streams', 'applicable_programs',
                'theory_marks', 'practical_marks', 'has_practical', 'subject_category'
            ]);
        });

        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn([
                'education_level', 'stream', 'program_code', 'assessment_category',
                'weightage_percentage', 'auto_enroll_students', 'is_published',
                'minimum_pass_percentage', 'overall_pass_percentage',
                'requires_attendance', 'minimum_attendance_percentage'
            ]);
        });

        Schema::table('exam_types', function (Blueprint $table) {
            $table->dropColumn(['applicable_streams', 'applicable_programs']);
        });

        // Revert exam_type enum to original
        DB::statement("ALTER TABLE exams MODIFY COLUMN exam_type ENUM('internal', 'board', 'practical', 'midterm', 'annual', 'quiz', 'test', 'final', 'assignment') DEFAULT 'internal'");
    }
};
