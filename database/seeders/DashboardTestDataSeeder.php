<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\ClassSection;
use App\Models\Course;
use App\Models\AcademicYear;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\User;
use Carbon\Carbon;

class DashboardTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create academic year
        $academicYear = AcademicYear::firstOrCreate(
            ['name' => '2024-2025'],
            [
                'code' => '2024-25',
                'start_date' => '2024-09-01',
                'end_date' => '2025-06-30',
                'is_active' => true,
                'is_current' => true
            ]
        );

        // Get or create faculty and department
        $faculty = Faculty::firstOrCreate(
            ['name' => 'Computer Science'],
            ['code' => 'CS', 'is_active' => true]
        );

        $department = Department::firstOrCreate(
            ['name' => 'Software Engineering'],
            [
                'faculty_id' => $faculty->id,
                'code' => 'SE',
                'is_active' => true
            ]
        );

        // Get or create course
        $course = Course::firstOrCreate(
            ['title' => 'Bachelor of Computer Science'],
            [
                'faculty_id' => $faculty->id,
                'code' => 'BCS',
                'is_active' => true
            ]
        );

        // Get or create class section
        $classSection = ClassSection::firstOrCreate(
            ['name' => 'BCS-2024'],
            [
                'course_id' => $course->id,
                'academic_year_id' => $academicYear->id,
                'status' => 'active'
            ]
        );

        // Create test enrollments for the last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $enrollmentCount = rand(5, 15); // Random number of enrollments per month
            
            for ($j = 0; $j < $enrollmentCount; $j++) {
                // Create user
                $user = User::create([
                    'name' => 'Test Student ' . ($i * 10 + $j),
                    'email' => 'test' . time() . ($i * 10 + $j) . '@example.com',
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]);

                // Create student
                $student = Student::create([
                    'user_id' => $user->id,
                    'admission_number' => 'STU' . time() . str_pad($i * 10 + $j, 4, '0', STR_PAD_LEFT),
                    'faculty_id' => $faculty->id,
                    'department_id' => $department->id,
                    'academic_year_id' => $academicYear->id,
                    'created_at' => $date->copy()->addDays(rand(1, 28)),
                ]);

                // Create enrollment
                Enrollment::create([
                    'student_id' => $student->id,
                    'class_id' => $classSection->id,
                    'academic_year_id' => $academicYear->id,
                    'semester' => 1,
                    'enrollment_date' => $date->copy()->addDays(rand(1, 28)),
                    'status' => 'enrolled',
                    'created_at' => $date->copy()->addDays(rand(1, 28)),
                ]);
            }
        }

        // Create test payments for the last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $paymentCount = rand(10, 25); // Random number of payments per month
            
            for ($j = 0; $j < $paymentCount; $j++) {
                $student = Student::inRandomOrder()->first();
                if ($student) {
                    Payment::create([
                        'student_id' => $student->id,
                        'amount' => rand(5000, 50000),
                        'payment_method' => ['cash', 'bank_transfer', 'online'][rand(0, 2)],
                        'payment_reference' => 'REF' . time() . rand(1000, 9999),
                        'payment_date' => $date->copy()->addDays(rand(1, 28)),
                        'status' => 'completed',
                        'created_at' => $date->copy()->addDays(rand(1, 28)),
                    ]);
                }
            }
        }

        // Create test invoices
        $students = Student::all();
        foreach ($students as $student) {
            $invoiceCount = rand(1, 3);
            for ($i = 0; $i < $invoiceCount; $i++) {
                $status = ['paid', 'sent', 'partially_paid', 'overdue'][rand(0, 3)];
                $dueDate = Carbon::now()->addDays(rand(-30, 30));
                
                Invoice::create([
                    'student_id' => $student->id,
                    'invoice_number' => 'INV' . str_pad($student->id * 100 + $i, 6, '0', STR_PAD_LEFT),
                    'total_amount' => rand(10000, 100000),
                    'balance' => $status === 'paid' ? 0 : rand(1000, 50000),
                    'due_date' => $dueDate,
                    'status' => $status,
                    'created_at' => Carbon::now()->subDays(rand(1, 90)),
                ]);
            }
        }

        // Create test grades
        $students = Student::all();
        $gradeLetters = ['A', 'B', 'C', 'D', 'F'];
        $gradePoints = [4.0, 3.0, 2.0, 1.0, 0.0];
        
        foreach ($students as $student) {
            $gradeCount = rand(3, 8);
            for ($i = 0; $i < $gradeCount; $i++) {
                $gradeIndex = rand(0, 4);
                Grade::create([
                    'student_id' => $student->id,
                    'enrollment_id' => $student->enrollments->first()?->id,
                    'letter_grade' => $gradeLetters[$gradeIndex],
                    'grade_point' => $gradePoints[$gradeIndex],
                    'score' => rand(60, 100),
                    'max_score' => 100,
                    'grade_type' => 'exam',
                    'graded_at' => Carbon::now()->subDays(rand(1, 30)),
                    'created_at' => Carbon::now()->subDays(rand(1, 30)),
                ]);
            }
        }

        $this->command->info('Dashboard test data seeded successfully!');
    }
} 