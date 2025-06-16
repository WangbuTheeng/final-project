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
        Schema::create('marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('enrollment_id')->constrained('enrollments')->onDelete('cascade');
            $table->decimal('theory_marks', 8, 2)->nullable();
            $table->decimal('practical_marks', 8, 2)->nullable();
            $table->decimal('internal_marks', 8, 2)->nullable();
            $table->decimal('total_marks', 8, 2)->default(0);
            $table->decimal('obtained_marks', 8, 2)->default(0);
            $table->decimal('percentage', 5, 2)->default(0);
            $table->string('grade_letter', 2)->nullable(); // A+, A, B+, etc.
            $table->decimal('grade_point', 3, 2)->nullable(); // 4.0 scale
            $table->enum('status', ['pass', 'fail', 'absent', 'incomplete'])->default('pass');
            $table->text('remarks')->nullable();
            $table->foreignId('entered_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('entered_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['exam_id', 'subject_id']);
            $table->index(['student_id', 'exam_id']);
            $table->index(['enrollment_id', 'subject_id']);
            $table->index('status');

            // Unique constraint to prevent duplicate marks entry
            $table->unique(['exam_id', 'subject_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marks');
    }
};
