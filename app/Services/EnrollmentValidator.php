<?php

namespace App\Services;

use App\Models\Student;
use App\Models\ClassSection;
use App\Models\Course;

class EnrollmentValidator
{
    private array $errors = [];
    
    public function __construct(
        private Student $student,
        private ClassSection $class,
        private int $academicYearId
    ) {}
    
    public function validate(): EnrollmentValidationResult
    {
        $this->checkCapacity();
        $this->checkDuplicateEnrollment();
        $this->checkPrerequisites();
        $this->checkStudentStatus();
        $this->checkClassStatus();
        
        return new EnrollmentValidationResult(
            valid: empty($this->errors),
            errors: $this->errors
        );
    }
    
    private function checkCapacity(): void
    {
        if (!$this->class->hasAvailableSlots()) {
            $this->errors[] = 'Class has reached maximum capacity (' . $this->class->capacity . ' students)';
        }
    }
    
    private function checkDuplicateEnrollment(): void
    {
        $exists = $this->student->enrollments()
            ->withTrashed()
            ->where('class_id', $this->class->id)
            ->where('academic_year_id', $this->academicYearId)
            ->exists();
            
        if ($exists) {
            $this->errors[] = 'Student is already enrolled in this class for the selected academic year';
        }
    }
    
    private function checkPrerequisites(): void
    {
        $course = $this->class->course;
        
        // Skip if no prerequisites defined
        if (empty($course->prerequisites)) {
            return;
        }
        
        // Get completed course IDs for this student
        $completedCourseIds = $this->student->completedEnrollments()
            ->join('classes', 'enrollments.class_id', '=', 'classes.id')
            ->pluck('classes.course_id')
            ->toArray();
            
        $missingPrerequisites = array_diff($course->prerequisites, $completedCourseIds);
        
        if (!empty($missingPrerequisites)) {
            $courseNames = Course::whereIn('id', $missingPrerequisites)
                ->pluck('title')
                ->toArray();
                
            $this->errors[] = 'Missing prerequisites: ' . implode(', ', $courseNames);
        }
    }
    
    private function checkStudentStatus(): void
    {
        if ($this->student->status !== 'active') {
            $statusText = ucfirst(str_replace('_', ' ', $this->student->status));
            $this->errors[] = "Student account is {$statusText}. Only active students can enroll in classes";
        }
    }
    
    private function checkClassStatus(): void
    {
        if ($this->class->status !== 'active') {
            $statusText = ucfirst(str_replace('_', ' ', $this->class->status));
            $this->errors[] = "Class is {$statusText}. Only active classes accept new enrollments";
        }
    }
    
    private function checkAcademicYearStatus(): void
    {
        $academicYear = $this->class->academicYear;
        
        if ($academicYear && $academicYear->status !== 'active') {
            $this->errors[] = 'Cannot enroll in classes for inactive academic year';
        }
    }
    
    private function checkEnrollmentPeriod(): void
    {
        // Add logic to check if enrollment is within allowed period
        // This would depend on your business rules
        $academicYear = $this->class->academicYear;
        
        if ($academicYear && $academicYear->enrollment_end_date && now()->gt($academicYear->enrollment_end_date)) {
            $this->errors[] = 'Enrollment period has ended for this academic year';
        }
    }
}