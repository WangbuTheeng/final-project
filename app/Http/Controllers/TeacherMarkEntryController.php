<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Mark;
use App\Models\ExamComponentMark;
use App\Models\ExamComponent;
use App\Models\Enrollment;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TeacherMarkEntryController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:Teacher|Admin|Super Admin');
    }

    /**
     * Display teacher's assigned exams for mark entry
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get exams where the teacher is assigned to subjects
        $query = Exam::with(['class.course', 'academicYear', 'examType', 'subjects'])
            ->whereHas('subjects', function ($q) use ($user) {
                $q->where('instructor_id', $user->id);
            })
            ->where('status', '!=', 'cancelled')
            ->orderBy('exam_date', 'desc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('education_level')) {
            $query->where('education_level', $request->education_level);
        }

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        $exams = $query->paginate(15);

        // Get filter options
        $academicYears = AcademicYear::active()->get();
        $statuses = ['scheduled', 'ongoing', 'completed'];

        return view('teacher.marks.index', compact('exams', 'academicYears', 'statuses'));
    }

    /**
     * Show mark entry form for a specific exam and subject
     */
    public function create(Request $request)
    {
        $examId = $request->get('exam_id');
        $subjectId = $request->get('subject_id');

        if (!$examId || !$subjectId) {
            return redirect()->route('teacher.marks.index')
                ->with('error', 'Please select an exam and subject.');
        }

        $exam = Exam::with(['class', 'academicYear', 'examType', 'gradingSystem'])->findOrFail($examId);
        $subject = Subject::findOrFail($subjectId);

        // Verify teacher is assigned to this subject
        if ($subject->instructor_id !== Auth::id() && !Auth::user()->hasRole('Admin') && !Auth::user()->hasRole('Super Admin')) {
            abort(403, 'You are not authorized to enter marks for this subject.');
        }

        // Get enrolled students
        $enrollments = Enrollment::with(['student.user'])
            ->where('class_id', $exam->class_id)
            ->where('academic_year_id', $exam->academic_year_id)
            ->where('status', 'enrolled')
            ->orderBy('student_id')
            ->get();

        // Get existing marks
        $existingMarks = Mark::where('exam_id', $exam->id)
            ->where('subject_id', $subject->id)
            ->get()
            ->keyBy('student_id');

        // Get exam components for internal assessments
        $examComponents = [];
        $existingComponentMarks = collect();
        
        if ($exam->isInternal() && $exam->examType && $exam->examType->code === 'IA_BACH') {
            $examComponents = ExamComponent::forEducationLevel($exam->education_level)
                ->active()
                ->ordered()
                ->get();

            $existingComponentMarks = ExamComponentMark::where('exam_id', $exam->id)
                ->where('subject_id', $subject->id)
                ->get()
                ->groupBy('student_id');
        }

        return view('teacher.marks.create', compact(
            'exam', 
            'subject', 
            'enrollments', 
            'existingMarks', 
            'examComponents',
            'existingComponentMarks'
        ));
    }

    /**
     * Store marks for students
     */
    public function store(Request $request)
    {
        $examId = $request->get('exam_id');
        $subjectId = $request->get('subject_id');
        $isDraft = $request->has('save_draft');

        $exam = Exam::findOrFail($examId);
        $subject = Subject::findOrFail($subjectId);

        // Verify teacher authorization
        if ($subject->instructor_id !== Auth::id() && !Auth::user()->hasRole(['Admin', 'Super Admin'])) {
            abort(403, 'You are not authorized to enter marks for this subject.');
        }

        $rules = [
            'marks' => 'required|array',
            'marks.*.student_id' => 'required|exists:students,id',
        ];

        // Add validation rules based on exam type
        if ($exam->isInternal() && $exam->examType && $exam->examType->code === 'IA_BACH') {
            // Component-wise validation for Bachelor's internal assessment
            $rules['component_marks'] = 'array';
            $rules['component_marks.*.*.marks'] = 'nullable|numeric|min:0';
        } else {
            // Regular exam validation
            $rules['marks.*.theory_marks'] = 'nullable|numeric|min:0';
            $rules['marks.*.practical_marks'] = 'nullable|numeric|min:0';
            $rules['marks.*.obtained_marks'] = 'nullable|numeric|min:0';
        }

        $request->validate($rules);

        try {
            DB::transaction(function () use ($request, $exam, $subject, $isDraft) {
                $marksData = $request->get('marks', []);
                $componentMarksData = $request->get('component_marks', []);

                foreach ($marksData as $markData) {
                    $studentId = $markData['student_id'];
                    
                    // Get enrollment
                    $enrollment = Enrollment::where('student_id', $studentId)
                        ->where('class_id', $exam->class_id)
                        ->where('academic_year_id', $exam->academic_year_id)
                        ->first();

                    if (!$enrollment) {
                        continue;
                    }

                    if ($exam->isInternal() && $exam->examType && $exam->examType->code === 'IA_BACH') {
                        // Handle component-wise marks for Bachelor's internal assessment
                        $this->storeComponentMarks($exam, $subject, $studentId, $enrollment, $componentMarksData[$studentId] ?? [], $isDraft);
                    } else {
                        // Handle regular exam marks
                        $this->storeRegularMarks($exam, $subject, $studentId, $enrollment, $markData, $isDraft);
                    }
                }
            });

            $message = $isDraft ? 'Marks saved as draft successfully.' : 'Marks submitted successfully.';
            return redirect()->route('teacher.marks.create', [
                'exam_id' => $examId,
                'subject_id' => $subjectId
            ])->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error saving marks: ' . $e->getMessage());
        }
    }

    /**
     * Store component-wise marks for internal assessments
     */
    private function storeComponentMarks($exam, $subject, $studentId, $enrollment, $componentData, $isDraft)
    {
        $totalMarks = 0;
        $totalObtained = 0;

        foreach ($componentData as $componentId => $data) {
            $component = ExamComponent::find($componentId);
            if (!$component) continue;

            $marks = $data['marks'] ?? 0;
            $maxMarks = $component->default_marks;

            // Validate marks don't exceed maximum
            if ($marks > $maxMarks) {
                throw new \Exception("Marks for component {$component->name} cannot exceed {$maxMarks}");
            }

            // Create or update component mark
            ExamComponentMark::updateOrCreate(
                [
                    'exam_id' => $exam->id,
                    'subject_id' => $subject->id,
                    'student_id' => $studentId,
                    'exam_component_id' => $componentId,
                ],
                [
                    'enrollment_id' => $enrollment->id,
                    'marks_obtained' => $marks,
                    'max_marks' => $maxMarks,
                    'percentage' => $maxMarks > 0 ? ($marks / $maxMarks) * 100 : 0,
                    'status' => $isDraft ? 'draft' : 'submitted',
                    'entered_by' => Auth::id(),
                    'entered_at' => now(),
                ]
            );

            $totalMarks += $maxMarks;
            $totalObtained += $marks;
        }

        // Create or update overall mark record
        if ($totalMarks > 0) {
            $percentage = ($totalObtained / $totalMarks) * 100;
            $gradeScale = $exam->getGradeByPercentage($percentage);

            Mark::updateOrCreate(
                [
                    'exam_id' => $exam->id,
                    'subject_id' => $subject->id,
                    'student_id' => $studentId,
                ],
                [
                    'enrollment_id' => $enrollment->id,
                    'internal_marks' => $totalObtained,
                    'total_marks' => $totalMarks,
                    'obtained_marks' => $totalObtained,
                    'percentage' => $percentage,
                    'grade_letter' => $gradeScale ? $gradeScale->grade_letter : null,
                    'grade_point' => $gradeScale ? $gradeScale->grade_point : null,
                    'status' => $isDraft ? 'incomplete' : 'pass', // Using valid enum values until database is updated
                    'entered_by' => Auth::id(),
                    'entered_at' => now(),
                ]
            );
        }
    }

    /**
     * Store regular exam marks
     */
    private function storeRegularMarks($exam, $subject, $studentId, $enrollment, $markData, $isDraft)
    {
        $theoryMarks = $markData['theory_marks'] ?? 0;
        $practicalMarks = $markData['practical_marks'] ?? 0;
        $obtainedMarks = $markData['obtained_marks'] ?? ($theoryMarks + $practicalMarks);

        // Get subject marks configuration
        $examSubject = $exam->examSubjects()->where('subject_id', $subject->id)->first();
        $totalMarks = $examSubject ? $examSubject->total_marks : $exam->total_marks;

        // Validate marks
        if ($obtainedMarks > $totalMarks) {
            throw new \Exception("Obtained marks cannot exceed total marks ({$totalMarks})");
        }

        $percentage = $totalMarks > 0 ? ($obtainedMarks / $totalMarks) * 100 : 0;
        $gradeScale = $exam->getGradeByPercentage($percentage);

        Mark::updateOrCreate(
            [
                'exam_id' => $exam->id,
                'subject_id' => $subject->id,
                'student_id' => $studentId,
            ],
            [
                'enrollment_id' => $enrollment->id,
                'theory_marks' => $theoryMarks > 0 ? $theoryMarks : null,
                'practical_marks' => $practicalMarks > 0 ? $practicalMarks : null,
                'total_marks' => $totalMarks,
                'obtained_marks' => $obtainedMarks,
                'percentage' => $percentage,
                'grade_letter' => $gradeScale ? $gradeScale->grade_letter : null,
                'grade_point' => $gradeScale ? $gradeScale->grade_point : null,
                'status' => $isDraft ? 'incomplete' : 'pass', // Using valid enum values until database is updated
                'entered_by' => Auth::id(),
                'entered_at' => now(),
            ]
        );
    }

    /**
     * Get teacher's assigned subjects for an exam (AJAX)
     */
    public function getAssignedSubjects(Request $request)
    {
        $examId = $request->get('exam_id');
        $exam = Exam::find($examId);

        if (!$exam) {
            return response()->json([]);
        }

        $subjects = $exam->subjects()
            ->where('instructor_id', Auth::id())
            ->get(['id', 'name', 'code']);

        return response()->json($subjects);
    }

    /**
     * Get mark entry progress for an exam and subject (AJAX)
     */
    public function getMarkEntryProgress(Request $request)
    {
        $examId = $request->get('exam_id');
        $subjectId = $request->get('subject_id');

        $totalStudents = Enrollment::where('class_id', function($query) use ($examId) {
                $query->select('class_id')->from('exams')->where('id', $examId);
            })
            ->where('status', 'enrolled')
            ->count();

        $marksEntered = Mark::where('exam_id', $examId)
            ->where('subject_id', $subjectId)
            ->count();

        $draftMarks = Mark::where('exam_id', $examId)
            ->where('subject_id', $subjectId)
            ->where('status', 'draft')
            ->count();

        $submittedMarks = Mark::where('exam_id', $examId)
            ->where('subject_id', $subjectId)
            ->where('status', 'submitted')
            ->count();

        return response()->json([
            'total_students' => $totalStudents,
            'marks_entered' => $marksEntered,
            'draft_marks' => $draftMarks,
            'submitted_marks' => $submittedMarks,
            'completion_percentage' => $totalStudents > 0 ? round(($marksEntered / $totalStudents) * 100, 2) : 0
        ]);
    }
}
