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
        Schema::create('exam_component_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('exam_component_id')->constrained('exam_components')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('enrollment_id')->nullable()->constrained('enrollments')->onDelete('cascade');
            
            // Mark details
            $table->decimal('marks_obtained', 8, 2)->default(0);
            $table->decimal('total_marks', 8, 2);
            $table->decimal('percentage', 5, 2)->nullable();
            
            // Status and metadata
            $table->enum('status', ['draft', 'submitted', 'verified', 'published'])->default('draft');
            $table->text('remarks')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            
            // Audit fields
            $table->foreignId('entered_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Ensure unique combination
            $table->unique(['exam_id', 'exam_component_id', 'student_id', 'subject_id'], 'unique_component_mark');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_component_marks');
    }
};
