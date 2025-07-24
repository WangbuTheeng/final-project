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
        Schema::create('exam_components', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Attendance, Assignment, Quiz, Presentation, etc.
            $table->string('code')->unique(); // ATT, ASG, QUZ, PRE, etc.
            $table->text('description')->nullable();
            $table->enum('component_type', ['attendance', 'assignment', 'quiz', 'presentation', 'practical', 'viva', 'project', 'midterm', 'other']);
            $table->decimal('default_marks', 8, 2)->default(0);
            $table->decimal('default_weightage', 5, 2)->nullable(); // Percentage of total internal
            $table->enum('education_level', ['plus_two', 'bachelors', 'both'])->default('both');
            $table->boolean('is_active')->default(true);
            $table->integer('order_sequence')->default(0);
            $table->json('applicable_programs')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_components');
    }
};
