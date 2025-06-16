<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollegeSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'college_name',
        'college_address',
        'college_phone',
        'college_email',
        'college_website',
        'logo_path',
        'result_header',
        'result_footer',
        'principal_name',
        'principal_signature_path',
        'exam_controller_name',
        'exam_controller_signature_path',
        'registrar_name',
        'registrar_signature_path',
        'class_teacher_name',
        'class_teacher_signature_path',
        'hod_name',
        'hod_signature_path',
        'marksheet_settings',
        'grading_system',
        'pass_percentage',
        'show_grade_points',
        'show_percentage'
    ];

    protected $casts = [
        'marksheet_settings' => 'array',
        'pass_percentage' => 'decimal:2',
        'show_grade_points' => 'boolean',
        'show_percentage' => 'boolean'
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
