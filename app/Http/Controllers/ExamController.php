<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Course;
use App\Models\ClassSection;
use App\Models\Subject;
use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the exams.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('manage-exams');

        $query = Exam::with(['class.course', 'subject', 'academicYear', 'creator']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('class.course', function($courseQuery) use ($search) {
                      $courseQuery->where('title', 'like', "%{$search}%");
                  })
                  ->orWhereHas('subject', function($subjectQuery) use ($search) {
                      $subjectQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('exam_type')) {
            $query->where('exam_type', $request->exam_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        $exams = $query->orderBy('exam_date', 'desc')->paginate(15);

        // Get filter options
        $academicYears = AcademicYear::active()->orderBy('name')->get();
        $examTypes = array_keys(Exam::getExamTypes());
        $statuses = ['scheduled', 'ongoing', 'completed', 'cancelled'];

        // Statistics
        $totalExams = Exam::count();
        $upcomingExams = Exam::upcoming()->count();
        $ongoingExams = Exam::ongoing()->count();
        $completedExams = Exam::completed()->count();

        return view('exams.index', compact(
            'exams', 'academicYears', 'examTypes', 'statuses',
            'totalExams', 'upcomingExams', 'ongoingExams', 'completedExams'
        ));
    }

    /**
     * Show the form for creating a new exam.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->authorize('manage-exams');

        $academicYears = AcademicYear::active()->orderBy('name')->get();
        $classes = ClassSection::with(['course.faculty', 'academicYear'])
            ->active()
            ->orderBy('name')
            ->get();

        $examTypes = Exam::getExamTypes();

        // Pre-select class if provided
        $selectedClass = $request->filled('class_id') ?
            ClassSection::find($request->class_id) : null;

        return view('exams.create', compact(
            'academicYears', 'classes', 'examTypes', 'selectedClass'
        ));
    }

    /**
     * Store a newly created exam in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('manage-exams');

        // Get the selected class to determine if it's semester or year-based
        $class = ClassSection::with('course')->find($request->class_id);
        $isSemesterBased = ($class && $class->course->organization_type === 'semester');

        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'class_id' => ['required', 'exists:classes,id'],
            'course_id' => ['nullable', 'exists:courses,id'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'exam_type' => ['required', 'in:internal,board,practical,midterm,annual,quiz,test,final,assignment'],
            'exam_date' => ['required', 'date', 'after:now'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:480'],
            'total_marks' => ['nullable', 'numeric', 'min:1', 'max:1000'], // Made nullable for auto-calculation
            'theory_marks' => ['nullable', 'numeric', 'min:0'],
            'practical_marks' => ['nullable', 'numeric', 'min:0'],
            'pass_mark' => ['required', 'numeric', 'min:0'],
            'venue' => ['nullable', 'string', 'max:255'],
            'instructions' => ['nullable', 'string'],
            'is_multi_subject' => ['nullable', 'boolean'],
            'auto_load_subjects' => ['nullable', 'boolean'],
            'subjects' => ['nullable', 'array'], // For multi-subject exams
            'subjects.*' => ['exists:subjects,id'],
        ];

        // Add semester or year validation based on course type
        if ($isSemesterBased) {
            $rules['semester'] = ['required', 'integer', 'in:1,2,3,4,5,6,7,8'];
        } else {
            $rules['year'] = ['required', 'integer', 'in:1,2,3,4'];
        }

        $request->validate($rules);

        // Validate that theory + practical marks = total marks if both are provided
        if ($request->filled('theory_marks') && $request->filled('practical_marks')) {
            $totalCalculated = $request->theory_marks + $request->practical_marks;
            if (abs($totalCalculated - $request->total_marks) > 0.01) {
                return redirect()->back()
                    ->with('error', 'Theory marks + practical marks must equal total marks')
                    ->withInput();
            }
        }

        // Validate pass mark is not greater than total marks
        if ($request->pass_mark > $request->total_marks) {
            return redirect()->back()
                ->with('error', 'Pass mark cannot be greater than total marks')
                ->withInput();
        }

        try {
            $exam = null;

            DB::transaction(function () use ($request, $isSemesterBased, &$exam) {
                $exam = Exam::create([
                    'title' => $request->title,
                    'class_id' => $request->class_id,
                    'course_id' => $request->course_id,
                    'subject_id' => $request->subject_id,
                    'academic_year_id' => $request->academic_year_id,
                    'exam_type' => $request->exam_type,
                    'semester' => $isSemesterBased ? $request->semester : null,
                    'year' => $isSemesterBased ? null : $request->year,
                    'exam_date' => $request->exam_date,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'duration_minutes' => $request->duration_minutes,
                    'total_marks' => $request->total_marks,
                    'theory_marks' => $request->theory_marks,
                    'practical_marks' => $request->practical_marks,
                    'pass_mark' => $request->pass_mark,
                    'venue' => $request->venue,
                    'instructions' => $request->instructions,
                    'status' => 'scheduled',
                    'is_multi_subject' => $request->boolean('is_multi_subject'),
                    'auto_load_subjects' => $request->boolean('auto_load_subjects'),
                    'created_by' => Auth::id(),
                ]);

                // Auto-load all subjects for course if requested
                if ($request->boolean('auto_load_subjects')) {
                    $exam->autoLoadCourseSubjects();
                }
                // If this is a class-wide exam (no specific subject), attach all subjects from the class
                elseif (!$request->filled('subject_id')) {
                    $classSubjects = Subject::where('class_id', $request->class_id)
                        ->active()
                        ->get();

                    $totalTheoryMarks = 0;
                    $totalPracticalMarks = 0;

                    foreach ($classSubjects as $subject) {
                        $exam->examSubjects()->create([
                            'subject_id' => $subject->id,
                            'theory_marks' => $subject->full_marks_theory ?? 0,
                            'practical_marks' => $subject->full_marks_practical ?? 0,
                            'pass_marks_theory' => $subject->pass_marks_theory ?? 0,
                            'pass_marks_practical' => $subject->pass_marks_practical ?? 0,
                            'is_active' => true,
                        ]);

                        $totalTheoryMarks += $subject->full_marks_theory ?? 0;
                        $totalPracticalMarks += $subject->full_marks_practical ?? 0;
                    }

                    // Update exam total marks
                    $exam->theory_marks = $totalTheoryMarks;
                    $exam->practical_marks = $totalPracticalMarks;
                    $exam->total_marks = $totalTheoryMarks + $totalPracticalMarks;
                    $exam->is_multi_subject = true;
                    $exam->save();
                }

                // If subjects are provided, attach them to the exam
                if ($request->filled('subjects')) {
                    foreach ($request->subjects as $subjectId) {
                        $subject = Subject::find($subjectId);
                        if ($subject) {
                            $exam->examSubjects()->create([
                                'subject_id' => $subjectId,
                                'theory_marks' => $subject->full_marks_theory,
                                'practical_marks' => $subject->full_marks_practical,
                                'pass_marks_theory' => $subject->pass_marks_theory,
                                'pass_marks_practical' => $subject->pass_marks_practical,
                                'is_active' => true,
                            ]);
                        }
                    }
                }
            });

            return redirect()->route('exams.index')
                ->with('success', 'Exam created successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating exam: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified exam.
     *
     * @param  \App\Models\Exam  $exam
     * @return \Illuminate\Http\Response
     */
    public function show(Exam $exam)
    {
        // Check if user has permission to view exams
        $this->authorize('manage-exams');

        $exam->load(['class.course.faculty', 'subject', 'academicYear', 'creator', 'grades.student.user']);

        // Get exam statistics
        $stats = [
            'total_enrolled' => $exam->getEnrolledStudentsCount(),
            'total_graded' => $exam->getGradedStudentsCount(),
            'average_score' => $exam->getAverageScore(),
            'pass_rate' => $exam->getPassRate(),
            'highest_score' => $exam->grades()->max('score') ?? 0,
            'lowest_score' => $exam->grades()->min('score') ?? 0,
        ];

        return view('exams.show', compact('exam', 'stats'));
    }

    /**
     * Show the form for editing the specified exam.
     *
     * @param  \App\Models\Exam  $exam
     * @return \Illuminate\Http\Response
     */
    public function edit(Exam $exam)
    {
        $this->authorize('manage-exams');

        $exam->load(['class.course', 'subject', 'academicYear']);

        $academicYears = AcademicYear::active()->orderBy('name')->get();
        $classes = ClassSection::with(['course.faculty', 'academicYear'])
            ->active()
            ->orderBy('name')
            ->get();

        $examTypes = Exam::getExamTypes();

        return view('exams.edit', compact(
            'exam', 'academicYears', 'classes', 'examTypes'
        ));
    }

    /**
     * Update the specified exam in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Exam  $exam
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Exam $exam)
    {
        $this->authorize('manage-exams');

        // Get the selected class to determine organization type
        $selectedClass = ClassSection::find($request->class_id);
        $isSemesterBased = $selectedClass && $selectedClass->course->organization_type === 'semester';

        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'class_id' => ['required', 'exists:classes,id'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'exam_type' => ['required', 'in:internal,board,practical,midterm,annual,quiz,test,final,assignment'],
            'exam_date' => ['required', 'date'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:480'],
            'total_marks' => ['required', 'numeric', 'min:1', 'max:1000'],
            'theory_marks' => ['nullable', 'numeric', 'min:0'],
            'practical_marks' => ['nullable', 'numeric', 'min:0'],
            'pass_mark' => ['required', 'numeric', 'min:0'],
            'venue' => ['nullable', 'string', 'max:255'],
            'instructions' => ['nullable', 'string'],
            'status' => ['required', 'in:scheduled,ongoing,completed,cancelled'],
        ];

        // Add conditional validation for semester/year
        if ($isSemesterBased) {
            $rules['semester'] = ['required', 'integer', 'min:1', 'max:8'];
        } else {
            $rules['year'] = ['required', 'integer', 'min:1', 'max:4'];
        }

        $request->validate($rules);

        // Validate marks distribution
        if ($request->filled('theory_marks') && $request->filled('practical_marks')) {
            $totalCalculated = $request->theory_marks + $request->practical_marks;
            if (abs($totalCalculated - $request->total_marks) > 0.01) {
                return redirect()->back()
                    ->with('error', 'Theory marks + practical marks must equal total marks')
                    ->withInput();
            }
        }

        // Validate pass mark doesn't exceed total marks
        if ($request->pass_mark > $request->total_marks) {
            return redirect()->back()
                ->with('error', 'Pass mark cannot exceed total marks')
                ->withInput();
        }

        try {
            DB::transaction(function () use ($request, $exam, $isSemesterBased) {
                $exam->update([
                    'title' => $request->title,
                    'class_id' => $request->class_id,
                    'subject_id' => $request->subject_id,
                    'academic_year_id' => $request->academic_year_id,
                    'exam_type' => $request->exam_type,
                    'semester' => $isSemesterBased ? $request->semester : null,
                    'year' => $isSemesterBased ? null : $request->year,
                    'exam_date' => $request->exam_date,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'duration_minutes' => $request->duration_minutes,
                    'total_marks' => $request->total_marks,
                    'theory_marks' => $request->theory_marks,
                    'practical_marks' => $request->practical_marks,
                    'pass_mark' => $request->pass_mark,
                    'venue' => $request->venue,
                    'instructions' => $request->instructions,
                    'status' => $request->status,
                ]);
            });

            return redirect()->route('exams.show', $exam)
                ->with('success', 'Exam updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating exam: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified exam from storage.
     *
     * @param  \App\Models\Exam  $exam
     * @return \Illuminate\Http\Response
     */
    public function destroy(Exam $exam)
    {
        $this->authorize('manage-exams');

        // Check if exam has grades
        if ($exam->grades()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete exam that has grades recorded.');
        }

        try {
            $exam->delete();
            return redirect()->route('exams.index')
                ->with('success', 'Exam deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting exam: ' . $e->getMessage());
        }
    }

    /**
     * Show grade entry form for an exam
     */
    public function grades(Exam $exam)
    {
        $this->authorize('manage-exams');

        $exam->load(['class.course', 'subject', 'academicYear']);

        // Get enrolled students for this exam
        $enrollments = Enrollment::with(['student.user'])
            ->where('class_id', $exam->class_id)
            ->where('academic_year_id', $exam->academic_year_id)
            ->where('status', 'enrolled');

        // Filter by semester or year based on course type
        if ($exam->semester) {
            $enrollments->where('semester', $exam->semester);
        } elseif ($exam->year) {
            $enrollments->where('year', $exam->year);
        }

        $enrollments = $enrollments->orderBy('student_id')->get();

        // Get existing grades for this exam
        $existingGrades = Grade::where('exam_id', $exam->id)
            ->get()
            ->keyBy('student_id');

        return view('exams.grades', compact('exam', 'enrollments', 'existingGrades'));
    }

    /**
     * Store grades for an exam
     */
    public function storeGrades(Request $request, Exam $exam)
    {
        $this->authorize('manage-exams');

        $request->validate([
            'grades' => ['required', 'array'],
            'grades.*.student_id' => ['required', 'exists:students,id'],
            'grades.*.theory_score' => ['nullable', 'numeric', 'min:0'],
            'grades.*.practical_score' => ['nullable', 'numeric', 'min:0'],
            'grades.*.remarks' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            DB::transaction(function () use ($request, $exam) {
                foreach ($request->grades as $gradeData) {
                    $studentId = $gradeData['student_id'];
                    $theoryScore = $gradeData['theory_score'] ?? 0;
                    $practicalScore = $gradeData['practical_score'] ?? 0;

                    // Calculate total score
                    $totalScore = $theoryScore + $practicalScore;

                    // Skip if no scores entered
                    if ($totalScore <= 0) {
                        continue;
                    }

                    // Validate scores don't exceed maximum
                    if ($exam->hasTheory() && $theoryScore > $exam->theory_marks) {
                        throw new \Exception("Theory score for student {$studentId} exceeds maximum marks");
                    }

                    if ($exam->hasPractical() && $practicalScore > $exam->practical_marks) {
                        throw new \Exception("Practical score for student {$studentId} exceeds maximum marks");
                    }

                    if ($totalScore > $exam->total_marks) {
                        throw new \Exception("Total score for student {$studentId} exceeds maximum marks");
                    }

                    // Get enrollment
                    $enrollment = Enrollment::where('student_id', $studentId)
                        ->where('class_id', $exam->class_id)
                        ->where('academic_year_id', $exam->academic_year_id)
                        ->first();

                    if (!$enrollment) {
                        continue;
                    }

                    // Create or update grade
                    Grade::updateOrCreate(
                        [
                            'student_id' => $studentId,
                            'exam_id' => $exam->id,
                            'enrollment_id' => $enrollment->id,
                        ],
                        [
                            'subject_id' => $exam->subject_id,
                            'academic_year_id' => $exam->academic_year_id,
                            'semester' => $exam->semester,
                            'year' => $exam->year,
                            'grade_type' => 'exam',
                            'theory_score' => $exam->hasTheory() ? $theoryScore : null,
                            'practical_score' => $exam->hasPractical() ? $practicalScore : null,
                            'score' => $totalScore,
                            'max_score' => $exam->total_marks,
                            'remarks' => $gradeData['remarks'] ?? null,
                            'graded_by' => Auth::id(),
                            'graded_at' => now(),
                        ]
                    );
                }
            });

            return redirect()->route('exams.grades', $exam)
                ->with('success', 'Grades saved successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error saving grades: ' . $e->getMessage())
                ->withInput();
        }
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
            ->get(['id', 'name', 'code', 'full_marks_theory', 'pass_marks_theory', 'full_marks_practical', 'pass_marks_practical', 'is_practical']);

        return response()->json($subjects);
    }

    /**
     * Get subject marks for auto-loading (AJAX)
     */
    public function getSubjectMarks(Request $request)
    {
        $subjectId = $request->input('subject_id');

        if (!$subjectId) {
            return response()->json([]);
        }

        $subject = Subject::find($subjectId);

        if (!$subject) {
            return response()->json([]);
        }

        return response()->json([
            'theory_marks' => $subject->full_marks_theory,
            'practical_marks' => $subject->full_marks_practical,
            'total_marks' => $subject->total_full_marks,
            'pass_marks_theory' => $subject->pass_marks_theory,
            'pass_marks_practical' => $subject->pass_marks_practical,
            'total_pass_marks' => $subject->total_pass_marks,
            'is_practical' => $subject->is_practical,
            'has_theory' => $subject->hasTheoryComponent(),
            'has_practical' => $subject->hasPracticalComponent(),
        ]);
    }

    /**
     * Get class marks breakdown for auto-loading (AJAX)
     */
    public function getClassMarks(Request $request)
    {
        $classId = $request->input('class_id');

        if (!$classId) {
            return response()->json([]);
        }

        $class = ClassSection::find($classId);

        if (!$class) {
            return response()->json([]);
        }

        $breakdown = $class->getSubjectMarksBreakdown();

        return response()->json($breakdown);
    }
}