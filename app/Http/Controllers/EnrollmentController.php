<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Student;
use App\Models\ClassSection;
use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
        $this->authorize('view-enrollments');

        $currentAcademicYear = AcademicYear::current();
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $departments = Department::active()->get();

        // Get filters
        $selectedAcademicYear = $request->get('academic_year_id', $currentAcademicYear->id);
        $selectedSemester = $request->get('semester', 'first');
        $selectedDepartment = $request->get('department_id');
        $selectedStatus = $request->get('status');

        // Build query
        $enrollments = Enrollment::with(['student.user', 'student.department', 'class.course', 'academicYear'])
            ->where('academic_year_id', $selectedAcademicYear)
            ->where('semester', $selectedSemester);

        if ($selectedDepartment) {
            $enrollments->whereHas('student', function ($query) use ($selectedDepartment) {
                $query->where('department_id', $selectedDepartment);
            });
        }

        if ($selectedStatus) {
            $enrollments->where('status', $selectedStatus);
        }

        $enrollments = $enrollments->paginate(20);

        // Get statistics
        $stats = $this->getEnrollmentStats($selectedAcademicYear, $selectedSemester, $selectedDepartment);

        return view('enrollments.index', compact(
            'enrollments',
            'academicYears',
            'departments',
            'currentAcademicYear',
            'selectedAcademicYear',
            'selectedSemester',
            'selectedDepartment',
            'selectedStatus',
            'stats'
        ));
    }

    /**
     * Show student enrollment form
     */
    public function create(Request $request)
    {
        $this->authorize('create-enrollments');

        $currentAcademicYear = AcademicYear::current();
        $academicYears = AcademicYear::active()->get();
        $departments = Department::active()->get();

        $selectedAcademicYear = $request->get('academic_year_id', $currentAcademicYear->id);
        $selectedSemester = $request->get('semester', 'first');
        $selectedDepartment = $request->get('department_id');
        $selectedLevel = $request->get('level');

        // Get students based on filters
        $students = collect();
        if ($selectedDepartment && $selectedLevel) {
            $students = Student::with('user')
                ->active()
                ->where('department_id', $selectedDepartment)
                ->where('current_level', $selectedLevel)
                ->get();
        }

        // Get available classes for enrollment
        $availableClasses = collect();
        if ($selectedDepartment && $selectedLevel) {
            $availableClasses = ClassSection::with(['course', 'instructor.user'])
                ->whereHas('course', function ($query) use ($selectedDepartment, $selectedLevel) {
                    $query->where('department_id', $selectedDepartment)
                          ->where('level', $selectedLevel)
                          ->where('is_active', true);
                })
                ->where('academic_year_id', $selectedAcademicYear)
                ->where('semester', $selectedSemester)
                ->where('status', 'active')
                ->get();
        }

        return view('enrollments.create', compact(
            'academicYears',
            'departments',
            'students',
            'availableClasses',
            'selectedAcademicYear',
            'selectedSemester',
            'selectedDepartment',
            'selectedLevel'
        ));
    }

    /**
     * Store new enrollment
     */
    public function store(Request $request)
    {
        $this->authorize('create-enrollments');

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester' => 'required|in:first,second',
            'enrollment_date' => 'required|date'
        ]);

        try {
            DB::beginTransaction();

            $student = Student::findOrFail($request->student_id);
            $class = ClassSection::findOrFail($request->class_id);

            // Check if student can enroll in this course
            if (!$student->canEnrollInCourse($class->course, $request->academic_year_id, $request->semester)) {
                return back()->with('error', 'Student cannot enroll in this course. Check prerequisites and existing enrollments.');
            }

            // Check class capacity
            if ($class->current_enrollment >= $class->capacity) {
                return back()->with('error', 'Class is at full capacity.');
            }

            // Create enrollment
            $enrollment = Enrollment::create([
                'student_id' => $request->student_id,
                'class_id' => $request->class_id,
                'academic_year_id' => $request->academic_year_id,
                'semester' => $request->semester,
                'enrollment_date' => $request->enrollment_date,
                'status' => 'enrolled'
            ]);

            DB::commit();

            return redirect()->route('enrollments.index')
                ->with('success', 'Student enrolled successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
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
        $this->authorize('create-enrollments');

        $currentAcademicYear = AcademicYear::current();
        $academicYears = AcademicYear::active()->get();
        $departments = Department::active()->get();

        return view('enrollments.bulk-create', compact('academicYears', 'departments', 'currentAcademicYear'));
    }

    /**
     * Process bulk enrollment
     */
    public function bulkStore(Request $request)
    {
        $this->authorize('create-enrollments');

        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester' => 'required|in:first,second',
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
                        ->where('semester', $request->semester)
                        ->where('status', 'active')
                        ->first();

                    if (!$class) {
                        $errors[] = "No active class found for course {$course->code}";
                        continue;
                    }

                    // Check if student can enroll
                    if (!$student->canEnrollInCourse($course, $request->academic_year_id, $request->semester)) {
                        $errors[] = "Student {$student->matric_number} cannot enroll in {$course->code}";
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
                        'semester' => $request->semester,
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
        $this->authorize('edit-enrollments');

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
    private function getEnrollmentStats($academicYearId, $semester, $departmentId = null)
    {
        $query = Enrollment::where('academic_year_id', $academicYearId)
            ->where('semester', $semester);

        if ($departmentId) {
            $query->whereHas('student', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        return [
            'total' => $query->count(),
            'enrolled' => $query->where('status', 'enrolled')->count(),
            'completed' => $query->where('status', 'completed')->count(),
            'failed' => $query->where('status', 'failed')->count(),
            'dropped' => $query->where('status', 'dropped')->count(),
        ];
    }
}
