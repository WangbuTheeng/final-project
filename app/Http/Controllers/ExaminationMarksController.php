<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Mark;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Enrollment;
use App\Models\GradeScale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ExaminationMarksController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show marks entry form for an examination
     */
    public function entry(Exam $examination)
    {
        // Check permissions
        if (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('Teacher')) {
            abort(403, 'Unauthorized access to marks entry.');
        }

        $examination->load(['class', 'academicYear', 'subjects', 'examType']);

        // Get enrolled students for this class
        $enrollments = Enrollment::with(['student.user'])
            ->where('class_id', $examination->class_id)
            ->where('academic_year_id', $examination->academic_year_id)
            ->where('status', 'enrolled')
            ->get();

        // Get subjects for this examination
        if ($examination->is_multi_subject) {
            $subjects = $examination->subjects;
        } else {
            $subjects = collect([$examination->subject]);
        }

        // Get existing marks
        $existingMarks = Mark::where('exam_id', $examination->id)
            ->get()
            ->groupBy(function($mark) {
                return $mark->student_id . '_' . $mark->subject_id;
            });

        return view('examinations.marks.entry', compact('examination', 'enrollments', 'subjects', 'existingMarks'));
    }

    /**
     * Store marks for examination
     */
    public function store(Request $request, Exam $examination)
    {
        // Debug: Log the incoming request data
        \Log::info('Marks Entry Request Data:', [
            'marks' => $request->marks,
            'marks_count' => count($request->marks ?? []),
            'examination_id' => $examination->id,
            'request_all' => $request->all()
        ]);

        // Basic validation - handle associative array structure
        if (!$request->has('marks') || !is_array($request->marks)) {
            return redirect()->back()
                ->with('error', 'No marks data received')
                ->withInput();
        }

        // Validate each mark entry individually since we have associative keys
        foreach ($request->marks as $key => $markData) {
            // Only validate if the entry has some data
            if (!empty($markData['theory_marks']) || !empty($markData['practical_marks']) || !empty($markData['internal_marks'])) {
                $request->validate([
                    "marks.{$key}.student_id" => 'required|exists:students,id',
                    "marks.{$key}.subject_id" => 'required|exists:subjects,id',
                    "marks.{$key}.enrollment_id" => 'required|exists:enrollments,id',
                    "marks.{$key}.theory_marks" => 'nullable|numeric|min:0',
                    "marks.{$key}.practical_marks" => 'nullable|numeric|min:0',
                    "marks.{$key}.internal_marks" => 'nullable|numeric|min:0',
                ]);
            }
        }

        // Validate marks against subject limits
        $errors = [];
        foreach ($request->marks as $key => $markData) {
            if (!isset($markData['subject_id'])) continue;

            $subject = Subject::find($markData['subject_id']);
            if (!$subject) continue;

            // Get the correct theory and practical marks limits
            $theoryLimit = 0;
            $practicalLimit = 0;

            if ($examination->is_multi_subject) {
                $subjectPivot = $examination->subjects()->where('subject_id', $subject->id)->first();
                $theoryLimit = $subjectPivot->pivot->theory_marks ?? 0;
                $practicalLimit = $subjectPivot->pivot->practical_marks ?? 0;
            } else {
                $theoryLimit = $subject->full_marks_theory ?? 0;
                $practicalLimit = $subject->full_marks_practical ?? 0;
            }

            // Validate theory marks
            if (isset($markData['theory_marks']) && $markData['theory_marks'] !== null && $markData['theory_marks'] !== '') {
                if ($theoryLimit > 0 && $markData['theory_marks'] > $theoryLimit) {
                    $errors["marks.{$key}.theory_marks"] = "Theory marks for {$subject->name} cannot exceed {$theoryLimit}";
                }
            }

            // Validate practical marks
            if (isset($markData['practical_marks']) && $markData['practical_marks'] !== null && $markData['practical_marks'] !== '') {
                if ($practicalLimit > 0 && $markData['practical_marks'] > $practicalLimit) {
                    $errors["marks.{$key}.practical_marks"] = "Practical marks for {$subject->name} cannot exceed {$practicalLimit}";
                }
            }
        }

        if (!empty($errors)) {
            return redirect()->back()
                ->withErrors($errors)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $processedCount = 0;
            foreach ($request->marks as $key => $markData) {
                \Log::info("Processing mark entry: {$key}", $markData);

                // Skip if no marks entered for this student-subject combination
                if ((empty($markData['theory_marks']) || $markData['theory_marks'] === '') &&
                    (empty($markData['practical_marks']) || $markData['practical_marks'] === '') &&
                    (empty($markData['internal_marks']) || $markData['internal_marks'] === '')) {
                    \Log::info("Skipping empty mark entry: {$key}");
                    continue;
                }

                $this->processStudentMark($examination, $markData);
                $processedCount++;
            }

            \Log::info("Processed {$processedCount} mark entries");

            DB::commit();
            return redirect()->route('examinations.marks.entry', $examination)
                ->with('success', 'Marks saved successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Error saving marks: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Process individual student mark
     */
    private function processStudentMark(Exam $examination, array $markData)
    {
        $studentId = $markData['student_id'];
        $subjectId = $markData['subject_id'];
        $enrollmentId = $markData['enrollment_id'];

        // Get enrollment
        $enrollment = Enrollment::find($enrollmentId);

        if (!$enrollment) {
            throw new \Exception("Enrollment not found for enrollment ID: {$enrollmentId}");
        }

        // Calculate marks
        $theoryMarks = $markData['theory_marks'] ?? 0;
        $practicalMarks = $markData['practical_marks'] ?? 0;
        $internalMarks = $markData['internal_marks'] ?? 0;
        $obtainedMarks = $theoryMarks + $practicalMarks + $internalMarks;

        // Get total marks for this subject
        if ($examination->is_multi_subject) {
            $subjectPivot = $examination->subjects()->where('subject_id', $subjectId)->first();
            $totalMarks = ($subjectPivot->pivot->theory_marks ?? 0) + 
                         ($subjectPivot->pivot->practical_marks ?? 0);
        } else {
            $totalMarks = $examination->total_marks;
        }

        // Calculate percentage and grade
        $percentage = $totalMarks > 0 ? ($obtainedMarks / $totalMarks) * 100 : 0;
        $gradeScale = $this->getGradeByPercentage($percentage);

        // Create or update mark record
        Mark::updateOrCreate(
            [
                'exam_id' => $examination->id,
                'subject_id' => $subjectId,
                'student_id' => $studentId,
            ],
            [
                'enrollment_id' => $enrollment->id,
                'theory_marks' => $theoryMarks > 0 ? $theoryMarks : null,
                'practical_marks' => $practicalMarks > 0 ? $practicalMarks : null,
                'internal_marks' => $internalMarks > 0 ? $internalMarks : null,
                'total_marks' => $totalMarks,
                'obtained_marks' => $obtainedMarks,
                'percentage' => $percentage,
                'grade_letter' => $gradeScale->grade_letter ?? null,
                'grade_point' => $gradeScale->grade_point ?? null,
                'status' => 'pass', // Using 'pass' until database enum is updated
                'remarks' => $markData['remarks'] ?? null,
                'entered_by' => Auth::id(),
                'entered_at' => now(),
            ]
        );
    }

    /**
     * Get grade by percentage
     */
    private function getGradeByPercentage($percentage)
    {
        return GradeScale::where('min_percentage', '<=', $percentage)
            ->where('max_percentage', '>=', $percentage)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Show marks for an examination
     */
    public function show(Exam $examination)
    {
        $examination->load(['class', 'academicYear', 'subjects', 'examType']);

        $marks = Mark::with(['student.user', 'subject'])
            ->where('exam_id', $examination->id)
            ->get()
            ->groupBy('student_id');

        return view('examinations.marks.show', compact('examination', 'marks'));
    }

    /**
     * Export marks to Excel/PDF
     */
    public function export(Exam $examination, $format = 'excel')
    {
        // TODO: Implement export functionality
        return redirect()->back()->with('info', 'Export functionality will be implemented soon.');
    }

    /**
     * Bulk import marks from Excel
     */
    public function import(Request $request, Exam $examination)
    {
        // TODO: Implement import functionality
        return redirect()->back()->with('info', 'Import functionality will be implemented soon.');
    }
}
