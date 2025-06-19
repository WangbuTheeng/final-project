<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Course;
use App\Models\ClassSection;
use App\Models\Enrollment;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class EnrollmentService
{
    /**
     * Enroll a student in a course
     */
    public function enrollStudent(Student $student, ClassSection $class, $academicYearId, $semester, $enrollmentDate = null)
    {
        DB::beginTransaction();

        try {
            // Validate enrollment
            $this->validateEnrollment($student, $class, $academicYearId, $semester);

            // Create enrollment
            $enrollment = Enrollment::create([
                'student_id' => $student->id,
                'class_id' => $class->id,
                'academic_year_id' => $academicYearId,
                'semester' => $semester,
                'enrollment_date' => $enrollmentDate ?? now(),
                'status' => 'enrolled'
            ]);

            DB::commit();

            return $enrollment;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Bulk enroll students in multiple courses
     */
    public function bulkEnrollStudents(Collection $students, array $courseIds, $academicYearId, $semester, $enrollmentDate = null)
    {
        DB::beginTransaction();

        try {
            $enrollments = [];
            $errors = [];

            foreach ($students as $student) {
                foreach ($courseIds as $courseId) {
                    try {
                        $course = Course::findOrFail($courseId);
                        $class = $this->findAvailableClass($course, $academicYearId, $semester);

                        if (!$class) {
                            $errors[] = "No available class for course {$course->code}";
                            continue;
                        }

                        $enrollment = $this->enrollStudent($student, $class, $academicYearId, $semester, $enrollmentDate);
                        $enrollments[] = $enrollment;

                    } catch (\Exception $e) {
                        $errors[] = "Error enrolling student {$student->admission_number} in course {$course->code}: " . $e->getMessage();
                    }
                }
            }

            DB::commit();

            return [
                'enrollments' => $enrollments,
                'errors' => $errors,
                'success_count' => count($enrollments),
                'error_count' => count($errors)
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Drop an enrollment
     */
    public function dropEnrollment(Enrollment $enrollment, $reason = null)
    {
        if (!$enrollment->canBeDropped()) {
            throw new \Exception('This enrollment cannot be dropped at this time.');
        }

        DB::beginTransaction();

        try {
            $enrollment->drop($reason);
            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get enrollment recommendations for a student
     */
    public function getEnrollmentRecommendations(Student $student, $academicYearId, $semester)
    {
        // Get courses for student's department (level checking removed)
        $availableCourses = Course::where('department_id', $student->department_id)
            ->where('is_active', true)
            ->where(function ($query) use ($semester) {
                $query->where('semester', $semester)
                      ->orWhere('semester', 'both');
            })
            ->get();

        $recommendations = [];
        $alreadyEnrolled = [];
        $prerequisitesMissing = [];

        foreach ($availableCourses as $course) {
            // Check if already enrolled
            $existingEnrollment = $student->enrollments()
                ->whereHas('class', function ($query) use ($course) {
                    $query->where('course_id', $course->id);
                })
                ->where('academic_year_id', $academicYearId)
                ->where('semester', $semester)
                ->where('status', 'enrolled')
                ->exists();

            if ($existingEnrollment) {
                $alreadyEnrolled[] = $course;
                continue;
            }

            // Check if already completed
            $completed = $student->completedEnrollments()
                ->whereHas('class', function ($query) use ($course) {
                    $query->where('course_id', $course->id);
                })
                ->exists();

            if ($completed) {
                continue;
            }

            // Check prerequisites
            if (!$this->hasMetPrerequisites($student, $course)) {
                $prerequisitesMissing[] = $course;
                continue;
            }

            // Check if class is available
            $availableClass = $this->findAvailableClass($course, $academicYearId, $semester);
            if ($availableClass) {
                $recommendations[] = [
                    'course' => $course,
                    'class' => $availableClass,
                    'priority' => $this->calculateCoursePriority($course, $student)
                ];
            }
        }

        // Sort recommendations by priority
        usort($recommendations, function ($a, $b) {
            return $b['priority'] <=> $a['priority'];
        });

        return [
            'recommendations' => $recommendations,
            'already_enrolled' => $alreadyEnrolled,
            'prerequisites_missing' => $prerequisitesMissing
        ];
    }

    /**
     * Calculate student's course load for a semester
     */
    public function calculateCourseLoad(Student $student, $academicYearId, $semester)
    {
        $enrollments = $student->enrollmentsForSemester($academicYearId, $semester)
            ->where('status', 'enrolled')
            ->with('class.course')
            ->get();

        $totalCredits = $enrollments->sum(function ($enrollment) {
            return $enrollment->class->course->credit_units;
        });

        $courseCount = $enrollments->count();

        return [
            'total_credits' => $totalCredits,
            'course_count' => $courseCount,
            'enrollments' => $enrollments,
            'is_overload' => $totalCredits > 24, // Assuming 24 is max credits per semester
            'is_underload' => $totalCredits < 12  // Assuming 12 is min credits per semester
        ];
    }

    /**
     * Generate enrollment report for a department
     */
    public function generateDepartmentEnrollmentReport($departmentId, $academicYearId, $semester)
    {
        $enrollments = Enrollment::whereHas('student', function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->where('academic_year_id', $academicYearId)
            ->where('semester', $semester)
            ->with(['student.user', 'class.course'])
            ->get();

        $stats = [
            'total_enrollments' => $enrollments->count(),
            'unique_students' => $enrollments->unique('student_id')->count(),
            'unique_courses' => $enrollments->unique('class.course.id')->count(),
            'by_status' => $enrollments->groupBy('status')->map->count(),

            'by_course' => $enrollments->groupBy('class.course.code')->map->count()
        ];

        return [
            'enrollments' => $enrollments,
            'statistics' => $stats
        ];
    }

    /**
     * Validate enrollment eligibility
     */
    private function validateEnrollment(Student $student, ClassSection $class, $academicYearId, $semester)
    {
        // Check if student is active
        if ($student->status !== 'active') {
            throw new \Exception('Student is not active.');
        }

        // Check if class is active
        if ($class->status !== 'active') {
            throw new \Exception('Class is not active.');
        }

        // Check class capacity
        if ($class->current_enrollment >= $class->capacity) {
            throw new \Exception('Class is at full capacity.');
        }

        // Check if student can enroll in this course
        if (!$student->canEnrollInCourse($class->course, $academicYearId, $semester)) {
            throw new \Exception('Student is not eligible to enroll in this course.');
        }

        // Check for duplicate enrollment
        $existingEnrollment = Enrollment::where('student_id', $student->id)
            ->where('class_id', $class->id)
            ->where('academic_year_id', $academicYearId)
            ->where('semester', $semester)
            ->where('status', 'enrolled')
            ->exists();

        if ($existingEnrollment) {
            throw new \Exception('Student is already enrolled in this class.');
        }
    }

    /**
     * Find available class for a course
     */
    private function findAvailableClass(Course $course, $academicYearId, $semester)
    {
        return ClassSection::where('course_id', $course->id)
            ->where('academic_year_id', $academicYearId)
            ->where('semester', $semester)
            ->where('status', 'active')
            ->whereColumn('current_enrollment', '<', 'capacity')
            ->first();
    }

    /**
     * Check if student has met prerequisites for a course
     */
    private function hasMetPrerequisites(Student $student, Course $course)
    {
        if (empty($course->prerequisites)) {
            return true;
        }

        $completedCourses = $student->completedEnrollments()
            ->with('class.course')
            ->get()
            ->pluck('class.course.id')
            ->toArray();

        foreach ($course->prerequisites as $prerequisiteId) {
            if (!in_array($prerequisiteId, $completedCourses)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate course priority for recommendations
     */
    private function calculateCoursePriority(Course $course, Student $student)
    {
        $priority = 0;

        // Core courses have higher priority
        if ($course->course_type === 'core') {
            $priority += 10;
        }

        // Courses with fewer prerequisites have higher priority
        $prerequisiteCount = count($course->prerequisites ?? []);
        $priority += (5 - $prerequisiteCount);

        // Courses that are prerequisites for other courses have higher priority
        $isPrerequisite = Course::where('department_id', $student->department_id)
            ->whereJsonContains('prerequisites', $course->id)
            ->exists();

        if ($isPrerequisite) {
            $priority += 5;
        }

        return $priority;
    }
}
