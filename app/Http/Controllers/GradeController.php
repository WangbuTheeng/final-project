<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Student;
use App\Models\Exam;
use App\Models\ClassSection;
use App\Models\Subject;
use App\Models\AcademicYear;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class GradeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of grades
     */
    public function index(Request $request)
    {
        // Check if user has Super Admin, Admin, or Teacher role
        if (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('Teacher')) {
            abort(403, 'Unauthorized access to Grades.');
        }

        $query = Grade::with(['student.user', 'exam', 'subject', 'academicYear', 'grader']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student.user', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            })->orWhereHas('student', function($q) use ($search) {
                $q->where('admission_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('grade_type')) {
            $query->where('grade_type', $request->grade_type);
        }

        if ($request->filled('letter_grade')) {
            $query->where('letter_grade', $request->letter_grade);
        }

        $grades = $query->orderBy('graded_at', 'desc')->paginate(20);

        // Get filter options
        $academicYears = AcademicYear::active()->orderBy('name')->get();
        $gradeTypes = ['ca', 'exam', 'final'];
        $letterGrades = ['A', 'B', 'C', 'D', 'E', 'F'];

        // Statistics
        $totalGrades = Grade::count();
        $averageScore = Grade::avg('score');
        $passRate = Grade::passing()->count() / max($totalGrades, 1) * 100;
        $recentGrades = Grade::where('graded_at', '>=', now()->subDays(7))->count();

        return view('grades.index', compact(
            'grades', 'academicYears', 'gradeTypes', 'letterGrades',
            'totalGrades', 'averageScore', 'passRate', 'recentGrades'
        ));
    }

    /**
     * Show grade entry form for a specific class/subject
     */
    public function create(Request $request)
    {
        // Check if user has Super Admin or Admin role
        if (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('Admin')) {
            abort(403, 'Unauthorized access to create Grades.');
        }

        $academicYears = AcademicYear::active()->orderBy('name')->get();
        $classes = ClassSection::with(['course.faculty', 'academicYear'])
            ->active()
            ->orderBy('name')
            ->get();

        $selectedClass = $request->filled('class_id') ? 
            ClassSection::find($request->class_id) : null;

        $subjects = $selectedClass ? 
            Subject::where('class_id', $selectedClass->id)->active()->get() : collect();

        return view('grades.create', compact(
            'academicYears', 'classes', 'subjects', 'selectedClass'
        ));
    }

    /**
     * Store a newly created grade in storage.
     */
    public function store(Request $request)
    {
        // Check if user has Super Admin or Admin role
        if (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('Admin')) {
            abort(403, 'Unauthorized access to store Grades.');
        }

        $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'exam_id' => ['nullable', 'exists:exams,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'semester' => ['nullable', 'integer', 'min:1', 'max:8'],
            'year' => ['nullable', 'integer', 'min:1', 'max:4'],
            'grade_type' => ['required', 'in:ca,exam,final'],
            'theory_score' => ['nullable', 'numeric', 'min:0'],
            'practical_score' => ['nullable', 'numeric', 'min:0'],
            'score' => ['required', 'numeric', 'min:0'],
            'max_score' => ['required', 'numeric', 'min:1'],
            'remarks' => ['nullable', 'string', 'max:500'],
        ]);

        // Validate that score doesn't exceed max_score
        if ($request->score > $request->max_score) {
            return redirect()->back()
                ->with('error', 'Score cannot exceed maximum score.')
                ->withInput();
        }

        // Get enrollment for the student
        $enrollment = Enrollment::where('student_id', $request->student_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('status', 'enrolled')
            ->first();

        if (!$enrollment) {
            return redirect()->back()
                ->with('error', 'Student is not enrolled for the selected academic year.')
                ->withInput();
        }

        try {
            // Calculate total score if theory and practical are provided
            $totalScore = $request->score;
            if ($request->theory_score !== null && $request->practical_score !== null) {
                $totalScore = $request->theory_score + $request->practical_score;
            }

            // Create the grade
            Grade::create([
                'student_id' => $request->student_id,
                'enrollment_id' => $enrollment->id,
                'subject_id' => $request->subject_id,
                'exam_id' => $request->exam_id,
                'academic_year_id' => $request->academic_year_id,
                'semester' => $request->semester,
                'year' => $request->year,
                'grade_type' => $request->grade_type,
                'theory_score' => $request->theory_score,
                'practical_score' => $request->practical_score,
                'score' => $totalScore,
                'max_score' => $request->max_score,
                'remarks' => $request->remarks,
                'graded_by' => Auth::id(),
                'graded_at' => now(),
            ]);

            return redirect()->route('grades.index')
                ->with('success', 'Grade created successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating grade: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show bulk grade entry form
     */
    public function bulkEntry(Request $request)
    {
        // Check if user has Super Admin or Admin role
        if (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('Admin')) {
            abort(403, 'Unauthorized access to bulk Grade entry.');
        }

        // Get filter options for the form
        $academicYears = AcademicYear::active()->orderBy('name')->get();
        $classes = ClassSection::with(['course.faculty', 'academicYear'])
            ->active()
            ->orderBy('name')
            ->get();

        $selectedClass = $request->filled('class_id') ?
            ClassSection::find($request->class_id) : null;

        $subjects = $selectedClass ?
            Subject::where('class_id', $selectedClass->id)->active()->get() : collect();

        // If no filters are provided, show the filter form
        if (!$request->filled(['class_id', 'academic_year_id', 'grade_type'])) {
            return view('grades.bulk-entry', compact(
                'academicYears', 'classes', 'subjects'
            ));
        }

        // Validate the request
        $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'semester' => ['nullable', 'integer', 'min:1', 'max:8'],
            'year' => ['nullable', 'integer', 'min:1', 'max:4'],
            'grade_type' => ['required', 'in:ca,exam,final'],
        ]);

        $class = ClassSection::with(['course', 'academicYear'])->find($request->class_id);
        $subject = $request->subject_id ? Subject::find($request->subject_id) : null;

        // Get enrolled students
        $enrollments = Enrollment::with(['student.user'])
            ->where('class_id', $request->class_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('status', 'enrolled');

        if ($request->semester) {
            $enrollments->whereHas('class', function($q) use ($request) {
                $q->where('semester', $request->semester);
            });
        }

        if ($request->year) {
            $enrollments->where('year', $request->year);
        }

        $enrollments = $enrollments->orderBy('student_id')->get();

        // Get existing grades
        $existingGrades = Grade::where('academic_year_id', $request->academic_year_id)
            ->where('grade_type', $request->grade_type)
            ->when($request->subject_id, function($q) use ($request) {
                return $q->where('subject_id', $request->subject_id);
            })
            ->when($request->semester, function($q) use ($request) {
                return $q->where('semester', $request->semester);
            })
            ->when($request->year, function($q) use ($request) {
                return $q->where('year', $request->year);
            })
            ->get()
            ->keyBy('student_id');

        return view('grades.bulk-entry', compact(
            'academicYears', 'classes', 'subjects', 'class', 'subject',
            'enrollments', 'existingGrades', 'request'
        ));
    }

    /**
     * Store bulk grades
     */
    public function storeBulk(Request $request)
    {
        // Check if user has Super Admin or Admin role
        if (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('Admin')) {
            abort(403, 'Unauthorized access to store bulk Grades.');
        }

        $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'semester' => ['nullable', 'integer', 'min:1', 'max:8'],
            'year' => ['nullable', 'integer', 'min:1', 'max:4'],
            'grade_type' => ['required', 'in:ca,exam,final'],
            'grades' => ['required', 'array'],
            'grades.*.student_id' => ['required', 'exists:students,id'],
            'grades.*.enrollment_id' => ['required', 'exists:enrollments,id'],
            'grades.*.theory_score' => ['nullable', 'numeric', 'min:0'],
            'grades.*.practical_score' => ['nullable', 'numeric', 'min:0'],
            'grades.*.score' => ['nullable', 'numeric', 'min:0'],
            'grades.*.remarks' => ['nullable', 'string', 'max:500'],
        ]);

        $subject = $request->subject_id ? Subject::find($request->subject_id) : null;

        try {
            DB::transaction(function () use ($request, $subject) {
                foreach ($request->grades as $gradeData) {
                    $studentId = $gradeData['student_id'];
                    $enrollmentId = $gradeData['enrollment_id'];

                    // Calculate scores
                    $theoryScore = $gradeData['theory_score'] ?? null;
                    $practicalScore = $gradeData['practical_score'] ?? null;
                    $totalScore = $gradeData['score'] ?? null;

                    // If subject has theory/practical components, calculate total
                    if ($subject && $subject->hasTheoryComponent() && $subject->hasPracticalComponent()) {
                        $totalScore = ($theoryScore ?? 0) + ($practicalScore ?? 0);
                    } elseif ($subject && $subject->hasTheoryComponent() && !$subject->hasPracticalComponent()) {
                        $totalScore = $theoryScore;
                    } elseif ($subject && $subject->hasPracticalComponent() && !$subject->hasTheoryComponent()) {
                        $totalScore = $practicalScore;
                    }

                    // Skip if no scores entered
                    if (is_null($totalScore) || $totalScore <= 0) {
                        continue;
                    }

                    // Validate scores don't exceed maximum
                    if ($subject) {
                        if ($subject->hasTheoryComponent() && $theoryScore > $subject->full_marks_theory) {
                            throw new \Exception("Theory score for student {$studentId} exceeds maximum marks");
                        }
                        if ($subject->hasPracticalComponent() && $practicalScore > $subject->full_marks_practical) {
                            throw new \Exception("Practical score for student {$studentId} exceeds maximum marks");
                        }
                    }

                    // Determine max score
                    $maxScore = $subject ? $subject->total_full_marks : 100;

                    if ($totalScore > $maxScore) {
                        throw new \Exception("Total score for student {$studentId} exceeds maximum marks");
                    }

                    // Create or update grade
                    Grade::updateOrCreate(
                        [
                            'student_id' => $studentId,
                            'enrollment_id' => $enrollmentId,
                            'subject_id' => $request->subject_id,
                            'academic_year_id' => $request->academic_year_id,
                            'semester' => $request->semester,
                            'year' => $request->year,
                            'grade_type' => $request->grade_type,
                        ],
                        [
                            'theory_score' => $theoryScore,
                            'practical_score' => $practicalScore,
                            'score' => $totalScore,
                            'max_score' => $maxScore,
                            'remarks' => $gradeData['remarks'] ?? null,
                            'graded_by' => Auth::id(),
                            'graded_at' => now(),
                        ]
                    );
                }
            });

            return redirect()->route('grades.bulk-entry', $request->only([
                'class_id', 'academic_year_id', 'subject_id', 'semester', 'year', 'grade_type'
            ]))->with('success', 'Grades saved successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error saving grades: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show student grade report
     */
    public function studentReport(Student $student)
    {
        // Check if user has Super Admin, Admin, or Teacher role
        if (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('Teacher')) {
            abort(403, 'Unauthorized access to view Grade reports.');
        }

        $student->load(['user', 'department', 'academicYear']);

        $grades = Grade::with(['exam', 'subject', 'academicYear'])
            ->where('student_id', $student->id)
            ->orderBy('academic_year_id')
            ->orderBy('semester')
            ->orderBy('year')
            ->get()
            ->groupBy(['academic_year_id', function($grade) {
                return $grade->semester ?? $grade->year;
            }]);

        // Calculate statistics
        $totalGrades = Grade::where('student_id', $student->id)->count();
        $averageScore = Grade::where('student_id', $student->id)->avg('score');
        $cgpa = $student->cgpa;
        $passRate = Grade::where('student_id', $student->id)->passing()->count() / max($totalGrades, 1) * 100;

        return view('grades.student-report', compact(
            'student', 'grades', 'totalGrades', 'averageScore', 'cgpa', 'passRate'
        ));
    }

    /**
     * Get subjects for a class (AJAX)
     */
    public function getSubjects(Request $request)
    {
        $classId = $request->input('class_id');
        
        if (!$classId) {
            return response()->json([]);
        }

        $subjects = Subject::where('class_id', $classId)
            ->where('is_active', true)
            ->orderBy('order_sequence')
            ->get(['id', 'name', 'code']);

        return response()->json($subjects);
    }

    /**
     * Display the specified grade.
     */
    public function show(Grade $grade)
    {
        // Check if user has Super Admin, Admin, or Teacher role
        if (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('Teacher')) {
            abort(403, 'Unauthorized access to view Grade details.');
        }

        $grade->load(['student.user', 'exam', 'subject', 'academicYear', 'grader']);

        return view('grades.show', compact('grade'));
    }

    /**
     * Test method to check if controller is working
     */
    public function test()
    {
        return response()->json([
            'message' => 'GradeController is working!',
            'timestamp' => now(),
            'user' => auth()->user()->name ?? 'Guest'
        ]);
    }
}
