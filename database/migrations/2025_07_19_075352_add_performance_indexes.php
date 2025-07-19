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
        // Students table indexes
        Schema::table('students', function (Blueprint $table) {
            $table->index(['department_id', 'status'], 'idx_students_dept_status');
            $table->index(['academic_year_id', 'status'], 'idx_students_year_status');
        });
        
        // Enrollments table indexes
        Schema::table('enrollments', function (Blueprint $table) {
            $table->index(['student_id', 'class_id', 'academic_year_id'], 'idx_enrollments_student_class_year');
            $table->index(['class_id', 'status'], 'idx_enrollments_class_status');
            $table->index(['final_grade'], 'idx_enrollments_grade');
        });
        
        // Classes table indexes
        Schema::table('classes', function (Blueprint $table) {
            $table->index(['course_id', 'academic_year_id', 'semester'], 'idx_classes_course_year_semester');
            $table->index(['instructor_id', 'status'], 'idx_classes_instructor_status');
        });
        
        // Users table indexes
        Schema::table('users', function (Blueprint $table) {
            $table->index(['role', 'status'], 'idx_users_role_status');
        });
        
        // Invoices table indexes (if table exists)
        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->index(['student_id', 'status'], 'idx_invoices_student_status');
                $table->index(['academic_year_id', 'status'], 'idx_invoices_year_status');
            });
        }
        
        // Payments table indexes (if table exists)
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->index(['student_id', 'status'], 'idx_payments_student_status');
                $table->index(['payment_date'], 'idx_payments_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex('idx_students_dept_status');
            $table->dropIndex('idx_students_year_status');
        });
        
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropIndex('idx_enrollments_student_class_year');
            $table->dropIndex('idx_enrollments_class_status');
            $table->dropIndex('idx_enrollments_grade');
        });
        
        Schema::table('classes', function (Blueprint $table) {
            $table->dropIndex('idx_classes_course_year_semester');
            $table->dropIndex('idx_classes_instructor_status');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_role_status');
        });
        
        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropIndex('idx_invoices_student_status');
                $table->dropIndex('idx_invoices_year_status');
            });
        }
        
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropIndex('idx_payments_student_status');
                $table->dropIndex('idx_payments_date');
            });
        }
    }
};