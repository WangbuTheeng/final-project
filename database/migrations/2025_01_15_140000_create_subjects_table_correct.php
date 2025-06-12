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
            $table->string('name'); // e.g., "Variables and Data Types", "Functions", "Loops"
            $table->string('code')->unique(); // e.g., "ENG101-S01", "MATH101-S03"
            $table->text('description')->nullable();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade'); // Belongs to a specific class
            $table->foreignId('instructor_id')->nullable()->constrained('users')->onDelete('set null'); // Subject instructor (can be different from class instructor)
            $table->integer('order_sequence')->default(1); // Order within the class (1st topic, 2nd topic, etc.)
            $table->integer('duration_hours')->nullable(); // How many hours allocated to this subject
            $table->integer('credit_weight')->nullable(); // Weight/importance in the overall class
            $table->date('start_date')->nullable(); // When this subject/topic starts
            $table->date('end_date')->nullable(); // When this subject/topic ends
            $table->json('learning_objectives')->nullable(); // Specific learning objectives for this subject
            $table->json('resources')->nullable(); // Books, materials, links for this subject
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->enum('subject_type', ['theory', 'practical', 'mixed'])->default('theory');
            $table->boolean('is_mandatory')->default(true); // Is this subject mandatory or optional
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index(['class_id', 'order_sequence']);
            $table->index(['class_id', 'is_active']);
            $table->index('code');
            $table->index('is_active');
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
