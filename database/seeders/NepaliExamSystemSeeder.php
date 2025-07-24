<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExamType;
use App\Models\ExamComponent;
use App\Models\GradingSystem;
use App\Models\GradeScale;

class NepaliExamSystemSeeder extends Seeder
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
     * Seed Nepali grading scales
     */
    private function seedGradingScales()
    {
        // Create +2 Level Grading System
        $plusTwoGrading = GradingSystem::updateOrCreate(
            ['code' => 'NEB_PLUS_TWO'],
            [
                'name' => '+2 Level Grading (NEB)',
                'description' => 'National Examination Board grading system for +2 level',
                'status' => 'active',
                'is_default' => false,
                'order_sequence' => 1
            ]
        );

        // +2 Level Grade Scales
        $plusTwoGrades = [
            ['grade_letter' => 'A+', 'min_percentage' => 90, 'max_percentage' => 100, 'grade_point' => 4.0, 'description' => 'Outstanding'],
            ['grade_letter' => 'A', 'min_percentage' => 80, 'max_percentage' => 89, 'grade_point' => 3.6, 'description' => 'Excellent'],
            ['grade_letter' => 'B+', 'min_percentage' => 70, 'max_percentage' => 79, 'grade_point' => 3.2, 'description' => 'Very Good'],
            ['grade_letter' => 'B', 'min_percentage' => 60, 'max_percentage' => 69, 'grade_point' => 2.8, 'description' => 'Good'],
            ['grade_letter' => 'C+', 'min_percentage' => 50, 'max_percentage' => 59, 'grade_point' => 2.4, 'description' => 'Satisfactory'],
            ['grade_letter' => 'C', 'min_percentage' => 40, 'max_percentage' => 49, 'grade_point' => 2.0, 'description' => 'Acceptable'],
            ['grade_letter' => 'D+', 'min_percentage' => 35, 'max_percentage' => 39, 'grade_point' => 1.6, 'description' => 'Partially Acceptable'],
            ['grade_letter' => 'D', 'min_percentage' => 32, 'max_percentage' => 34, 'grade_point' => 1.2, 'description' => 'Insufficient'],
            ['grade_letter' => 'NG', 'min_percentage' => 0, 'max_percentage' => 31, 'grade_point' => 0.0, 'description' => 'Not Graded']
        ];

        foreach ($plusTwoGrades as $grade) {
            GradeScale::updateOrCreate(
                [
                    'grading_system_id' => $plusTwoGrading->id,
                    'grade_letter' => $grade['grade_letter']
                ],
                $grade
            );
        }

        // Create Bachelor's Level Grading System
        $bachelorsGrading = GradingSystem::updateOrCreate(
            ['code' => 'UNIV_BACHELORS'],
            [
                'name' => 'Bachelor\'s Level Grading (University)',
                'description' => 'University grading system for Bachelor\'s level programs',
                'status' => 'active',
                'is_default' => false,
                'order_sequence' => 2
            ]
        );

        // Bachelor's Level Grade Scales
        $bachelorsGrades = [
            ['grade_letter' => 'A+', 'min_percentage' => 90, 'max_percentage' => 100, 'grade_point' => 4.0, 'description' => 'Outstanding'],
            ['grade_letter' => 'A', 'min_percentage' => 85, 'max_percentage' => 89, 'grade_point' => 3.7, 'description' => 'Excellent'],
            ['grade_letter' => 'A-', 'min_percentage' => 80, 'max_percentage' => 84, 'grade_point' => 3.3, 'description' => 'Very Good'],
            ['grade_letter' => 'B+', 'min_percentage' => 75, 'max_percentage' => 79, 'grade_point' => 3.0, 'description' => 'Good'],
            ['grade_letter' => 'B', 'min_percentage' => 70, 'max_percentage' => 74, 'grade_point' => 2.7, 'description' => 'Above Average'],
            ['grade_letter' => 'B-', 'min_percentage' => 65, 'max_percentage' => 69, 'grade_point' => 2.3, 'description' => 'Average'],
            ['grade_letter' => 'C+', 'min_percentage' => 60, 'max_percentage' => 64, 'grade_point' => 2.0, 'description' => 'Below Average'],
            ['grade_letter' => 'C', 'min_percentage' => 55, 'max_percentage' => 59, 'grade_point' => 1.7, 'description' => 'Marginal'],
            ['grade_letter' => 'C-', 'min_percentage' => 50, 'max_percentage' => 54, 'grade_point' => 1.3, 'description' => 'Unsatisfactory'],
            ['grade_letter' => 'D', 'min_percentage' => 40, 'max_percentage' => 49, 'grade_point' => 1.0, 'description' => 'Very Poor'],
            ['grade_letter' => 'F', 'min_percentage' => 0, 'max_percentage' => 39, 'grade_point' => 0.0, 'description' => 'Fail']
        ];

        foreach ($bachelorsGrades as $grade) {
            GradeScale::updateOrCreate(
                [
                    'grading_system_id' => $bachelorsGrading->id,
                    'grade_letter' => $grade['grade_letter']
                ],
                $grade
            );
        }
    }

    /**
     * Seed Nepali exam types
     */
    private function seedExamTypes()
    {
        $examTypes = [
            // +2 Level Exam Types
            [
                'name' => 'First Term Exam',
                'code' => 'FTE',
                'description' => 'First term examination for +2 level students',
                'education_level' => 'plus_two',
                'assessment_category' => 'internal',
                'default_weightage' => 25.00,
                'default_duration_minutes' => 180,
                'order_sequence' => 1,
                'applicable_streams' => ['Science', 'Management', 'Humanities']
            ],
            [
                'name' => 'Mid-Term Exam',
                'code' => 'MTE',
                'description' => 'Mid-term examination for +2 level students',
                'education_level' => 'plus_two',
                'assessment_category' => 'internal',
                'default_weightage' => 25.00,
                'default_duration_minutes' => 180,
                'order_sequence' => 2,
                'applicable_streams' => ['Science', 'Management', 'Humanities']
            ],
            [
                'name' => 'Pre-Board Exam',
                'code' => 'PBE',
                'description' => 'Pre-board examination for +2 level students (NEB preparation)',
                'education_level' => 'plus_two',
                'assessment_category' => 'internal',
                'default_weightage' => 30.00,
                'default_duration_minutes' => 180,
                'order_sequence' => 3,
                'applicable_streams' => ['Science', 'Management', 'Humanities']
            ],
            [
                'name' => 'Internal Assessment',
                'code' => 'IA',
                'description' => 'Continuous internal assessment',
                'education_level' => 'plus_two',
                'assessment_category' => 'internal',
                'default_weightage' => 20.00,
                'default_duration_minutes' => null,
                'order_sequence' => 4,
                'applicable_streams' => ['Science', 'Management', 'Humanities']
            ],

            // Bachelor's Level Exam Types
            [
                'name' => 'Internal Assessment',
                'code' => 'IA_BACH',
                'description' => 'Internal assessment for Bachelor\'s level (40% weightage)',
                'education_level' => 'bachelors',
                'assessment_category' => 'internal',
                'default_weightage' => 40.00,
                'default_duration_minutes' => null,
                'order_sequence' => 5,
                'applicable_programs' => ['BBS', 'BSc CSIT', 'BCA', 'BA', 'BSc']
            ],
            [
                'name' => 'Mid-Semester Exam',
                'code' => 'MSE',
                'description' => 'Mid-semester examination for Bachelor\'s level',
                'education_level' => 'bachelors',
                'assessment_category' => 'internal',
                'default_weightage' => 20.00,
                'default_duration_minutes' => 120,
                'order_sequence' => 6,
                'applicable_programs' => ['BBS', 'BSc CSIT', 'BCA', 'BA', 'BSc']
            ],
            [
                'name' => 'Final Semester Exam',
                'code' => 'FSE',
                'description' => 'Final semester examination for Bachelor\'s level (60% weightage)',
                'education_level' => 'bachelors',
                'assessment_category' => 'external',
                'default_weightage' => 60.00,
                'default_duration_minutes' => 180,
                'order_sequence' => 7,
                'applicable_programs' => ['BBS', 'BSc CSIT', 'BCA', 'BA', 'BSc']
            ],
            [
                'name' => 'Practical Exam',
                'code' => 'PE',
                'description' => 'Practical examination for subjects with practical components',
                'education_level' => 'both',
                'assessment_category' => 'both',
                'default_weightage' => null,
                'default_duration_minutes' => 120,
                'order_sequence' => 8,
                'applicable_streams' => ['Science'],
                'applicable_programs' => ['BSc CSIT', 'BCA', 'BSc']
            ],
            [
                'name' => 'Viva-Voce',
                'code' => 'VV',
                'description' => 'Oral examination/viva-voce',
                'education_level' => 'both',
                'assessment_category' => 'both',
                'default_weightage' => null,
                'default_duration_minutes' => 30,
                'order_sequence' => 9
            ],
            [
                'name' => 'Re-sit/Chance Exam',
                'code' => 'RE',
                'description' => 'Re-examination for failed subjects',
                'education_level' => 'both',
                'assessment_category' => 'both',
                'default_weightage' => null,
                'default_duration_minutes' => 180,
                'order_sequence' => 10
            ]
        ];

        foreach ($examTypes as $examType) {
            ExamType::updateOrCreate(
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
            // Bachelor's Level Components
            [
                'name' => 'Attendance',
                'code' => 'ATT',
                'description' => 'Class attendance marks',
                'component_type' => 'attendance',
                'default_marks' => 10.00,
                'default_weightage' => 25.00,
                'education_level' => 'bachelors',
                'order_sequence' => 1
            ],
            [
                'name' => 'Assignment',
                'code' => 'ASG',
                'description' => 'Assignment submission and quality',
                'component_type' => 'assignment',
                'default_marks' => 15.00,
                'default_weightage' => 37.50,
                'education_level' => 'bachelors',
                'order_sequence' => 2
            ],
            [
                'name' => 'Quiz/Test',
                'code' => 'QUZ',
                'description' => 'Class quizzes and tests',
                'component_type' => 'quiz',
                'default_marks' => 10.00,
                'default_weightage' => 25.00,
                'education_level' => 'bachelors',
                'order_sequence' => 3
            ],
            [
                'name' => 'Presentation',
                'code' => 'PRE',
                'description' => 'Class presentation and participation',
                'component_type' => 'presentation',
                'default_marks' => 5.00,
                'default_weightage' => 12.50,
                'education_level' => 'bachelors',
                'order_sequence' => 4
            ],

            // +2 Level Components
            [
                'name' => 'Class Test',
                'code' => 'CT',
                'description' => 'Regular class tests',
                'component_type' => 'quiz',
                'default_marks' => 20.00,
                'default_weightage' => 50.00,
                'education_level' => 'plus_two',
                'order_sequence' => 5
            ],
            [
                'name' => 'Assignment (+2)',
                'code' => 'ASG_12',
                'description' => 'Homework and assignments for +2 level',
                'component_type' => 'assignment',
                'default_marks' => 15.00,
                'default_weightage' => 37.50,
                'education_level' => 'plus_two',
                'order_sequence' => 6
            ],
            [
                'name' => 'Attendance (+2)',
                'code' => 'ATT_12',
                'description' => 'Class attendance for +2 level',
                'component_type' => 'attendance',
                'default_marks' => 5.00,
                'default_weightage' => 12.50,
                'education_level' => 'plus_two',
                'order_sequence' => 7
            ],

            // Common Components
            [
                'name' => 'Practical Work',
                'code' => 'PRAC',
                'description' => 'Laboratory and practical work',
                'component_type' => 'practical',
                'default_marks' => 20.00,
                'default_weightage' => null,
                'education_level' => 'both',
                'order_sequence' => 8
            ],
            [
                'name' => 'Project Work',
                'code' => 'PROJ',
                'description' => 'Project-based assessment',
                'component_type' => 'project',
                'default_marks' => 25.00,
                'default_weightage' => null,
                'education_level' => 'both',
                'order_sequence' => 9
            ]
        ];

        foreach ($components as $component) {
            ExamComponent::updateOrCreate(
                ['code' => $component['code']],
                $component
            );
        }
    }

    /**
     * Seed Nepal-specific grading scales
     */
    private function seedNepaliGradingScales()
    {
        // Create Nepal University Grading System for Bachelor's
        $nepaliUniversitySystem = GradingSystem::updateOrCreate(
            ['code' => 'NEPAL_UNIV'],
            [
                'name' => 'Nepal University System',
                'code' => 'NEPAL_UNIV',
                'description' => 'Standard grading system used by Nepal universities',
                'status' => 'active',
                'is_default' => false,
                'order_sequence' => 1
            ]
        );

        // Nepal University Grade Scales
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
            GradeScale::create([
                'scale_name' => 'Nepal University Standard',
                'grade_letter' => $grade['grade_letter'],
                'min_percentage' => $grade['min_percentage'],
                'max_percentage' => $grade['max_percentage'],
                'grade_point' => $grade['grade_point'],
                'description' => $grade['description'],
                'status' => $grade['grade_point'] > 0 ? 'pass' : 'fail',
                'is_active' => true,
                'sort_order' => $index + 1
            ]);
        }

        // Create NEB Grading System for +2
        $nebSystem = GradingSystem::updateOrCreate(
            ['code' => 'NEB_PLUS_TWO'],
            [
                'name' => 'NEB +2 Grading System',
                'code' => 'NEB_PLUS_TWO',
                'description' => 'National Examination Board grading system for +2 level',
                'status' => 'active',
                'is_default' => true,
                'order_sequence' => 2
            ]
        );

        // NEB +2 Grade Scales (similar to university but may have slight differences)
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
            GradeScale::create([
                'scale_name' => 'NEB +2 Standard',
                'grade_letter' => $grade['grade_letter'],
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
}
