<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExamType;

class UpdatedExamTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing exam types
        ExamType::truncate();

        $examTypes = [
            [
                'name' => 'First Assessment',
                'code' => 'FA',
                'description' => 'First assessment examination',
                'education_level' => 'both',
                'assessment_category' => 'internal',
                'default_weightage' => 10.00,
                'default_duration_minutes' => 90,
                'is_active' => true,
                'order_sequence' => 1
            ],
            [
                'name' => 'First Terminal',
                'code' => 'FT',
                'description' => 'First terminal examination',
                'education_level' => 'both',
                'assessment_category' => 'internal',
                'default_weightage' => 20.00,
                'default_duration_minutes' => 180,
                'is_active' => true,
                'order_sequence' => 2
            ],
            [
                'name' => 'Second Assessment',
                'code' => 'SA',
                'description' => 'Second assessment examination',
                'education_level' => 'both',
                'assessment_category' => 'internal',
                'default_weightage' => 10.00,
                'default_duration_minutes' => 90,
                'is_active' => true,
                'order_sequence' => 3
            ],
            [
                'name' => 'Second Terminal',
                'code' => 'ST',
                'description' => 'Second terminal examination',
                'education_level' => 'both',
                'assessment_category' => 'internal',
                'default_weightage' => 20.00,
                'default_duration_minutes' => 180,
                'is_active' => true,
                'order_sequence' => 4
            ],
            [
                'name' => 'Third Assessment',
                'code' => 'TA',
                'description' => 'Third assessment examination',
                'education_level' => 'both',
                'assessment_category' => 'internal',
                'default_weightage' => 10.00,
                'default_duration_minutes' => 90,
                'is_active' => true,
                'order_sequence' => 5
            ],
            [
                'name' => 'Final Term',
                'code' => 'FTM',
                'description' => 'Final term examination',
                'education_level' => 'both',
                'assessment_category' => 'external',
                'default_weightage' => 60.00,
                'default_duration_minutes' => 180,
                'is_active' => true,
                'order_sequence' => 6
            ],
            [
                'name' => 'Monthly Term',
                'code' => 'MT',
                'description' => 'Monthly term examination',
                'education_level' => 'both',
                'assessment_category' => 'internal',
                'default_weightage' => 5.00,
                'default_duration_minutes' => 60,
                'is_active' => true,
                'order_sequence' => 7
            ],
            [
                'name' => 'Weekly Test',
                'code' => 'WT',
                'description' => 'Weekly test examination',
                'education_level' => 'both',
                'assessment_category' => 'internal',
                'default_weightage' => 5.00,
                'default_duration_minutes' => 45,
                'is_active' => true,
                'order_sequence' => 8
            ]
        ];

        foreach ($examTypes as $examType) {
            ExamType::create($examType);
        }

        $this->command->info('Updated exam types seeded successfully!');
    }
}
