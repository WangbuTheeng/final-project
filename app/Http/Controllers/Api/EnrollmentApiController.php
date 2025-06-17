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
     * Get courses for bulk enrollment by faculty
     */
    public function getCourses(Request $request)
    {
        try {
            $request->validate([
                'faculty_id' => 'required|exists:faculties,id',
                'academic_year_id' => 'required|exists:academic_years,id'
            ]);

            \Log::info('Getting courses for faculty: ' . $request->faculty_id . ', academic year: ' . $request->academic_year_id);

            // Get courses for the faculty that have active classes
            $courses = Course::where('faculty_id', $request->faculty_id)
                ->where('is_active', true)
                ->whereHas('classes', function ($query) use ($request) {
                    $query->where('academic_year_id', $request->academic_year_id)
                          ->where('status', 'active');
                })
                ->with(['classes' => function ($query) use ($request) {
                    $query->where('academic_year_id', $request->academic_year_id)
                          ->where('status', 'active')
                          ->with(['enrollments' => function ($q) use ($request) {
                              $q->where('academic_year_id', $request->academic_year_id)
                                ->where('status', 'enrolled');
                          }]);
                }])
                ->get();

            \Log::info('Found ' . $courses->count() . ' courses');

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
                        'classes_count' => $course->classes->count(),
                        'total_capacity' => $course->classes->sum('capacity'),
                        'current_enrollment' => $course->classes->sum(function ($class) {
                            return $class->enrollments->count();
                        })
                    ];
                })
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getCourses: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get students for bulk enrollment preview
     */
    public function getStudents(Request $request)
    {
        try {
            $request->validate([
                'faculty_id' => 'required|exists:faculties,id',
                'academic_year_id' => 'sometimes|exists:academic_years,id',
                'class_ids' => 'sometimes|array',
                'class_ids.*' => 'exists:classes,id'
            ]);

            \Log::info('Getting students for faculty: ' . $request->faculty_id);

            $query = Student::with(['user', 'department', 'faculty']);

            // Filter by faculty
            $query->where('faculty_id', $request->faculty_id);

            // If specific classes are selected, show students not enrolled in those classes
            if ($request->class_ids && $request->academic_year_id) {
                $query->whereDoesntHave('enrollments', function ($q) use ($request) {
                    $q->whereIn('class_id', $request->class_ids)
                      ->where('academic_year_id', $request->academic_year_id)
                      ->where('status', 'enrolled');
                });
            }

            // Only active students
            $query->where('status', 'active');

            $students = $query->orderBy('admission_number')->get();

            \Log::info('Found ' . $students->count() . ' students');

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
        } catch (\Exception $e) {
            \Log::error('Error in getStudents: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Create a new course
     */
    public function createCourse(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'code' => 'required|string|max:20|unique:courses,code',
                'faculty_id' => 'required|exists:faculties,id',
                'credit_units' => 'required|integer|min:1|max:6',
                'course_type' => 'required|in:core,elective,general',
                'organization_type' => 'required|in:yearly,semester',
                'year' => 'required_if:organization_type,yearly|integer|min:1|max:5',
                'semester_period' => 'required_if:organization_type,semester|integer|min:1|max:8',
                'description' => 'nullable|string',
                'is_active' => 'boolean'
            ]);

            $courseData = [
                'title' => $request->title,
                'code' => strtoupper($request->code),
                'faculty_id' => $request->faculty_id,
                'credit_units' => $request->credit_units,
                'course_type' => $request->course_type,
                'organization_type' => $request->organization_type,
                'description' => $request->description,
                'is_active' => $request->is_active ?? true
            ];

            if ($request->organization_type === 'yearly') {
                $courseData['year'] = $request->year;
            } else {
                $courseData['semester_period'] = $request->semester_period;
            }

            $course = Course::create($courseData);

            return response()->json([
                'success' => true,
                'message' => 'Course created successfully',
                'course' => [
                    'id' => $course->id,
                    'code' => $course->code,
                    'title' => $course->title,
                    'credit_units' => $course->credit_units,
                    'course_type' => $course->course_type,
                    'organization_type' => $course->organization_type,
                    'year' => $course->year,
                    'semester_period' => $course->semester_period
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error creating course: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error creating course: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new class
     */
    public function createClass(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'course_id' => 'required|exists:courses,id',
                'academic_year_id' => 'required|exists:academic_years,id',
                'capacity' => 'required|integer|min:1|max:200',
                'room' => 'nullable|string|max:100',
                'semester' => 'required|integer|min:1|max:8',
                'year' => 'required|integer|min:1|max:5',
                'schedule' => 'nullable|string',
                'status' => 'required|in:active,inactive'
            ]);

            $classData = [
                'name' => $request->name,
                'course_id' => $request->course_id,
                'academic_year_id' => $request->academic_year_id,
                'capacity' => $request->capacity,
                'room' => $request->room,
                'semester' => $request->semester,
                'year' => $request->year,
                'schedule' => $request->schedule,
                'status' => $request->status
            ];

            $class = ClassSection::create($classData);

            return response()->json([
                'success' => true,
                'message' => 'Class created successfully',
                'class' => [
                    'id' => $class->id,
                    'name' => $class->name,
                    'capacity' => $class->capacity,
                    'room' => $class->room,
                    'semester' => $class->semester,
                    'year' => $class->year,
                    'schedule' => $class->schedule,
                    'status' => $class->status
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error creating class: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error creating class: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get classes by selected courses for bulk enrollment
     */
    public function getClassesByCourses(Request $request)
    {
        $request->validate([
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id',
            'academic_year_id' => 'required|exists:academic_years,id'
        ]);

        $classes = ClassSection::whereIn('course_id', $request->course_ids)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('status', 'active')
            ->with(['course', 'instructor', 'enrollments' => function ($query) use ($request) {
                $query->where('academic_year_id', $request->academic_year_id)
                      ->where('status', 'enrolled');
            }])
            ->get();

        return response()->json([
            'classes' => $classes->map(function ($class) {
                return [
                    'id' => $class->id,
                    'name' => $class->name,
                    'course' => [
                        'id' => $class->course->id,
                        'code' => $class->course->code,
                        'title' => $class->course->title,
                        'credit_units' => $class->course->credit_units
                    ],
                    'capacity' => $class->capacity,
                    'current_enrollment' => $class->enrollments->count(),
                    'available_slots' => $class->capacity - $class->enrollments->count(),
                    'instructor' => $class->instructor ? [
                        'id' => $class->instructor->id,
                        'name' => $class->instructor->full_name,
                        'email' => $class->instructor->email
                    ] : null,
                    'room' => $class->room,
                    'schedule' => $class->schedule,
                    'semester' => $class->semester,
                    'year' => $class->year
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
