<?php

namespace App\Http\Controllers;

use App\Models\Examination;
use App\Models\ExamEnrollment;
use App\Models\ExamResult;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ExamResultController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display exam results for an examination
     */
    public function index(Examination $examination)
    {
        $examination->load([
            'subject',
            'class',
            'academicYear',
            'examEnrollments.student.user',
            'examEnrollments.examResult'
        ]);

        $enrollments = $examination->examEnrollments()
                                 ->with(['student.user', 'examResult'])
                                 ->orderBy('exam_roll_number')
                                 ->get();

        return view('exam-results.index', compact('examination', 'enrollments'));
    }

    /**
     * Show marks entry form
     */
    public function create(Examination $examination)
    {
        if ($examination->status === 'scheduled') {
            return redirect()->route('examinations.show', $examination)
                           ->with('error', 'Cannot enter marks for scheduled examination. Please mark it as ongoing or completed first.');
        }

        $enrollments = $examination->examEnrollments()
                                 ->with(['student.user', 'examResult'])
                                 ->where('attendance_status', 'present')
                                 ->orderBy('exam_roll_number')
                                 ->get();

        return view('exam-results.create', compact('examination', 'enrollments'));
    }

    /**
     * Store exam results
     */
    public function store(Request $request, Examination $examination)
    {
        $request->validate([
            'results' => 'required|array',
            'results.*.student_id' => 'required|exists:students,id',
            'results.*.exam_enrollment_id' => 'required|exists:exam_enrollments,id',
            'results.*.internal_marks' => 'nullable|numeric|min:0|max:' . $examination->total_marks,
            'results.*.final_marks' => 'nullable|numeric|min:0|max:' . $examination->total_marks,
            'results.*.practical_marks' => 'nullable|numeric|min:0|max:' . $examination->total_marks,
            'results.*.assignment_marks' => 'nullable|numeric|min:0|max:' . $examination->total_marks,
            'results.*.presentation_marks' => 'nullable|numeric|min:0|max:' . $examination->total_marks,
            'results.*.attendance_marks' => 'nullable|numeric|min:0|max:10',
            'results.*.examiner_remarks' => 'nullable|string|max:500'
        ]);

        DB::transaction(function () use ($request, $examination) {
            foreach ($request->results as $resultData) {
                // Check if result already exists
                $existingResult = ExamResult::where('examination_id', $examination->id)
                                          ->where('student_id', $resultData['student_id'])
                                          ->first();

                if ($existingResult) {
                    // Update existing result
                    $existingResult->update([
                        'internal_marks' => $resultData['internal_marks'] ?? null,
                        'final_marks' => $resultData['final_marks'] ?? null,
                        'practical_marks' => $resultData['practical_marks'] ?? null,
                        'assignment_marks' => $resultData['assignment_marks'] ?? null,
                        'presentation_marks' => $resultData['presentation_marks'] ?? null,
                        'attendance_marks' => $resultData['attendance_marks'] ?? null,
                        'examiner_remarks' => $resultData['examiner_remarks'] ?? null,
                        'verification_status' => 'pending'
                    ]);

                    $existingResult->calculateResult();
                } else {
                    // Create new result
                    $result = ExamResult::create([
                        'examination_id' => $examination->id,
                        'student_id' => $resultData['student_id'],
                        'exam_enrollment_id' => $resultData['exam_enrollment_id'],
                        'internal_marks' => $resultData['internal_marks'] ?? null,
                        'final_marks' => $resultData['final_marks'] ?? null,
                        'practical_marks' => $resultData['practical_marks'] ?? null,
                        'assignment_marks' => $resultData['assignment_marks'] ?? null,
                        'presentation_marks' => $resultData['presentation_marks'] ?? null,
                        'attendance_marks' => $resultData['attendance_marks'] ?? null,
                        'examiner_remarks' => $resultData['examiner_remarks'] ?? null,
                        'verification_status' => 'pending',
                        'attempt_number' => 1
                    ]);

                    $result->calculateResult();
                }
            }

            // Update examination status if all results are entered
            if ($examination->status !== 'completed') {
                $examination->update(['status' => 'completed']);
            }
        });

        return redirect()->route('exam-results.index', $examination)
                        ->with('success', 'Exam results saved successfully.');
    }

    /**
     * Show individual result for editing
     */
    public function edit(Examination $examination, ExamResult $examResult)
    {
        if ($examResult->verification_status === 'verified') {
            return redirect()->route('exam-results.index', $examination)
                           ->with('error', 'Cannot edit verified results.');
        }

        $examResult->load(['student.user', 'examEnrollment']);

        return view('exam-results.edit', compact('examination', 'examResult'));
    }

    /**
     * Update individual result
     */
    public function update(Request $request, Examination $examination, ExamResult $examResult)
    {
        if ($examResult->verification_status === 'verified') {
            return redirect()->route('exam-results.index', $examination)
                           ->with('error', 'Cannot update verified results.');
        }

        $request->validate([
            'internal_marks' => 'nullable|numeric|min:0|max:' . $examination->total_marks,
            'final_marks' => 'nullable|numeric|min:0|max:' . $examination->total_marks,
            'practical_marks' => 'nullable|numeric|min:0|max:' . $examination->total_marks,
            'assignment_marks' => 'nullable|numeric|min:0|max:' . $examination->total_marks,
            'presentation_marks' => 'nullable|numeric|min:0|max:' . $examination->total_marks,
            'attendance_marks' => 'nullable|numeric|min:0|max:10',
            'examiner_remarks' => 'nullable|string|max:500'
        ]);

        $examResult->update([
            'internal_marks' => $request->internal_marks,
            'final_marks' => $request->final_marks,
            'practical_marks' => $request->practical_marks,
            'assignment_marks' => $request->assignment_marks,
            'presentation_marks' => $request->presentation_marks,
            'attendance_marks' => $request->attendance_marks,
            'examiner_remarks' => $request->examiner_remarks,
            'verification_status' => 'pending'
        ]);

        $examResult->calculateResult();

        return redirect()->route('exam-results.index', $examination)
                        ->with('success', 'Result updated successfully.');
    }

    /**
     * Verify results
     */
    public function verify(Request $request, Examination $examination)
    {
        $request->validate([
            'result_ids' => 'required|array',
            'result_ids.*' => 'exists:exam_results,id'
        ]);

        ExamResult::whereIn('id', $request->result_ids)
                 ->where('examination_id', $examination->id)
                 ->update([
                     'verification_status' => 'verified',
                     'verified_by' => Auth::id(),
                     'verified_at' => now()
                 ]);

        return redirect()->route('exam-results.index', $examination)
                        ->with('success', 'Results verified successfully.');
    }

    /**
     * Publish results
     */
    public function publish(Request $request, Examination $examination)
    {
        $unverifiedCount = ExamResult::where('examination_id', $examination->id)
                                   ->where('verification_status', '!=', 'verified')
                                   ->count();

        if ($unverifiedCount > 0) {
            return redirect()->route('exam-results.index', $examination)
                           ->with('error', "Cannot publish results. {$unverifiedCount} results are still unverified.");
        }

        ExamResult::where('examination_id', $examination->id)
                 ->whereNull('result_published_at')
                 ->update(['result_published_at' => now()]);

        return redirect()->route('exam-results.index', $examination)
                        ->with('success', 'Results published successfully.');
    }

    /**
     * Generate result report
     */
    public function report(Examination $examination)
    {
        $examination->load([
            'subject',
            'class',
            'academicYear',
            'examResults.student.user',
            'examResults.examEnrollment'
        ]);

        $results = $examination->examResults()
                             ->with(['student.user', 'examEnrollment'])
                             ->where('verification_status', 'verified')
                             ->orderBy('percentage', 'desc')
                             ->get();

        $stats = [
            'total_students' => $results->count(),
            'passed' => $results->where('result_status', 'pass')->count(),
            'failed' => $results->where('result_status', 'fail')->count(),
            'absent' => $results->where('result_status', 'absent')->count(),
            'average_percentage' => $results->avg('percentage'),
            'highest_percentage' => $results->max('percentage'),
            'lowest_percentage' => $results->min('percentage'),
            'grade_distribution' => $results->groupBy('grade_letter')->map->count()
        ];

        return view('exam-results.report', compact('examination', 'results', 'stats'));
    }

    /**
     * Student result view
     */
    public function studentResult(Request $request)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            return redirect()->back()->with('error', 'Student profile not found.');
        }

        $results = ExamResult::where('student_id', $student->id)
                           ->with(['examination.subject', 'examination.class', 'examination.academicYear'])
                           ->where('verification_status', 'verified')
                           ->whereNotNull('result_published_at')
                           ->orderBy('created_at', 'desc')
                           ->paginate(10);

        return view('exam-results.student', compact('results', 'student'));
    }
}
