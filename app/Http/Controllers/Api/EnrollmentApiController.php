<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Student;
use App\Models\ClassSection;
use App\Models\Faculty;
use App\Models\Exam;
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
            'department_id' => 'sometimes|exists:departments,id',
            'faculty_id' => 'sometimes|exists:faculties,id',
            'class_id' => 'sometimes|exists:classes,id',
            'academic_year_id' => 'sometimes|exists:academic_years,id',
            'level' => 'sometimes|in:100,200,300,400,500',
            'status' => 'sometimes|in:active,graduated,suspended,withdrawn,deferred'
        ]);

        $query = Student::with(['user', 'department', 'faculty']);

        // If class_id and academic_year_id are provided, get enrolled students
        if ($request->class_id && $request->academic_year_id) {
            $query->whereHas('enrollments', function ($q) use ($request) {
                $q->where('class_id', $request->class_id)
                  ->where('academic_year_id', $request->academic_year_id)
                  ->where('status', 'enrolled');
            });
        } else {
            // Filter by department if provided and not empty
            if ($request->department_id && $request->department_id !== '') {
                $query->where('department_id', $request->department_id);
            } elseif ($request->faculty_id && $request->faculty_id !== '') {
                // If no department but faculty is provided, filter by faculty
                $query->where('faculty_id', $request->faculty_id);
            }

            if ($request->level) {
                $query->where('current_level', $request->level);
            }
        }

        if ($request->status) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'active'); // Default to active students
        }

        $students = $query->orderBy('admission_number')->get();

        return response()->json([
            'students' => $students->map(function ($student) {
                return [
                    'id' => $student->id,
                    'admission_number' => $student->admission_number,
                    'user' => [
                        'first_name' => $student->user->first_name,
                        'last_name' => $student->user->last_name,
                        'full_name' => $student->user->full_name,
                        'email' => $student->user->email
                    ],
                    'department' => $student->department ? [
                        'id' => $student->department->id,
                        'name' => $student->department->name
                    ] : null,
                    'faculty' => $student->faculty ? [
                        'id' => $student->faculty->id,
                        'name' => $student->faculty->name
                    ] : null,
                    'current_level' => $student->current_level
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

    /**
     * Get faculties for selected academic year
     */
    public function getFaculties(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id'
        ]);

        $faculties = \App\Models\Faculty::active()
            ->whereHas('courses', function ($query) use ($request) {
                $query->where('is_active', true)
                      ->whereHas('classes', function ($classQuery) use ($request) {
                          $classQuery->where('academic_year_id', $request->academic_year_id)
                                    ->where('status', 'active');
                      });
            })
            ->orderBy('name')
            ->get();

        return response()->json([
            'faculties' => $faculties->map(function ($faculty) {
                return [
                    'id' => $faculty->id,
                    'name' => $faculty->name,
                    'code' => $faculty->code
                ];
            })
        ]);
    }

    /**
     * Get courses for selected faculty and academic year
     */
    public function getCoursesByFaculty(Request $request)
    {
        $request->validate([
            'faculty_id' => 'required|exists:faculties,id',
            'academic_year_id' => 'required|exists:academic_years,id'
        ]);

        $courseQuery = \App\Models\Course::where('is_active', true)
            ->where('faculty_id', $request->faculty_id)
            ->whereHas('classes', function ($query) use ($request) {
                $query->where('academic_year_id', $request->academic_year_id)
                      ->where('status', 'active');
            });

        $courses = $courseQuery->with(['classes' => function ($query) use ($request) {
                $query->where('academic_year_id', $request->academic_year_id)
                      ->where('status', 'active');
            }, 'department', 'faculty'])
            ->orderBy('code')
            ->get();

        return response()->json([
            'courses' => $courses->map(function ($course) {
                return [
                    'id' => $course->id,
                    'code' => $course->code,
                    'title' => $course->title,
                    'credit_units' => $course->credit_units,
                    'course_type' => $course->course_type,
                    'organization_type' => $course->organization_type,
                    'year' => $course->year,
                    'semester_period' => $course->semester_period,
                    'period_display' => $course->period_display,
                    'classes_count' => $course->classes->count(),
                    'department' => $course->department ? [
                        'id' => $course->department->id,
                        'name' => $course->department->name
                    ] : null
                ];
            })
        ]);
    }

    /**
     * Get classes for selected course
     */
    public function getClassesByCourse(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'academic_year_id' => 'required|exists:academic_years,id'
        ]);

        $classes = \App\Models\ClassSection::where('course_id', $request->course_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('status', 'active')
            ->with(['instructor', 'course'])
            ->orderBy('name')
            ->get();

        return response()->json([
            'classes' => $classes->map(function ($class) {
                return [
                    'id' => $class->id,
                    'name' => $class->name,
                    'capacity' => $class->capacity,
                    'current_enrollment' => $class->current_enrollment,
                    'available_slots' => $class->available_slots,
                    'enrollment_percentage' => $class->enrollment_percentage,
                    'instructor' => $class->instructor ? [
                        'id' => $class->instructor->id,
                        'name' => $class->instructor->full_name,
                        'email' => $class->instructor->email
                    ] : null,
                    'room' => $class->room,
                    'schedule' => $class->schedule,
                    'display_name' => $class->display_name
                ];
            })
        ]);
    }

    /**
     * Get departments by faculty (AJAX endpoint)
     */
    public function getDepartmentsByFaculty($facultyId)
    {
        $departments = \App\Models\Department::where('faculty_id', $facultyId)
            ->where('is_active', true)
            ->select('id', 'name')
            ->get();

        return response()->json($departments);
    }

    /**
     * Get exams for a class (AJAX endpoint)
     */
    public function getExams(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id'
        ]);

        $exams = Exam::where('class_id', $request->class_id)
            ->where('status', '!=', 'cancelled')
            ->orderBy('exam_date', 'desc')
            ->get(['id', 'title', 'exam_type', 'exam_date']);

        return response()->json($exams->map(function ($exam) {
            return [
                'id' => $exam->id,
                'title' => $exam->title,
                'exam_type' => $exam->exam_type,
                'exam_date' => $exam->exam_date->format('M d, Y')
            ];
        }));
    }
}
