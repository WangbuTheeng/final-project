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
        Schema::create('exam_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // First Term Exam, Mid-Term Exam, etc.
            $table->string('code')->unique(); // FTE, MTE, PBE, etc.
            $table->text('description')->nullable();
            $table->enum('education_level', ['plus_two', 'bachelors', 'both'])->default('both');
            $table->enum('assessment_category', ['internal', 'external', 'both'])->default('internal');
            $table->decimal('default_weightage', 5, 2)->nullable(); // Default percentage weightage
            $table->integer('default_duration_minutes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('order_sequence')->default(0);
            $table->json('applicable_streams')->nullable(); // For +2: Science, Management, Humanities
            $table->json('applicable_programs')->nullable(); // For Bachelor's: BBS, BSc CSIT, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_types');
    }
};
