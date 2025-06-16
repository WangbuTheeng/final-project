<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Mark;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\Subject;
use App\Models\GradeScale;
use App\Models\CollegeSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BulkMarksController extends Controller
{
    /**
     * Display bulk marks entry form
     */
    public function index(Request $request)
    {
        $this->authorize('manage-exams');

        $exams = Exam::with(['class.course', 'academicYear', 'subjects', 'subject'])
            ->when($request->filled('exam_id'), function ($query) use ($request) {
                $query->where('id', $request->exam_id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $selectedExam = $request->filled('exam_id') ?
            Exam::with(['class.course', 'academicYear', 'subjects', 'subject'])->find($request->exam_id) : null;

        return view('bulk-marks.index', compact('exams', 'selectedExam'));
    }

    /**
     * Show bulk marks entry form for specific exam
     */
    public function create(Request $request)
    {
        $this->authorize('manage-exams');

        $examId = $request->get('exam_id');
        if (!$examId) {
            return redirect()->route('bulk-marks.index')
                ->with('error', 'Please select an exam first.');
        }

        $exam = Exam::with(['class.course', 'academicYear', 'subjects', 'subject'])->findOrFail($examId);

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

        // Get existing marks for this exam
        $existingMarks = Mark::where('exam_id', $exam->id)
            ->get()
            ->groupBy(['student_id', 'subject_id']);

        // Get subjects for this exam
        if ($exam->is_multi_subject) {
            $subjects = $exam->subjects;
        } elseif ($exam->subject_id && $exam->subject) {
            $subjects = collect([$exam->subject]);
        } else {
            // For class-wide exams without specific subject, get all subjects from the class
            $subjects = Subject::where('class_id', $exam->class_id)
                ->where('is_active', true)
                ->get();
        }

        return view('bulk-marks.create', compact('exam', 'enrollments', 'existingMarks', 'subjects'));
    }

    /**
     * Store bulk marks
     */
    public function store(Request $request)
    {
        $this->authorize('manage-exams');

        $examId = $request->get('exam_id');
        $exam = Exam::findOrFail($examId);

        // Validate the request
        $validator = Validator::make($request->all(), [
            'exam_id' => 'required|exists:exams,id',
            'marks' => 'required|array',
            'marks.*.student_id' => 'required|exists:students,id',
            'marks.*.subject_id' => 'required|exists:subjects,id',
            'marks.*.theory_marks' => 'nullable|numeric|min:0',
            'marks.*.practical_marks' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please correct the validation errors.');
        }

        try {
            DB::transaction(function () use ($request, $exam) {
                $marksData = $request->get('marks', []);

                foreach ($marksData as $markData) {
                    // Ensure $markData is treated as an array
                    $this->processStudentMark($exam, (array) $markData);
                }
            });

            return redirect()->route('bulk-marks.index', ['exam_id' => $examId])
                ->with('success', 'Marks have been saved successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while saving marks: ' . $e->getMessage());
        }
    }

    /**
     * Process individual student mark
     */
    private function processStudentMark($exam, $markData)
    {
        $studentId = $markData['student_id'];
        $subjectId = $markData['subject_id'];
        $theoryMarks = $markData['theory_marks'] ?? 0;
        $practicalMarks = $markData['practical_marks'] ?? 0;

        // Get enrollment
        $enrollment = Enrollment::where('student_id', $studentId)
            ->where('class_id', $exam->class_id)
            ->where('academic_year_id', $exam->academic_year_id)
            ->first();

        if (!$enrollment) {
            throw new \Exception("Student enrollment not found for student ID: {$studentId}");
        }

        // Get subject details for validation
        $subject = Subject::find($subjectId);
        if (!$subject) {
            throw new \Exception("Subject not found for ID: {$subjectId}");
        }

        // Validate marks against subject maximums
        $maxTheory = $subject->full_marks_theory ?? 0;
        $maxPractical = $subject->full_marks_practical ?? 0;

        if ($theoryMarks > $maxTheory) {
            throw new \Exception("Theory marks ({$theoryMarks}) exceed maximum ({$maxTheory}) for subject: {$subject->name}");
        }

        if ($practicalMarks > $maxPractical) {
            throw new \Exception("Practical marks ({$practicalMarks}) exceed maximum ({$maxPractical}) for subject: {$subject->name}");
        }

        // Calculate totals
        $totalMarks = $maxTheory + $maxPractical;
        $obtainedMarks = $theoryMarks + $practicalMarks;
        $percentage = $totalMarks > 0 ? ($obtainedMarks / $totalMarks) * 100 : 0;

        // Determine grade
        $gradeScale = GradeScale::where('min_percent', '<=', $percentage)
            ->where('max_percent', '>=', $percentage)
            ->where('status', 'active')
            ->first();

        $gradeLetter = $gradeScale ? $gradeScale->grade_letter : 'F';
        $gradePoint = $gradeScale ? $gradeScale->grade_point : 0;

        // Determine status
        $passPercentage = CollegeSetting::getPassPercentage();
        $status = $percentage >= $passPercentage ? 'pass' : 'fail';

        // Create or update mark record
        Mark::updateOrCreate(
            [
                'exam_id' => $exam->id,
                'subject_id' => $subjectId,
                'student_id' => $studentId,
                'enrollment_id' => $enrollment->id,
            ],
            [
                'theory_marks' => $theoryMarks,
                'practical_marks' => $practicalMarks,
                'total_marks' => $totalMarks,
                'obtained_marks' => $obtainedMarks,
                'percentage' => $percentage,
                'grade_letter' => $gradeLetter,
                'grade_point' => $gradePoint,
                'status' => $status,
                'entered_by' => auth()->id(),
                'entered_at' => now(),
            ]
        );
    }
}
