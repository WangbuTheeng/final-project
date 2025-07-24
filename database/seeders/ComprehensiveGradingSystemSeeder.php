<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GradingSystem;
use App\Models\GradeScale;

class ComprehensiveGradingSystemSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create or update Nepal University Standard Grading System
        $nepalSystem = GradingSystem::updateOrCreate(
            ['code' => 'NUS'],
            [
                'name' => 'Nepal University Standard',
                'description' => 'Standard grading system used by Nepal Universities with minimum 50% passing requirement',
                'status' => 'active',
                'is_default' => true,
                'order_sequence' => 1
            ]
        );

        // Grade scales based on the provided image
        $gradeScales = [
            [
                'grade_letter' => 'A',
                'min_percentage' => 90.00,
                'max_percentage' => 100.00,
                'grade_point' => 4.00,
                'description' => 'Distinction',
                'status' => 'pass',
                'order_sequence' => 1
            ],
            [
                'grade_letter' => 'A-',
                'min_percentage' => 80.00,
                'max_percentage' => 89.99,
                'grade_point' => 3.85, // Average of 3.7-3.99
                'description' => 'Very Good',
                'status' => 'pass',
                'order_sequence' => 2
            ],
            [
                'grade_letter' => 'B+',
                'min_percentage' => 70.00,
                'max_percentage' => 79.99,
                'grade_point' => 3.51, // Average of 3.3-3.69
                'description' => 'First Division',
                'status' => 'pass',
                'order_sequence' => 3
            ],
            [
                'grade_letter' => 'B',
                'min_percentage' => 60.00,
                'max_percentage' => 69.99,
                'grade_point' => 3.15, // Average of 3.0-3.29
                'description' => 'Second Division',
                'status' => 'pass',
                'order_sequence' => 4
            ],
            [
                'grade_letter' => 'B-',
                'min_percentage' => 50.00,
                'max_percentage' => 59.99,
                'grade_point' => 2.85, // Average of 2.7-2.99
                'description' => 'Pass',
                'status' => 'pass',
                'order_sequence' => 5
            ],
            [
                'grade_letter' => 'F',
                'min_percentage' => 0.00,
                'max_percentage' => 49.99,
                'grade_point' => 0.00, // Below 2.7
                'description' => 'Fail',
                'status' => 'fail',
                'order_sequence' => 6
            ],
            [
                'grade_letter' => 'N/G',
                'min_percentage' => 0.00,
                'max_percentage' => 100.00,
                'grade_point' => 0.00,
                'description' => 'No Grade - Failed in Theory or Practical Component',
                'status' => 'fail',
                'order_sequence' => 7
            ]
        ];

        // Clear existing grade scales for this system
        GradeScale::where('grading_system_id', $nepalSystem->id)->delete();

        foreach ($gradeScales as $scale) {
            GradeScale::create([
                'grading_system_id' => $nepalSystem->id,
                'scale_name' => 'Nepal University Standard',
                'grade_letter' => $scale['grade_letter'],
                'min_percentage' => $scale['min_percentage'],
                'max_percentage' => $scale['max_percentage'],
                'min_percent' => $scale['min_percentage'], // For view compatibility
                'max_percent' => $scale['max_percentage'], // For view compatibility
                'grade_point' => $scale['grade_point'],
                'description' => $scale['description'],
                'status' => $scale['status'],
                'is_active' => true,
                'sort_order' => $scale['order_sequence'],
                'order_sequence' => $scale['order_sequence']
            ]);
        }

        // Create or update Alternative Grading System (for flexibility)
        $alternativeSystem = GradingSystem::updateOrCreate(
            ['code' => 'ALT'],
            [
                'name' => 'Alternative Grading System',
                'description' => 'Alternative grading system with different grade points',
                'status' => 'active',
                'is_default' => false,
                'order_sequence' => 2
            ]
        );

        // Alternative grade scales
        $alternativeScales = [
            [
                'grade_letter' => 'A+',
                'min_percentage' => 95.00,
                'max_percentage' => 100.00,
                'grade_point' => 4.00,
                'description' => 'Excellent',
                'status' => 'pass',
                'order_sequence' => 1
            ],
            [
                'grade_letter' => 'A',
                'min_percentage' => 85.00,
                'max_percentage' => 94.99,
                'grade_point' => 3.75,
                'description' => 'Very Good',
                'status' => 'pass',
                'order_sequence' => 2
            ],
            [
                'grade_letter' => 'B+',
                'min_percentage' => 75.00,
                'max_percentage' => 84.99,
                'grade_point' => 3.25,
                'description' => 'Good',
                'status' => 'pass',
                'order_sequence' => 3
            ],
            [
                'grade_letter' => 'B',
                'min_percentage' => 65.00,
                'max_percentage' => 74.99,
                'grade_point' => 2.75,
                'description' => 'Satisfactory',
                'status' => 'pass',
                'order_sequence' => 4
            ],
            [
                'grade_letter' => 'C',
                'min_percentage' => 50.00,
                'max_percentage' => 64.99,
                'grade_point' => 2.00,
                'description' => 'Pass',
                'status' => 'pass',
                'order_sequence' => 5
            ],
            [
                'grade_letter' => 'F',
                'min_percentage' => 0.00,
                'max_percentage' => 49.99,
                'grade_point' => 0.00,
                'description' => 'Fail',
                'status' => 'fail',
                'order_sequence' => 6
            ],
            [
                'grade_letter' => 'N/G',
                'min_percentage' => 0.00,
                'max_percentage' => 100.00,
                'grade_point' => 0.00,
                'description' => 'No Grade - Failed in Theory or Practical Component',
                'status' => 'fail',
                'order_sequence' => 7
            ]
        ];

        // Clear existing grade scales for this system
        GradeScale::where('grading_system_id', $alternativeSystem->id)->delete();

        foreach ($alternativeScales as $scale) {
            GradeScale::create([
                'grading_system_id' => $alternativeSystem->id,
                'scale_name' => 'Alternative Grading System',
                'grade_letter' => $scale['grade_letter'],
                'min_percentage' => $scale['min_percentage'],
                'max_percentage' => $scale['max_percentage'],
                'min_percent' => $scale['min_percentage'], // For view compatibility
                'max_percent' => $scale['max_percentage'], // For view compatibility
                'grade_point' => $scale['grade_point'],
                'description' => $scale['description'],
                'status' => $scale['status'],
                'is_active' => true,
                'sort_order' => $scale['order_sequence'],
                'order_sequence' => $scale['order_sequence']
            ]);
        }

        $this->command->info('Comprehensive grading systems created successfully!');
        $this->command->info('- Nepal University Standard (Default)');
        $this->command->info('- Alternative Grading System');
        $this->command->info('Both systems include N/G grade for theory/practical failures.');
    }
}
