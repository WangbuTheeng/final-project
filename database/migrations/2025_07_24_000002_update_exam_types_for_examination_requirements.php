<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ExamType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Clear existing exam types
        ExamType::query()->delete();
        
        // Insert new exam types according to examination requirements
        $examTypes = [
            [
                'name' => 'First Assessment',
                'code' => 'FA',
                'description' => 'First assessment examination',
                'education_level' => 'both',
                'assessment_category' => 'internal',
                'default_weightage' => 15.00,
                'default_duration_minutes' => 120,
                'is_active' => true,
                'order_sequence' => 1
            ],
            [
                'name' => 'First Terminal',
                'code' => 'FT',
                'description' => 'First terminal examination',
                'education_level' => 'both',
                'assessment_category' => 'internal',
                'default_weightage' => 25.00,
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
                'default_weightage' => 15.00,
                'default_duration_minutes' => 120,
                'is_active' => true,
                'order_sequence' => 3
            ],
            [
                'name' => 'Second Terminal',
                'code' => 'ST',
                'description' => 'Second terminal examination',
                'education_level' => 'both',
                'assessment_category' => 'internal',
                'default_weightage' => 25.00,
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
                'default_weightage' => 15.00,
                'default_duration_minutes' => 120,
                'is_active' => true,
                'order_sequence' => 5
            ],
            [
                'name' => 'Final Term',
                'code' => 'FTM',
                'description' => 'Final term examination',
                'education_level' => 'both',
                'assessment_category' => 'external',
                'default_weightage' => 50.00,
                'default_duration_minutes' => 240,
                'is_active' => true,
                'order_sequence' => 6
            ],
            [
                'name' => 'Monthly Term',
                'code' => 'MT',
                'description' => 'Monthly term examination',
                'education_level' => 'both',
                'assessment_category' => 'internal',
                'default_weightage' => 10.00,
                'default_duration_minutes' => 90,
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
                'default_duration_minutes' => 60,
                'is_active' => true,
                'order_sequence' => 8
            ]
        ];

        foreach ($examTypes as $examType) {
            ExamType::create($examType);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Delete the new exam types
        ExamType::query()->delete();
    }
};
