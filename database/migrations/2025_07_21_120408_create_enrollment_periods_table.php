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
        Schema::create('enrollment_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Fall 2024 Enrollment", "Spring 2025 Enrollment"
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->enum('semester', ['first', 'second', 'summer']); // Nepal university semesters
            $table->enum('type', ['regular', 'late', 'makeup', 'readmission'])->default('regular');

            // Enrollment period dates
            $table->date('enrollment_start_date');
            $table->date('enrollment_end_date');
            $table->date('add_drop_deadline');
            $table->date('late_enrollment_deadline')->nullable();

            // Fee structure
            $table->decimal('base_enrollment_fee', 10, 2)->default(0);
            $table->decimal('late_enrollment_penalty', 10, 2)->default(0);
            $table->decimal('per_credit_fee', 10, 2)->default(0);

            // Status and settings
            $table->boolean('is_active')->default(true);
            $table->boolean('allow_waitlist')->default(true);
            $table->integer('max_credits_per_student')->default(21); // Nepal university standard
            $table->integer('min_credits_per_student')->default(12);

            // Nepal-specific settings
            $table->decimal('minimum_attendance_required', 5, 2)->default(75.00);
            $table->boolean('requires_prerequisite_check')->default(true);
            $table->text('enrollment_instructions')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['academic_year_id', 'semester'], 'ep_academic_year_semester_idx');
            $table->index(['enrollment_start_date', 'enrollment_end_date'], 'ep_enrollment_dates_idx');
            $table->index('is_active', 'ep_is_active_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollment_periods');
    }
};
