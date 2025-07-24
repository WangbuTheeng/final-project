<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\ClassSection;
use App\Models\User;

class TUSubjectsSeeder extends Seeder
{
    /**
     * Run the database seeds for TU subjects.
     */
    public function run(): void
    {
        // Get instructors (faculty role)
        $instructors = User::where('role', 'faculty')->get();

        // If no faculty users exist, skip seeding subjects
        if ($instructors->isEmpty()) {
            $this->command->warn('No faculty users found. Skipping subject seeding.');
            return;
        }
        
        // BCA 1st Semester Subjects
        $bcaFirstSem = ClassSection::where('name', 'BCA 1st Semester')->first();
        if ($bcaFirstSem) {
            $bcaFirstSemSubjects = [
                [
                    'name' => 'Computer Fundamentals & Applications',
                    'code' => 'CACS101',
                    'description' => 'Introduction to computer systems and applications',
                    'order_sequence' => 1,
                    'duration_hours' => 150,
                    'credit_weight' => 100,
                    'difficulty_level' => 'beginner',
                    'subject_type' => 'theory',
                    'is_mandatory' => true,
                    'full_marks_theory' => 80,
                    'pass_marks_theory' => 32,
                    'full_marks_practical' => 20,
                    'pass_marks_practical' => 8,
                    'is_practical' => true,
                ],
                [
                    'name' => 'Society and Technology',
                    'code' => 'CACS102',
                    'description' => 'Impact of technology on society',
                    'order_sequence' => 2,
                    'duration_hours' => 150,
                    'credit_weight' => 100,
                    'difficulty_level' => 'beginner',
                    'subject_type' => 'theory',
                    'is_mandatory' => true,
                    'full_marks_theory' => 100,
                    'pass_marks_theory' => 40,
                ],
                [
                    'name' => 'English I',
                    'code' => 'CACS103',
                    'description' => 'English language and communication skills',
                    'order_sequence' => 3,
                    'duration_hours' => 150,
                    'credit_weight' => 100,
                    'difficulty_level' => 'beginner',
                    'subject_type' => 'theory',
                    'is_mandatory' => true,
                    'full_marks_theory' => 100,
                    'pass_marks_theory' => 40,
                ],
                [
                    'name' => 'Mathematics I',
                    'code' => 'CACS104',
                    'description' => 'Basic mathematics for computer applications',
                    'order_sequence' => 4,
                    'duration_hours' => 150,
                    'credit_weight' => 100,
                    'difficulty_level' => 'intermediate',
                    'subject_type' => 'theory',
                    'is_mandatory' => true,
                    'full_marks_theory' => 100,
                    'pass_marks_theory' => 40,
                ],
                [
                    'name' => 'Digital Logic',
                    'code' => 'CACS105',
                    'description' => 'Digital logic circuits and systems',
                    'order_sequence' => 5,
                    'duration_hours' => 150,
                    'credit_weight' => 100,
                    'difficulty_level' => 'intermediate',
                    'subject_type' => 'mixed',
                    'is_mandatory' => true,
                    'full_marks_theory' => 80,
                    'pass_marks_theory' => 32,
                    'full_marks_practical' => 20,
                    'pass_marks_practical' => 8,
                    'is_practical' => true,
                ],
            ];

            foreach ($bcaFirstSemSubjects as $subjectData) {
                $subjectData['class_id'] = $bcaFirstSem->id;
                $subjectData['instructor_id'] = $instructors->random()->id;
                $subjectData['learning_objectives'] = [
                    'Understand basic concepts',
                    'Apply theoretical knowledge',
                    'Develop practical skills'
                ];
                Subject::updateOrCreate(
                    [
                        'code' => $subjectData['code'],
                        'class_id' => $subjectData['class_id']
                    ],
                    $subjectData
                );
            }
        }

        // BCA 2nd Semester Subjects
        $bcaSecondSem = ClassSection::where('name', 'BCA 2nd Semester')->first();
        if ($bcaSecondSem) {
            $bcaSecondSemSubjects = [
                [
                    'name' => 'C Programming',
                    'code' => 'CACS151',
                    'description' => 'Programming in C language',
                    'order_sequence' => 1,
                    'duration_hours' => 150,
                    'credit_weight' => 100,
                    'difficulty_level' => 'intermediate',
                    'subject_type' => 'mixed',
                    'is_mandatory' => true,
                    'full_marks_theory' => 60,
                    'pass_marks_theory' => 24,
                    'full_marks_practical' => 40,
                    'pass_marks_practical' => 16,
                    'is_practical' => true,
                ],
                [
                    'name' => 'Financial Accounting',
                    'code' => 'CACS152',
                    'description' => 'Basic principles of accounting',
                    'order_sequence' => 2,
                    'duration_hours' => 150,
                    'credit_weight' => 100,
                    'difficulty_level' => 'beginner',
                    'subject_type' => 'theory',
                    'is_mandatory' => true,
                    'full_marks_theory' => 100,
                    'pass_marks_theory' => 40,
                ],
                [
                    'name' => 'English II',
                    'code' => 'CACS153',
                    'description' => 'Advanced English communication',
                    'order_sequence' => 3,
                    'duration_hours' => 150,
                    'credit_weight' => 100,
                    'difficulty_level' => 'intermediate',
                    'subject_type' => 'theory',
                    'is_mandatory' => true,
                    'full_marks_theory' => 100,
                    'pass_marks_theory' => 40,
                ],
                [
                    'name' => 'Mathematics II',
                    'code' => 'CACS154',
                    'description' => 'Advanced mathematics for computing',
                    'order_sequence' => 4,
                    'duration_hours' => 150,
                    'credit_weight' => 100,
                    'difficulty_level' => 'intermediate',
                    'subject_type' => 'theory',
                    'is_mandatory' => true,
                    'full_marks_theory' => 100,
                    'pass_marks_theory' => 40,
                ],
                [
                    'name' => 'Microprocessor and Computer Architecture',
                    'code' => 'CACS155',
                    'description' => 'Computer architecture and microprocessors',
                    'order_sequence' => 5,
                    'duration_hours' => 150,
                    'credit_weight' => 100,
                    'difficulty_level' => 'advanced',
                    'subject_type' => 'mixed',
                    'is_mandatory' => true,
                    'full_marks_theory' => 80,
                    'pass_marks_theory' => 32,
                    'full_marks_practical' => 20,
                    'pass_marks_practical' => 8,
                    'is_practical' => true,
                ],
            ];

            foreach ($bcaSecondSemSubjects as $subjectData) {
                $subjectData['class_id'] = $bcaSecondSem->id;
                $subjectData['instructor_id'] = $instructors->random()->id;
                $subjectData['learning_objectives'] = [
                    'Master core concepts',
                    'Implement practical solutions',
                    'Analyze complex problems'
                ];
                Subject::updateOrCreate(
                    [
                        'code' => $subjectData['code'],
                        'class_id' => $subjectData['class_id']
                    ],
                    $subjectData
                );
            }
        }

        // BBS 1st Year Subjects
        $bbsFirstYear = ClassSection::where('name', 'BBS 1st Year')->first();
        if ($bbsFirstYear) {
            $bbsFirstYearSubjects = [
                [
                    'name' => 'Principles of Management',
                    'code' => 'MGT211',
                    'description' => 'Basic principles and functions of management',
                    'order_sequence' => 1,
                    'duration_hours' => 150,
                    'credit_weight' => 100,
                    'difficulty_level' => 'beginner',
                    'subject_type' => 'theory',
                    'is_mandatory' => true,
                    'full_marks_theory' => 100,
                    'pass_marks_theory' => 40,
                ],
                [
                    'name' => 'Business English',
                    'code' => 'ENG211',
                    'description' => 'English for business communication',
                    'order_sequence' => 2,
                    'duration_hours' => 150,
                    'credit_weight' => 100,
                    'difficulty_level' => 'beginner',
                    'subject_type' => 'theory',
                    'is_mandatory' => true,
                    'full_marks_theory' => 100,
                    'pass_marks_theory' => 40,
                ],
                [
                    'name' => 'Business Mathematics',
                    'code' => 'STA211',
                    'description' => 'Mathematics for business applications',
                    'order_sequence' => 3,
                    'duration_hours' => 150,
                    'credit_weight' => 100,
                    'difficulty_level' => 'intermediate',
                    'subject_type' => 'theory',
                    'is_mandatory' => true,
                    'full_marks_theory' => 100,
                    'pass_marks_theory' => 40,
                ],
                [
                    'name' => 'Microeconomics',
                    'code' => 'ECO211',
                    'description' => 'Principles of microeconomics',
                    'order_sequence' => 4,
                    'duration_hours' => 150,
                    'credit_weight' => 100,
                    'difficulty_level' => 'intermediate',
                    'subject_type' => 'theory',
                    'is_mandatory' => true,
                    'full_marks_theory' => 100,
                    'pass_marks_theory' => 40,
                ],
                [
                    'name' => 'Accounting for Financial Analysis and Planning',
                    'code' => 'ACC211',
                    'description' => 'Financial accounting principles',
                    'order_sequence' => 5,
                    'duration_hours' => 150,
                    'credit_weight' => 100,
                    'difficulty_level' => 'intermediate',
                    'subject_type' => 'theory',
                    'is_mandatory' => true,
                    'full_marks_theory' => 100,
                    'pass_marks_theory' => 40,
                ],
            ];

            foreach ($bbsFirstYearSubjects as $subjectData) {
                $subjectData['class_id'] = $bbsFirstYear->id;
                $subjectData['instructor_id'] = $instructors->random()->id;
                $subjectData['learning_objectives'] = [
                    'Understand business fundamentals',
                    'Apply management principles',
                    'Develop analytical skills'
                ];
                Subject::updateOrCreate(
                    [
                        'code' => $subjectData['code'],
                        'class_id' => $subjectData['class_id']
                    ],
                    $subjectData
                );
            }
        }

        $this->command->info('Created TU subjects successfully!');
    }
}
