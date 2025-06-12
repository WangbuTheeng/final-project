<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Student;
use App\Models\ClassSection;
use Illuminate\Http\Request;

class EnrollmentApiController extends Controller
{
    /**
     * Get courses for bulk enrollment
     */
    public function getCourses(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'level' => 'required|in:100,200,300,400,500',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester' => 'required|in:first,second'
        ]);

        $courses = Course::where('department_id', $request->department_id)
            ->where('level', $request->level)
            ->where('is_active', true)
            ->where(function ($query) use ($request) {
                $query->where('semester', $request->semester)
                      ->orWhere('semester', 'both');
            })
            ->whereHas('classes', function ($query) use ($request) {
                $query->where('academic_year_id', $request->academic_year_id)
                      ->where('semester', $request->semester)
                      ->where('status', 'active');
            })
            ->with(['classes' => function ($query) use ($request) {
                $query->where('academic_year_id', $request->academic_year_id)
                      ->where('semester', $request->semester)
                      ->where('status', 'active');
            }])
            ->get();

        return response()->json([
            'courses' => $courses->map(function ($course) {
                return [
                    'id' => $course->id,
                    'code' => $course->code,
                    'title' => $course->title,
                    'credit_units' => $course->credit_units,
                    'course_type' => $course->course_type,
                    'has_available_class' => $course->classes->where('current_enrollment', '<', $course->classes->first()->capacity ?? 0)->count() > 0
                ];
            })
        ]);
    }

    /**
     * Get students for bulk enrollment preview
     */
    public function getStudents(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'level' => 'required|in:100,200,300,400,500',
            'status' => 'sometimes|in:active,graduated,suspended,withdrawn,deferred'
        ]);

        $students = Student::with('user')
            ->where('department_id', $request->department_id)
            ->where('current_level', $request->level)
            ->when($request->status, function ($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->get();

        return response()->json([
            'students' => $students->map(function ($student) {
                return [
                    'id' => $student->id,
                    'matric_number' => $student->matric_number,
                    'user' => [
                        'first_name' => $student->user->first_name,
                        'last_name' => $student->user->last_name,
                        'full_name' => $student->user->full_name,
                        'email' => $student->user->email
                    ]
                ];
            })
        ]);
    }

    /**
     * Get available classes for a course
     */
    public function getAvailableClasses(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester' => 'required|in:first,second'
        ]);

        $classes = ClassSection::where('course_id', $request->course_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('semester', $request->semester)
            ->where('status', 'active')
            ->whereColumn('current_enrollment', '<', 'capacity')
            ->with(['instructor.user', 'course'])
            ->get();

        return response()->json([
            'classes' => $classes->map(function ($class) {
                return [
                    'id' => $class->id,
                    'name' => $class->name,
                    'capacity' => $class->capacity,
                    'current_enrollment' => $class->current_enrollment,
                    'available_slots' => $class->available_slots,
                    'instructor' => $class->instructor ? [
                        'name' => $class->instructor->full_name,
                        'email' => $class->instructor->email
                    ] : null,
                    'schedule' => $class->schedule
                ];
            })
        ]);
    }

    /**
     * Check enrollment eligibility
     */
    public function checkEligibility(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester' => 'required|in:first,second'
        ]);

        $student = Student::findOrFail($request->student_id);
        $course = Course::findOrFail($request->course_id);

        $canEnroll = $student->canEnrollInCourse($course, $request->academic_year_id, $request->semester);
        $reasons = [];

        if (!$canEnroll) {
            // Check specific reasons
            $existingEnrollment = $student->enrollments()
                ->whereHas('class', function ($query) use ($course) {
                    $query->where('course_id', $course->id);
                })
                ->where('academic_year_id', $request->academic_year_id)
                ->where('semester', $request->semester)
                ->where('status', 'enrolled')
                ->exists();

            if ($existingEnrollment) {
                $reasons[] = 'Student is already enrolled in this course for the selected semester';
            }

            if ($course->level != $student->current_level) {
                $reasons[] = 'Course level does not match student\'s current level';
            }

            if (!empty($course->prerequisites)) {
                $completedCourses = $student->completedEnrollments()
                    ->with('class.course')
                    ->get()
                    ->pluck('class.course.id')
                    ->toArray();

                $missingPrerequisites = [];
                foreach ($course->prerequisites as $prerequisiteId) {
                    if (!in_array($prerequisiteId, $completedCourses)) {
                        $prerequisiteCourse = Course::find($prerequisiteId);
                        if ($prerequisiteCourse) {
                            $missingPrerequisites[] = $prerequisiteCourse->code;
                        }
                    }
                }

                if (!empty($missingPrerequisites)) {
                    $reasons[] = 'Missing prerequisites: ' . implode(', ', $missingPrerequisites);
                }
            }
        }

        return response()->json([
            'eligible' => $canEnroll,
            'reasons' => $reasons
        ]);
    }

    /**
     * Get enrollment statistics
     */
    public function getEnrollmentStats(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester' => 'required|in:first,second',
            'department_id' => 'sometimes|exists:departments,id'
        ]);

        $query = \App\Models\Enrollment::where('academic_year_id', $request->academic_year_id)
            ->where('semester', $request->semester);

        if ($request->department_id) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        $stats = [
            'total' => $query->count(),
            'enrolled' => $query->where('status', 'enrolled')->count(),
            'completed' => $query->where('status', 'completed')->count(),
            'failed' => $query->where('status', 'failed')->count(),
            'dropped' => $query->where('status', 'dropped')->count(),
        ];

        // Get enrollment by level
        $enrollmentsByLevel = $query->with('student')
            ->get()
            ->groupBy('student.current_level')
            ->map->count();

        // Get enrollment by course type
        $enrollmentsByCourseType = $query->with('class.course')
            ->get()
            ->groupBy('class.course.course_type')
            ->map->count();

        return response()->json([
            'stats' => $stats,
            'by_level' => $enrollmentsByLevel,
            'by_course_type' => $enrollmentsByCourseType
        ]);
    }
}
