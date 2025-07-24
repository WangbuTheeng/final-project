<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Course;

class FixCourseOrganizationTypes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'courses:fix-organization-types';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix course organization types and ensure proper semester/yearly setup';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking course organization types...');
        
        $courses = Course::all();
        $this->info("Found {$courses->count()} courses");
        
        $semesterCount = 0;
        $yearlyCount = 0;
        $nullCount = 0;
        
        foreach ($courses as $course) {
            if ($course->organization_type === 'semester') {
                $semesterCount++;
            } elseif ($course->organization_type === 'yearly') {
                $yearlyCount++;
            } else {
                $nullCount++;
                // Fix null organization types - default to semester
                $course->update(['organization_type' => 'semester']);
                $this->info("Fixed course {$course->code} - set to semester");
            }
        }
        
        $this->info("Course organization summary:");
        $this->info("- Semester-based: {$semesterCount}");
        $this->info("- Yearly-based: {$yearlyCount}");
        $this->info("- Fixed (was null): {$nullCount}");
        
        // Create sample courses if none exist
        if ($courses->count() === 0) {
            $this->info('No courses found. Creating sample courses...');
            
            Course::create([
                'title' => 'Computer Science Fundamentals',
                'code' => 'CS101',
                'description' => 'Introduction to Computer Science',
                'department_id' => 1, // Adjust as needed
                'credit_units' => 3,
                'organization_type' => 'semester',
                'semester_period' => 1,
                'course_type' => 'core',
                'is_active' => true
            ]);
            
            Course::create([
                'title' => 'Mathematics for Engineers',
                'code' => 'MATH101',
                'description' => 'Basic Mathematics',
                'department_id' => 1, // Adjust as needed
                'credit_units' => 4,
                'organization_type' => 'yearly',
                'year' => 1,
                'course_type' => 'core',
                'is_active' => true
            ]);
            
            $this->info('Created sample semester and yearly courses');
        }
        
        $this->info('Course organization types check completed!');
        
        return 0;
    }
}
