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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->enum('semester', ['first', 'second']);
            $table->enum('status', ['enrolled', 'dropped', 'completed', 'failed'])->default('enrolled');
            $table->date('enrollment_date');
            $table->date('drop_date')->nullable();
            $table->text('drop_reason')->nullable();
            $table->decimal('attendance_percentage', 5, 2)->nullable();

            // Grade fields
            $table->decimal('ca_score', 5, 2)->nullable(); // Continuous Assessment score (out of 30)
            $table->decimal('exam_score', 5, 2)->nullable(); // Exam score (out of 70)
            $table->decimal('total_score', 5, 2)->nullable(); // Total score (out of 100)
            $table->char('final_grade', 1)->nullable(); // A, B, C, D, E, F

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['student_id', 'academic_year_id', 'semester']);
            $table->index(['class_id', 'status']);
            $table->index('status');
            $table->index('final_grade');

            // Unique constraint to prevent duplicate enrollments
            $table->unique(['student_id', 'class_id', 'academic_year_id', 'semester']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
