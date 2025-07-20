<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\ClassSection;
use App\Models\User;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get BCA classes first, then other classes
        $bcaClasses = ClassSection::with('course')
            ->whereHas('course', function($query) {
                $query->where('title', 'like', '%Computer Application%')
                      ->orWhere('code', 'like', '%BCA%');
            })
            ->get();

        $otherClasses = ClassSection::with('course')
            ->whereDoesntHave('course', function($query) {
                $query->where('title', 'like', '%Computer Application%')
                      ->orWhere('code', 'like', '%BCA%');
            })
            ->take(3)
            ->get();

        $classes = $bcaClasses->merge($otherClasses);
        
        if ($classes->isEmpty()) {
            $this->command->warn('No classes found. Please run AcademicStructureSeeder first.');
            return;
        }

        // Get an instructor
        $instructor = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['Teacher', 'Admin']);
        })->first();

        foreach ($classes as $class) {
            $courseCode = $class->course->code;
            $className = $class->name;

            // Create subjects based on the course type
            if (str_contains($class->course->title, 'Computer Application') || str_contains($class->course->code, 'BCA')) {
                $this->createBCASubjects($class, $courseCode, $instructor);
            } elseif (str_contains($class->course->title, 'Programming')) {
                $this->createProgrammingSubjects($class, $courseCode, $instructor);
            } elseif (str_contains($class->course->title, 'Mathematics') || str_contains($class->course->title, 'Calculus')) {
                $this->createMathSubjects($class, $courseCode, $instructor);
            } else {
                $this->createGeneralSubjects($class, $courseCode, $instructor);
            }
        }

        $this->command->info('Subjects seeded successfully!');
        $this->command->info('Created subjects for ' . $classes->count() . ' classes');
        $this->command->info('Total subjects: ' . Subject::count());
    }

    private function createProgrammingSubjects($class, $courseCode, $instructor)
    {
        $subjects = [
            [
                'name' => 'Introduction to Programming Concepts',
                'code' => $courseCode . '-S01',
                'description' => 'Basic programming concepts and problem-solving techniques',
                'order_sequence' => 1,
                'duration_hours' => 8,
                'difficulty_level' => 'beginner',
                'subject_type' => 'theory',
                'learning_objectives' => [
                    'Understand basic programming concepts',
                    'Learn problem-solving techniques',
                    'Introduction to algorithms'
                ]
            ],
            [
                'name' => 'Variables and Data Types',
                'code' => $courseCode . '-S02',
                'description' => 'Understanding variables, data types, and memory management',
                'order_sequence' => 2,
                'duration_hours' => 6,
                'difficulty_level' => 'beginner',
                'subject_type' => 'mixed',
                'learning_objectives' => [
                    'Declare and use variables',
                    'Understand different data types',
                    'Basic memory concepts'
                ]
            ],
            [
                'name' => 'Control Structures',
                'code' => $courseCode . '-S03',
                'description' => 'Conditional statements and loops',
                'order_sequence' => 3,
                'duration_hours' => 10,
                'difficulty_level' => 'intermediate',
                'subject_type' => 'practical',
                'learning_objectives' => [
                    'Use if-else statements',
                    'Implement loops',
                    'Nested control structures'
                ]
            ],
            [
                'name' => 'Functions and Procedures',
                'code' => $courseCode . '-S04',
                'description' => 'Creating and using functions, parameter passing',
                'order_sequence' => 4,
                'duration_hours' => 8,
                'difficulty_level' => 'intermediate',
                'subject_type' => 'practical',
                'learning_objectives' => [
                    'Define and call functions',
                    'Understand parameter passing',
                    'Return values and scope'
                ]
            ]
        ];

        $this->createSubjectsForClass($class, $subjects, $instructor);
    }

    private function createMathSubjects($class, $courseCode, $instructor)
    {
        $subjects = [
            [
                'name' => 'Limits and Continuity',
                'code' => $courseCode . '-S01',
                'description' => 'Introduction to limits and continuous functions',
                'order_sequence' => 1,
                'duration_hours' => 12,
                'difficulty_level' => 'intermediate',
                'subject_type' => 'theory',
                'learning_objectives' => [
                    'Understand the concept of limits',
                    'Evaluate limits algebraically',
                    'Understand continuity'
                ]
            ],
            [
                'name' => 'Derivatives',
                'code' => $courseCode . '-S02',
                'description' => 'Differentiation rules and applications',
                'order_sequence' => 2,
                'duration_hours' => 15,
                'difficulty_level' => 'intermediate',
                'subject_type' => 'mixed',
                'learning_objectives' => [
                    'Apply differentiation rules',
                    'Find derivatives of complex functions',
                    'Solve optimization problems'
                ]
            ],
            [
                'name' => 'Integration',
                'code' => $courseCode . '-S03',
                'description' => 'Integration techniques and applications',
                'order_sequence' => 3,
                'duration_hours' => 15,
                'difficulty_level' => 'advanced',
                'subject_type' => 'mixed',
                'learning_objectives' => [
                    'Apply integration techniques',
                    'Solve definite and indefinite integrals',
                    'Applications of integration'
                ]
            ]
        ];

        $this->createSubjectsForClass($class, $subjects, $instructor);
    }

    private function createBCASubjects($class, $courseCode, $instructor)
    {
        // Determine semester based on class name or semester field
        $semester = $class->semester ?? 1;

        // Get BCA subjects based on semester
        $subjects = $this->getBCASubjectsBySemester($semester, $courseCode);

        $this->createSubjectsForClass($class, $subjects, $instructor);
    }

    private function getBCASubjectsBySemester($semester, $courseCode)
    {
        $allSubjects = [
            1 => [ // Semester I
                [
                    'name' => 'Computer Fundamentals & Applications',
                    'code' => 'CACS101',
                    'description' => 'Introduction to computer systems, hardware, software, and basic applications',
                    'credit_weight' => 4,
                    'subject_type' => 'mixed',
                    'difficulty_level' => 'beginner',
                    'is_practical' => true,
                    'full_marks_theory' => 60,
                    'full_marks_practical' => 40,
                ],
                [
                    'name' => 'Society and Technology',
                    'code' => 'CASO102',
                    'description' => 'Impact of technology on society and ethical considerations',
                    'credit_weight' => 3,
                    'subject_type' => 'theory',
                    'difficulty_level' => 'beginner',
                    'is_practical' => false,
                    'full_marks_theory' => 100,
                    'full_marks_practical' => 0,
                ],
                [
                    'name' => 'English I',
                    'code' => 'CAEN103',
                    'description' => 'Basic English communication skills and grammar',
                    'credit_weight' => 3,
                    'subject_type' => 'theory',
                    'difficulty_level' => 'beginner',
                    'is_practical' => false,
                    'full_marks_theory' => 100,
                    'full_marks_practical' => 0,
                ],
                [
                    'name' => 'Mathematics I',
                    'code' => 'CAMT104',
                    'description' => 'Algebra, trigonometry, and basic calculus',
                    'credit_weight' => 3,
                    'subject_type' => 'mixed',
                    'difficulty_level' => 'intermediate',
                    'is_practical' => true,
                    'full_marks_theory' => 80,
                    'full_marks_practical' => 20,
                ],
                [
                    'name' => 'Digital Logic',
                    'code' => 'CACS105',
                    'description' => 'Boolean algebra, logic gates, and digital circuits',
                    'credit_weight' => 3,
                    'subject_type' => 'mixed',
                    'difficulty_level' => 'intermediate',
                    'is_practical' => true,
                    'full_marks_theory' => 70,
                    'full_marks_practical' => 30,
                ],
            ],
            2 => [ // Semester II
                [
                    'name' => 'C Programming',
                    'code' => 'CACS151',
                    'description' => 'Programming fundamentals using C language',
                    'credit_weight' => 4,
                    'subject_type' => 'mixed',
                    'difficulty_level' => 'intermediate',
                    'is_practical' => true,
                    'full_marks_theory' => 60,
                    'full_marks_practical' => 40,
                ],
                [
                    'name' => 'Financial Accounting',
                    'code' => 'CAAC152',
                    'description' => 'Basic accounting principles and financial statements',
                    'credit_weight' => 3,
                    'subject_type' => 'mixed',
                    'difficulty_level' => 'beginner',
                    'is_practical' => true,
                    'full_marks_theory' => 80,
                    'full_marks_practical' => 20,
                ],
                [
                    'name' => 'English II',
                    'code' => 'CAEN153',
                    'description' => 'Advanced English communication and writing skills',
                    'credit_weight' => 3,
                    'subject_type' => 'theory',
                    'difficulty_level' => 'intermediate',
                    'is_practical' => false,
                    'full_marks_theory' => 100,
                    'full_marks_practical' => 0,
                ],
                [
                    'name' => 'Mathematics II',
                    'code' => 'CAMT154',
                    'description' => 'Calculus, differential equations, and linear algebra',
                    'credit_weight' => 3,
                    'subject_type' => 'mixed',
                    'difficulty_level' => 'intermediate',
                    'is_practical' => true,
                    'full_marks_theory' => 80,
                    'full_marks_practical' => 20,
                ],
                [
                    'name' => 'Microprocessor and Computer Architecture',
                    'code' => 'CACS155',
                    'description' => 'Computer architecture and microprocessor fundamentals',
                    'credit_weight' => 3,
                    'subject_type' => 'mixed',
                    'difficulty_level' => 'intermediate',
                    'is_practical' => true,
                    'full_marks_theory' => 70,
                    'full_marks_practical' => 30,
                ],
            ],
            3 => [ // Semester III
                [
                    'name' => 'Data Structure and Algorithms',
                    'code' => 'CACS201',
                    'description' => 'Data structures, algorithms, and their implementation',
                    'credit_weight' => 3,
                    'subject_type' => 'mixed',
                    'difficulty_level' => 'intermediate',
                    'is_practical' => true,
                    'full_marks_theory' => 60,
                    'full_marks_practical' => 40,
                ],
                [
                    'name' => 'Probability and Statistics',
                    'code' => 'CAST202',
                    'description' => 'Statistical methods and probability theory',
                    'credit_weight' => 3,
                    'subject_type' => 'mixed',
                    'difficulty_level' => 'intermediate',
                    'is_practical' => true,
                    'full_marks_theory' => 80,
                    'full_marks_practical' => 20,
                ],
                [
                    'name' => 'System Analysis and Design',
                    'code' => 'CACS203',
                    'description' => 'System development life cycle and design methodologies',
                    'credit_weight' => 3,
                    'subject_type' => 'theory',
                    'difficulty_level' => 'intermediate',
                    'is_practical' => false,
                    'full_marks_theory' => 100,
                    'full_marks_practical' => 0,
                ],
                [
                    'name' => 'OOP in Java',
                    'code' => 'CACS204',
                    'description' => 'Object-oriented programming concepts using Java',
                    'credit_weight' => 3,
                    'subject_type' => 'mixed',
                    'difficulty_level' => 'intermediate',
                    'is_practical' => true,
                    'full_marks_theory' => 60,
                    'full_marks_practical' => 40,
                ],
                [
                    'name' => 'Web Technology',
                    'code' => 'CACS205',
                    'description' => 'HTML, CSS, JavaScript, and web development fundamentals',
                    'credit_weight' => 3,
                    'subject_type' => 'mixed',
                    'difficulty_level' => 'intermediate',
                    'is_practical' => true,
                    'full_marks_theory' => 50,
                    'full_marks_practical' => 50,
                ],
            ],
            4 => [ // Semester IV
                [
                    'name' => 'Operating System',
                    'code' => 'CACS251',
                    'description' => 'Operating system concepts, processes, and memory management',
                    'credit_weight' => 4,
                    'subject_type' => 'mixed',
                    'difficulty_level' => 'intermediate',
                    'is_practical' => true,
                    'full_marks_theory' => 60,
                    'full_marks_practical' => 40,
                ],
                [
                    'name' => 'Numerical Methods',
                    'code' => 'CACS252',
                    'description' => 'Numerical analysis and computational methods',
                    'credit_weight' => 3,
                    'subject_type' => 'theory',
                    'difficulty_level' => 'intermediate',
                    'is_practical' => false,
                    'full_marks_theory' => 100,
                    'full_marks_practical' => 0,
                ],
                [
                    'name' => 'Software Engineering',
                    'code' => 'CACS253',
                    'description' => 'Software development methodologies and project management',
                    'credit_weight' => 3,
                    'subject_type' => 'theory',
                    'difficulty_level' => 'intermediate',
                    'is_practical' => false,
                    'full_marks_theory' => 100,
                    'full_marks_practical' => 0,
                ],
                [
                    'name' => 'Scripting Language',
                    'code' => 'CACS254',
                    'description' => 'Scripting languages for automation and web development',
                    'credit_weight' => 3,
                    'subject_type' => 'mixed',
                    'difficulty_level' => 'intermediate',
                    'is_practical' => true,
                    'full_marks_theory' => 70,
                    'full_marks_practical' => 30,
                ],
                [
                    'name' => 'Database Management System',
                    'code' => 'CACS255',
                    'description' => 'Database design, SQL, and database administration',
                    'credit_weight' => 3,
                    'subject_type' => 'mixed',
                    'difficulty_level' => 'intermediate',
                    'is_practical' => true,
                    'full_marks_theory' => 60,
                    'full_marks_practical' => 40,
                ],
                [
                    'name' => 'Project I',
                    'code' => 'CAPJ256',
                    'description' => 'First project work and implementation',
                    'credit_weight' => 2,
                    'subject_type' => 'practical',
                    'difficulty_level' => 'intermediate',
                    'is_practical' => true,
                    'full_marks_theory' => 0,
                    'full_marks_practical' => 100,
                ],
            ],
            5 => [ // Semester V
                [
                    'name' => 'MIS and e-Business',
                    'code' => 'CACS301',
                    'description' => 'Management Information Systems and electronic business',
                    'credit_weight' => 3,
                    'subject_type' => 'mixed',
                    'difficulty_level' => 'intermediate',
                    'is_practical' => true,
                    'full_marks_theory' => 70,
                    'full_marks_practical' => 30,
                ],
                [
                    'name' => 'DotNet Technology',
                    'code' => 'CACS302',
                    'description' => '.NET framework and application development',
                    'credit_weight' => 3,
                    'subject_type' => 'mixed',
                    'difficulty_level' => 'intermediate',
                    'is_practical' => true,
                    'full_marks_theory' => 50,
                    'full_marks_practical' => 50,
                ],
                [
                    'name' => 'Computer Networking',
                    'code' => 'CACS303',
                    'description' => 'Network protocols, architecture, and administration',
                    'credit_weight' => 3,
                    'subject_type' => 'mixed',
                    'difficulty_level' => 'intermediate',
                    'is_practical' => true,
                    'full_marks_theory' => 70,
                    'full_marks_practical' => 30,
                ],
                [
                    'name' => 'Introduction to Management',
                    'code' => 'CAMG304',
                    'description' => 'Basic management principles and organizational behavior',
                    'credit_weight' => 3,
                    'subject_type' => 'theory',
                    'difficulty_level' => 'beginner',
                    'is_practical' => false,
                    'full_marks_theory' => 100,
                    'full_marks_practical' => 0,
                ],
                [
                    'name' => 'Computer Graphics and Animation',
                    'code' => 'CACS305',
                    'description' => 'Computer graphics principles and animation techniques',
                    'credit_weight' => 3,
                    'subject_type' => 'mixed',
                    'difficulty_level' => 'intermediate',
                    'is_practical' => true,
                    'full_marks_theory' => 60,
                    'full_marks_practical' => 40,
                ],
            ],
            6 => [ // Semester VI
                [
                    'name' => 'Mobile Programming',
                    'code' => 'CACS351',
                    'description' => 'Mobile application development for Android and iOS',
                    'credit_weight' => 3,
                    'subject_type' => 'mixed',
                    'difficulty_level' => 'advanced',
                    'is_practical' => true,
                    'full_marks_theory' => 50,
                    'full_marks_practical' => 50,
                ],
                [
                    'name' => 'Distributed System',
                    'code' => 'CACS352',
                    'description' => 'Distributed computing concepts and architectures',
                    'credit_weight' => 3,
                    'subject_type' => 'theory',
                    'difficulty_level' => 'advanced',
                    'is_practical' => false,
                    'full_marks_theory' => 100,
                    'full_marks_practical' => 0,
                ],
                [
                    'name' => 'Applied Economics',
                    'code' => 'CACS353',
                    'description' => 'Economic principles applied to technology and business',
                    'credit_weight' => 3,
                    'subject_type' => 'theory',
                    'difficulty_level' => 'intermediate',
                    'is_practical' => false,
                    'full_marks_theory' => 100,
                    'full_marks_practical' => 0,
                ],
                [
                    'name' => 'Advanced Java Programming',
                    'code' => 'CACS354',
                    'description' => 'Advanced Java concepts including frameworks and enterprise development',
                    'credit_weight' => 3,
                    'subject_type' => 'mixed',
                    'difficulty_level' => 'advanced',
                    'is_practical' => true,
                    'full_marks_theory' => 50,
                    'full_marks_practical' => 50,
                ],
                [
                    'name' => 'Network Programming',
                    'code' => 'CACS355',
                    'description' => 'Programming for networked applications and protocols',
                    'credit_weight' => 3,
                    'subject_type' => 'mixed',
                    'difficulty_level' => 'advanced',
                    'is_practical' => true,
                    'full_marks_theory' => 60,
                    'full_marks_practical' => 40,
                ],
                [
                    'name' => 'Project II',
                    'code' => 'CAPJ356',
                    'description' => 'Second project work with advanced implementation',
                    'credit_weight' => 2,
                    'subject_type' => 'practical',
                    'difficulty_level' => 'advanced',
                    'is_practical' => true,
                    'full_marks_theory' => 0,
                    'full_marks_practical' => 100,
                ],
            ],
            7 => [ // Semester VII
                [
                    'name' => 'Cyber Law and Professional Ethics',
                    'code' => 'CACS401',
                    'description' => 'Legal and ethical issues in computing and technology',
                    'credit_weight' => 3,
                    'subject_type' => 'theory',
                    'difficulty_level' => 'intermediate',
                    'is_practical' => false,
                    'full_marks_theory' => 100,
                    'full_marks_practical' => 0,
                ],
                [
                    'name' => 'Cloud Computing',
                    'code' => 'CACS402',
                    'description' => 'Cloud computing concepts, services, and deployment models',
                    'credit_weight' => 3,
                    'subject_type' => 'mixed',
                    'difficulty_level' => 'advanced',
                    'is_practical' => true,
                    'full_marks_theory' => 60,
                    'full_marks_practical' => 40,
                ],
                [
                    'name' => 'Internship',
                    'code' => 'CAIN103',
                    'description' => 'Professional internship and industry experience',
                    'credit_weight' => 3,
                    'subject_type' => 'practical',
                    'difficulty_level' => 'advanced',
                    'is_practical' => true,
                    'full_marks_theory' => 0,
                    'full_marks_practical' => 100,
                ],
                [
                    'name' => 'Artificial Intelligence',
                    'code' => 'CACS410',
                    'description' => 'AI concepts, machine learning, and intelligent systems',
                    'credit_weight' => 3,
                    'subject_type' => 'mixed',
                    'difficulty_level' => 'advanced',
                    'is_practical' => true,
                    'full_marks_theory' => 70,
                    'full_marks_practical' => 30,
                ],
                [
                    'name' => 'Database Administration',
                    'code' => 'CACS405',
                    'description' => 'Advanced database administration and optimization',
                    'credit_weight' => 3,
                    'subject_type' => 'mixed',
                    'difficulty_level' => 'advanced',
                    'is_practical' => true,
                    'full_marks_theory' => 60,
                    'full_marks_practical' => 40,
                ],
            ],
            8 => [ // Semester VIII
                [
                    'name' => 'Operations Research',
                    'code' => 'CAOR451',
                    'description' => 'Mathematical optimization and decision-making techniques',
                    'credit_weight' => 3,
                    'subject_type' => 'theory',
                    'difficulty_level' => 'advanced',
                    'is_practical' => false,
                    'full_marks_theory' => 100,
                    'full_marks_practical' => 0,
                ],
                [
                    'name' => 'Project III',
                    'code' => 'CAPJ452',
                    'description' => 'Final capstone project with comprehensive implementation',
                    'credit_weight' => 6,
                    'subject_type' => 'practical',
                    'difficulty_level' => 'advanced',
                    'is_practical' => true,
                    'full_marks_theory' => 0,
                    'full_marks_practical' => 100,
                ],
                [
                    'name' => 'Information Security',
                    'code' => 'CACS459',
                    'description' => 'Cybersecurity principles, cryptography, and security management',
                    'credit_weight' => 3,
                    'subject_type' => 'mixed',
                    'difficulty_level' => 'advanced',
                    'is_practical' => true,
                    'full_marks_theory' => 70,
                    'full_marks_practical' => 30,
                ],
                [
                    'name' => 'Machine Learning',
                    'code' => 'CACS456',
                    'description' => 'Machine learning algorithms and applications',
                    'credit_weight' => 3,
                    'subject_type' => 'mixed',
                    'difficulty_level' => 'advanced',
                    'is_practical' => true,
                    'full_marks_theory' => 60,
                    'full_marks_practical' => 40,
                ],
            ],
        ];

        // Get subjects for the specified semester, or return empty array if semester not found
        $semesterSubjects = $allSubjects[$semester] ?? [];

        // Add common fields and format for the seeder
        return array_map(function($subject, $index) use ($courseCode) {
            return array_merge($subject, [
                'order_sequence' => $index + 1,
                'duration_hours' => $subject['credit_weight'] * 15, // Approximate hours per credit
                'learning_objectives' => $this->generateLearningObjectives($subject['name']),
            ]);
        }, $semesterSubjects, array_keys($semesterSubjects));
    }

    private function generateLearningObjectives($subjectName)
    {
        $objectives = [
            'Computer Fundamentals & Applications' => [
                'Understand basic computer hardware and software concepts',
                'Learn fundamental computer applications',
                'Develop basic computer literacy skills',
                'Understand operating system basics'
            ],
            'Society and Technology' => [
                'Analyze the impact of technology on society',
                'Understand ethical issues in technology',
                'Explore digital divide and accessibility',
                'Examine technology policy and governance'
            ],
            'English I' => [
                'Develop basic English communication skills',
                'Improve grammar and vocabulary',
                'Practice reading comprehension',
                'Learn basic writing techniques'
            ],
            'Mathematics I' => [
                'Master algebraic operations and equations',
                'Understand trigonometric functions',
                'Learn basic calculus concepts',
                'Apply mathematical problem-solving techniques'
            ],
            'Digital Logic' => [
                'Understand Boolean algebra principles',
                'Design and analyze logic circuits',
                'Learn about digital number systems',
                'Implement combinational and sequential circuits'
            ],
            'C Programming' => [
                'Learn C programming syntax and semantics',
                'Understand programming logic and algorithms',
                'Develop problem-solving skills through coding',
                'Master control structures and functions'
            ],
            'Financial Accounting' => [
                'Understand basic accounting principles',
                'Learn to prepare financial statements',
                'Master double-entry bookkeeping',
                'Analyze financial transactions'
            ],
            'English II' => [
                'Enhance advanced communication skills',
                'Develop academic writing abilities',
                'Improve presentation and speaking skills',
                'Analyze literary and technical texts'
            ],
            'Mathematics II' => [
                'Master calculus and differential equations',
                'Understand linear algebra concepts',
                'Apply mathematical modeling techniques',
                'Solve complex mathematical problems'
            ],
            'Microprocessor and Computer Architecture' => [
                'Understand computer architecture principles',
                'Learn microprocessor organization and design',
                'Study instruction sets and assembly language',
                'Analyze computer performance metrics'
            ],
            'Data Structure and Algorithms' => [
                'Implement various data structures',
                'Analyze algorithm complexity',
                'Design efficient algorithms',
                'Apply data structures to solve problems'
            ],
            'Probability and Statistics' => [
                'Understand probability theory and distributions',
                'Apply statistical methods to data analysis',
                'Learn hypothesis testing and inference',
                'Use statistical software for analysis'
            ],
            'System Analysis and Design' => [
                'Understand system development methodologies',
                'Learn requirements analysis techniques',
                'Design system architecture and interfaces',
                'Apply project management principles'
            ],
            'OOP in Java' => [
                'Master object-oriented programming concepts',
                'Implement classes, objects, and inheritance',
                'Understand polymorphism and encapsulation',
                'Develop Java applications using OOP principles'
            ],
            'Web Technology' => [
                'Create responsive web pages using HTML/CSS',
                'Implement interactive features with JavaScript',
                'Understand client-server architecture',
                'Develop dynamic web applications'
            ],
            'Operating System' => [
                'Understand operating system concepts and architecture',
                'Learn process management and scheduling',
                'Master memory management techniques',
                'Implement file system operations'
            ],
            'Numerical Methods' => [
                'Apply numerical techniques to solve mathematical problems',
                'Understand error analysis and convergence',
                'Implement numerical algorithms',
                'Use computational methods for engineering problems'
            ],
            'Software Engineering' => [
                'Understand software development life cycle',
                'Apply software engineering methodologies',
                'Learn project management techniques',
                'Design and document software systems'
            ],
            'Scripting Language' => [
                'Master scripting language syntax and features',
                'Automate system administration tasks',
                'Develop web applications using scripting',
                'Integrate scripts with other applications'
            ],
            'Database Management System' => [
                'Design and implement relational databases',
                'Master SQL for data manipulation',
                'Understand database normalization',
                'Learn database administration and optimization'
            ],
            'Project I' => [
                'Apply theoretical knowledge to practical problems',
                'Develop project planning and management skills',
                'Implement a complete software solution',
                'Document and present project work'
            ],
            'MIS and e-Business' => [
                'Understand management information systems',
                'Learn e-business models and strategies',
                'Analyze business processes and requirements',
                'Design information systems for organizations'
            ],
            'DotNet Technology' => [
                'Master .NET framework and architecture',
                'Develop applications using C# and VB.NET',
                'Implement web applications using ASP.NET',
                'Understand .NET ecosystem and tools'
            ],
            'Computer Networking' => [
                'Understand network protocols and architectures',
                'Configure and manage network devices',
                'Implement network security measures',
                'Troubleshoot network connectivity issues'
            ],
            'Introduction to Management' => [
                'Understand basic management principles',
                'Learn organizational behavior concepts',
                'Apply leadership and communication skills',
                'Analyze business environments and strategies'
            ],
            'Computer Graphics and Animation' => [
                'Understand computer graphics principles',
                'Implement 2D and 3D graphics algorithms',
                'Create animations and visual effects',
                'Use graphics software and tools'
            ],
            'Mobile Programming' => [
                'Develop mobile applications for Android/iOS',
                'Understand mobile app architecture',
                'Implement user interfaces for mobile devices',
                'Deploy and distribute mobile applications'
            ],
            'Distributed System' => [
                'Understand distributed computing concepts',
                'Learn distributed algorithms and protocols',
                'Implement distributed applications',
                'Analyze distributed system performance'
            ],
            'Applied Economics' => [
                'Apply economic principles to technology decisions',
                'Understand market structures and competition',
                'Analyze cost-benefit relationships',
                'Evaluate economic impact of technology'
            ],
            'Advanced Java Programming' => [
                'Master advanced Java concepts and frameworks',
                'Develop enterprise Java applications',
                'Implement design patterns in Java',
                'Use Java for web and mobile development'
            ],
            'Network Programming' => [
                'Develop networked applications and services',
                'Implement client-server architectures',
                'Use network programming APIs and protocols',
                'Create distributed network applications'
            ],
            'Project II' => [
                'Implement advanced software solutions',
                'Apply software engineering best practices',
                'Integrate multiple technologies and platforms',
                'Demonstrate advanced technical skills'
            ],
            'Cyber Law and Professional Ethics' => [
                'Understand legal issues in computing',
                'Learn professional ethics and responsibilities',
                'Analyze privacy and security regulations',
                'Apply ethical decision-making in technology'
            ],
            'Cloud Computing' => [
                'Understand cloud computing models and services',
                'Deploy applications on cloud platforms',
                'Implement cloud security and governance',
                'Optimize cloud resource utilization'
            ],
            'Internship' => [
                'Gain practical industry experience',
                'Apply academic knowledge in professional settings',
                'Develop workplace skills and competencies',
                'Build professional networks and relationships'
            ],
            'Artificial Intelligence' => [
                'Understand AI concepts and techniques',
                'Implement machine learning algorithms',
                'Develop intelligent systems and applications',
                'Apply AI to solve real-world problems'
            ],
            'Database Administration' => [
                'Master advanced database administration',
                'Implement database security and backup strategies',
                'Optimize database performance and tuning',
                'Manage enterprise database systems'
            ],
            'Operations Research' => [
                'Apply mathematical optimization techniques',
                'Solve complex decision-making problems',
                'Use operations research tools and software',
                'Analyze and improve operational efficiency'
            ],
            'Project III' => [
                'Complete a comprehensive capstone project',
                'Demonstrate mastery of technical skills',
                'Apply project management methodologies',
                'Present and defend project outcomes'
            ],
            'Information Security' => [
                'Understand cybersecurity principles and practices',
                'Implement security controls and measures',
                'Analyze security threats and vulnerabilities',
                'Design secure systems and applications'
            ],
            'Machine Learning' => [
                'Understand machine learning algorithms and techniques',
                'Implement supervised and unsupervised learning',
                'Apply machine learning to data analysis',
                'Evaluate and optimize machine learning models'
            ],
        ];

        return $objectives[$subjectName] ?? [
            'Understand fundamental concepts of the subject',
            'Apply theoretical knowledge to practical problems',
            'Develop analytical and critical thinking skills',
            'Demonstrate competency in subject-specific skills'
        ];
    }

    private function createGeneralSubjects($class, $courseCode, $instructor)
    {
        $subjects = [
            [
                'name' => 'Course Introduction',
                'code' => $courseCode . '-S01',
                'description' => 'Introduction to the course and learning objectives',
                'order_sequence' => 1,
                'duration_hours' => 2,
                'difficulty_level' => 'beginner',
                'subject_type' => 'theory',
                'learning_objectives' => [
                    'Understand course objectives',
                    'Learn assessment methods',
                    'Course overview'
                ]
            ],
            [
                'name' => 'Fundamental Concepts',
                'code' => $courseCode . '-S02',
                'description' => 'Basic concepts and terminology',
                'order_sequence' => 2,
                'duration_hours' => 8,
                'difficulty_level' => 'beginner',
                'subject_type' => 'theory',
                'learning_objectives' => [
                    'Learn basic terminology',
                    'Understand fundamental concepts',
                    'Historical context'
                ]
            ],
            [
                'name' => 'Practical Applications',
                'code' => $courseCode . '-S03',
                'description' => 'Real-world applications and case studies',
                'order_sequence' => 3,
                'duration_hours' => 10,
                'difficulty_level' => 'intermediate',
                'subject_type' => 'practical',
                'learning_objectives' => [
                    'Apply concepts to real scenarios',
                    'Analyze case studies',
                    'Problem-solving skills'
                ]
            ]
        ];

        $this->createSubjectsForClass($class, $subjects, $instructor);
    }

    private function createSubjectsForClass($class, $subjects, $instructor)
    {
        foreach ($subjects as $subjectData) {
            // Determine if subject is practical based on name or predefined flag
            $isPractical = $subjectData['is_practical'] ??
                          (str_contains(strtolower($subjectData['name']), 'practical') ||
                          str_contains(strtolower($subjectData['name']), 'lab') ||
                          str_contains(strtolower($subjectData['name']), 'workshop'));

            // Use predefined marks if available, otherwise generate them
            if (isset($subjectData['full_marks_theory']) && isset($subjectData['full_marks_practical'])) {
                $examMarks = [
                    'full_marks_theory' => $subjectData['full_marks_theory'],
                    'pass_marks_theory' => $subjectData['full_marks_theory'] > 0 ? (int)($subjectData['full_marks_theory'] * 0.4) : null,
                    'full_marks_practical' => $subjectData['full_marks_practical'],
                    'pass_marks_practical' => $subjectData['full_marks_practical'] > 0 ? (int)($subjectData['full_marks_practical'] * 0.4) : null,
                ];
            } else {
                $examMarks = $this->generateExamMarks($subjectData['subject_type'], $isPractical);
            }

            // Use predefined credit weight if available
            $creditWeight = $subjectData['credit_weight'] ?? rand(10, 30);

            Subject::firstOrCreate([
                'code' => $subjectData['code']
            ], array_merge($subjectData, [
                'class_id' => $class->id,
                'instructor_id' => $instructor?->id,
                'credit_weight' => $creditWeight,
                'is_mandatory' => true,
                'is_active' => true,
                'is_practical' => $isPractical,
                'full_marks_theory' => $examMarks['full_marks_theory'],
                'pass_marks_theory' => $examMarks['pass_marks_theory'],
                'full_marks_practical' => $examMarks['full_marks_practical'],
                'pass_marks_practical' => $examMarks['pass_marks_practical'],
                'resources' => [
                    'textbook' => 'Course Textbook Chapter ' . $subjectData['order_sequence'],
                    'online_resources' => 'Online materials and videos',
                    'assignments' => 'Practice exercises and assignments'
                ]
            ]));
        }
    }

    private function generateExamMarks($subjectType, $isPractical)
    {
        $marks = [
            'full_marks_theory' => null,
            'pass_marks_theory' => null,
            'full_marks_practical' => null,
            'pass_marks_practical' => null,
        ];

        switch ($subjectType) {
            case 'theory':
                $marks['full_marks_theory'] = rand(80, 100);
                $marks['pass_marks_theory'] = (int)($marks['full_marks_theory'] * 0.4); // 40% pass marks
                break;

            case 'practical':
                $marks['full_marks_practical'] = rand(50, 100);
                $marks['pass_marks_practical'] = (int)($marks['full_marks_practical'] * 0.4); // 40% pass marks
                break;

            case 'mixed':
                $marks['full_marks_theory'] = rand(60, 80);
                $marks['pass_marks_theory'] = (int)($marks['full_marks_theory'] * 0.4);
                $marks['full_marks_practical'] = rand(20, 40);
                $marks['pass_marks_practical'] = (int)($marks['full_marks_practical'] * 0.4);
                break;
        }

        // If subject is marked as practical but type is theory, add practical component
        if ($isPractical && $subjectType === 'theory') {
            $marks['full_marks_practical'] = rand(20, 30);
            $marks['pass_marks_practical'] = (int)($marks['full_marks_practical'] * 0.4);
        }

        return $marks;
    }
}
