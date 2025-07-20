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
        // Add Nepal-specific fields to users table
        Schema::table('users', function (Blueprint $table) {
            // Nepal-specific personal information
            $table->string('citizenship_number')->nullable()->after('phone');
            $table->text('permanent_address')->nullable()->after('address');
            $table->text('temporary_address')->nullable()->after('permanent_address');
            $table->string('district')->nullable()->after('city');
            $table->string('province')->nullable()->after('district');
            $table->string('religion')->nullable()->after('province');
            $table->string('caste_ethnicity')->nullable()->after('religion');
            $table->enum('blood_group', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->nullable()->after('caste_ethnicity');

            // Update country default to Nepal
            $table->string('country')->default('Nepal')->change();
        });

        // Add Nepal-specific fields to students table
        Schema::table('students', function (Blueprint $table) {
            // Academic background
            $table->string('previous_school_name')->nullable()->after('guardian_info');
            $table->string('slc_see_board')->nullable()->after('previous_school_name');
            $table->year('slc_see_year')->nullable()->after('slc_see_board');
            $table->string('slc_see_marks')->nullable()->after('slc_see_year');
            $table->string('plus_two_board')->nullable()->after('slc_see_marks');
            $table->year('plus_two_year')->nullable()->after('plus_two_board');
            $table->string('plus_two_marks')->nullable()->after('plus_two_year');
            $table->enum('plus_two_stream', ['Science', 'Management', 'Humanities', 'Technical', 'Other'])->nullable()->after('plus_two_marks');

            // Enhanced guardian/family information
            $table->string('father_name')->nullable()->after('plus_two_stream');
            $table->string('father_occupation')->nullable()->after('father_name');
            $table->string('mother_name')->nullable()->after('father_occupation');
            $table->string('mother_occupation')->nullable()->after('mother_name');
            $table->string('guardian_citizenship_number')->nullable()->after('mother_occupation');
            $table->decimal('annual_family_income', 10, 2)->nullable()->after('guardian_citizenship_number');

            // Additional information
            $table->text('scholarship_info')->nullable()->after('annual_family_income');
            $table->boolean('hostel_required')->default(false)->after('scholarship_info');
            $table->text('medical_info')->nullable()->after('hostel_required');
            $table->decimal('entrance_exam_score', 5, 2)->nullable()->after('medical_info');
            $table->text('preferred_subjects')->nullable()->after('entrance_exam_score');

            // Document paths
            $table->string('photo_path')->nullable()->after('preferred_subjects');
            $table->json('document_paths')->nullable()->after('photo_path'); // For storing multiple document file paths
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'citizenship_number',
                'permanent_address',
                'temporary_address',
                'district',
                'province',
                'religion',
                'caste_ethnicity',
                'blood_group'
            ]);

            // Revert country default
            $table->string('country')->default('Nigeria')->change();
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'previous_school_name',
                'slc_see_board',
                'slc_see_year',
                'slc_see_marks',
                'plus_two_board',
                'plus_two_year',
                'plus_two_marks',
                'plus_two_stream',
                'father_name',
                'father_occupation',
                'mother_name',
                'mother_occupation',
                'guardian_citizenship_number',
                'annual_family_income',
                'scholarship_info',
                'hostel_required',
                'medical_info',
                'entrance_exam_score',
                'preferred_subjects',
                'photo_path',
                'document_paths'
            ]);
        });
    }
};
