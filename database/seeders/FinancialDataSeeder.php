<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fee;
use App\Models\Teacher;
use App\Models\AcademicYear;
use App\Models\Department;

class FinancialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the active academic year
        $academicYear = AcademicYear::where('is_active', true)->first();
        
        if (!$academicYear) {
            $this->command->error('No active academic year found. Please create an academic year first.');
            return;
        }

        // Get some departments
        $departments = Department::take(3)->get();

        // Create sample fees
        $feeTypes = [
            ['name' => 'Tuition Fee', 'type' => 'tuition', 'amount' => 50000],
            ['name' => 'Library Fee', 'type' => 'library', 'amount' => 2000],
            ['name' => 'Laboratory Fee', 'type' => 'laboratory', 'amount' => 5000],
            ['name' => 'Sports Fee', 'type' => 'sports', 'amount' => 1500],
            ['name' => 'Medical Fee', 'type' => 'medical', 'amount' => 1000],
            ['name' => 'Registration Fee', 'type' => 'registration', 'amount' => 3000],
            ['name' => 'Examination Fee', 'type' => 'examination', 'amount' => 2500],
        ];

        foreach ($feeTypes as $feeData) {
            Fee::create([
                'name' => $feeData['name'],
                'code' => Fee::generateFeeCode($feeData['type'], $academicYear->id),
                'description' => 'Standard ' . $feeData['name'] . ' for academic year ' . $academicYear->name,
                'fee_type' => $feeData['type'],
                'amount' => $feeData['amount'],
                'course_id' => null, // Apply to all courses
                'department_id' => null, // Apply to all departments
                'academic_year_id' => $academicYear->id,
                'is_mandatory' => in_array($feeData['type'], ['tuition', 'registration']),
                'is_active' => true,
                'due_date' => now()->addDays(30)
            ]);
        }

        // Create department-specific fees
        foreach ($departments as $department) {
            Fee::create([
                'name' => $department->name . ' Department Fee',
                'code' => Fee::generateFeeCode('other', $academicYear->id),
                'description' => 'Special fee for ' . $department->name . ' department',
                'fee_type' => 'other',
                'amount' => 3000,
                'department_id' => $department->id,
                'academic_year_id' => $academicYear->id,
                'semester' => 'both',
                'is_mandatory' => false,
                'is_active' => true,
                'due_date' => now()->addDays(45)
            ]);
        }

        // Create sample teachers
        $teacherData = [
            [
                'name' => 'Dr. John Smith',
                'department' => 'Computer Science',
                'position' => 'Professor',
                'salary' => 75000
            ],
            [
                'name' => 'Prof. Sarah Johnson',
                'department' => 'Mathematics',
                'position' => 'Associate Professor',
                'salary' => 65000
            ],
            [
                'name' => 'Dr. Michael Brown',
                'department' => 'Physics',
                'position' => 'Assistant Professor',
                'salary' => 55000
            ],
            [
                'name' => 'Ms. Emily Davis',
                'department' => 'English',
                'position' => 'Lecturer',
                'salary' => 45000
            ],
            [
                'name' => 'Dr. Robert Wilson',
                'department' => 'Chemistry',
                'position' => 'Professor',
                'salary' => 70000
            ]
        ];

        foreach ($teacherData as $data) {
            Teacher::create([
                'teacher_name' => $data['name'],
                'employee_id' => Teacher::generateEmployeeId($data['department']),
                'email' => strtolower(str_replace([' ', '.'], ['', ''], $data['name'])) . '@college.edu',
                'phone' => '+1-555-' . rand(1000, 9999),
                'department' => $data['department'],
                'position' => $data['position'],
                'hire_date' => now()->subYears(rand(1, 10)),
                'basic_salary' => $data['salary'],
                'status' => 'active',
                'bank_account' => 'ACC-' . rand(100000, 999999),
                'address' => rand(100, 999) . ' Main Street, City, State ' . rand(10000, 99999)
            ]);
        }

        $this->command->info('Financial data seeded successfully!');
        $this->command->info('Created ' . Fee::count() . ' fees and ' . Teacher::count() . ' teachers.');
    }
}
