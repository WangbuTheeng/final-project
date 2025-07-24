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
        Schema::table('college_settings', function (Blueprint $table) {
            // Add more fields for enhanced marksheet formatting
            if (!Schema::hasColumn('college_settings', 'college_code')) {
                $table->string('college_code')->nullable()->after('college_name');
            }
            if (!Schema::hasColumn('college_settings', 'affiliation')) {
                $table->string('affiliation')->nullable()->after('college_address');
            }
            if (!Schema::hasColumn('college_settings', 'university_name')) {
                $table->string('university_name')->nullable()->after('affiliation');
            }
            if (!Schema::hasColumn('college_settings', 'college_motto')) {
                $table->text('college_motto')->nullable()->after('university_name');
            }

            // Enhanced signature fields
            if (!Schema::hasColumn('college_settings', 'vice_principal_name')) {
                $table->string('vice_principal_name')->nullable()->after('principal_signature_path');
            }
            if (!Schema::hasColumn('college_settings', 'vice_principal_signature_path')) {
                $table->string('vice_principal_signature_path')->nullable()->after('vice_principal_name');
            }
            if (!Schema::hasColumn('college_settings', 'academic_coordinator_name')) {
                $table->string('academic_coordinator_name')->nullable()->after('registrar_signature_path');
            }
            if (!Schema::hasColumn('college_settings', 'academic_coordinator_signature_path')) {
                $table->string('academic_coordinator_signature_path')->nullable()->after('academic_coordinator_name');
            }

            // Marksheet formatting options
            if (!Schema::hasColumn('college_settings', 'show_college_logo')) {
                $table->boolean('show_college_logo')->default(true)->after('logo_path');
            }
            if (!Schema::hasColumn('college_settings', 'show_watermark')) {
                $table->boolean('show_watermark')->default(false)->after('show_college_logo');
            }
            if (!Schema::hasColumn('college_settings', 'watermark_text')) {
                $table->string('watermark_text')->nullable()->after('show_watermark');
            }
            if (!Schema::hasColumn('college_settings', 'marksheet_layout')) {
                $table->enum('marksheet_layout', ['standard', 'compact', 'detailed'])->default('standard')->after('watermark_text');
            }
            if (!Schema::hasColumn('college_settings', 'show_subject_codes')) {
                $table->boolean('show_subject_codes')->default(false)->after('marksheet_layout');
            }
            if (!Schema::hasColumn('college_settings', 'show_attendance')) {
                $table->boolean('show_attendance')->default(false)->after('show_subject_codes');
            }
            if (!Schema::hasColumn('college_settings', 'show_remarks')) {
                $table->boolean('show_remarks')->default(true)->after('show_attendance');
            }
            if (!Schema::hasColumn('college_settings', 'show_grade_scale')) {
                $table->boolean('show_grade_scale')->default(true)->after('show_remarks');
            }

            // Print settings
            if (!Schema::hasColumn('college_settings', 'paper_size')) {
                $table->enum('paper_size', ['A4', 'Letter', 'Legal'])->default('A4')->after('show_grade_scale');
            }
            if (!Schema::hasColumn('college_settings', 'orientation')) {
                $table->enum('orientation', ['portrait', 'landscape'])->default('portrait')->after('paper_size');
            }
            if (!Schema::hasColumn('college_settings', 'margin_top')) {
                $table->integer('margin_top')->default(20)->after('orientation');
            }
            if (!Schema::hasColumn('college_settings', 'margin_bottom')) {
                $table->integer('margin_bottom')->default(20)->after('margin_top');
            }
            if (!Schema::hasColumn('college_settings', 'margin_left')) {
                $table->integer('margin_left')->default(20)->after('margin_bottom');
            }
            if (!Schema::hasColumn('college_settings', 'margin_right')) {
                $table->integer('margin_right')->default(20)->after('margin_left');
            }

            // Colors and styling
            if (!Schema::hasColumn('college_settings', 'primary_color')) {
                $table->string('primary_color')->default('#2563eb')->after('margin_right');
            }
            if (!Schema::hasColumn('college_settings', 'secondary_color')) {
                $table->string('secondary_color')->default('#6b7280')->after('primary_color');
            }
            if (!Schema::hasColumn('college_settings', 'header_background_color')) {
                $table->string('header_background_color')->default('#f8fafc')->after('secondary_color');
            }

            // Additional information fields
            if (!Schema::hasColumn('college_settings', 'examination_rules')) {
                $table->text('examination_rules')->nullable()->after('header_background_color');
            }
            if (!Schema::hasColumn('college_settings', 'grade_calculation_method')) {
                $table->text('grade_calculation_method')->nullable()->after('examination_rules');
            }
            if (!Schema::hasColumn('college_settings', 'contact_person_name')) {
                $table->string('contact_person_name')->nullable()->after('grade_calculation_method');
            }
            if (!Schema::hasColumn('college_settings', 'contact_person_phone')) {
                $table->string('contact_person_phone')->nullable()->after('contact_person_name');
            }
            if (!Schema::hasColumn('college_settings', 'contact_person_email')) {
                $table->string('contact_person_email')->nullable()->after('contact_person_phone');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('college_settings', function (Blueprint $table) {
            $columnsToCheck = [
                'college_code',
                'affiliation',
                'university_name',
                'college_motto',
                'vice_principal_name',
                'vice_principal_signature_path',
                'academic_coordinator_name',
                'academic_coordinator_signature_path',
                'show_college_logo',
                'show_watermark',
                'watermark_text',
                'marksheet_layout',
                'show_subject_codes',
                'show_attendance',
                'show_remarks',
                'show_grade_scale',
                'paper_size',
                'orientation',
                'margin_top',
                'margin_bottom',
                'margin_left',
                'margin_right',
                'primary_color',
                'secondary_color',
                'header_background_color',
                'examination_rules',
                'grade_calculation_method',
                'contact_person_name',
                'contact_person_phone',
                'contact_person_email'
            ];

            $columnsToRemove = [];
            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('college_settings', $column)) {
                    $columnsToRemove[] = $column;
                }
            }

            if (!empty($columnsToRemove)) {
                $table->dropColumn($columnsToRemove);
            }
        });
    }
};
