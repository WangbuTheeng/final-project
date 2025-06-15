<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Student;
use App\Models\ClassSection;
use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Import Log facade

class EnrollmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display enrollment dashboard
     */
    public function index(Request $request)
    {
        $this->authorize('manage-enrollments');

        $currentAcademicYear = AcademicYear::current();
        // Show only active academic years
        $academicYears = AcademicYear::active()->orderBy('name', 'desc')->get();
        $departments = Department::active()->get();
        $faculties = Faculty::active()->get();

        // Get filters
        $selectedAcademicYear = $request->get('academic_year_id', $currentAcademicYear ? $currentAcademicYear->id : null);
        $selectedDepartment = $request->get('department_id');
        $selectedFaculty = $request->get('faculty_id');
        $selectedCourse = $request->get('course_id');
        $selectedClass = $request->get('class_id');
        $selectedStatus = $request->get('status');

        // Get available courses based on selected filters
        $availableCourses = collect();
        if ($selectedAcademicYear && $selectedFaculty) {
            $courseQuery = Course::with('faculty')
                ->where('is_active', true)
                ->where('faculty_id', $selectedFaculty)
                ->whereHas('classes', function ($query) use ($selectedAcademicYear) {
                    $query->where('academic_year_id', $selectedAcademicYear)
                          ->where('status', 'active');
                });

            $availableCourses = $courseQuery->orderBy('code')->get();
        }

        // Get available classes based on selected filters
        $availableClasses = collect();
        if ($selectedCourse && $selectedAcademicYear) {
            $availableClasses = ClassSection::with(['course', 'instructor'])
                ->where('course_id', $selectedCourse)
                ->where('academic_year_id', $selectedAcademicYear)
                ->where('status', 'active')
                ->orderBy('name')
                ->get();
        }

        // Build enrollment query
        $enrollments = Enrollment::with(['student.user', 'student.department', 'student.faculty', 'class.course.faculty', 'academicYear'])
            ->where('academic_year_id', $selectedAcademicYear);

        if ($selectedDepartment) {
            $enrollments->whereHas('student', function ($query) use ($selectedDepartment) {
                $query->where('department_id', $selectedDepartment);
            });
        }

        if ($selectedFaculty) {
            $enrollments->whereHas('class.course', function ($query) use ($selectedFaculty) {
                $query->where('faculty_id', $selectedFaculty);
            });
        }

        if ($selectedCourse) {
            $enrollments->whereHas('class', function ($query) use ($selectedCourse) {
                $query->where('course_id', $selectedCourse);
            });
        }

        if ($selectedClass) {
            $enrollments->where('class_id', $selectedClass);
        }

        if ($selectedStatus) {
            $enrollments->where('status', $selectedStatus);
        }

        $enrollments = $enrollments->paginate(20);

        // Get statistics
        $stats = $this->getEnrollmentStats($selectedAcademicYear, $selectedDepartment, $selectedFaculty, $selectedCourse, $selectedClass);

        return view('enrollments.index', compact(
            'enrollments',
            'academicYears',
            'departments',
            'faculties',
            'availableCourses',
            'availableClasses',
            'currentAcademicYear',
            'selectedAcademicYear',
            'selectedDepartment',
            'selectedFaculty',
            'selectedCourse',
            'selectedClass',
            'selectedStatus',
            'stats'
        ));
    }

    /**
     * Show student enrollment form
     */
    public function create(Request $request)
    {
        $this->authorize('manage-enrollments');

        $currentAcademicYear = AcademicYear::current();
        $academicYears = AcademicYear::where('is_active', true)->get();
        $faculties = Faculty::active()->get();

        $selectedAcademicYear = $request->get('academic_year_id', $currentAcademicYear ? $currentAcademicYear->id : null);
        $selectedFaculty = $request->get('faculty_id');
        $selectedCourse = $request->get('course_id');
        $selectedClass = $request->get('class_id');

        // Get students based on filters - we'll get all students for now
        // and filter by department in the frontend based on selected course
        $students = collect();
        if ($selectedClass) {
            $class = ClassSection::with('course.department')->find($selectedClass);
            if ($class && $class->course && $class->course->department) {
                $students = Student::with('user')
                    ->active()
                    ->where('department_id', $class->course->department_id)
                    ->whereDoesntHave('enrollments', function ($query) use ($selectedAcademicYear, $selectedClass) {
                        $query->where('academic_year_id', $selectedAcademicYear)
                              ->where('class_id', $selectedClass)
                              ->where('status', 'enrolled');
                    })
                    ->get();
            }
        }

        // Get available courses for selected faculty
        $availableCourses = collect();
        if ($selectedFaculty && $selectedAcademicYear) {
            $availableCourses = Course::with('department')
                ->where('faculty_id', $selectedFaculty)
                ->where('is_active', true)
                ->whereHas('classes', function ($query) use ($selectedAcademicYear) {
                    $query->where('academic_year_id', $selectedAcademicYear)
                          ->where('status', 'active');
                })
                ->orderBy('code')
                ->get();
        }

        // Get available classes for selected course
        $availableClasses = collect();
        if ($selectedCourse && $selectedAcademicYear) {
            $availableClasses = ClassSection::with(['course', 'instructor'])
                ->where('course_id', $selectedCourse)
                ->where('academic_year_id', $selectedAcademicYear)
                ->where('status', 'active')
                ->orderBy('name')
                ->get();
        }

        return view('enrollments.create', compact(
            'academicYears',
            'faculties',
            'students',
            'availableCourses',
            'availableClasses',
            'selectedAcademicYear',
            'selectedFaculty',
            'selectedCourse',
            'selectedClass'
        ));
    }

    /**
     * Store new enrollment
     */
    public function store(Request $request)
    {
        $this->authorize('manage-enrollments');

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'enrollment_date' => 'required|date'
        ]);

        try {
            DB::beginTransaction();

            $student = Student::findOrFail($request->student_id);
            // Eager load the 'course' relationship for the ClassSection
            $class = ClassSection::with('course')->findOrFail($request->class_id);

            // Check if student can enroll in this course
            list($canEnroll, $reasons) = $student->canEnrollInCourse($class, $request->academic_year_id, $class->semester);

            if (!$canEnroll) {
                $errorMessage = 'Student cannot enroll in this course.';
                if (!empty($reasons)) {
                    $errorMessage .= ' Reasons: ' . implode(' ', $reasons);
                }
                return redirect()->route('enrollments.create', ['error_message' => $errorMessage]);
            }

            // Check class capacity
            if ($class->current_enrollment >= $class->capacity) {
                return redirect()->route('enrollments.create', ['error_message' => 'Class is at full capacity. Please select another class or contact administration.']);
            }

            // Create enrollment
            $enrollment = Enrollment::create([
                'student_id' => $request->student_id,
                'class_id' => $request->class_id,
                'academic_year_id' => $request->academic_year_id,
                'semester' => $class->semester,
                'enrollment_date' => $request->enrollment_date,
                'status' => 'enrolled'
            ]);

            DB::commit();

            Log::debug('Enrollment created successfully, redirecting to index.'); // Debug log

            return redirect()->route('enrollments.index')
                ->with('success', 'Student enrolled successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error enrolling student: ' . $e->getMessage()); // Log the error
            return back()->with('error', 'Error enrolling student: ' . $e->getMessage());
        }
    }

    /**
     * Show enrollment details
     */
    public function show(Enrollment $enrollment)
    {
        $this->authorize('view-enrollments');

        $enrollment->load(['student.user', 'student.department', 'class.course', 'class.instructor.user', 'academicYear']);

        return view('enrollments.show', compact('enrollment'));
    }

    /**
     * Show bulk enrollment form
     */
    public function bulkCreate()
    {
        $this->authorize('manage-enrollments');

        $currentAcademicYear = AcademicYear::current();
        $academicYears = AcademicYear::where('is_active', true)->get();
        $departments = Department::active()->get();

        return view('enrollments.bulk-create', compact('academicYears', 'departments', 'currentAcademicYear'));
    }

    /**
     * Process bulk enrollment
     */
    public function bulkStore(Request $request)
    {
        $this->authorize('manage-enrollments');

        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'department_id' => 'required|exists:departments,id',
            'level' => 'required|in:100,200,300,400,500',
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id',
            'enrollment_date' => 'required|date'
        ]);

        try {
            DB::beginTransaction();

            // Get students for the department and level
            $students = Student::active()
                ->where('department_id', $request->department_id)
                ->where('current_level', $request->level)
                ->get();

            if ($students->isEmpty()) {
                return back()->with('error', 'No active students found for the selected department and level.');
            }

            $enrollmentCount = 0;
            $errors = [];

            foreach ($students as $student) {
                foreach ($request->course_ids as $courseId) {
                    $course = Course::find($courseId);
                    
                    // Find available class for this course
                    $class = ClassSection::where('course_id', $courseId)
                        ->where('academic_year_id', $request->academic_year_id)
                        ->where('status', 'active')
                        ->first();

                    if (!$class) {
                        $errors[] = "No active class found for course {$course->code}";
                        continue;
                    }

                    // Check if student can enroll
                    if (!$student->canEnrollInCourse($course, $request->academic_year_id, $class->semester)) {
                        $errors[] = "Student {$student->admission_number} cannot enroll in {$course->code}";
                        continue;
                    }

                    // Check class capacity
                    if ($class->current_enrollment >= $class->capacity) {
                        $errors[] = "Class for {$course->code} is at full capacity";
                        continue;
                    }

                    // Create enrollment
                    Enrollment::create([
                        'student_id' => $student->id,
                        'class_id' => $class->id,
                        'academic_year_id' => $request->academic_year_id,
                        'semester' => $class->semester,
                        'enrollment_date' => $request->enrollment_date,
                        'status' => 'enrolled'
                    ]);

                    $enrollmentCount++;
                }
            }

            DB::commit();

            $message = "Successfully enrolled {$enrollmentCount} student-course combinations.";
            if (!empty($errors)) {
                $message .= " Errors: " . implode(', ', array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $message .= " and " . (count($errors) - 5) . " more.";
                }
            }

            return redirect()->route('enrollments.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error processing bulk enrollment: ' . $e->getMessage());
        }
    }

    /**
     * Drop an enrollment
     */
    public function drop(Request $request, Enrollment $enrollment)
    {
        $this->authorize('manage-enrollments');

        $request->validate([
            'drop_reason' => 'required|string|max:500'
        ]);

        try {
            $enrollment->drop($request->drop_reason);

            return back()->with('success', 'Enrollment dropped successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Get enrollment statistics
     */
    private function getEnrollmentStats($academicYearId, $departmentId = null, $facultyId = null, $courseId = null, $classId = null)
    {
        $query = Enrollment::where('academic_year_id', $academicYearId);

        if ($departmentId) {
            $query->whereHas('student', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        if ($facultyId) {
            $query->whereHas('class.course', function ($q) use ($facultyId) {
                $q->where('faculty_id', $facultyId);
            });
        }

        if ($courseId) {
            $query->whereHas('class', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }

        if ($classId) {
            $query->where('class_id', $classId);
        }

        return [
            'total' => $query->count(),
            'enrolled' => (clone $query)->where('status', 'enrolled')->count(),
            'completed' => (clone $query)->where('status', 'completed')->count(),
            'failed' => (clone $query)->where('status', 'failed')->count(),
            'dropped' => (clone $query)->where('status', 'dropped')->count(),
        ];
    }
}
