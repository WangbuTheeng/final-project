<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Student;
use App\Models\Mark;
use App\Models\CollegeSetting;
use App\Models\GradeScale;
use Barryvdh\DomPDF\Facade\Pdf;

class MarksheetController extends Controller
{
    /**
     * Display marksheet search form
     */
    public function index()
    {
        $this->authorize('manage-exams');

        // Only show exams that have marks entered
        $exams = Exam::with(['class.course.faculty', 'academicYear'])
            ->where('status', 'completed')
            ->whereHas('marks') // Only exams with marks
            ->orderBy('exam_date', 'desc')
            ->get();

        return view('marksheets.index', compact('exams'));
    }

    /**
     * Generate marksheet for a student in an exam
     */
    public function generate(Request $request, Exam $exam, Student $student)
    {
        $this->authorize('manage-exams');

        // Get student marks for this exam
        $marks = Mark::with(['subject', 'exam'])
            ->where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->get();

        if ($marks->isEmpty()) {
            return redirect()->back()->with('error', 'No marks found for this student in the selected exam.');
        }

        // Calculate totals
        $totalObtained = $marks->sum('obtained_marks');
        $totalMaximum = $marks->sum('total_marks');
        $overallPercentage = $totalMaximum > 0 ? ($totalObtained / $totalMaximum) * 100 : 0;

        // Determine overall grade
        $overallGrade = GradeScale::getGradeByPercentage($overallPercentage);

        // Calculate GPA (credit-weighted)
        $totalCredits = 0;
        $totalGradePoints = 0;

        foreach ($marks as $mark) {
            $creditWeight = $mark->subject->credit_weight ?? 3; // Default 3 credits
            $totalCredits += $creditWeight;
            $totalGradePoints += ($mark->grade_point ?? 0) * $creditWeight;
        }

        $gpa = $totalCredits > 0 ? $totalGradePoints / $totalCredits : 0;

        // Get college settings
        $collegeSettings = CollegeSetting::getSettings();

        // Determine result status
        $passPercentage = $collegeSettings->pass_percentage;
        $resultStatus = $overallPercentage >= $passPercentage ? 'PASS' : 'FAIL';

        // Check for distinction
        if ($overallPercentage >= 75) {
            $resultStatus = 'DISTINCTION';
        } elseif ($overallPercentage >= 60) {
            $resultStatus = 'FIRST DIVISION';
        } elseif ($overallPercentage >= 50) {
            $resultStatus = 'SECOND DIVISION';
        } elseif ($overallPercentage >= $passPercentage) {
            $resultStatus = 'THIRD DIVISION';
        }

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

        return view('marksheets.template', $data);
    }

    /**
     * Generate PDF marksheet
     */
    public function generatePdf(Request $request, Exam $exam, Student $student)
    {
        $this->authorize('manage-exams');

        // Get the same data as the preview
        $marks = Mark::with(['subject', 'exam'])
            ->where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->get();

        if ($marks->isEmpty()) {
            return redirect()->back()->with('error', 'No marks found for this student in the selected exam.');
        }

        // Calculate totals (same logic as generate method)
        $totalObtained = $marks->sum('obtained_marks');
        $totalMaximum = $marks->sum('total_marks');
        $overallPercentage = $totalMaximum > 0 ? ($totalObtained / $totalMaximum) * 100 : 0;
        $overallGrade = GradeScale::getGradeByPercentage($overallPercentage);

        // Calculate GPA
        $totalCredits = 0;
        $totalGradePoints = 0;

        foreach ($marks as $mark) {
            $creditWeight = $mark->subject->credit_weight ?? 3;
            $totalCredits += $creditWeight;
            $totalGradePoints += ($mark->grade_point ?? 0) * $creditWeight;
        }

        $gpa = $totalCredits > 0 ? $totalGradePoints / $totalCredits : 0;
        $collegeSettings = CollegeSetting::getSettings();
        $passPercentage = $collegeSettings->pass_percentage;
        $resultStatus = $overallPercentage >= $passPercentage ? 'PASS' : 'FAIL';

        if ($overallPercentage >= 75) {
            $resultStatus = 'DISTINCTION';
        } elseif ($overallPercentage >= 60) {
            $resultStatus = 'FIRST DIVISION';
        } elseif ($overallPercentage >= 50) {
            $resultStatus = 'SECOND DIVISION';
        } elseif ($overallPercentage >= $passPercentage) {
            $resultStatus = 'THIRD DIVISION';
        }

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

        $pdf = Pdf::loadView('marksheets.pdf', $data);
        $pdf->setPaper('A4', 'portrait');

        $filename = sprintf(
            'marksheet_%s_%s_%s.pdf',
            $student->student_id,
            $exam->title,
            now()->format('Y-m-d')
        );

        return $pdf->download($filename);
    }

    /**
     * Generate bulk marksheets for all students in an exam
     */
    public function generateBulk(Request $request, Exam $exam)
    {
        $this->authorize('manage-exams');

        // Get all students who have marks in this exam
        $studentsWithMarks = Mark::where('exam_id', $exam->id)
            ->with('student.user')
            ->distinct('student_id')
            ->get()
            ->pluck('student')
            ->unique('id');

        if ($studentsWithMarks->isEmpty()) {
            return redirect()->back()->with('error', 'No marks found for any students in this exam.');
        }

        return view('marksheets.bulk', compact('exam', 'studentsWithMarks'));
    }

    /**
     * Get students by exam (AJAX)
     */
    public function getStudentsByExam(Request $request)
    {
        $examId = $request->input('exam_id');

        if (!$examId) {
            return response()->json([]);
        }

        $students = Mark::where('exam_id', $examId)
            ->with('student.user')
            ->distinct('student_id')
            ->get()
            ->pluck('student')
            ->unique('id')
            ->values();

        return response()->json($students);
    }
}
