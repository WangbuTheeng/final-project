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
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->integer('credit_units')->default(3);
            $table->integer('level')->default(100); // 100, 200, 300, 400, 500
            $table->enum('semester', ['first', 'second', 'both'])->default('first');
            $table->enum('course_type', ['core', 'elective', 'general'])->default('core');
            $table->json('prerequisites')->nullable(); // Array of prerequisite course IDs
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index(['department_id', 'level', 'semester']);
            $table->index(['level', 'semester']);
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
