<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExamRequest;
use App\Http\Requests\UpdateExamRequest;
use App\Models\Exam;
use App\Models\ClassSection;
use App\Models\AcademicYear;
use App\Services\ExamService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

/**
 * Class ExamController
 *
 * Handles exam management operations following Laravel best practices.
 *
 * @package App\Http\Controllers
 */
class ExamController extends Controller
{
    /**
     * The exam service instance.
     *
     * @var ExamService
     */
    protected ExamService $examService;

    /**
     * Create a new controller instance.
     *
     * @param ExamService $examService
     * @return void
     */
    public function __construct(ExamService $examService)
    {
        $this->middleware('auth');
        $this->examService = $examService;
    }

    /**
     * Display a listing of the exams.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $this->authorize('view-exams');

        // Get filter parameters
        $filters = $request->only([
            'exam_type', 'semester', 'status', 'academic_year_id', 'class_id', 'search'
        ]);

        // Get paginated exams
        $exams = $this->examService->getPaginatedExams($filters, 15);

        // Get statistics
        $statistics = $this->examService->getExamStatistics($filters);

        // Get filter options
        $examTypes = Exam::getExamTypes();
        $semesters = Exam::getSemesters();
        $statuses = Exam::getStatuses();
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $classes = ClassSection::with('course')->get();

        return view('exams.index', compact(
            'exams',
            'statistics',
            'examTypes',
            'semesters',
            'statuses',
            'academicYears',
            'classes',
            'filters'
        ));
    }

    /**
     * Show the form for creating a new exam.
     *
     * @return View
     */
    public function create(): View
    {
        $this->authorize('create-exams');

        // Get form options
        $examTypes = Exam::getExamTypes();
        $semesters = Exam::getSemesters();
        $academicYears = AcademicYear::active()->get();
        $classes = ClassSection::with(['course', 'instructor.user'])->get();

        return view('exams.create', compact(
            'examTypes',
            'semesters',
            'academicYears',
            'classes'
        ));
    }

    /**
     * Store a newly created exam in storage.
     *
     * @param StoreExamRequest $request
     * @return RedirectResponse
     */
    public function store(StoreExamRequest $request): RedirectResponse
    {
        try {
            $exam = $this->examService->createExam($request->validatedWithDefaults());

            return redirect()->route('exams.index')
                ->with('success', "Exam '{$exam->title}' created successfully.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating exam: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified exam.
     *
     * @param Exam $exam
     * @return View
     */
    public function show(Exam $exam): View
    {
        $this->authorize('view-exams');

        // Load relationships
        $exam->load([
            'classSection.course',
            'academicYear',
            'creator',
            'grades.student.user'
        ]);

        // Get exam students
        $students = $this->examService->getExamStudents($exam);

        // Get exam statistics
        $gradeStats = $exam->grades()
            ->selectRaw('
                COUNT(*) as total_graded,
                AVG(score) as average_score,
                MAX(score) as highest_score,
                MIN(score) as lowest_score,
                SUM(CASE WHEN letter_grade != "F" THEN 1 ELSE 0 END) as passed_count
            ')
            ->first();

        return view('exams.show', compact('exam', 'students', 'gradeStats'));
    }

    /**
     * Show the form for editing the specified exam.
     *
     * @param Exam $exam
     * @return View|RedirectResponse
     */
    public function edit(Exam $exam)
    {
        $this->authorize('edit-exams');

        // Check if exam can be edited
        if (!$this->examService->canEditExam($exam)) {
            return redirect()->route('exams.show', $exam)
                ->with('error', 'This exam cannot be edited because it has associated grades or is completed.');
        }

        // Get form options
        $examTypes = Exam::getExamTypes();
        $semesters = Exam::getSemesters();
        $statuses = Exam::getStatuses();
        $academicYears = AcademicYear::active()->get();
        $classes = ClassSection::with(['course', 'instructor.user'])->get();

        return view('exams.edit', compact(
            'exam',
            'examTypes',
            'semesters',
            'statuses',
            'academicYears',
            'classes'
        ));
    }

    /**
     * Update the specified exam in storage.
     *
     * @param UpdateExamRequest $request
     * @param Exam $exam
     * @return RedirectResponse
     */
    public function update(UpdateExamRequest $request, Exam $exam): RedirectResponse
    {
        // Check if exam can be edited
        if (!$this->examService->canEditExam($exam)) {
            return redirect()->route('exams.show', $exam)
                ->with('error', 'This exam cannot be edited because it has associated grades or is completed.');
        }

        try {
            $this->examService->updateExam($exam, $request->validated());

            return redirect()->route('exams.show', $exam)
                ->with('success', "Exam '{$exam->title}' updated successfully.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating exam: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified exam from storage.
     *
     * @param Exam $exam
     * @return RedirectResponse
     */
    public function destroy(Exam $exam): RedirectResponse
    {
        $this->authorize('delete-exams');

        try {
            $this->examService->deleteExam($exam);

            return redirect()->route('exams.index')
                ->with('success', 'Exam deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Start an exam (AJAX endpoint).
     *
     * @param Exam $exam
     * @return JsonResponse
     */
    public function start(Exam $exam): JsonResponse
    {
        $this->authorize('edit-exams');

        try {
            $this->examService->startExam($exam);

            return response()->json([
                'success' => true,
                'message' => 'Exam started successfully.',
                'status' => $exam->status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Complete an exam (AJAX endpoint).
     *
     * @param Exam $exam
     * @return JsonResponse
     */
    public function complete(Exam $exam): JsonResponse
    {
        $this->authorize('edit-exams');

        try {
            $this->examService->completeExam($exam);

            return response()->json([
                'success' => true,
                'message' => 'Exam completed successfully.',
                'status' => $exam->status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Cancel an exam (AJAX endpoint).
     *
     * @param Exam $exam
     * @return JsonResponse
     */
    public function cancel(Exam $exam): JsonResponse
    {
        $this->authorize('edit-exams');

        try {
            $this->examService->cancelExam($exam);

            return response()->json([
                'success' => true,
                'message' => 'Exam cancelled successfully.',
                'status' => $exam->status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get upcoming exams (AJAX endpoint).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function upcoming(Request $request): JsonResponse
    {
        $this->authorize('view-exams');

        $classId = $request->get('class_id');
        $limit = $request->get('limit', 10);

        $upcomingExams = $this->examService->getUpcomingExams($classId, $limit);

        return response()->json([
            'success' => true,
            'data' => $upcomingExams
        ]);
    }
}
} 