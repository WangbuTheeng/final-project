<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mark;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\ClassSection;
use App\Models\AcademicYear;
use App\Models\GradeScale;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MarkController extends Controller
{
    /**
     * Display marks search form
     */
    public function index()
    {
        // Check if user has Super Admin or Admin role
        if (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('Admin')) {
            abort(403, 'Unauthorized access to Marks management.');
        }

        $academicYears = AcademicYear::active()->orderBy('name')->get();
        $courses = Course::with('faculty')->active()->orderBy('title')->get();

        return view('marks.index', compact('academicYears', 'courses'));
    }

    /**
     * Search for exam to enter marks
     */
    public function search(Request $request)
    {
        // Check if user has Super Admin or Admin role
        if (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('Admin')) {
            abort(403, 'Unauthorized access to search Marks.');
        }

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'class_id' => 'required|exists:classes,id',
            'exam_id' => 'required|exists:exams,id'
        ]);

        $exam = Exam::with(['class.course.faculty', 'academicYear', 'examSubjects.subject', 'subject'])
            ->findOrFail($request->exam_id);

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

        // Get subjects for this exam - completely rewritten approach
        $subjects = collect();

        try {
            // Strategy 1: Try to get exam subjects from pivot table (for multi-subject exams)
            $examSubjects = $exam->examSubjects()->with('subject')->where('is_active', true)->get();

            if ($examSubjects->isNotEmpty()) {
                // Convert ExamSubject models to consistent format
                $subjects = $examSubjects->map(function ($examSubject) {
                    return (object) [
                        'exam_id' => $examSubject->exam_id,
                        'subject_id' => $examSubject->subject_id,
                        'theory_marks' => $examSubject->theory_marks ?? 0,
                        'practical_marks' => $examSubject->practical_marks ?? 0,
                        'pass_marks_theory' => $examSubject->pass_marks_theory ?? 0,
                        'pass_marks_practical' => $examSubject->pass_marks_practical ?? 0,
                        'is_active' => $examSubject->is_active,
                        'subject' => $examSubject->subject
                    ];
                });
            }
            // Strategy 2: Single-subject exam
            elseif ($exam->subject_id && $exam->subject) {
                $subject = $exam->subject;
                $examSubject = (object) [
                    'exam_id' => $exam->id,
                    'subject_id' => $exam->subject_id,
                    'theory_marks' => $exam->theory_marks ?? $subject->full_marks_theory ?? 0,
                    'practical_marks' => $exam->practical_marks ?? $subject->full_marks_practical ?? 0,
                    'pass_marks_theory' => $subject->pass_marks_theory ?? 0,
                    'pass_marks_practical' => $subject->pass_marks_practical ?? 0,
                    'is_active' => true,
                    'subject' => $subject
                ];
                $subjects = collect([$examSubject]);
            }
            // Strategy 3: Class-wide exam (get all subjects from class)
            else {
                $classSubjects = Subject::where('class_id', $exam->class_id)
                    ->where('is_active', true)
                    ->get();

                if ($classSubjects->isNotEmpty()) {
                    $subjects = $classSubjects->map(function ($subject) use ($exam) {
                        return (object) [
                            'exam_id' => $exam->id,
                            'subject_id' => $subject->id,
                            'theory_marks' => $subject->full_marks_theory ?? 0,
                            'practical_marks' => $subject->full_marks_practical ?? 0,
                            'pass_marks_theory' => $subject->pass_marks_theory ?? 0,
                            'pass_marks_practical' => $subject->pass_marks_practical ?? 0,
                            'is_active' => true,
                            'subject' => $subject
                        ];
                    });
                }
            }



        } catch (\Exception $e) {
            \Log::error('Error getting subjects for marks entry: ' . $e->getMessage(), [
                'exam_id' => $exam->id,
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('marks.index')
                ->with('error', 'Error loading exam subjects: ' . $e->getMessage());
        }

        // If no subjects found, return error
        if ($subjects->isEmpty()) {
            return redirect()->route('marks.index')
                ->with('error', 'No subjects found for this exam. Please configure subjects for the exam first.');
        }



        // Get existing marks
        $existingMarks = Mark::where('exam_id', $exam->id)
            ->get()
            ->groupBy(function($mark) {
                return $mark->student_id . '_' . $mark->subject_id;
            });

        return view('marks.entry', compact('exam', 'enrollments', 'subjects', 'existingMarks'));
    }

    /**
     * Store bulk marks
     */
    public function storeBulk(Request $request)
    {
        // Check if user has Super Admin or Admin role
        if (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('Admin')) {
            abort(403, 'Unauthorized access to store Marks.');
        }

        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'marks' => 'required|array',
            'marks.*.student_id' => 'required|exists:students,id',
            'marks.*.enrollment_id' => 'required|exists:enrollments,id',
            'marks.*.subject_id' => 'required|exists:subjects,id',
            'marks.*.theory_marks' => 'nullable|numeric|min:0',
            'marks.*.practical_marks' => 'nullable|numeric|min:0',
            'marks.*.internal_marks' => 'nullable|numeric|min:0',
            'marks.*.total_marks' => 'required|numeric|min:1',
        ]);

        $exam = Exam::findOrFail($request->exam_id);

        try {
            DB::transaction(function () use ($request, $exam) {
                foreach ($request->marks as $markData) {
                    // Skip if no marks entered
                    if (empty($markData['theory_marks']) &&
                        empty($markData['practical_marks']) &&
                        empty($markData['internal_marks'])) {
                        continue;
                    }

                    // Calculate obtained marks
                    $obtainedMarks = 0;
                    $obtainedMarks += $markData['theory_marks'] ?? 0;
                    $obtainedMarks += $markData['practical_marks'] ?? 0;
                    $obtainedMarks += $markData['internal_marks'] ?? 0;

                    // Calculate percentage
                    $percentage = ($obtainedMarks / $markData['total_marks']) * 100;

                    // Determine grade
                    $gradeScale = GradeScale::getGradeByPercentage($percentage);

                    // Determine status
                    $passPercentage = \App\Models\CollegeSetting::getPassPercentage();
                    $status = $percentage >= $passPercentage ? 'pass' : 'fail';

                    // Create or update mark
                    Mark::updateOrCreate(
                        [
                            'exam_id' => $request->exam_id,
                            'subject_id' => $markData['subject_id'],
                            'student_id' => $markData['student_id'],
                        ],
                        [
                            'enrollment_id' => $markData['enrollment_id'],
                            'theory_marks' => $markData['theory_marks'],
                            'practical_marks' => $markData['practical_marks'],
                            'internal_marks' => $markData['internal_marks'],
                            'total_marks' => $markData['total_marks'],
                            'obtained_marks' => $obtainedMarks,
                            'percentage' => $percentage,
                            'grade_letter' => $gradeScale->grade_letter ?? null,
                            'grade_point' => $gradeScale->grade_point ?? null,
                            'status' => $status,
                            'remarks' => $markData['remarks'] ?? null,
                            'entered_by' => Auth::id(),
                            'entered_at' => now(),
                        ]
                    );
                }
            });

            return redirect()->back()->with('success', 'Marks saved successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error saving marks: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Get classes by course (AJAX)
     */
    public function getClassesByCourse(Request $request)
    {
        $courseId = $request->input('course_id');

        if (!$courseId) {
            return response()->json([]);
        }

        $classes = ClassSection::with(['academicYear'])
            ->where('course_id', $courseId)
            ->active()
            ->orderBy('name')
            ->get();

        return response()->json($classes);
    }

    /**
     * Get exams by class (AJAX)
     */
    public function getExamsByClass(Request $request)
    {
        $classId = $request->input('class_id');

        if (!$classId) {
            return response()->json([]);
        }

        $exams = Exam::where('class_id', $classId)
            ->where('status', '!=', 'cancelled')
            ->orderBy('exam_date', 'desc')
            ->get();

        return response()->json($exams);
    }
}
