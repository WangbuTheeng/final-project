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

        // Create Tribhuvan University Faculties
        $faculties = [
            [
                'name' => 'Faculty of Humanities and Social Sciences',
                'code' => 'FoHSS',
                'description' => 'Humanities, Social Sciences, and Computer Applications (BCA, BA, etc.)',
                'location' => 'Kirtipur Campus',
                'phone' => '+977-1-4331564',
                'email' => 'fohss@tu.edu.np',
                'is_active' => true
            ],
            [
                'name' => 'Faculty of Management',
                'code' => 'FoM',
                'description' => 'Business and Management programs (BBA, BBM, BIM, etc.)',
                'location' => 'Kirtipur Campus',
                'phone' => '+977-1-4331565',
                'email' => 'fom@tu.edu.np',
                'is_active' => true
            ],
            [
                'name' => 'Faculty of Engineering',
                'code' => 'FoE',
                'description' => 'Engineering programs (BE Computer, Civil, Electronics, etc.)',
                'location' => 'Pulchowk Campus',
                'phone' => '+977-1-5525555',
                'email' => 'foe@tu.edu.np',
                'is_active' => true
            ],
            [
                'name' => 'Institute of Science and Technology',
                'code' => 'IoST',
                'description' => 'Science and Technology programs (BIT, BSc CSIT, etc.)',
                'location' => 'Kirtipur Campus',
                'phone' => '+977-1-4331566',
                'email' => 'iost@tu.edu.np',
                'is_active' => true
            ],
            [
                'name' => 'Faculty of Education',
                'code' => 'FoEd',
                'description' => 'Education and Teacher Training programs',
                'location' => 'Kirtipur Campus',
                'phone' => '+977-1-4331567',
                'email' => 'foed@tu.edu.np',
                'is_active' => true
            ],
            [
                'name' => 'Faculty of Law',
                'code' => 'FoL',
                'description' => 'Law and Legal Studies programs',
                'location' => 'Kirtipur Campus',
                'phone' => '+977-1-4331568',
                'email' => 'fol@tu.edu.np',
                'is_active' => true
            ]
        ];

        foreach ($faculties as $facultyData) {
            Faculty::firstOrCreate(['code' => $facultyData['code']], $facultyData);
        }

        // Get created faculties
        $fohss = Faculty::where('code', 'FoHSS')->first();
        $fom = Faculty::where('code', 'FoM')->first();
        $foe = Faculty::where('code', 'FoE')->first();
        $iost = Faculty::where('code', 'IoST')->first();
        $foed = Faculty::where('code', 'FoEd')->first();
        $fol = Faculty::where('code', 'FoL')->first();

        // Create TU departments
        $departments = [
            // FoHSS Departments
            [
                'name' => 'Computer Applications',
                'code' => 'CA',
                'description' => 'Bachelor in Computer Applications (BCA)',
                'faculty_id' => $fohss->id,
                'location' => 'FoHSS Building',
                'duration_years' => 4,
                'degree_type' => 'bachelor',
                'is_active' => true
            ],
            [
                'name' => 'English',
                'code' => 'ENG',
                'description' => 'English Language and Literature',
                'faculty_id' => $fohss->id,
                'location' => 'FoHSS Building',
                'duration_years' => 4,
                'degree_type' => 'bachelor',
                'is_active' => true
            ],
            // FoM Departments
            [
                'name' => 'Business Administration',
                'code' => 'BA',
                'description' => 'Bachelor of Business Administration (BBA)',
                'faculty_id' => $fom->id,
                'location' => 'Management Building',
                'duration_years' => 4,
                'degree_type' => 'bachelor',
                'is_active' => true
            ],
            [
                'name' => 'Business Management',
                'code' => 'BM',
                'description' => 'Bachelor of Business Management (BBM)',
                'faculty_id' => $fom->id,
                'location' => 'Management Building',
                'duration_years' => 4,
                'degree_type' => 'bachelor',
                'is_active' => true
            ],
            // IoST Departments
            [
                'name' => 'Information Technology',
                'code' => 'IT',
                'description' => 'Bachelor in Information Technology (BIT)',
                'faculty_id' => $iost->id,
                'location' => 'Science Building',
                'duration_years' => 4,
                'degree_type' => 'bachelor',
                'is_active' => true
            ],
            [
                'name' => 'Computer Science and Information Technology',
                'code' => 'CSIT',
                'description' => 'BSc Computer Science and Information Technology',
                'faculty_id' => $iost->id,
                'location' => 'Science Building',
                'duration_years' => 4,
                'degree_type' => 'bachelor',
                'is_active' => true
            ],
            // FoE Departments
            [
                'name' => 'Computer Engineering',
                'code' => 'CE',
                'description' => 'Bachelor in Computer Engineering',
                'faculty_id' => $foe->id,
                'location' => 'Pulchowk Campus',
                'duration_years' => 4,
                'degree_type' => 'bachelor',
                'is_active' => true
            ]
        ];

        foreach ($departments as $deptData) {
            Department::firstOrCreate(['code' => $deptData['code']], $deptData);
        }

        $caDept = Department::where('code', 'CA')->first();
        $baDept = Department::where('code', 'BA')->first();
        $itDept = Department::where('code', 'IT')->first();
        $ceDept = Department::where('code', 'CE')->first();

        // Create TU Courses (semester-based as per TU system)
        $courses = [
            // BCA Courses (FoHSS)
            [
                'title' => 'Computer Fundamentals and Applications',
                'code' => 'CACS101',
                'description' => 'Introduction to computer systems and basic applications',
                'department_id' => $caDept->id,
                'credit_units' => 3,
                'organization_type' => 'semester',
                'semester_period' => 1,
                'course_type' => 'core',
                'is_active' => true
            ],
            [
                'title' => 'Society and Technology',
                'code' => 'CACS102',
                'description' => 'Impact of technology on society',
                'department_id' => $caDept->id,
                'credit_units' => 3,
                'organization_type' => 'semester',
                'semester_period' => 1,
                'course_type' => 'core',
                'is_active' => true
            ],
            [
                'title' => 'English I',
                'code' => 'CAEN103',
                'description' => 'Basic English communication skills',
                'department_id' => $caDept->id,
                'credit_units' => 3,
                'organization_type' => 'semester',
                'semester_period' => 1,
                'course_type' => 'core',
                'is_active' => true
            ],
            [
                'title' => 'Mathematics I',
                'code' => 'CAMT104',
                'description' => 'Basic mathematics for computer applications',
                'department_id' => $caDept->id,
                'credit_units' => 3,
                'organization_type' => 'semester',
                'semester_period' => 1,
                'course_type' => 'core',
                'is_active' => true
            ],
            [
                'title' => 'Digital Logic',
                'code' => 'CADL105',
                'description' => 'Digital logic circuits and Boolean algebra',
                'department_id' => $caDept->id,
                'credit_units' => 3,
                'organization_type' => 'semester',
                'semester_period' => 1,
                'course_type' => 'core',
                'is_active' => true
            ],
            // BCA Semester 2 Courses
            [
                'title' => 'C Programming',
                'code' => 'CACP201',
                'description' => 'Programming in C language',

                'department_id' => $caDept->id,
                'credit_units' => 3,
                'organization_type' => 'semester',
                'semester_period' => 2,
                'course_type' => 'core',
                'is_active' => true
            ],
            [
                'title' => 'Financial Accounting',
                'code' => 'CAFA202',
                'description' => 'Basic principles of accounting',

                'department_id' => $caDept->id,
                'credit_units' => 3,
                'organization_type' => 'semester',
                'semester_period' => 2,
                'course_type' => 'core',
                'is_active' => true
            ],

            // BBA Courses (FoM)
            [
                'title' => 'Principles of Management',
                'code' => 'MGT101',
                'description' => 'Introduction to management principles',

                'department_id' => $baDept->id,
                'credit_units' => 3,
                'organization_type' => 'semester',
                'semester_period' => 1,
                'course_type' => 'core',
                'is_active' => true
            ],
            [
                'title' => 'Business English',
                'code' => 'ENG101',
                'description' => 'English for business communication',

                'department_id' => $baDept->id,
                'credit_units' => 3,
                'organization_type' => 'semester',
                'semester_period' => 1,
                'course_type' => 'core',
                'is_active' => true
            ],

            // BIT Courses (IoST)
            [
                'title' => 'Programming in C',
                'code' => 'BIT101',
                'description' => 'Introduction to programming using C',

                'department_id' => $itDept->id,
                'credit_units' => 3,
                'organization_type' => 'semester',
                'semester_period' => 1,
                'course_type' => 'core',
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
            // Determine semester for ClassSection based on Course organization_type
            $classSemester = 1; // Default to semester 1
            if ($course->organization_type === 'semester') {
                // For semester-based courses, use the actual semester_period
                $classSemester = $course->semester_period ?? 1;
            }

            ClassSection::firstOrCreate([
                'name' => $course->code . '-A',
                'course_id' => $course->id,
                'academic_year_id' => $academicYear->id,
                'semester' => $classSemester // Use the determined semester
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

        $this->command->info('Tribhuvan University academic structure seeded successfully!');
        $this->command->info('Created:');
        $this->command->info('- ' . Faculty::count() . ' TU Faculties (FoHSS, FoM, FoE, IoST, etc.)');
        $this->command->info('- ' . Department::count() . ' Departments (BCA, BBA, BIT, etc.)');
        $this->command->info('- ' . Course::count() . ' TU Courses (semester-based)');
        $this->command->info('- ' . ClassSection::count() . ' Classes');
        $this->command->info('');
        $this->command->info('TU Structure: Faculty → Department → Course → Class');
        $this->command->info('All courses follow TU\'s 8-semester system.');
    }
}
