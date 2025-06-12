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
        Schema::create('fees', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Tuition Fee", "Library Fee", "Lab Fee"
            $table->string('code')->unique(); // e.g., "TF", "LF", "LAB"
            $table->text('description')->nullable();
            $table->enum('fee_type', ['tuition', 'library', 'laboratory', 'sports', 'medical', 'accommodation', 'registration', 'examination', 'other'])->default('other');
            $table->decimal('amount', 10, 2);
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('cascade'); // Department-specific fees
            $table->integer('level')->nullable(); // Level-specific fees (100, 200, etc.)
            $table->enum('study_mode', ['full_time', 'part_time', 'distance', 'all'])->default('all');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->enum('semester', ['first', 'second', 'both'])->default('both');
            $table->boolean('is_mandatory')->default(true);
            $table->boolean('is_active')->default(true);
            $table->date('due_date')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['academic_year_id', 'semester', 'is_active']);
            $table->index(['department_id', 'level']);
            $table->index(['fee_type', 'is_active']);
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fees');
    }
};
