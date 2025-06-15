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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('admission_number')->unique();
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade'); // Admission year
            $table->integer('current_level')->default(100); // 100, 200, 300, 400, 500
            $table->enum('mode_of_entry', ['entrance_exam', 'direct_entry', 'transfer'])->default('entrance_exam');
            $table->enum('study_mode', ['full_time', 'part_time', 'distance'])->default('full_time');
            $table->enum('status', ['active', 'graduated', 'suspended', 'withdrawn', 'deferred'])->default('active');
            $table->decimal('cgpa', 3, 2)->nullable(); // Cumulative GPA
            $table->integer('total_credits_earned')->default(0);
            $table->date('expected_graduation_date')->nullable();
            $table->date('actual_graduation_date')->nullable();
            $table->json('guardian_info')->nullable(); // Guardian/Parent information
            $table->timestamps();

            // Indexes
            $table->index(['department_id', 'current_level', 'status']);
            $table->index(['academic_year_id', 'status']);
            $table->index('status');
            $table->unique(['user_id']); // One student record per user
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
