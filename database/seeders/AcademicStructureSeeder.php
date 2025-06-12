<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\Course;
use App\Models\ClassSection;
use App\Models\AcademicYear;
use App\Models\User;

class AcademicStructureSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create Academic Year first
        $academicYear = AcademicYear::firstOrCreate([
            'code' => '2024-2025'
        ], [
            'name' => '2024-2025 Academic Year',
            'start_date' => '2024-09-01',
            'end_date' => '2025-08-31',
            'is_current' => true,
            'is_active' => true,
            'description' => 'Current academic year for testing',
            'semester_config' => [
                'first_semester' => ['start' => '2024-09-01', 'end' => '2025-01-31'],
                'second_semester' => ['start' => '2025-02-01', 'end' => '2025-08-31']
            ]
        ]);

        // Create Faculties
        $faculties = [
            [
                'name' => 'Faculty of Engineering',
                'code' => 'ENG',
                'description' => 'Engineering and Technology programs',
                'location' => 'Engineering Building',
                'phone' => '+1-555-0101',
                'email' => 'engineering@university.edu',
                'is_active' => true
            ],
            [
                'name' => 'Faculty of Sciences',
                'code' => 'SCI',
                'description' => 'Natural and Applied Sciences',
                'location' => 'Science Complex',
                'phone' => '+1-555-0102',
                'email' => 'sciences@university.edu',
                'is_active' => true
            ],
            [
                'name' => 'Faculty of Arts',
                'code' => 'ART',
                'description' => 'Liberal Arts and Humanities',
                'location' => 'Arts Building',
                'phone' => '+1-555-0103',
                'email' => 'arts@university.edu',
                'is_active' => true
            ]
        ];

        foreach ($faculties as $facultyData) {
            Faculty::firstOrCreate(['code' => $facultyData['code']], $facultyData);
        }

        // Get created faculties
        $engineering = Faculty::where('code', 'ENG')->first();
        $sciences = Faculty::where('code', 'SCI')->first();
        $arts = Faculty::where('code', 'ART')->first();

        // Create some optional departments
        $departments = [
            [
                'name' => 'Computer Science',
                'code' => 'CSC',
                'description' => 'Computer Science and Software Engineering',
                'faculty_id' => $engineering->id,
                'location' => 'CS Building',
                'duration_years' => 4,
                'degree_type' => 'bachelor',
                'is_active' => true
            ],
            [
                'name' => 'Mathematics',
                'code' => 'MTH',
                'description' => 'Pure and Applied Mathematics',
                'faculty_id' => $sciences->id,
                'location' => 'Math Building',
                'duration_years' => 4,
                'degree_type' => 'bachelor',
                'is_active' => true
            ]
        ];

        foreach ($departments as $deptData) {
            Department::firstOrCreate(['code' => $deptData['code']], $deptData);
        }

        $csDept = Department::where('code', 'CSC')->first();
        $mathDept = Department::where('code', 'MTH')->first();

        // Create Courses directly under Faculties (new structure)
        $courses = [
            // Engineering Faculty Courses
            [
                'title' => 'Introduction to Programming',
                'code' => 'ENG101',
                'description' => 'Basic programming concepts and problem solving',
                'faculty_id' => $engineering->id,
                'department_id' => $csDept->id, // Optional department
                'credit_units' => 3,
                'level' => 100,
                'semester' => 'first',
                'course_type' => 'core',
                'is_active' => true
            ],
            [
                'title' => 'Data Structures and Algorithms',
                'code' => 'ENG201',
                'description' => 'Advanced programming with data structures',
                'faculty_id' => $engineering->id,
                'department_id' => $csDept->id,
                'credit_units' => 4,
                'level' => 200,
                'semester' => 'second',
                'course_type' => 'core',
                'is_active' => true
            ],
            [
                'title' => 'Engineering Mathematics',
                'code' => 'ENG102',
                'description' => 'Mathematical foundations for engineering',
                'faculty_id' => $engineering->id,
                'department_id' => null, // No department - directly under faculty
                'credit_units' => 3,
                'level' => 100,
                'semester' => 'first',
                'course_type' => 'core',
                'is_active' => true
            ],
            
            // Sciences Faculty Courses
            [
                'title' => 'Calculus I',
                'code' => 'SCI101',
                'description' => 'Differential and integral calculus',
                'faculty_id' => $sciences->id,
                'department_id' => $mathDept->id,
                'credit_units' => 4,
                'level' => 100,
                'semester' => 'first',
                'course_type' => 'core',
                'is_active' => true
            ],
            [
                'title' => 'General Physics',
                'code' => 'SCI102',
                'description' => 'Introduction to physics principles',
                'faculty_id' => $sciences->id,
                'department_id' => null, // No department
                'credit_units' => 3,
                'level' => 100,
                'semester' => 'second',
                'course_type' => 'core',
                'is_active' => true
            ],
            
            // Arts Faculty Courses
            [
                'title' => 'English Composition',
                'code' => 'ART101',
                'description' => 'Academic writing and communication',
                'faculty_id' => $arts->id,
                'department_id' => null,
                'credit_units' => 3,
                'level' => 100,
                'semester' => 'first',
                'course_type' => 'general',
                'is_active' => true
            ],
            [
                'title' => 'Philosophy of Science',
                'code' => 'ART201',
                'description' => 'Philosophical foundations of scientific thought',
                'faculty_id' => $arts->id,
                'department_id' => null,
                'credit_units' => 2,
                'level' => 200,
                'semester' => 'both',
                'course_type' => 'elective',
                'is_active' => true
            ]
        ];

        foreach ($courses as $courseData) {
            Course::firstOrCreate(['code' => $courseData['code']], $courseData);
        }

        // Create some Classes for the courses
        $instructor = User::whereHas('roles', function($query) {
            $query->where('name', 'Teacher');
        })->first();

        if (!$instructor) {
            $instructor = User::whereHas('roles', function($query) {
                $query->where('name', 'Admin');
            })->first();
        }

        $coursesToCreateClasses = Course::take(4)->get();

        foreach ($coursesToCreateClasses as $course) {
            ClassSection::firstOrCreate([
                'name' => $course->code . '-A',
                'course_id' => $course->id,
                'academic_year_id' => $academicYear->id,
                'semester' => $course->semester === 'both' ? 'first' : $course->semester
            ], [
                'instructor_id' => $instructor?->id,
                'room' => 'Room ' . rand(101, 999),
                'capacity' => rand(30, 50),
                'enrolled_count' => 0,
                'status' => 'active',
                'start_date' => $academicYear->start_date,
                'end_date' => $academicYear->end_date,
                'schedule' => [
                    'monday' => ['start' => '09:00', 'end' => '10:30'],
                    'wednesday' => ['start' => '09:00', 'end' => '10:30'],
                    'friday' => ['start' => '09:00', 'end' => '10:30']
                ]
            ]);
        }

        $this->command->info('Academic structure seeded successfully!');
        $this->command->info('Created:');
        $this->command->info('- ' . Faculty::count() . ' Faculties');
        $this->command->info('- ' . Department::count() . ' Departments (optional)');
        $this->command->info('- ' . Course::count() . ' Courses');
        $this->command->info('- ' . ClassSection::count() . ' Classes');
        $this->command->info('');
        $this->command->info('Structure: Faculty → Course → Class');
        $this->command->info('Departments are optional organizational units.');
    }
}
