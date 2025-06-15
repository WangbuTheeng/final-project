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
        // Get some classes to add subjects to
        $classes = ClassSection::with('course')->take(3)->get();
        
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
            if (str_contains($class->course->title, 'Programming')) {
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
            // Determine if subject is practical based on name
            $isPractical = str_contains(strtolower($subjectData['name']), 'practical') ||
                          str_contains(strtolower($subjectData['name']), 'lab') ||
                          str_contains(strtolower($subjectData['name']), 'workshop');

            // Set exam marks based on subject type
            $examMarks = $this->generateExamMarks($subjectData['subject_type'], $isPractical);

            Subject::firstOrCreate([
                'code' => $subjectData['code']
            ], array_merge($subjectData, [
                'class_id' => $class->id,
                'instructor_id' => $instructor?->id,
                'credit_weight' => rand(10, 30),
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
