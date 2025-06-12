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
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->enum('exam_type', ['quiz', 'test', 'midterm', 'final', 'practical', 'assignment'])->default('test');
            $table->enum('semester', ['first', 'second']);
            $table->datetime('exam_date');
            $table->integer('duration_minutes')->default(120); // Duration in minutes
            $table->decimal('total_marks', 8, 2)->default(100);
            $table->decimal('pass_mark', 8, 2)->default(40);
            $table->string('venue')->nullable();
            $table->text('instructions')->nullable();
            $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled'])->default('scheduled');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Indexes
            $table->index(['class_id', 'exam_type', 'semester']);
            $table->index(['academic_year_id', 'semester']);
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
