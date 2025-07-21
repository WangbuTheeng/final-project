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
        Schema::table('enrollments', function (Blueprint $table) {
            // Nepal-specific enrollment fields
            $table->decimal('credit_hours', 4, 2)->nullable()->after('academic_year_id');
            $table->decimal('fee_amount', 10, 2)->nullable()->after('credit_hours');
            $table->enum('enrollment_type', ['regular', 'late', 'makeup', 'readmission'])->default('regular')->after('fee_amount');
            $table->integer('waitlist_position')->nullable()->after('enrollment_type');
            $table->boolean('prerequisites_met')->default(true)->after('waitlist_position');
            $table->date('fee_payment_date')->nullable()->after('prerequisites_met');
            $table->enum('payment_status', ['pending', 'paid', 'partial', 'waived'])->default('pending')->after('fee_payment_date');

            // Academic calendar fields
            $table->date('enrollment_period_start')->nullable()->after('payment_status');
            $table->date('enrollment_period_end')->nullable()->after('enrollment_period_start');
            $table->date('add_drop_deadline')->nullable()->after('enrollment_period_end');

            // Nepal university specific fields
            $table->boolean('attendance_required')->default(true)->after('add_drop_deadline');
            $table->decimal('minimum_attendance_percentage', 5, 2)->default(75.00)->after('attendance_required');
            $table->text('enrollment_notes')->nullable()->after('minimum_attendance_percentage');

            // Update status enum to include waitlisted
            $table->dropColumn('status');
        });

        // Add the updated status column
        Schema::table('enrollments', function (Blueprint $table) {
            $table->enum('status', ['enrolled', 'waitlisted', 'dropped', 'completed', 'failed', 'withdrawn'])->default('enrolled')->after('class_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            // Remove Nepal-specific fields
            $table->dropColumn([
                'credit_hours',
                'fee_amount',
                'enrollment_type',
                'waitlist_position',
                'prerequisites_met',
                'fee_payment_date',
                'payment_status',
                'enrollment_period_start',
                'enrollment_period_end',
                'add_drop_deadline',
                'attendance_required',
                'minimum_attendance_percentage',
                'enrollment_notes'
            ]);

            // Restore original status enum
            $table->dropColumn('status');
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->enum('status', ['enrolled', 'dropped', 'completed', 'failed'])->default('enrolled')->after('class_id');
        });
    }
};
