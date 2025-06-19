<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Student;
use App\Models\Mark;
use App\Models\Grade;
use App\Models\GradeScale;
use App\Models\ClassSection;
use App\Models\AcademicYear;
use App\Models\CollegeSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class ResultController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display result management dashboard
     */
    public function index()
    {
        // Allow both manage-exams and view-exams (for teachers)
        if (!auth()->user()->can('manage-exams') && !auth()->user()->can('view-exams')) {
            abort(403, 'Unauthorized');
        }

        // Get completed exams with marks
        $exams = Exam::with(['class.course.faculty', 'academicYear', 'marks'])
            ->where('status', 'completed')
            ->whereHas('marks')
            ->orderBy('exam_date', 'desc')
            ->paginate(15);

        // Statistics
        $stats = [
            'total_exams' => Exam::where('status', 'completed')->count(),
            'total_results' => Mark::distinct('exam_id')->count(),
            'total_students_graded' => Mark::distinct('student_id')->count(),
            'pending_results' => Exam::where('status', 'completed')
                ->whereDoesntHave('marks')
                ->count()
        ];

        return view('results.index', compact('exams', 'stats'));
    }

    /**
     * Generate comprehensive result for an exam
     */
    public function generate(Request $request, Exam $exam)
    {
        // Allow both manage-exams and view-exams (for teachers to view results)
        if (!auth()->user()->can('manage-exams') && !auth()->user()->can('view-exams')) {
            abort(403, 'Unauthorized');
        }

        $exam->load(['class.course.faculty', 'academicYear', 'subjects']);

        // Get all marks for this exam
        $marks = Mark::with(['student.user', 'subject'])
            ->where('exam_id', $exam->id)
            ->get();

        if ($marks->isEmpty()) {
            return redirect()->back()->with('error', 'No marks found for this exam.');
        }

        // Group marks by student
        $studentResults = $marks->groupBy('student_id')->map(function ($studentMarks) use ($exam) {
            $student = $studentMarks->first()->student;
            $totalObtained = $studentMarks->sum('obtained_marks');
            $totalMaximum = $studentMarks->sum('total_marks');
            $percentage = $totalMaximum > 0 ? ($totalObtained / $totalMaximum) * 100 : 0;
            
            // Calculate GPA using TU format
            $gpa = $this->calculateTUGPA($percentage);
            $letterGrade = $this->getLetterGrade($percentage);
            $resultStatus = $this->getResultStatus($studentMarks, $percentage);

            return [
                'student' => $student,
                'marks' => $studentMarks,
                'total_obtained' => $totalObtained,
                'total_maximum' => $totalMaximum,
                'percentage' => $percentage,
                'gpa' => $gpa,
                'letter_grade' => $letterGrade,
                'result_status' => $resultStatus,
                'rank' => 0 // Will be calculated later
            ];
        });

        // Sort by percentage and assign ranks
        $sortedResults = $studentResults->sortByDesc('percentage')->values();
        $sortedResults->each(function ($result, $index) {
            $result['rank'] = $index + 1;
        });

        // Calculate class statistics
        $classStats = [
            'total_students' => $sortedResults->count(),
            'passed' => $sortedResults->where('result_status', 'Pass')->count(),
            'failed' => $sortedResults->where('result_status', 'Fail')->count(),
            'highest_percentage' => $sortedResults->max('percentage'),
            'lowest_percentage' => $sortedResults->min('percentage'),
            'average_percentage' => $sortedResults->avg('percentage'),
            'pass_rate' => $sortedResults->count() > 0 ? 
                ($sortedResults->where('result_status', 'Pass')->count() / $sortedResults->count()) * 100 : 0
        ];

        return view('results.generate', compact('exam', 'sortedResults', 'classStats'));
    }

    /**
     * Generate PDF result for an exam
     */
    public function generatePdf(Request $request, Exam $exam)
    {
        $this->authorize('manage-exams');

        // Get the same data as the preview
        $data = $this->getResultData($exam);
        
        if (empty($data['sortedResults'])) {
            return redirect()->back()->with('error', 'No results found for this exam.');
        }

        $pdf = \PDF::loadView('results.pdf', $data);
        $pdf->setPaper('A4', 'landscape'); // Landscape for better table display

        $filename = sprintf(
            'result_%s_%s_%s.pdf',
            str_replace(' ', '_', $exam->title),
            $exam->class->name,
            now()->format('Y-m-d')
        );

        return $pdf->download($filename);
    }

    /**
     * Generate individual student marksheet
     */
    public function studentMarksheet(Request $request, Exam $exam, Student $student)
    {
        $this->authorize('manage-exams');

        $marks = Mark::with(['subject', 'exam'])
            ->where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->get();

        if ($marks->isEmpty()) {
            return redirect()->back()->with('error', 'No marks found for this student in the selected exam.');
        }

        $totalObtained = $marks->sum('obtained_marks');
        $totalMaximum = $marks->sum('total_marks');
        $overallPercentage = $totalMaximum > 0 ? ($totalObtained / $totalMaximum) * 100 : 0;
        
        $gpa = $this->calculateTUGPA($overallPercentage);
        $overallGrade = $this->getLetterGrade($overallPercentage);
        $resultStatus = $this->getResultStatus($marks, $overallPercentage);

        // Get college settings
        $collegeSettings = CollegeSetting::first();

        $data = [
            'exam' => $exam,
            'student' => $student,
            'marks' => $marks,
            'totalObtained' => $totalObtained,
            'totalMaximum' => $totalMaximum,
            'overallPercentage' => $overallPercentage,
            'overallGrade' => $overallGrade,
            'gpa' => $gpa,
            'resultStatus' => $resultStatus,
            'collegeSettings' => $collegeSettings,
        ];

        return view('results.student-marksheet', $data);
    }

    /**
     * Generate PDF marksheet for individual student
     */
    public function studentMarksheetPdf(Request $request, Exam $exam, Student $student)
    {
        $this->authorize('manage-exams');

        // Get the same data as the preview
        $data = $this->getStudentMarksheetData($exam, $student);
        
        if (empty($data['marks'])) {
            return redirect()->back()->with('error', 'No marks found for this student.');
        }

        $pdf = \PDF::loadView('results.student-marksheet-pdf', $data);
        $pdf->setPaper('A4', 'portrait');

        $filename = sprintf(
            'marksheet_%s_%s_%s.pdf',
            $student->student_id,
            str_replace(' ', '_', $exam->title),
            now()->format('Y-m-d')
        );

        return $pdf->download($filename);
    }

    /**
     * Calculate TU format GPA
     */
    private function calculateTUGPA($percentage)
    {
        if ($percentage >= 80) return 4.0;
        if ($percentage >= 70) return 3.6;
        if ($percentage >= 60) return 3.2;
        if ($percentage >= 50) return 2.8;
        if ($percentage >= 45) return 2.4;
        if ($percentage >= 40) return 2.0;
        return 0.0;
    }

    /**
     * Get letter grade based on percentage
     */
    private function getLetterGrade($percentage)
    {
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C+';
        if ($percentage >= 45) return 'C';
        if ($percentage >= 40) return 'D';
        return 'F';
    }

    /**
     * Determine result status
     */
    private function getResultStatus($marks, $overallPercentage)
    {
        // Check if student failed in any subject
        foreach ($marks as $mark) {
            if ($mark->percentage < 40) {
                return 'Fail';
            }
        }
        
        // Check overall percentage
        return $overallPercentage >= 40 ? 'Pass' : 'Fail';
    }

    /**
     * Get result data for an exam
     */
    private function getResultData(Exam $exam)
    {
        $exam->load(['class.course.faculty', 'academicYear', 'subjects']);

        $marks = Mark::with(['student.user', 'subject'])
            ->where('exam_id', $exam->id)
            ->get();

        $studentResults = $marks->groupBy('student_id')->map(function ($studentMarks) use ($exam) {
            $student = $studentMarks->first()->student;
            $totalObtained = $studentMarks->sum('obtained_marks');
            $totalMaximum = $studentMarks->sum('total_marks');
            $percentage = $totalMaximum > 0 ? ($totalObtained / $totalMaximum) * 100 : 0;
            
            return [
                'student' => $student,
                'marks' => $studentMarks,
                'total_obtained' => $totalObtained,
                'total_maximum' => $totalMaximum,
                'percentage' => $percentage,
                'gpa' => $this->calculateTUGPA($percentage),
                'letter_grade' => $this->getLetterGrade($percentage),
                'result_status' => $this->getResultStatus($studentMarks, $percentage),
            ];
        });

        $sortedResults = $studentResults->sortByDesc('percentage')->values();
        $sortedResults->each(function ($result, $index) {
            $result['rank'] = $index + 1;
        });

        $classStats = [
            'total_students' => $sortedResults->count(),
            'passed' => $sortedResults->where('result_status', 'Pass')->count(),
            'failed' => $sortedResults->where('result_status', 'Fail')->count(),
            'highest_percentage' => $sortedResults->max('percentage'),
            'lowest_percentage' => $sortedResults->min('percentage'),
            'average_percentage' => $sortedResults->avg('percentage'),
            'pass_rate' => $sortedResults->count() > 0 ? 
                ($sortedResults->where('result_status', 'Pass')->count() / $sortedResults->count()) * 100 : 0
        ];

        return compact('exam', 'sortedResults', 'classStats');
    }

    /**
     * Get student marksheet data
     */
    private function getStudentMarksheetData(Exam $exam, Student $student)
    {
        $marks = Mark::with(['subject', 'exam'])
            ->where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->get();

        $totalObtained = $marks->sum('obtained_marks');
        $totalMaximum = $marks->sum('total_marks');
        $overallPercentage = $totalMaximum > 0 ? ($totalObtained / $totalMaximum) * 100 : 0;

        return [
            'exam' => $exam,
            'student' => $student,
            'marks' => $marks,
            'totalObtained' => $totalObtained,
            'totalMaximum' => $totalMaximum,
            'overallPercentage' => $overallPercentage,
            'overallGrade' => $this->getLetterGrade($overallPercentage),
            'gpa' => $this->calculateTUGPA($overallPercentage),
            'resultStatus' => $this->getResultStatus($marks, $overallPercentage),
            'collegeSettings' => CollegeSetting::first(),
        ];
    }

    /**
     * Bulk result generation for multiple students
     */
    public function bulkGenerate(Request $request, Exam $exam)
    {
        $this->authorize('manage-exams');

        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id'
        ]);

        $students = Student::whereIn('id', $request->student_ids)->get();
        $results = [];

        foreach ($students as $student) {
            $data = $this->getStudentMarksheetData($exam, $student);
            if (!empty($data['marks'])) {
                $results[] = $data;
            }
        }

        if (empty($results)) {
            return redirect()->back()->with('error', 'No marks found for selected students.');
        }

        $pdf = \PDF::loadView('results.bulk-marksheets-pdf', compact('results', 'exam'));
        $pdf->setPaper('A4', 'portrait');

        $filename = sprintf(
            'bulk_marksheets_%s_%s.pdf',
            str_replace(' ', '_', $exam->title),
            now()->format('Y-m-d')
        );

        return $pdf->download($filename);
    }
}
