<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use App\Models\ClassSection;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TUStudentsSeeder extends Seeder
{
    /**
     * Run the database seeds for TU students.
     */
    public function run(): void
    {
        // Nepali names for realistic data
        $firstNames = [
            'Aarav', 'Aayush', 'Abhishek', 'Aditya', 'Ajay', 'Akash', 'Amit', 'Anish', 'Ankit', 'Arjun',
            'Ashish', 'Bibek', 'Bikash', 'Binod', 'Deepak', 'Dipesh', 'Gagan', 'Hari', 'Kiran', 'Krishna',
            'Manish', 'Nabin', 'Niraj', 'Pawan', 'Prakash', 'Prashant', 'Rajesh', 'Rajan', 'Ram', 'Roshan',
            'Sagar', 'Sandip', 'Santosh', 'Shyam', 'Sujan', 'Suman', 'Sunil', 'Surya', 'Umesh', 'Vivek',
            'Aastha', 'Anita', 'Anjana', 'Anju', 'Archana', 'Asha', 'Bina', 'Binita', 'Gita', 'Kamala',
            'Kavita', 'Laxmi', 'Maya', 'Nisha', 'Pooja', 'Puja', 'Radha', 'Rama', 'Rashmi', 'Rita',
            'Sabina', 'Sadhana', 'Sangita', 'Sarita', 'Shanti', 'Sharmila', 'Shova', 'Sita', 'Sunita', 'Usha'
        ];

        $lastNames = [
            'Adhikari', 'Aryal', 'Bajracharya', 'Basnet', 'Bhandari', 'Bhatta', 'Bhattarai', 'Chaudhary',
            'Dahal', 'Dhakal', 'Gautam', 'Ghimire', 'Gurung', 'Joshi', 'Kafle', 'Karki', 'KC', 'Khadka',
            'Lamichhane', 'Lama', 'Maharjan', 'Magar', 'Mainali', 'Malla', 'Manandhar', 'Neupane',
            'Oli', 'Pandey', 'Pandit', 'Pant', 'Pokharel', 'Poudel', 'Pradhan', 'Rai', 'Regmi', 'Rijal',
            'Sapkota', 'Sharma', 'Sherpa', 'Shrestha', 'Subedi', 'Tamang', 'Thapa', 'Tiwari', 'Upreti'
        ];

        $districts = [
            'Kathmandu', 'Lalitpur', 'Bhaktapur', 'Chitwan', 'Pokhara', 'Butwal', 'Biratnagar', 'Janakpur',
            'Dharan', 'Hetauda', 'Birgunj', 'Nepalgunj', 'Dhangadhi', 'Mahendranagar', 'Gorkha', 'Lamjung'
        ];

        // Get class sections
        $classSections = ClassSection::all();
        
        $createdStudents = [];
        $enrollmentCounter = 1;

        foreach ($classSections as $class) {
            // Create students for each class (70-90% of capacity)
            $numStudents = rand(
                (int)($class->capacity * 0.7), 
                (int)($class->capacity * 0.9)
            );

            for ($i = 1; $i <= $numStudents; $i++) {
                $firstName = $firstNames[array_rand($firstNames)];
                $lastName = $lastNames[array_rand($lastNames)];
                $fullName = $firstName . ' ' . $lastName;
                
                // Generate TU registration number based on course
                $courseCode = $class->course->code;
                $year = '2081'; // Current Nepali year
                $regNumber = $courseCode . '-' . $year . '-' . str_pad($enrollmentCounter, 4, '0', STR_PAD_LEFT);
                
                // Create user account
                $email = strtolower($firstName . '.' . $lastName . $enrollmentCounter . '@student.tu.edu.np');
                $user = User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => $fullName,
                        'email' => $email,
                        'password' => Hash::make('password123'),
                        'role' => 'student',
                        'phone' => '+977-98' . rand(10000000, 99999999),
                        'address' => $districts[array_rand($districts)] . ', Nepal',
                    ]
                );

                // Create student profile (using actual table structure)
                $guardianInfo = [
                    'guardian_name' => $lastNames[array_rand($lastNames)] . ' ' . $lastNames[array_rand($lastNames)],
                    'guardian_phone' => '+977-98' . rand(10000000, 99999999),
                    'guardian_email' => strtolower('guardian' . $enrollmentCounter . '@gmail.com'),
                    'emergency_contact' => '+977-98' . rand(10000000, 99999999),
                    'nationality' => 'Nepali',
                    'religion' => ['Hindu', 'Buddhist', 'Christian', 'Muslim', 'Other'][array_rand(['Hindu', 'Buddhist', 'Christian', 'Muslim', 'Other'])],
                ];

                $student = Student::updateOrCreate(
                    ['admission_number' => $regNumber],
                    [
                        'user_id' => $user->id,
                        'admission_number' => $regNumber,
                        'department_id' => $class->course->department_id ?? null,
                        'faculty_id' => $class->course->department->faculty_id ?? null,
                        'academic_year_id' => $class->academic_year_id,
                        'mode_of_entry' => 'entrance_exam',
                        'status' => 'active',
                        'guardian_info' => json_encode($guardianInfo),
                    ]
                );

                // Create enrollment
                Enrollment::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'class_id' => $class->id,
                        'academic_year_id' => $class->academic_year_id,
                    ],
                    [
                        'enrollment_date' => $class->start_date,
                        'status' => 'enrolled',
                    ]
                );

                $createdStudents[] = $student;
                $enrollmentCounter++;
            }

            $this->command->info("Created {$numStudents} students for {$class->name}");
        }

        $this->command->info('Created ' . count($createdStudents) . ' students and enrollments successfully!');
    }
}
