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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // e.g., "CSC101-01", "MTH201-02"
            $table->text('description')->nullable();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('instructor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('order_sequence')->default(1); // Order within the class
            $table->integer('duration_hours')->nullable(); // Total hours for this subject
            $table->integer('credit_weight')->nullable(); // Credit weight/points
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->json('learning_objectives')->nullable(); // Array of learning objectives
            $table->json('resources')->nullable(); // Array of resources (textbooks, materials, etc.)
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->enum('subject_type', ['theory', 'practical', 'mixed'])->default('theory');
            $table->boolean('is_mandatory')->default(true);
            $table->boolean('is_active')->default(true);

            // Theory/Practical marks fields for exam management
            $table->integer('full_marks_theory')->nullable();
            $table->integer('pass_marks_theory')->nullable();
            $table->integer('full_marks_practical')->nullable();
            $table->integer('pass_marks_practical')->nullable();
            $table->boolean('is_practical')->default(false);

            $table->timestamps();

            // Indexes
            $table->index(['class_id', 'order_sequence']);
            $table->index(['instructor_id', 'is_active']);
            $table->index(['difficulty_level', 'subject_type']);
            $table->index('is_active');
            
            // Unique constraint to prevent duplicate order sequences within the same class
            $table->unique(['class_id', 'order_sequence']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
