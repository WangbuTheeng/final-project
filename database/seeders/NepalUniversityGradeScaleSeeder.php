<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GradeScale;

class NepalUniversityGradeScaleSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Nepal University Standard Grade Scale
        $gradeScales = [
            [
                'scale_name' => 'Nepal University Standard',
                'grade_letter' => 'A+',
                'min_percentage' => 90.00,
                'max_percentage' => 100.00,
                'min_percent' => 90.00,
                'max_percent' => 100.00,
                'grade_point' => 4.00,
                'description' => 'Outstanding',
                'status' => 'pass',
                'is_active' => true,
                'sort_order' => 1,
                'order_sequence' => 1
            ],
            [
                'scale_name' => 'Nepal University Standard',
                'grade_letter' => 'A',
                'min_percentage' => 80.00,
                'max_percentage' => 89.99,
                'min_percent' => 80.00,
                'max_percent' => 89.99,
                'grade_point' => 3.60,
                'description' => 'Excellent',
                'status' => 'pass',
                'is_active' => true,
                'sort_order' => 2,
                'order_sequence' => 2
            ],
            [
                'scale_name' => 'Nepal University Standard',
                'grade_letter' => 'B+',
                'min_percentage' => 70.00,
                'max_percentage' => 79.99,
                'min_percent' => 70.00,
                'max_percent' => 79.99,
                'grade_point' => 3.20,
                'description' => 'Very Good',
                'status' => 'pass',
                'is_active' => true,
                'sort_order' => 3,
                'order_sequence' => 3
            ],
            [
                'scale_name' => 'Nepal University Standard',
                'grade_letter' => 'B',
                'min_percentage' => 60.00,
                'max_percentage' => 69.99,
                'min_percent' => 60.00,
                'max_percent' => 69.99,
                'grade_point' => 2.80,
                'description' => 'Good',
                'status' => 'pass',
                'is_active' => true,
                'sort_order' => 4,
                'order_sequence' => 4
            ],
            [
                'scale_name' => 'Nepal University Standard',
                'grade_letter' => 'C+',
                'min_percentage' => 50.00,
                'max_percentage' => 59.99,
                'min_percent' => 50.00,
                'max_percent' => 59.99,
                'grade_point' => 2.40,
                'description' => 'Satisfactory',
                'status' => 'pass',
                'is_active' => true,
                'sort_order' => 5,
                'order_sequence' => 5
            ],
            [
                'scale_name' => 'Nepal University Standard',
                'grade_letter' => 'C',
                'min_percentage' => 45.00,
                'max_percentage' => 49.99,
                'min_percent' => 45.00,
                'max_percent' => 49.99,
                'grade_point' => 2.00,
                'description' => 'Acceptable',
                'status' => 'pass',
                'is_active' => true,
                'sort_order' => 6,
                'order_sequence' => 6
            ],
            [
                'scale_name' => 'Nepal University Standard',
                'grade_letter' => 'D',
                'min_percentage' => 40.00,
                'max_percentage' => 44.99,
                'min_percent' => 40.00,
                'max_percent' => 44.99,
                'grade_point' => 1.60,
                'description' => 'Partially Acceptable',
                'status' => 'pass',
                'is_active' => true,
                'sort_order' => 7,
                'order_sequence' => 7
            ],
            [
                'scale_name' => 'Nepal University Standard',
                'grade_letter' => 'F',
                'min_percentage' => 0.00,
                'max_percentage' => 39.99,
                'min_percent' => 0.00,
                'max_percent' => 39.99,
                'grade_point' => 0.00,
                'description' => 'Fail',
                'status' => 'fail',
                'is_active' => true,
                'sort_order' => 8,
                'order_sequence' => 8
            ]
        ];

        foreach ($gradeScales as $gradeScale) {
            GradeScale::updateOrCreate(
                [
                    'grade_letter' => $gradeScale['grade_letter'],
                    'scale_name' => $gradeScale['scale_name']
                ],
                $gradeScale
            );
        }

        $this->command->info('Nepal University Grade Scale seeded successfully!');
    }
}
