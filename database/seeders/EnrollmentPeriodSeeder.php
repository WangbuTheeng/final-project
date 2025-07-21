<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EnrollmentPeriod;
use App\Models\AcademicYear;
use Carbon\Carbon;

class EnrollmentPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create academic year if it doesn't exist
        $academicYear = AcademicYear::firstOrCreate([
            'name' => '2024-2025',
            'code' => '2024-25',
            'start_date' => '2024-07-01',
            'end_date' => '2025-06-30',
            'is_active' => true
        ]);

        // Create enrollment periods for Nepal's academic calendar
        $enrollmentPeriods = [
            [
                'name' => 'First Semester 2024-2025 Regular Enrollment',
                'academic_year_id' => $academicYear->id,
                'semester' => 'first',
                'type' => 'regular',
                'enrollment_start_date' => '2024-07-01',
                'enrollment_end_date' => '2024-07-15',
                'add_drop_deadline' => '2024-07-30',
                'late_enrollment_deadline' => '2024-08-15',
                'base_enrollment_fee' => 2000.00, // NPR 2000
                'late_enrollment_penalty' => 500.00, // NPR 500
                'per_credit_fee' => 150.00, // NPR 150 per credit
                'is_active' => true,
                'allow_waitlist' => true,
                'max_credits_per_student' => 21,
                'min_credits_per_student' => 12,
                'minimum_attendance_required' => 75.00,
                'requires_prerequisite_check' => true,
                'enrollment_instructions' => 'Students must complete fee payment within 7 days of enrollment. Minimum 75% attendance is required for all courses.',
                'notes' => 'First semester enrollment for academic year 2024-2025'
            ],
            [
                'name' => 'Second Semester 2024-2025 Regular Enrollment',
                'academic_year_id' => $academicYear->id,
                'semester' => 'second',
                'type' => 'regular',
                'enrollment_start_date' => '2025-01-01',
                'enrollment_end_date' => '2025-01-15',
                'add_drop_deadline' => '2025-01-30',
                'late_enrollment_deadline' => '2025-02-15',
                'base_enrollment_fee' => 2000.00,
                'late_enrollment_penalty' => 500.00,
                'per_credit_fee' => 150.00,
                'is_active' => false, // Not yet active
                'allow_waitlist' => true,
                'max_credits_per_student' => 21,
                'min_credits_per_student' => 12,
                'minimum_attendance_required' => 75.00,
                'requires_prerequisite_check' => true,
                'enrollment_instructions' => 'Students must have passed first semester courses to enroll in second semester. Fee payment required within 7 days.',
                'notes' => 'Second semester enrollment for academic year 2024-2025'
            ],
            [
                'name' => 'Summer Semester 2025 Makeup Enrollment',
                'academic_year_id' => $academicYear->id,
                'semester' => 'summer',
                'type' => 'makeup',
                'enrollment_start_date' => '2025-05-01',
                'enrollment_end_date' => '2025-05-15',
                'add_drop_deadline' => '2025-05-25',
                'late_enrollment_deadline' => '2025-06-01',
                'base_enrollment_fee' => 1500.00,
                'late_enrollment_penalty' => 300.00,
                'per_credit_fee' => 200.00, // Higher fee for summer/makeup
                'is_active' => false,
                'allow_waitlist' => false,
                'max_credits_per_student' => 12, // Lower limit for summer
                'min_credits_per_student' => 3,
                'minimum_attendance_required' => 80.00, // Higher attendance requirement
                'requires_prerequisite_check' => true,
                'enrollment_instructions' => 'Summer semester is for makeup courses only. Students must have failed or missed courses from regular semesters.',
                'notes' => 'Summer makeup semester for academic year 2024-2025'
            ]
        ];

        foreach ($enrollmentPeriods as $period) {
            EnrollmentPeriod::firstOrCreate(
                [
                    'name' => $period['name'],
                    'academic_year_id' => $period['academic_year_id'],
                    'semester' => $period['semester']
                ],
                $period
            );
        }

        $this->command->info('Enrollment periods created successfully!');
    }
}
