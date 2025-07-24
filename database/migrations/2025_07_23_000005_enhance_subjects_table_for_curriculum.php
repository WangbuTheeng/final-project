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
        Schema::table('subjects', function (Blueprint $table) {
            // Curriculum level
            if (!Schema::hasColumn('subjects', 'education_level')) {
                $table->enum('education_level', ['plus_two', 'bachelors', 'both'])->after('code')->default('both');
            }

            // Subject classification
            if (!Schema::hasColumn('subjects', 'subject_type')) {
                $table->enum('subject_type', ['compulsory', 'elective', 'optional'])->after('education_level')->default('compulsory');
            }

            // Applicable streams/programs
            if (!Schema::hasColumn('subjects', 'applicable_streams')) {
                $table->json('applicable_streams')->after('subject_type')->nullable(); // For +2: Science, Management, Humanities
            }
            if (!Schema::hasColumn('subjects', 'applicable_programs')) {
                $table->json('applicable_programs')->after('applicable_streams')->nullable(); // For Bachelor's: BBS, BSc CSIT, etc.
            }

            // Class/Year/Semester mapping
            if (!Schema::hasColumn('subjects', 'applicable_classes')) {
                $table->json('applicable_classes')->after('applicable_programs')->nullable(); // Class 11, 12
            }
            if (!Schema::hasColumn('subjects', 'applicable_years')) {
                $table->json('applicable_years')->after('applicable_classes')->nullable(); // Year 1, 2, 3, 4
            }
            if (!Schema::hasColumn('subjects', 'applicable_semesters')) {
                $table->json('applicable_semesters')->after('applicable_years')->nullable(); // Semester 1, 2, 3, etc.
            }

            // Assessment configuration
            if (!Schema::hasColumn('subjects', 'has_practical')) {
                $table->boolean('has_practical')->after('applicable_semesters')->default(false);
            }
            if (!Schema::hasColumn('subjects', 'has_internal_assessment')) {
                $table->boolean('has_internal_assessment')->after('has_practical')->default(true);
            }
            if (!Schema::hasColumn('subjects', 'internal_weightage')) {
                $table->decimal('internal_weightage', 5, 2)->after('has_internal_assessment')->default(40.00); // 40% internal
            }
            if (!Schema::hasColumn('subjects', 'external_weightage')) {
                $table->decimal('external_weightage', 5, 2)->after('internal_weightage')->default(60.00); // 60% external
            }

            // Credit system (for Bachelor's)
            if (!Schema::hasColumn('subjects', 'credit_hours')) {
                $table->decimal('credit_hours', 4, 2)->after('external_weightage')->nullable();
            }
            if (!Schema::hasColumn('subjects', 'contact_hours_per_week')) {
                $table->integer('contact_hours_per_week')->after('credit_hours')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn([
                'education_level',
                'subject_type',
                'applicable_streams',
                'applicable_programs',
                'applicable_classes',
                'applicable_years',
                'applicable_semesters',
                'has_practical',
                'has_internal_assessment',
                'internal_weightage',
                'external_weightage',
                'credit_hours',
                'contact_hours_per_week'
            ]);
        });
    }
};
