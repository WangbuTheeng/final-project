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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->enum('exam_type', ['internal', 'board', 'practical', 'midterm', 'annual', 'quiz', 'test', 'final', 'assignment'])->default('internal');
            $table->integer('semester')->nullable(); // For semester-based courses
            $table->integer('year')->nullable(); // For year-based courses
            $table->datetime('exam_date');
            $table->date('start_date')->nullable(); // Exam period start date
            $table->date('end_date')->nullable(); // Exam period end date
            $table->integer('duration_minutes')->default(120); // Duration in minutes
            $table->decimal('total_marks', 8, 2)->default(100);
            $table->decimal('theory_marks', 8, 2)->nullable(); // Theory component marks
            $table->decimal('practical_marks', 8, 2)->nullable(); // Practical component marks
            $table->decimal('pass_mark', 8, 2)->default(40);
            $table->string('venue')->nullable();
            $table->text('instructions')->nullable();
            $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled'])->default('scheduled');
            $table->foreignId('grading_system_id')->nullable()->constrained('grading_systems')->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Indexes
            $table->index(['class_id', 'exam_type', 'semester']);
            $table->index(['class_id', 'exam_type', 'year']);
            $table->index(['subject_id', 'exam_type']);
            $table->index(['academic_year_id', 'semester']);
            $table->index(['academic_year_id', 'year']);
            $table->index(['exam_date', 'status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
