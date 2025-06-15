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
        Schema::create('exam_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->integer('theory_marks')->nullable(); // Override from subject
            $table->integer('practical_marks')->nullable(); // Override from subject
            $table->integer('pass_marks_theory')->nullable(); // Override from subject
            $table->integer('pass_marks_practical')->nullable(); // Override from subject
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index(['exam_id', 'subject_id']);
            $table->unique(['exam_id', 'subject_id']); // Prevent duplicate subject in same exam
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_subjects');
    }
};
