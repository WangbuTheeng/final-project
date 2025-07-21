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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('code')->unique(); // e.g., "CSC101", "MTH201"
            $table->text('description')->nullable();
            $table->foreignId('faculty_id')->constrained('faculties')->onDelete('cascade'); // Added faculty_id
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null'); // Made department_id nullable and set null on delete
            $table->integer('credit_units')->default(3);
            $table->enum('organization_type', ['yearly', 'semester'])->default('yearly'); // Course organization type
            $table->integer('year')->nullable(); // For yearly organization
            $table->integer('semester_period')->nullable(); // For semester organization
            $table->enum('course_type', ['core', 'elective', 'general'])->default('core');
            $table->enum('examination_system', ['annual', 'semester'])->default('semester')->comment('Annual system (yearly exam) or Semester system (semester-wise exam)');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index(['faculty_id', 'department_id', 'organization_type']);
            $table->index(['year', 'semester_period']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
