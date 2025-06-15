<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicYear;
use Carbon\Carbon;

class AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a sample academic year for 2024-2025
        AcademicYear::create([
            'name' => '2024/2025',
            'code' => '2024-25',
            'start_date' => Carbon::create(2024, 9, 1),
            'end_date' => Carbon::create(2025, 8, 31),
            'is_current' => true,
            'is_active' => true,
            'description' => 'Academic Year 2024-2025'
        ]);

        // Create another academic year for 2023-2024 (inactive)
        AcademicYear::create([
            'name' => '2023/2024',
            'code' => '2023-24',
            'start_date' => Carbon::create(2023, 9, 1),
            'end_date' => Carbon::create(2024, 8, 31),
            'is_current' => false,
            'is_active' => false,
            'description' => 'Academic Year 2023-2024'
        ]);
    }
}
