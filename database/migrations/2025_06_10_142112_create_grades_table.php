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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('enrollment_id')->constrained('enrollments')->onDelete('cascade');
            $table->foreignId('exam_id')->nullable()->constrained('exams')->onDelete('cascade');
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->integer('semester')->nullable(); // For semester-based courses
            $table->integer('year')->nullable(); // For year-based courses
            $table->enum('grade_type', ['ca', 'exam', 'final'])->default('final'); // CA = Continuous Assessment
            $table->decimal('theory_score', 8, 2)->nullable(); // Theory component score
            $table->decimal('practical_score', 8, 2)->nullable(); // Practical component score
            $table->decimal('score', 8, 2); // Total score (theory + practical)
            $table->decimal('max_score', 8, 2)->default(100);
            $table->string('letter_grade', 2)->nullable(); // A, B, C, D, E, F
            $table->decimal('grade_point', 3, 2)->nullable(); // 5.0 scale
            $table->text('remarks')->nullable();
            $table->foreignId('graded_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('graded_at');
            $table->timestamps();

            // Indexes
            $table->index(['student_id', 'academic_year_id', 'semester']);
            $table->index(['student_id', 'academic_year_id', 'year']);
            $table->index(['enrollment_id', 'grade_type']);
            $table->index(['exam_id', 'grade_type']);
            $table->index(['subject_id', 'grade_type']);
            $table->index('graded_by');

            // Unique constraint for specific grade types
            $table->unique(['enrollment_id', 'exam_id', 'grade_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
