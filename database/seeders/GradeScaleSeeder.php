<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\GradeScale;

class GradeScaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // TU (Tribhuvan University) Grade Scale
        $gradeScales = [
            [
                'grade_letter' => 'A+',
                'grade_point' => 4.00,
                'min_percent' => 90.00,
                'max_percent' => 100.00,
                'description' => 'Outstanding',
                'order_sequence' => 1
            ],
            [
                'grade_letter' => 'A',
                'grade_point' => 3.70,
                'min_percent' => 80.00,
                'max_percent' => 89.99,
                'description' => 'Excellent',
                'order_sequence' => 2
            ],
            [
                'grade_letter' => 'B+',
                'grade_point' => 3.30,
                'min_percent' => 70.00,
                'max_percent' => 79.99,
                'description' => 'Very Good',
                'order_sequence' => 3
            ],
            [
                'grade_letter' => 'B',
                'grade_point' => 3.00,
                'min_percent' => 60.00,
                'max_percent' => 69.99,
                'description' => 'Good',
                'order_sequence' => 4
            ],
            [
                'grade_letter' => 'C+',
                'grade_point' => 2.70,
                'min_percent' => 50.00,
                'max_percent' => 59.99,
                'description' => 'Satisfactory',
                'order_sequence' => 5
            ],
            [
                'grade_letter' => 'C',
                'grade_point' => 2.30,
                'min_percent' => 45.00,
                'max_percent' => 49.99,
                'description' => 'Acceptable',
                'order_sequence' => 6
            ],
            [
                'grade_letter' => 'D+',
                'grade_point' => 2.00,
                'min_percent' => 40.00,
                'max_percent' => 44.99,
                'description' => 'Partially Acceptable',
                'order_sequence' => 7
            ],
            [
                'grade_letter' => 'D',
                'grade_point' => 1.70,
                'min_percent' => 35.00,
                'max_percent' => 39.99,
                'description' => 'Insufficient',
                'order_sequence' => 8
            ],
            [
                'grade_letter' => 'F',
                'grade_point' => 0.00,
                'min_percent' => 0.00,
                'max_percent' => 34.99,
                'description' => 'Fail',
                'order_sequence' => 9
            ]
        ];

        foreach ($gradeScales as $gradeScale) {
            GradeScale::updateOrCreate(
                ['grade_letter' => $gradeScale['grade_letter']],
                $gradeScale
            );
        }
    }
}
