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
            $table->string('college_code')->nullable()->after('college_name');
            $table->string('affiliation')->nullable()->after('college_address');
            $table->string('university_name')->nullable()->after('affiliation');
            $table->text('college_motto')->nullable()->after('university_name');
            
            // Enhanced signature fields
            $table->string('vice_principal_name')->nullable()->after('principal_signature_path');
            $table->string('vice_principal_signature_path')->nullable()->after('vice_principal_name');
            $table->string('academic_coordinator_name')->nullable()->after('registrar_signature_path');
            $table->string('academic_coordinator_signature_path')->nullable()->after('academic_coordinator_name');
            
            // Marksheet formatting options
            $table->boolean('show_college_logo')->default(true)->after('logo_path');
            $table->boolean('show_watermark')->default(false)->after('show_college_logo');
            $table->string('watermark_text')->nullable()->after('show_watermark');
            $table->enum('marksheet_layout', ['standard', 'compact', 'detailed'])->default('standard')->after('watermark_text');
            $table->boolean('show_subject_codes')->default(false)->after('marksheet_layout');
            $table->boolean('show_attendance')->default(false)->after('show_subject_codes');
            $table->boolean('show_remarks')->default(true)->after('show_attendance');
            $table->boolean('show_grade_scale')->default(true)->after('show_remarks');
            
            // Print settings
            $table->enum('paper_size', ['A4', 'Letter', 'Legal'])->default('A4')->after('show_grade_scale');
            $table->enum('orientation', ['portrait', 'landscape'])->default('portrait')->after('paper_size');
            $table->integer('margin_top')->default(20)->after('orientation');
            $table->integer('margin_bottom')->default(20)->after('margin_top');
            $table->integer('margin_left')->default(20)->after('margin_bottom');
            $table->integer('margin_right')->default(20)->after('margin_left');
            
            // Colors and styling
            $table->string('primary_color')->default('#2563eb')->after('margin_right');
            $table->string('secondary_color')->default('#6b7280')->after('primary_color');
            $table->string('header_background_color')->default('#f8fafc')->after('secondary_color');
            
            // Additional information fields
            $table->text('examination_rules')->nullable()->after('header_background_color');
            $table->text('grade_calculation_method')->nullable()->after('examination_rules');
            $table->string('contact_person_name')->nullable()->after('grade_calculation_method');
            $table->string('contact_person_phone')->nullable()->after('contact_person_name');
            $table->string('contact_person_email')->nullable()->after('contact_person_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('college_settings', function (Blueprint $table) {
            $table->dropColumn([
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
            ]);
        });
    }
};
