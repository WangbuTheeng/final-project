<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Exam;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\AcademicYear;
use App\Services\GradeService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

/**
 * Class GradeController
 * 
 * Handles grade/result management operations.
 * 
 * @package App\Http\Controllers
 */
class GradeController extends Controller
{
    /**
     * The grade service instance.
     *
     * @var GradeService
     */
    protected GradeService $gradeService;

    /**
     * Create a new controller instance.
     *
     * @param GradeService $gradeService
     * @return void
     */
    public function __construct(GradeService $gradeService)
    {
        $this->middleware('auth');
        $this->gradeService = $gradeService;
    }

    /**
     * Display a listing of grades.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $this->authorize('view-grades');

        // Get filter parameters
        $filters = $request->only([
            'academic_year_id', 'semester', 'grade_type', 'class_id', 'student_id', 'search'
        ]);

        // Get paginated grades
        $grades = $this->gradeService->getPaginatedGrades($filters, 20);
        
        // Get filter options
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $gradeTypes = Grade::getGradeTypes();
        $semesters = [
            Grade::SEMESTER_FIRST => 'First Semester',
            Grade::SEMESTER_SECOND => 'Second Semester'
        ];

        return view('grades.index', compact(
            'grades',
            'academicYears',
            'gradeTypes',
            'semesters',
            'filters'
        ));
    }

    /**
     * Show the form for entering grades for an exam.
     *
     * @param Exam $exam
     * @return View
     */
    public function createForExam(Exam $exam): View
    {
        $this->authorize('create-grades');
        
        // Get students enrolled in the exam's class
        $students = $this->gradeService->getExamStudents($exam);
        
        // Get existing grades for this exam
        $existingGrades = Grade::where('exam_id', $exam->id)
            ->pluck('student_id')
            ->toArray();

        return view('grades.create-for-exam', compact('exam', 'students', 'existingGrades'));
    }

    /**
     * Store grades for an exam.
     *
     * @param Request $request
     * @param Exam $exam
     * @return RedirectResponse
     */
    public function storeForExam(Request $request, Exam $exam): RedirectResponse
    {
        $this->authorize('create-grades');

        $request->validate([
            'grades' => 'required|array',
            'grades.*.student_id' => 'required|exists:students,id',
            'grades.*.score' => 'required|numeric|min:0|max:' . $exam->total_marks,
            'grades.*.remarks' => 'nullable|string|max:500',
        ]);

        try {
            $gradesData = collect($request->grades)->map(function ($gradeData) use ($exam) {
                return array_merge($gradeData, [
                    'exam_id' => $exam->id,
                    'academic_year_id' => $exam->academic_year_id,
                    'semester' => $exam->semester,
                    'grade_type' => Grade::TYPE_EXAM,
                    'max_score' => $exam->total_marks,
                    'graded_by' => auth()->id(),
                    'graded_at' => now(),
                ]);
            })->toArray();

            $this->gradeService->createGradesForExam($exam, $gradesData);

            return redirect()->route('exams.show', $exam)
                ->with('success', 'Grades entered successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error entering grades: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display student results.
     *
     * @param Student $student
     * @param Request $request
     * @return View
     */
    public function studentResults(Student $student, Request $request): View
    {
        $this->authorize('view-grades');

        $academicYearId = $request->get('academic_year_id');
        $semester = $request->get('semester');

        // Get student's grades
        $results = $this->gradeService->getStudentResults($student, $academicYearId, $semester);
        
        // Get academic years for filter
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        
        // Calculate GPA for the selected period
        $gpa = $this->gradeService->calculateGPA($student, $academicYearId, $semester);

        return view('grades.student-results', compact(
            'student',
            'results',
            'academicYears',
            'gpa',
            'academicYearId',
            'semester'
        ));
    }

    /**
     * Display class results for an exam.
     *
     * @param Exam $exam
     * @return View
     */
    public function examResults(Exam $exam): View
    {
        $this->authorize('view-grades');

        // Get exam results with statistics
        $results = $this->gradeService->getExamResults($exam);
        $statistics = $this->gradeService->getExamStatistics($exam);

        return view('grades.exam-results', compact('exam', 'results', 'statistics'));
    }

    /**
     * Show the form for editing a grade.
     *
     * @param Grade $grade
     * @return View
     */
    public function edit(Grade $grade): View
    {
        $this->authorize('edit-grades');

        $grade->load(['student.user', 'exam', 'enrollment.class.course']);

        return view('grades.edit', compact('grade'));
    }

    /**
     * Update the specified grade.
     *
     * @param Request $request
     * @param Grade $grade
     * @return RedirectResponse
     */
    public function update(Request $request, Grade $grade): RedirectResponse
    {
        $this->authorize('edit-grades');

        $request->validate([
            'score' => 'required|numeric|min:0|max:' . $grade->max_score,
            'remarks' => 'nullable|string|max:500',
        ]);

        try {
            $this->gradeService->updateGrade($grade, $request->only(['score', 'remarks']));

            return redirect()->back()
                ->with('success', 'Grade updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating grade: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified grade.
     *
     * @param Grade $grade
     * @return RedirectResponse
     */
    public function destroy(Grade $grade): RedirectResponse
    {
        $this->authorize('delete-grades');

        try {
            $this->gradeService->deleteGrade($grade);

            return redirect()->back()
                ->with('success', 'Grade deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting grade: ' . $e->getMessage());
        }
    }

    /**
     * Generate result sheet for a class.
     *
     * @param Request $request
     * @return View
     */
    public function resultSheet(Request $request): View
    {
        $this->authorize('view-grades');

        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester' => 'required|in:first,second',
        ]);

        $classId = $request->get('class_id');
        $academicYearId = $request->get('academic_year_id');
        $semester = $request->get('semester');

        // Get result sheet data
        $resultSheet = $this->gradeService->generateResultSheet($classId, $academicYearId, $semester);

        return view('grades.result-sheet', compact('resultSheet'));
    }

    /**
     * Calculate and update final grades for enrollments.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function calculateFinalGrades(Request $request): JsonResponse
    {
        $this->authorize('edit-grades');

        $request->validate([
            'enrollment_ids' => 'required|array',
            'enrollment_ids.*' => 'exists:enrollments,id',
        ]);

        try {
            $updated = $this->gradeService->calculateFinalGrades($request->enrollment_ids);

            return response()->json([
                'success' => true,
                'message' => "Final grades calculated for {$updated} enrollments.",
                'updated_count' => $updated
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error calculating final grades: ' . $e->getMessage()
            ], 400);
        }
    }
}
