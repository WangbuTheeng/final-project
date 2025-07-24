<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExamType;
use App\Models\ExamComponent;
use App\Models\GradeScale;

class SimpleNepaliExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedGradingScales();
        $this->seedExamTypes();
        $this->seedExamComponents();
    }

    /**
     * Seed grading scales for Nepali educational standards
     */
    private function seedGradingScales()
    {
        // Nepal University Grade Scales for Bachelor's level
        $nepaliUniversityGrades = [
            ['grade_letter' => 'A+', 'grade_point' => 4.0, 'min_percentage' => 90, 'max_percentage' => 100, 'description' => 'Outstanding'],
            ['grade_letter' => 'A', 'grade_point' => 3.6, 'min_percentage' => 80, 'max_percentage' => 89, 'description' => 'Excellent'],
            ['grade_letter' => 'B+', 'grade_point' => 3.2, 'min_percentage' => 70, 'max_percentage' => 79, 'description' => 'Very Good'],
            ['grade_letter' => 'B', 'grade_point' => 2.8, 'min_percentage' => 60, 'max_percentage' => 69, 'description' => 'Good'],
            ['grade_letter' => 'C+', 'grade_point' => 2.4, 'min_percentage' => 50, 'max_percentage' => 59, 'description' => 'Satisfactory'],
            ['grade_letter' => 'C', 'grade_point' => 2.0, 'min_percentage' => 45, 'max_percentage' => 49, 'description' => 'Acceptable'],
            ['grade_letter' => 'D', 'grade_point' => 1.6, 'min_percentage' => 40, 'max_percentage' => 44, 'description' => 'Partially Acceptable'],
            ['grade_letter' => 'F', 'grade_point' => 0.0, 'min_percentage' => 0, 'max_percentage' => 39, 'description' => 'Fail']
        ];

        foreach ($nepaliUniversityGrades as $index => $grade) {
            GradeScale::firstOrCreate([
                'scale_name' => 'Nepal University Standard',
                'grade_letter' => $grade['grade_letter'],
            ], [
                'min_percentage' => $grade['min_percentage'],
                'max_percentage' => $grade['max_percentage'],
                'grade_point' => $grade['grade_point'],
                'description' => $grade['description'],
                'status' => $grade['grade_point'] > 0 ? 'pass' : 'fail',
                'is_active' => true,
                'sort_order' => $index + 1
            ]);
        }

        // NEB +2 Grade Scales
        $nebGrades = [
            ['grade_letter' => 'A+', 'grade_point' => 4.0, 'min_percentage' => 90, 'max_percentage' => 100, 'description' => 'Outstanding'],
            ['grade_letter' => 'A', 'grade_point' => 3.6, 'min_percentage' => 80, 'max_percentage' => 89, 'description' => 'Excellent'],
            ['grade_letter' => 'B+', 'grade_point' => 3.2, 'min_percentage' => 70, 'max_percentage' => 79, 'description' => 'Very Good'],
            ['grade_letter' => 'B', 'grade_point' => 2.8, 'min_percentage' => 60, 'max_percentage' => 69, 'description' => 'Good'],
            ['grade_letter' => 'C+', 'grade_point' => 2.4, 'min_percentage' => 50, 'max_percentage' => 59, 'description' => 'Satisfactory'],
            ['grade_letter' => 'C', 'grade_point' => 2.0, 'min_percentage' => 45, 'max_percentage' => 49, 'description' => 'Acceptable'],
            ['grade_letter' => 'D', 'grade_point' => 1.6, 'min_percentage' => 35, 'max_percentage' => 44, 'description' => 'Partially Acceptable'],
            ['grade_letter' => 'NG', 'grade_point' => 0.0, 'min_percentage' => 0, 'max_percentage' => 34, 'description' => 'Not Graded (Fail)']
        ];

        foreach ($nebGrades as $index => $grade) {
            GradeScale::firstOrCreate([
                'scale_name' => 'NEB +2 Standard',
                'grade_letter' => $grade['grade_letter'],
            ], [
                'min_percentage' => $grade['min_percentage'],
                'max_percentage' => $grade['max_percentage'],
                'grade_point' => $grade['grade_point'],
                'description' => $grade['description'],
                'status' => $grade['grade_point'] > 0 ? 'pass' : 'fail',
                'is_active' => true,
                'sort_order' => $index + 1
            ]);
        }
    }

    /**
     * Seed exam types for Nepali educational system
     */
    private function seedExamTypes()
    {
        $examTypes = [
            // +2 Level Exam Types
            [
                'name' => 'First Term Exam',
                'code' => 'FIRST_TERM',
                'description' => 'First terminal examination for +2 level',
                'education_level' => 'plus_two',
                'assessment_category' => 'internal',
                'default_weightage' => 25.00,
                'default_duration_minutes' => 180,
                'is_active' => true,
                'order_sequence' => 1
            ],
            [
                'name' => 'Mid-Term Exam',
                'code' => 'MID_TERM',
                'description' => 'Mid-terminal examination for +2 level',
                'education_level' => 'plus_two',
                'assessment_category' => 'internal',
                'default_weightage' => 25.00,
                'default_duration_minutes' => 180,
                'is_active' => true,
                'order_sequence' => 2
            ],
            [
                'name' => 'Pre-Board Exam',
                'code' => 'PRE_BOARD',
                'description' => 'Pre-board examination for +2 level (NEB preparation)',
                'education_level' => 'plus_two',
                'assessment_category' => 'internal',
                'default_weightage' => 30.00,
                'default_duration_minutes' => 180,
                'is_active' => true,
                'order_sequence' => 3
            ],
            [
                'name' => 'Internal Assessment',
                'code' => 'INTERNAL_ASSESS',
                'description' => 'Continuous internal assessment for +2 level',
                'education_level' => 'plus_two',
                'assessment_category' => 'internal',
                'default_weightage' => 20.00,
                'default_duration_minutes' => 0,
                'is_active' => true,
                'order_sequence' => 4
            ],
            // Bachelor's Level Exam Types
            [
                'name' => 'Internal Assessment',
                'code' => 'IA_BACH',
                'description' => 'Continuous internal assessment for Bachelor\'s level',
                'education_level' => 'bachelors',
                'assessment_category' => 'internal',
                'default_weightage' => 40.00,
                'default_duration_minutes' => 0,
                'is_active' => true,
                'order_sequence' => 5
            ],
            [
                'name' => 'Mid-Semester Exam',
                'code' => 'MID_SEM',
                'description' => 'Mid-semester examination for Bachelor\'s level',
                'education_level' => 'bachelors',
                'assessment_category' => 'internal',
                'default_weightage' => 20.00,
                'default_duration_minutes' => 120,
                'is_active' => true,
                'order_sequence' => 6
            ],
            [
                'name' => 'Practical/Lab Exam',
                'code' => 'PRACTICAL',
                'description' => 'Practical/Laboratory examination',
                'education_level' => 'both',
                'assessment_category' => 'both',
                'default_weightage' => 25.00,
                'default_duration_minutes' => 180,
                'is_active' => true,
                'order_sequence' => 7
            ]
        ];

        foreach ($examTypes as $examType) {
            ExamType::firstOrCreate(
                ['code' => $examType['code']],
                $examType
            );
        }
    }

    /**
     * Seed exam components for internal assessments
     */
    private function seedExamComponents()
    {
        $components = [
            // +2 Level Components
            [
                'name' => 'Class Test',
                'code' => 'CLASS_TEST',
                'description' => 'Regular class tests and quizzes',
                'component_type' => 'quiz',
                'default_marks' => 20.00,
                'default_weightage' => 50.00,
                'education_level' => 'plus_two',
                'is_active' => true,
                'order_sequence' => 1
            ],
            [
                'name' => 'Assignment',
                'code' => 'ASSIGNMENT',
                'description' => 'Home assignments and projects',
                'component_type' => 'assignment',
                'default_marks' => 15.00,
                'default_weightage' => 37.50,
                'education_level' => 'plus_two',
                'is_active' => true,
                'order_sequence' => 2
            ],
            [
                'name' => 'Attendance',
                'code' => 'ATTENDANCE',
                'description' => 'Class attendance marks',
                'component_type' => 'attendance',
                'default_marks' => 5.00,
                'default_weightage' => 12.50,
                'education_level' => 'plus_two',
                'is_active' => true,
                'order_sequence' => 3
            ],
            // Bachelor's Level Components
            [
                'name' => 'Attendance',
                'code' => 'BACH_ATTENDANCE',
                'description' => 'Class attendance for Bachelor\'s level',
                'component_type' => 'attendance',
                'default_marks' => 10.00,
                'default_weightage' => 25.00,
                'education_level' => 'bachelors',
                'is_active' => true,
                'order_sequence' => 4
            ],
            [
                'name' => 'Assignment',
                'code' => 'BACH_ASSIGNMENT',
                'description' => 'Assignments and home works for Bachelor\'s level',
                'component_type' => 'assignment',
                'default_marks' => 15.00,
                'default_weightage' => 37.50,
                'education_level' => 'bachelors',
                'is_active' => true,
                'order_sequence' => 5
            ],
            [
                'name' => 'Quiz/Test',
                'code' => 'BACH_QUIZ',
                'description' => 'Quizzes and class tests for Bachelor\'s level',
                'component_type' => 'quiz',
                'default_marks' => 10.00,
                'default_weightage' => 25.00,
                'education_level' => 'bachelors',
                'is_active' => true,
                'order_sequence' => 6
            ],
            [
                'name' => 'Presentation',
                'code' => 'PRESENTATION',
                'description' => 'Class presentations and seminars',
                'component_type' => 'presentation',
                'default_marks' => 5.00,
                'default_weightage' => 12.50,
                'education_level' => 'bachelors',
                'is_active' => true,
                'order_sequence' => 7
            ]
        ];

        foreach ($components as $component) {
            ExamComponent::firstOrCreate(
                ['code' => $component['code']],
                $component
            );
        }
    }
}
