<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GradingSystem;
use App\Models\GradeScale;

class GradingSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create TU (Tribhuvan University) Grading System
        $tuGradingSystem = GradingSystem::firstOrCreate(
            ['code' => 'TU'],
            [
                'name' => 'TU Grading System',
                'description' => 'Tribhuvan University standard grading system used in Nepal',
                'status' => 'active',
                'is_default' => true,
                'order_sequence' => 1,
            ]
        );

        // TU Grade Scales
        $tuGrades = [
            ['grade_letter' => 'A+', 'min_percent' => 90, 'max_percent' => 100, 'grade_point' => 4.0, 'description' => 'Outstanding'],
            ['grade_letter' => 'A', 'min_percent' => 80, 'max_percent' => 89, 'grade_point' => 3.6, 'description' => 'Excellent'],
            ['grade_letter' => 'B+', 'min_percent' => 70, 'max_percent' => 79, 'grade_point' => 3.2, 'description' => 'Very Good'],
            ['grade_letter' => 'B', 'min_percent' => 60, 'max_percent' => 69, 'grade_point' => 2.8, 'description' => 'Good'],
            ['grade_letter' => 'C+', 'min_percent' => 50, 'max_percent' => 59, 'grade_point' => 2.4, 'description' => 'Satisfactory'],
            ['grade_letter' => 'C', 'min_percent' => 40, 'max_percent' => 49, 'grade_point' => 2.0, 'description' => 'Acceptable'],
            ['grade_letter' => 'D', 'min_percent' => 32, 'max_percent' => 39, 'grade_point' => 1.6, 'description' => 'Partially Acceptable'],
            ['grade_letter' => 'F', 'min_percent' => 0, 'max_percent' => 31, 'grade_point' => 0.0, 'description' => 'Fail'],
        ];

        foreach ($tuGrades as $index => $grade) {
            GradeScale::firstOrCreate(
                [
                    'grading_system_id' => $tuGradingSystem->id,
                    'grade_letter' => $grade['grade_letter']
                ],
                [
                    'grade_point' => $grade['grade_point'],
                    'min_percentage' => $grade['min_percent'],
                    'max_percentage' => $grade['max_percent'],
                    'description' => $grade['description'],
                    'order_sequence' => $index + 1,
                    'status' => 'active',
                ]
            );
        }

        // Create CBSE Grading System
        $cbseGradingSystem = GradingSystem::firstOrCreate(
            ['code' => 'CBSE'],
            [
                'name' => 'CBSE Grading System',
                'description' => 'Central Board of Secondary Education grading system',
                'status' => 'active',
                'is_default' => false,
                'order_sequence' => 2,
            ]
        );

        // CBSE Grade Scales (adjusted to fit decimal(3,2) constraint)
        $cbseGrades = [
            ['grade_letter' => 'A1', 'min_percent' => 91, 'max_percent' => 100, 'grade_point' => 9.99, 'description' => 'Outstanding'],
            ['grade_letter' => 'A2', 'min_percent' => 81, 'max_percent' => 90, 'grade_point' => 9.0, 'description' => 'Excellent'],
            ['grade_letter' => 'B1', 'min_percent' => 71, 'max_percent' => 80, 'grade_point' => 8.0, 'description' => 'Very Good'],
            ['grade_letter' => 'B2', 'min_percent' => 61, 'max_percent' => 70, 'grade_point' => 7.0, 'description' => 'Good'],
            ['grade_letter' => 'C1', 'min_percent' => 51, 'max_percent' => 60, 'grade_point' => 6.0, 'description' => 'Fair'],
            ['grade_letter' => 'C2', 'min_percent' => 41, 'max_percent' => 50, 'grade_point' => 5.0, 'description' => 'Satisfactory'],
            ['grade_letter' => 'D', 'min_percent' => 33, 'max_percent' => 40, 'grade_point' => 4.0, 'description' => 'Needs Improvement'],
            ['grade_letter' => 'E', 'min_percent' => 0, 'max_percent' => 32, 'grade_point' => 0.0, 'description' => 'Fail'],
        ];

        foreach ($cbseGrades as $index => $grade) {
            GradeScale::firstOrCreate(
                [
                    'grading_system_id' => $cbseGradingSystem->id,
                    'grade_letter' => $grade['grade_letter']
                ],
                [
                    'grade_point' => $grade['grade_point'],
                    'min_percentage' => $grade['min_percent'],
                    'max_percentage' => $grade['max_percent'],
                    'description' => $grade['description'],
                    'order_sequence' => $index + 1,
                    'status' => 'active',
                ]
            );
        }

        // Create Simple Letter Grading System
        $simpleGradingSystem = GradingSystem::firstOrCreate(
            ['code' => 'SIMPLE'],
            [
                'name' => 'Simple Letter Grading',
                'description' => 'Traditional A-F letter grading system',
                'status' => 'active',
                'is_default' => false,
                'order_sequence' => 3,
            ]
        );

        // Simple Grade Scales
        $simpleGrades = [
            ['grade_letter' => 'A', 'min_percent' => 90, 'max_percent' => 100, 'grade_point' => 4.0, 'description' => 'Excellent'],
            ['grade_letter' => 'B', 'min_percent' => 80, 'max_percent' => 89, 'grade_point' => 3.0, 'description' => 'Good'],
            ['grade_letter' => 'C', 'min_percent' => 70, 'max_percent' => 79, 'grade_point' => 2.0, 'description' => 'Average'],
            ['grade_letter' => 'D', 'min_percent' => 60, 'max_percent' => 69, 'grade_point' => 1.0, 'description' => 'Below Average'],
            ['grade_letter' => 'F', 'min_percent' => 0, 'max_percent' => 59, 'grade_point' => 0.0, 'description' => 'Fail'],
        ];

        foreach ($simpleGrades as $index => $grade) {
            GradeScale::firstOrCreate(
                [
                    'grading_system_id' => $simpleGradingSystem->id,
                    'grade_letter' => $grade['grade_letter']
                ],
                [
                    'grade_point' => $grade['grade_point'],
                    'min_percent' => $grade['min_percent'],
                    'max_percent' => $grade['max_percent'],
                    'description' => $grade['description'],
                    'order_sequence' => $index + 1,
                    'status' => 'active',
                ]
            );
        }

        // Create Percentage-based Grading System
        $percentageGradingSystem = GradingSystem::firstOrCreate(
            ['code' => 'PERCENT'],
            [
                'name' => 'Percentage Grading',
                'description' => 'Percentage-based grading with detailed ranges',
                'status' => 'active',
                'is_default' => false,
                'order_sequence' => 4,
            ]
        );

        // Percentage Grade Scales (shortened to fit varchar(2) constraint)
        $percentageGrades = [
            ['grade_letter' => 'A+', 'min_percent' => 90, 'max_percent' => 100, 'grade_point' => 4.0, 'description' => 'Distinction (90-100%)'],
            ['grade_letter' => 'A', 'min_percent' => 80, 'max_percent' => 89, 'grade_point' => 3.5, 'description' => 'First Class (80-89%)'],
            ['grade_letter' => 'B+', 'min_percent' => 70, 'max_percent' => 79, 'grade_point' => 3.0, 'description' => 'Second Class (70-79%)'],
            ['grade_letter' => 'B', 'min_percent' => 60, 'max_percent' => 69, 'grade_point' => 2.5, 'description' => 'Second Class (60-69%)'],
            ['grade_letter' => 'C', 'min_percent' => 50, 'max_percent' => 59, 'grade_point' => 2.0, 'description' => 'Third Class (50-59%)'],
            ['grade_letter' => 'D', 'min_percent' => 40, 'max_percent' => 49, 'grade_point' => 1.5, 'description' => 'Pass (40-49%)'],
            ['grade_letter' => 'F', 'min_percent' => 0, 'max_percent' => 39, 'grade_point' => 0.0, 'description' => 'Fail (0-39%)'],
        ];

        foreach ($percentageGrades as $index => $grade) {
            GradeScale::firstOrCreate(
                [
                    'grading_system_id' => $percentageGradingSystem->id,
                    'grade_letter' => $grade['grade_letter']
                ],
                [
                    'grade_point' => $grade['grade_point'],
                    'min_percentage' => $grade['min_percent'],
                    'max_percentage' => $grade['max_percent'],
                    'description' => $grade['description'],
                    'order_sequence' => $index + 1,
                    'status' => 'active',
                ]
            );
        }
    }
}
