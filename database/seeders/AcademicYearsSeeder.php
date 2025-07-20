<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicYear;
use Carbon\Carbon;

class AcademicYearsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing academic years
        AcademicYear::truncate();

        // Create academic years for Tribhuvan University
        $academicYears = [
            [
                'name' => '2081-2082',
                'code' => '2081-82',
                'start_date' => '2024-07-15', // Nepali calendar: Shrawan 2081
                'end_date' => '2025-07-14',   // Nepali calendar: Ashadh 2082
                'description' => 'Academic Year 2081-2082 (Current)',
                'is_current' => true,
                'is_active' => true,
                'semester_config' => [
                    'total_semesters' => 2,
                    'semester_1' => [
                        'name' => 'First Semester',
                        'start_date' => '2024-07-15',
                        'end_date' => '2024-12-15',
                    ],
                    'semester_2' => [
                        'name' => 'Second Semester', 
                        'start_date' => '2025-01-15',
                        'end_date' => '2025-07-14',
                    ]
                ]
            ],
            [
                'name' => '2080-2081',
                'code' => '2080-81',
                'start_date' => '2023-07-15',
                'end_date' => '2024-07-14',
                'description' => 'Academic Year 2080-2081 (Previous)',
                'is_current' => false,
                'is_active' => false,
                'semester_config' => [
                    'total_semesters' => 2,
                    'semester_1' => [
                        'name' => 'First Semester',
                        'start_date' => '2023-07-15',
                        'end_date' => '2023-12-15',
                    ],
                    'semester_2' => [
                        'name' => 'Second Semester',
                        'start_date' => '2024-01-15', 
                        'end_date' => '2024-07-14',
                    ]
                ]
            ],
            [
                'name' => '2082-2083',
                'code' => '2082-83',
                'start_date' => '2025-07-15',
                'end_date' => '2026-07-14',
                'description' => 'Academic Year 2082-2083 (Upcoming)',
                'is_current' => false,
                'is_active' => false,
                'semester_config' => [
                    'total_semesters' => 2,
                    'semester_1' => [
                        'name' => 'First Semester',
                        'start_date' => '2025-07-15',
                        'end_date' => '2025-12-15',
                    ],
                    'semester_2' => [
                        'name' => 'Second Semester',
                        'start_date' => '2026-01-15',
                        'end_date' => '2026-07-14',
                    ]
                ]
            ],
            [
                'name' => '2079-2080',
                'code' => '2079-80',
                'start_date' => '2022-07-15',
                'end_date' => '2023-07-14',
                'description' => 'Academic Year 2079-2080 (Archived)',
                'is_current' => false,
                'is_active' => false,
                'semester_config' => [
                    'total_semesters' => 2,
                    'semester_1' => [
                        'name' => 'First Semester',
                        'start_date' => '2022-07-15',
                        'end_date' => '2022-12-15',
                    ],
                    'semester_2' => [
                        'name' => 'Second Semester',
                        'start_date' => '2023-01-15',
                        'end_date' => '2023-07-14',
                    ]
                ]
            ]
        ];

        foreach ($academicYears as $yearData) {
            AcademicYear::create($yearData);
        }

        $this->command->info('✅ Academic Years seeded successfully!');
        $this->command->info('📅 Created 4 academic years with current year: 2081-2082');
    }
}
