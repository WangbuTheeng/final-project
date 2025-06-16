<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CollegeSetting;

class CollegeSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CollegeSetting::updateOrCreate(
            ['id' => 1],
            [
                'college_name' => 'Sample College of Technology',
                'college_address' => 'Kathmandu, Nepal',
                'college_phone' => '+977-1-4444444',
                'college_email' => 'info@samplecollege.edu.np',
                'college_website' => 'www.samplecollege.edu.np',
                'result_header' => 'Office of the Controller of Examinations',
                'result_footer' => 'This is a computer generated marksheet. No signature required.',
                'principal_name' => 'Dr. John Doe',
                'exam_controller_name' => 'Prof. Jane Smith',
                'registrar_name' => 'Mr. Bob Johnson',
                'class_teacher_name' => 'Ms. Sarah Wilson',
                'hod_name' => 'Dr. Michael Brown',
                'grading_system' => 'both',
                'pass_percentage' => 40.00,
                'show_grade_points' => true,
                'show_percentage' => true,
                'marksheet_settings' => [
                    'show_logo' => true,
                    'show_signatures' => true,
                    'show_qr_code' => false,
                    'watermark_text' => 'OFFICIAL',
                    'show_issue_date' => true,
                    'show_grading_scale' => false,
                ]
            ]
        );
    }
}
