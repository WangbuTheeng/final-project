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
        if (!Schema::hasTable('exam_components')) {
            Schema::create('exam_components', function (Blueprint $table) {
                $table->id();
                $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
                $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
                $table->string('component_name'); // e.g., "Attendance", "Assignment 1", "Quiz", "Mid-term"
                $table->string('component_type'); // e.g., "attendance", "assignment", "quiz", "presentation", "mid_term"
                $table->decimal('max_marks', 8, 2);
                $table->decimal('weightage', 5, 2)->default(100.00); // Percentage weightage in final calculation
                $table->integer('order_sequence')->default(1);
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['exam_id', 'subject_id', 'component_type']);
                $table->index(['exam_id', 'order_sequence']);
            });
        }

        if (!Schema::hasTable('exam_component_marks')) {
            Schema::create('exam_component_marks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('exam_component_id')->constrained('exam_components')->onDelete('cascade');
                $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
                $table->foreignId('enrollment_id')->constrained('enrollments')->onDelete('cascade');
                $table->decimal('obtained_marks', 8, 2);
                $table->text('remarks')->nullable();
                $table->foreignId('entered_by')->constrained('users')->onDelete('cascade');
                $table->timestamp('entered_at')->nullable();
                $table->timestamps();

                $table->unique(['exam_component_id', 'student_id']);
                $table->index(['exam_component_id', 'student_id']);
                $table->index(['student_id', 'enrollment_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_component_marks');
        Schema::dropIfExists('exam_components');
    }
};
