<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Student;
use App\Models\Mark;
use App\Models\CollegeSetting;
use App\Models\GradeScale;
use PDF;

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

        // Determine overall grade using exam's grading system
        $overallGrade = $exam->getGradeByPercentage($overallPercentage);

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

        // Get the grading system for this exam
        $gradingSystem = $exam->getEffectiveGradingSystem();

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
            'gradingSystem' => $gradingSystem,
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
        $overallGrade = $exam->getGradeByPercentage($overallPercentage);

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

        // Get the grading system for this exam
        $gradingSystem = $exam->getEffectiveGradingSystem();

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
            'gradingSystem' => $gradingSystem,
        ];

        $pdf = \PDF::loadView('marksheets.pdf', $data);
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
     * Preview all students' marksheets at once
     */
    public function bulkPreview(Request $request, Exam $exam)
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

        // Generate marksheet data for all students
        $marksheetData = [];
        $collegeSettings = CollegeSetting::getSettings();
        $gradingSystem = $exam->getEffectiveGradingSystem();

        // Get exam subjects configuration with theory and practical marks
        $examSubjects = $exam->examSubjects()->with('subject')->get();

        foreach ($studentsWithMarks as $student) {
            $marks = Mark::with(['subject', 'exam'])
                ->where('exam_id', $exam->id)
                ->where('student_id', $student->id)
                ->get();

            if ($marks->isNotEmpty()) {
                // Calculate totals and grades
                $totalObtained = $marks->sum('obtained_marks');
                $totalMaximum = $marks->sum('total_marks');
                $overallPercentage = $totalMaximum > 0 ? ($totalObtained / $totalMaximum) * 100 : 0;

                // Get grade using the exam's grading system
                $overallGrade = $exam->getGradeByPercentage($overallPercentage);
                $gpa = $overallGrade ? $overallGrade->grade_point : 0;

                // Determine result status
                $passPercentage = CollegeSetting::getPassPercentage();
                $resultStatus = $overallPercentage >= $passPercentage ? 'PASS' : 'FAIL';

                $marksheetData[] = [
                    'student' => $student,
                    'marks' => $marks,
                    'totalObtained' => $totalObtained,
                    'totalMaximum' => $totalMaximum,
                    'overallPercentage' => $overallPercentage,
                    'overallGrade' => $overallGrade,
                    'gpa' => $gpa,
                    'resultStatus' => $resultStatus,
                ];
            }
        }

        return view('marksheets.bulk-preview', compact('exam', 'marksheetData', 'collegeSettings', 'gradingSystem', 'examSubjects'));
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

    /**
     * Generate Nepali format marksheet for a student
     */
    public function generateNepaliFormat(Request $request, Exam $exam, Student $student)
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

        // Get exam subjects configuration with theory and practical marks
        $examSubjects = $exam->examSubjects()->with('subject')->get();

        // Get selected grading system or use exam's default
        $gradingSystemId = $request->get('grading_system_id');
        $gradingSystem = $gradingSystemId ?
            GradingSystem::find($gradingSystemId) :
            $exam->getEffectiveGradingSystem();

        // Recalculate grades using selected grading system
        $totalObtained = 0;
        $totalMaximum = 0;
        $hasFailure = false;

        foreach ($marks as $mark) {
            $totalObtained += $mark->obtained_marks;
            $totalMaximum += $mark->total_marks;

            // Check for theory/practical failures
            if ($mark->hasTheoryPracticalFailure()) {
                $hasFailure = true;
            }
        }

        $overallPercentage = $totalMaximum > 0 ? ($totalObtained / $totalMaximum) * 100 : 0;

        // Determine overall grade and result status
        if ($hasFailure) {
            $overallGrade = $gradingSystem->gradeScales()->where('grade_letter', 'N/G')->first();
            $gpa = 0;
            $resultStatus = 'FAIL';
        } else {
            $overallGrade = $gradingSystem->getGradeByPercentage($overallPercentage);
            $gpa = $overallGrade ? $overallGrade->grade_point : 0;
            $passPercentage = CollegeSetting::getPassPercentage();
            $resultStatus = $overallPercentage >= $passPercentage ? 'PASS' : 'FAIL';
        }

        // Get college settings
        $collegeSettings = CollegeSetting::getSettings();

        $data = [
            'exam' => $exam,
            'student' => $student,
            'marks' => $marks,
            'examSubjects' => $examSubjects,
            'totalObtained' => $totalObtained,
            'totalMaximum' => $totalMaximum,
            'overallPercentage' => $overallPercentage,
            'overallGrade' => $overallGrade,
            'gpa' => $gpa,
            'resultStatus' => $resultStatus,
            'collegeSettings' => $collegeSettings,
            'gradingSystem' => $gradingSystem,
            'hasFailure' => $hasFailure,
        ];

        return view('marksheets.nepali-format', $data);
    }

    /**
     * Generate PDF marksheet in Nepali format
     */
    public function generateNepaliFormatPdf(Request $request, Exam $exam, Student $student)
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

        // Get exam subjects configuration with theory and practical marks
        $examSubjects = $exam->examSubjects()->with('subject')->get();

        // Get selected grading system or use exam's default
        $gradingSystemId = $request->get('grading_system_id');
        $gradingSystem = $gradingSystemId ?
            GradingSystem::find($gradingSystemId) :
            $exam->getEffectiveGradingSystem();

        // Recalculate grades using selected grading system
        $totalObtained = 0;
        $totalMaximum = 0;
        $hasFailure = false;

        foreach ($marks as $mark) {
            $totalObtained += $mark->obtained_marks;
            $totalMaximum += $mark->total_marks;

            // Check for theory/practical failures
            if ($mark->hasTheoryPracticalFailure()) {
                $hasFailure = true;
            }
        }

        $overallPercentage = $totalMaximum > 0 ? ($totalObtained / $totalMaximum) * 100 : 0;

        // Determine overall grade and result status
        if ($hasFailure) {
            $overallGrade = $gradingSystem->gradeScales()->where('grade_letter', 'N/G')->first();
            $gpa = 0;
            $resultStatus = 'FAIL';
        } else {
            $overallGrade = $gradingSystem->getGradeByPercentage($overallPercentage);
            $gpa = $overallGrade ? $overallGrade->grade_point : 0;
            $passPercentage = CollegeSetting::getPassPercentage();
            $resultStatus = $overallPercentage >= $passPercentage ? 'PASS' : 'FAIL';
        }

        // Get college settings
        $collegeSettings = CollegeSetting::getSettings();

        $data = [
            'exam' => $exam,
            'student' => $student,
            'marks' => $marks,
            'examSubjects' => $examSubjects,
            'totalObtained' => $totalObtained,
            'totalMaximum' => $totalMaximum,
            'overallPercentage' => $overallPercentage,
            'overallGrade' => $overallGrade,
            'gpa' => $gpa,
            'resultStatus' => $resultStatus,
            'collegeSettings' => $collegeSettings,
            'gradingSystem' => $gradingSystem,
            'hasFailure' => $hasFailure,
        ];

        $pdf = \PDF::loadView('marksheets.nepali-format', $data);
        $pdf->setPaper('A4', 'portrait');

        $filename = sprintf(
            'progress_report_%s_%s_%s_%s.pdf',
            $student->student_id,
            str_replace(' ', '_', $exam->title),
            $gradingSystem->code,
            now()->format('Y-m-d')
        );

        return $pdf->download($filename);
    }

    /**
     * Bulk preview in Nepali format for all students in an exam
     */
    public function bulkNepaliPreview(Request $request, Exam $exam)
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

        // Generate marksheet data for all students
        $marksheetData = [];
        $collegeSettings = CollegeSetting::getSettings();
        $gradingSystem = $exam->getEffectiveGradingSystem();

        // Get exam subjects configuration with theory and practical marks
        $examSubjects = $exam->examSubjects()->with('subject')->get();

        foreach ($studentsWithMarks as $student) {
            $marks = Mark::with(['subject', 'exam'])
                ->where('exam_id', $exam->id)
                ->where('student_id', $student->id)
                ->get();

            if ($marks->isNotEmpty()) {
                // Calculate totals and grades
                $totalObtained = $marks->sum('obtained_marks');
                $totalMaximum = $marks->sum('total_marks');
                $overallPercentage = $totalMaximum > 0 ? ($totalObtained / $totalMaximum) * 100 : 0;

                // Get grade using the exam's grading system
                $overallGrade = $exam->getGradeByPercentage($overallPercentage);
                $gpa = $overallGrade ? $overallGrade->grade_point : 0;

                // Determine result status
                $passPercentage = CollegeSetting::getPassPercentage();
                $resultStatus = $overallPercentage >= $passPercentage ? 'PASS' : 'FAIL';

                $marksheetData[] = [
                    'student' => $student,
                    'marks' => $marks,
                    'totalObtained' => $totalObtained,
                    'totalMaximum' => $totalMaximum,
                    'overallPercentage' => $overallPercentage,
                    'overallGrade' => $overallGrade,
                    'gpa' => $gpa,
                    'resultStatus' => $resultStatus,
                ];
            }
        }

        return view('marksheets.nepali-bulk-preview', compact('exam', 'marksheetData', 'collegeSettings', 'gradingSystem', 'examSubjects'));
    }
}
