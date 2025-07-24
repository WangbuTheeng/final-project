<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollegeSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'college_name',
        'college_code',
        'college_address',
        'college_phone',
        'college_email',
        'college_website',
        'affiliation',
        'university_name',
        'college_motto',
        'logo_path',
        'show_college_logo',
        'show_watermark',
        'watermark_text',
        'result_header',
        'result_footer',
        'principal_name',
        'principal_signature_path',
        'vice_principal_name',
        'vice_principal_signature_path',
        'exam_controller_name',
        'exam_controller_signature_path',
        'registrar_name',
        'registrar_signature_path',
        'academic_coordinator_name',
        'academic_coordinator_signature_path',
        'class_teacher_name',
        'class_teacher_signature_path',
        'hod_name',
        'hod_signature_path',
        'marksheet_settings',
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
        'grading_system',
        'pass_percentage',
        'show_grade_points',
        'show_percentage',
        'examination_rules',
        'grade_calculation_method',
        'contact_person_name',
        'contact_person_phone',
        'contact_person_email'
    ];

    protected $casts = [
        'marksheet_settings' => 'array',
        'pass_percentage' => 'decimal:2',
        'show_grade_points' => 'boolean',
        'show_percentage' => 'boolean',
        'show_college_logo' => 'boolean',
        'show_watermark' => 'boolean',
        'show_subject_codes' => 'boolean',
        'show_attendance' => 'boolean',
        'show_remarks' => 'boolean',
        'show_grade_scale' => 'boolean',
        'margin_top' => 'integer',
        'margin_bottom' => 'integer',
        'margin_left' => 'integer',
        'margin_right' => 'integer'
    ];



    /**
     * Get the college settings (singleton pattern)
     */
    public static function getSettings()
    {
        return static::first() ?? static::create([
            'college_name' => 'College Name',
            'college_address' => 'College Address',
            'grading_system' => 'both',
            'pass_percentage' => 40.00,
            'show_grade_points' => true,
            'show_percentage' => true
        ]);
    }

    /**
     * Get pass percentage
     */
    public static function getPassPercentage()
    {
        $settings = static::getSettings();
        return $settings->pass_percentage ?? 40.00;
    }

    /**
     * Get college name
     */
    public static function getCollegeName()
    {
        $settings = static::getSettings();
        return $settings->college_name ?? 'College Name';
    }

    /**
     * Get college address
     */
    public static function getCollegeAddress()
    {
        $settings = static::getSettings();
        return $settings->college_address ?? 'College Address';
    }

    /**
     * Get logo path
     */
    public static function getLogoPath()
    {
        $settings = static::getSettings();
        return $settings->logo_path;
    }

    /**
     * Get grading system
     */
    public static function getGradingSystem()
    {
        $settings = static::getSettings();
        return $settings->grading_system ?? 'both';
    }

    /**
     * Check if should show grade points
     */
    public static function shouldShowGradePoints()
    {
        $settings = static::getSettings();
        return $settings->show_grade_points ?? true;
    }

    /**
     * Check if should show percentage
     */
    public static function shouldShowPercentage()
    {
        $settings = static::getSettings();
        return $settings->show_percentage ?? true;
    }

    /**
     * Get result header
     */
    public static function getResultHeader()
    {
        $settings = static::getSettings();
        return $settings->result_header;
    }

    /**
     * Get result footer
     */
    public static function getResultFooter()
    {
        $settings = static::getSettings();
        return $settings->result_footer;
    }

    /**
     * Get principal details
     */
    public static function getPrincipalDetails()
    {
        $settings = static::getSettings();
        return [
            'name' => $settings->principal_name,
            'signature_path' => $settings->principal_signature_path
        ];
    }

    /**
     * Get exam controller details
     */
    public static function getExamControllerDetails()
    {
        $settings = static::getSettings();
        return [
            'name' => $settings->exam_controller_name,
            'signature_path' => $settings->exam_controller_signature_path
        ];
    }

    /**
     * Get registrar details
     */
    public static function getRegistrarDetails()
    {
        $settings = static::getSettings();
        return [
            'name' => $settings->registrar_name,
            'signature_path' => $settings->registrar_signature_path
        ];
    }

    /**
     * Get class teacher details
     */
    public static function getClassTeacherDetails()
    {
        $settings = static::getSettings();
        return [
            'name' => $settings->class_teacher_name,
            'signature_path' => $settings->class_teacher_signature_path
        ];
    }

    /**
     * Get HOD details
     */
    public static function getHodDetails()
    {
        $settings = static::getSettings();
        return [
            'name' => $settings->hod_name,
            'signature_path' => $settings->hod_signature_path
        ];
    }
}
