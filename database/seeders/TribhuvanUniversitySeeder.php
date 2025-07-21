<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\Course;
use App\Models\ClassSection;
use App\Models\Subject;
use App\Models\AcademicYear;
use App\Models\User;
use App\Models\Student;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Hash;

class TribhuvanUniversitySeeder extends Seeder
{
    /**
     * Run the database seeds for Tribhuvan University structure.
     */
    public function run(): void
    {
        // Create Academic Years
        $this->command->info('Creating academic years...');

        // Clear existing academic years
        AcademicYear::truncate();

        $academicYears = [
            [
                'name' => '2081-2082',
                'code' => '2081-82',
                'start_date' => '2024-07-15',
                'end_date' => '2025-07-14',
                'description' => 'Academic Year 2081-2082 (Current)',
                'is_current' => true,
                'is_active' => true,
            ],
            [
                'name' => '2080-2081',
                'code' => '2080-81',
                'start_date' => '2023-07-15',
                'end_date' => '2024-07-14',
                'description' => 'Academic Year 2080-2081 (Previous)',
                'is_current' => false,
                'is_active' => false,
            ],
            [
                'name' => '2082-2083',
                'code' => '2082-83',
                'start_date' => '2025-07-15',
                'end_date' => '2026-07-14',
                'description' => 'Academic Year 2082-2083 (Upcoming)',
                'is_current' => false,
                'is_active' => false,
            ]
        ];

        $createdAcademicYears = [];
        foreach ($academicYears as $yearData) {
            $createdAcademicYears[] = AcademicYear::create($yearData);
        }

        $academicYear = AcademicYear::where('is_current', true)->first();
        $this->command->info('✅ Created ' . count($academicYears) . ' academic years');

        // Create Faculties
        $faculties = [
            [
                'name' => 'Faculty of Management',
                'code' => 'FOM',
                'description' => 'Faculty of Management offers business and management education',
                'email' => 'fom@tu.edu.np',
                'phone' => '+977-1-4331976',
                'is_active' => true,
            ],
            [
                'name' => 'Faculty of Humanities and Social Sciences',
                'code' => 'FHSS',
                'description' => 'Faculty offering humanities, social sciences, and computer application programs',
                'email' => 'fhss@tu.edu.np',
                'phone' => '+977-1-4331977',
                'is_active' => true,
            ],
            [
                'name' => 'Faculty of Science and Technology',
                'code' => 'FST',
                'description' => 'Faculty offering science and technology programs',
                'email' => 'fst@tu.edu.np',
                'phone' => '+977-1-4331978',
                'is_active' => true,
            ],
            [
                'name' => 'Faculty of Education',
                'code' => 'FOE',
                'description' => 'Faculty offering education programs',
                'email' => 'foe@tu.edu.np',
                'phone' => '+977-1-4331979',
                'is_active' => true,
            ],
        ];

        $createdFaculties = [];
        foreach ($faculties as $facultyData) {
            $createdFaculties[$facultyData['code']] = Faculty::create($facultyData);
        }

        // Create Departments
        $departments = [
            // Faculty of Management Departments
            [
                'name' => 'Department of Business Management',
                'code' => 'DBM',
                'description' => 'Department offering business management programs',
                'faculty_id' => $createdFaculties['FOM']->id,
                'email' => 'dbm@tu.edu.np',
                'phone' => '+977-1-4331980',
                'is_active' => true,
            ],
            [
                'name' => 'Department of Accounting',
                'code' => 'DAC',
                'description' => 'Department offering accounting and finance programs',
                'faculty_id' => $createdFaculties['FOM']->id,
                'email' => 'dac@tu.edu.np',
                'phone' => '+977-1-4331981',
                'is_active' => true,
            ],
            // Faculty of Humanities and Social Sciences Departments
            [
                'name' => 'Department of Computer Application',
                'code' => 'DCA',
                'description' => 'Department offering computer application programs',
                'faculty_id' => $createdFaculties['FHSS']->id,
                'email' => 'dca@tu.edu.np',
                'phone' => '+977-1-4331982',
                'is_active' => true,
            ],
            [
                'name' => 'Department of Social Work',
                'code' => 'DSW',
                'description' => 'Department offering social work programs',
                'faculty_id' => $createdFaculties['FHSS']->id,
                'email' => 'dsw@tu.edu.np',
                'phone' => '+977-1-4331983',
                'is_active' => true,
            ],
            // Faculty of Science and Technology Departments
            [
                'name' => 'Department of Computer Science and Information Technology',
                'code' => 'DCSIT',
                'description' => 'Department offering computer science and IT programs',
                'faculty_id' => $createdFaculties['FST']->id,
                'email' => 'dcsit@tu.edu.np',
                'phone' => '+977-1-4331984',
                'is_active' => true,
            ],
            [
                'name' => 'Department of Mathematics',
                'code' => 'DMATH',
                'description' => 'Department offering mathematics programs',
                'faculty_id' => $createdFaculties['FST']->id,
                'email' => 'dmath@tu.edu.np',
                'phone' => '+977-1-4331985',
                'is_active' => true,
            ],
        ];

        $createdDepartments = [];
        foreach ($departments as $deptData) {
            $createdDepartments[$deptData['code']] = Department::create($deptData);
        }

        // Create Courses
        $courses = [
            // Management Faculty Courses
            [
                'title' => 'Bachelor of Business Studies',
                'code' => 'BBS',
                'description' => '4-year bachelor degree program in business studies',
                'department_id' => $createdDepartments['DBM']->id,
                'credit_units' => 126,
                'organization_type' => 'yearly',
                'year' => 4,
                'course_type' => 'core',
                'examination_system' => 'annual', // Traditional annual system
                'is_active' => true,
            ],
            [
                'title' => 'Bachelor of Business Administration',
                'code' => 'BBA',
                'description' => '4-year bachelor degree program in business administration',
                'department_id' => $createdDepartments['DBM']->id,
                'credit_units' => 120,
                'organization_type' => 'yearly',
                'year' => 4,
                'course_type' => 'core',
                'examination_system' => 'annual', // Traditional annual system
                'is_active' => true,
            ],
            // Humanities Faculty Courses
            [
                'title' => 'Bachelor of Computer Application',
                'code' => 'BCA',
                'description' => '4-year (8 semester) bachelor degree program in computer application',
                'department_id' => $createdDepartments['DCA']->id,
                'credit_units' => 126,
                'organization_type' => 'semester',
                'semester_period' => 8,
                'course_type' => 'core',
                'examination_system' => 'semester', // Modern semester system
                'is_active' => true,
            ],
            [
                'title' => 'Bachelor of Social Work',
                'code' => 'BSW',
                'description' => '4-year bachelor degree program in social work',
                'department_id' => $createdDepartments['DSW']->id,
                'credit_units' => 120,
                'organization_type' => 'yearly',
                'year' => 4,
                'course_type' => 'core',
                'examination_system' => 'annual', // Traditional annual system
                'is_active' => true,
            ],
            // Science Faculty Courses
            [
                'title' => 'Bachelor of Science in Computer Science and Information Technology',
                'code' => 'BSC-CSIT',
                'description' => '4-year (8 semester) bachelor degree program in computer science',
                'department_id' => $createdDepartments['DCSIT']->id,
                'credit_units' => 126,
                'organization_type' => 'semester',
                'semester_period' => 8,
                'course_type' => 'core',
                'examination_system' => 'semester', // Modern semester system
                'is_active' => true,
            ],
        ];

        $createdCourses = [];
        foreach ($courses as $courseData) {
            $createdCourses[$courseData['code']] = Course::create($courseData);
        }

        // Create some instructors
        $instructors = [
            [
                'name' => 'Dr. Ram Prasad Sharma',
                'email' => 'ram.sharma@tu.edu.np',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'phone' => '+977-9841234567',
                'address' => 'Kathmandu, Nepal',
                'is_active' => true,
            ],
            [
                'name' => 'Prof. Sita Devi Poudel',
                'email' => 'sita.poudel@tu.edu.np',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'phone' => '+977-9841234568',
                'address' => 'Lalitpur, Nepal',
                'is_active' => true,
            ],
            [
                'name' => 'Dr. Hari Bahadur Thapa',
                'email' => 'hari.thapa@tu.edu.np',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'phone' => '+977-9841234569',
                'address' => 'Bhaktapur, Nepal',
                'is_active' => true,
            ],
            [
                'name' => 'Ms. Gita Kumari Shrestha',
                'email' => 'gita.shrestha@tu.edu.np',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'phone' => '+977-9841234570',
                'address' => 'Kathmandu, Nepal',
                'is_active' => true,
            ],
        ];

        $createdInstructors = [];
        foreach ($instructors as $instructorData) {
            $createdInstructors[] = User::create($instructorData);
        }

        // Create Class Sections (Following TU structure)
        $classSections = [
            // BCA Classes (8 semesters)
            ['name' => 'BCA 1st Semester', 'course_id' => $createdCourses['BCA']->id, 'semester' => 1, 'capacity' => 40],
            ['name' => 'BCA 2nd Semester', 'course_id' => $createdCourses['BCA']->id, 'semester' => 2, 'capacity' => 40],
            ['name' => 'BCA 3rd Semester', 'course_id' => $createdCourses['BCA']->id, 'semester' => 3, 'capacity' => 40],
            ['name' => 'BCA 4th Semester', 'course_id' => $createdCourses['BCA']->id, 'semester' => 4, 'capacity' => 40],
            ['name' => 'BCA 5th Semester', 'course_id' => $createdCourses['BCA']->id, 'semester' => 5, 'capacity' => 40],
            ['name' => 'BCA 6th Semester', 'course_id' => $createdCourses['BCA']->id, 'semester' => 6, 'capacity' => 40],
            ['name' => 'BCA 7th Semester', 'course_id' => $createdCourses['BCA']->id, 'semester' => 7, 'capacity' => 40],
            ['name' => 'BCA 8th Semester', 'course_id' => $createdCourses['BCA']->id, 'semester' => 8, 'capacity' => 40],

            // BBS Classes (4 years)
            ['name' => 'BBS 1st Year', 'course_id' => $createdCourses['BBS']->id, 'semester' => 1, 'capacity' => 50],
            ['name' => 'BBS 2nd Year', 'course_id' => $createdCourses['BBS']->id, 'semester' => 2, 'capacity' => 50],
            ['name' => 'BBS 3rd Year', 'course_id' => $createdCourses['BBS']->id, 'semester' => 3, 'capacity' => 50],
            ['name' => 'BBS 4th Year', 'course_id' => $createdCourses['BBS']->id, 'semester' => 4, 'capacity' => 50],

            // BBA Classes (4 years)
            ['name' => 'BBA 1st Year', 'course_id' => $createdCourses['BBA']->id, 'semester' => 1, 'capacity' => 45],
            ['name' => 'BBA 2nd Year', 'course_id' => $createdCourses['BBA']->id, 'semester' => 2, 'capacity' => 45],
            ['name' => 'BBA 3rd Year', 'course_id' => $createdCourses['BBA']->id, 'semester' => 3, 'capacity' => 45],
            ['name' => 'BBA 4th Year', 'course_id' => $createdCourses['BBA']->id, 'semester' => 4, 'capacity' => 45],

            // BSW Classes (4 years)
            ['name' => 'BSW 1st Year', 'course_id' => $createdCourses['BSW']->id, 'semester' => 1, 'capacity' => 35],
            ['name' => 'BSW 2nd Year', 'course_id' => $createdCourses['BSW']->id, 'semester' => 2, 'capacity' => 35],
            ['name' => 'BSW 3rd Year', 'course_id' => $createdCourses['BSW']->id, 'semester' => 3, 'capacity' => 35],
            ['name' => 'BSW 4th Year', 'course_id' => $createdCourses['BSW']->id, 'semester' => 4, 'capacity' => 35],

            // BSC CSIT Classes (8 semesters)
            ['name' => 'BSC CSIT 1st Semester', 'course_id' => $createdCourses['BSC-CSIT']->id, 'semester' => 1, 'capacity' => 48],
            ['name' => 'BSC CSIT 2nd Semester', 'course_id' => $createdCourses['BSC-CSIT']->id, 'semester' => 2, 'capacity' => 48],
            ['name' => 'BSC CSIT 3rd Semester', 'course_id' => $createdCourses['BSC-CSIT']->id, 'semester' => 3, 'capacity' => 48],
            ['name' => 'BSC CSIT 4th Semester', 'course_id' => $createdCourses['BSC-CSIT']->id, 'semester' => 4, 'capacity' => 48],
        ];

        $createdClasses = [];
        foreach ($classSections as $classData) {
            $classData['academic_year_id'] = $academicYear->id;
            $classData['instructor_id'] = $createdInstructors[array_rand($createdInstructors)]->id;
            $classData['room'] = 'Room ' . rand(101, 999);
            $classData['start_date'] = $academicYear->start_date;
            $classData['end_date'] = $academicYear->end_date;
            $classData['status'] = 'active';

            $createdClasses[] = ClassSection::create($classData);
        }

        $this->command->info('Created class sections successfully!');
    }
}
