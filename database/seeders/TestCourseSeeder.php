<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Faculty;
use App\Models\Course;

class TestCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test faculty first
        $faculty = Faculty::create([
            'name' => 'Test Faculty',
            'code' => 'TF',
            'description' => 'Test faculty for course testing',
            'is_active' => true
        ]);

        // Create test courses
        Course::create([
            'title' => 'Test Course 1',
            'code' => 'TC101',
            'description' => 'Test course for yearly organization',
            'faculty_id' => $faculty->id,
            'credit_units' => 3,
            'organization_type' => 'yearly',
            'year' => 1,
            'course_type' => 'core',
            'is_active' => true
        ]);

        Course::create([
            'title' => 'Test Course 2',
            'code' => 'TC102',
            'description' => 'Test course for semester organization',
            'faculty_id' => $faculty->id,
            'credit_units' => 3,
            'organization_type' => 'semester',
            'semester_period' => 1,
            'course_type' => 'core',
            'is_active' => true
        ]);
    }
}
