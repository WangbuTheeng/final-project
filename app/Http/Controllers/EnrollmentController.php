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
            $courseQuery = Course::with('department.faculty')
                ->where('is_active', true)
                ->byFaculty($selectedFaculty)
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
        $enrollments = Enrollment::with(['student.user', 'student.department', 'student.faculty', 'class.course.department.faculty', 'academicYear'])
            ->where('academic_year_id', $selectedAcademicYear);

        if ($selectedDepartment) {
            $enrollments->whereHas('student', function ($query) use ($selectedDepartment) {
                $query->where('department_id', $selectedDepartment);
            });
        }

        if ($selectedFaculty) {
            $enrollments->whereHas('class.course.department', function ($query) use ($selectedFaculty) {
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
                ->byFaculty($selectedFaculty)
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

        // Get the class and course to determine examination system
        $class = \App\Models\ClassSection::findOrFail($request->class_id);
        $course = $class->course;

        // Dynamic validation based on examination system
        $rules = [
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'enrollment_date' => 'required|date',
            'enrollment_type' => 'required|in:regular,late,makeup,readmission',
            'payment_status' => 'required|in:pending,paid,partial,waived'
        ];

        // Only require semester for semester system courses
        if ($course->examination_system === 'semester') {
            $rules['semester'] = 'required|in:first,second,summer';
        }

        $request->validate($rules);

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
        $faculties = Faculty::active()->get();

        return view('enrollments.bulk-create', compact('academicYears', 'faculties', 'currentAcademicYear'));
    }

    /**
     * Process bulk enrollment
     */
    public function bulkStore(Request $request)
    {
        $this->authorize('manage-enrollments');

        // Basic validation
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'faculty_id' => 'required|exists:faculties,id',
            'class_ids' => 'required|array',
            'class_ids.*' => 'exists:classes,id',
            'enrollment_date' => 'required|date',
            'enrollment_type' => 'required|in:regular,late,makeup,readmission',
            'payment_status' => 'required|in:pending,paid,partial,waived'
        ]);

        try {
            DB::beginTransaction();

            $enrollmentCount = 0;
            $errors = [];
            $enrolledStudents = [];

            foreach ($request->class_ids as $classId) {
                $class = ClassSection::with(['course', 'enrollments.student'])->findOrFail($classId);

                // Get students already enrolled in this class to avoid duplicates
                $alreadyEnrolledStudentIds = $class->enrollments()
                    ->where('academic_year_id', $request->academic_year_id)
                    ->where('status', 'enrolled')
                    ->pluck('student_id')
                    ->toArray();

                // Get all active students from the faculty (through departments or directly)
                $students = Student::active()
                    ->where('faculty_id', $request->faculty_id)
                    ->whereNotIn('id', $alreadyEnrolledStudentIds)
                    ->get();

                if ($students->isEmpty()) {
                    $errors[] = "No eligible students found for class {$class->name} ({$class->course->code})";
                    continue;
                }

                foreach ($students as $student) {
                    // Check class capacity
                    if ($class->enrolled_count >= $class->capacity) {
                        $errors[] = "Class {$class->name} ({$class->course->code}) is at full capacity";
                        break;
                    }

                    // Check if student can enroll (enhanced validation)
                    $course = $class->course;

                    $existingEnrollment = Enrollment::where('student_id', $student->id)
                        ->where('class_id', $classId)
                        ->where('academic_year_id', $request->academic_year_id)
                        ->where('status', 'enrolled')
                        ->exists();

                    if ($existingEnrollment) {
                        continue; // Skip if already enrolled
                    }

                    // Calculate fees based on Nepal system
                    $baseFee = 2000; // NPR 2,000 base enrollment fee
                    $creditFee = $course->credit_units * 150; // NPR 150 per credit
                    $lateFee = $request->enrollment_type === 'late' ? 500 : 0; // NPR 500 late fee
                    $totalFee = $baseFee + $creditFee + $lateFee;

                    // Create enrollment with Nepal-specific fields
                    $enrollmentData = [
                        'student_id' => $student->id,
                        'class_id' => $class->id,
                        'academic_year_id' => $request->academic_year_id,
                        'enrollment_date' => $request->enrollment_date,
                        'enrollment_type' => $request->enrollment_type,
                        'payment_status' => $request->payment_status,
                        'credit_hours' => $course->credit_units,
                        'fee_amount' => $totalFee,
                        'minimum_attendance_percentage' => 75.0, // Nepal standard 75%
                        'attendance_required' => true,
                        'prerequisites_met' => true, // Assume met for bulk enrollment
                        'status' => 'enrolled'
                    ];



                    Enrollment::create($enrollmentData);

                    $enrollmentCount++;
                    $enrolledStudents[] = $student->admission_number;
                }
            }

            DB::commit();

            $message = "Successfully enrolled {$enrollmentCount} students in " . count($request->class_ids) . " class(es).";
            if (!empty($errors)) {
                $message .= " Issues: " . implode(', ', array_slice($errors, 0, 3));
                if (count($errors) > 3) {
                    $message .= " and " . (count($errors) - 3) . " more.";
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
            $query->whereHas('class.course.department', function ($q) use ($facultyId) {
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
