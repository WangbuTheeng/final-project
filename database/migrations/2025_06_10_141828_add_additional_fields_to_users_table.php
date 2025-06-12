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
        Schema::table('users', function (Blueprint $table) {
            // Personal Information
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('middle_name')->nullable()->after('last_name');
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('Nigeria');

            // Academic Information
            $table->string('employee_id')->nullable()->unique(); // For staff
            $table->string('student_id')->nullable()->unique(); // For students
            $table->date('hire_date')->nullable(); // For staff
            $table->date('admission_date')->nullable(); // For students

            // Profile
            $table->string('avatar')->nullable();
            $table->text('bio')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended', 'graduated'])->default('active');

            // Emergency Contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relationship')->nullable();

            // System fields
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name', 'last_name', 'middle_name', 'date_of_birth', 'gender',
                'phone', 'address', 'city', 'state', 'postal_code', 'country',
                'employee_id', 'student_id', 'hire_date', 'admission_date',
                'avatar', 'bio', 'status',
                'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relationship',
                'last_login_at', 'last_login_ip', 'is_verified', 'verified_at'
            ]);
        });
    }
};
