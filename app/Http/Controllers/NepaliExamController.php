<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamType;
use App\Models\ExamComponent;
use App\Models\AcademicYear;
use App\Models\ClassSection;
use App\Models\Course;
use App\Models\Faculty;
use App\Models\Subject;
use App\Models\GradingSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class NepaliExamController extends Controller
{
    /**
     * Display a listing of exams with Nepali system enhancements
     */
    public function index(Request $request)
    {
        $query = Exam::with([
            'examType', 
            'class.course', 
            'academicYear', 
            'gradingSystem',
            'subjects'
        ]);

        // Filter by education level
        if ($request->filled('education_level')) {
            $query->where('education_level', $request->education_level);
        }

        // Filter by assessment category
        if ($request->filled('assessment_category')) {
            $query->where('assessment_category', $request->assessment_category);
        }

        // Filter by exam type
        if ($request->filled('exam_type_id')) {
            $query->where('exam_type_id', $request->exam_type_id);
        }

        // Filter by stream (for +2)
        if ($request->filled('stream')) {
            $query->where('stream', $request->stream);
        }

        // Filter by program (for Bachelor's)
        if ($request->filled('program_code')) {
            $query->where('program_code', $request->program_code);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('examType', function ($et) use ($search) {
                      $et->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $exams = $query->latest()->paginate(15);

        // Get filter options
        $examTypes = ExamType::active()->ordered()->get();
        $academicYears = AcademicYear::active()->get();
        $gradingSystems = GradingSystem::active()->get();

        return view('nepali-exams.index', compact(
            'exams', 
            'examTypes', 
            'academicYears', 
            'gradingSystems'
        ));
    }

    /**
     * Show the form for creating a new exam with Nepali system
     */
    public function create()
    {
        $academicYears = AcademicYear::active()->get();
        $faculties = Faculty::active()->get();
        $gradingSystems = GradingSystem::active()->get();
        
        // Get exam types for both levels
        $plusTwoExamTypes = ExamType::getPlusTwoTypes();
        $bachelorsExamTypes = ExamType::getBachelorsTypes();
        
        // Get exam components for both levels
        $plusTwoComponents = ExamComponent::getPlusTwoComponents();
        $bachelorsComponents = ExamComponent::getBachelorsComponents();

        return view('nepali-exams.create', compact(
            'academicYears',
            'faculties',
            'gradingSystems',
            'plusTwoExamTypes',
            'bachelorsExamTypes',
            'plusTwoComponents',
            'bachelorsComponents'
        ));
    }

    /**
     * Store a newly created exam with Nepali system
     */
    public function store(Request $request)
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'education_level' => ['required', 'in:plus_two,bachelors'],
            'exam_type_id' => ['required', 'exists:exam_types,id'],
            'class_id' => ['required', 'exists:classes,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'exam_date' => ['required', 'date', 'after:today'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'duration_minutes' => ['required', 'integer', 'min:30', 'max:480'],
            'assessment_category' => ['required', 'in:internal,external,both'],
            'weightage_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'venue' => ['nullable', 'string', 'max:255'],
            'instructions' => ['nullable', 'string'],
            'grading_system_id' => ['nullable', 'exists:grading_systems,id'],
            'auto_load_subjects' => ['boolean'],
            'auto_enroll_students' => ['boolean'],
            'minimum_pass_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'overall_pass_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'requires_attendance' => ['boolean'],
            'minimum_attendance_percentage' => ['nullable', 'numeric', 'min:0', 'max:100']
        ];

        // Add conditional validation based on education level
        if ($request->education_level === 'plus_two') {
            $rules['stream'] = ['required', 'in:Science,Management,Humanities'];
        } else {
            $rules['program_code'] = ['required', 'string', 'max:20'];
        }

        $validated = $request->validate($rules);

        try {
            DB::beginTransaction();

            // Get exam type details
            $examType = ExamType::findOrFail($validated['exam_type_id']);

            $exam = Exam::create([
                'title' => $validated['title'],
                'education_level' => $validated['education_level'],
                'stream' => $validated['stream'] ?? null,
                'program_code' => $validated['program_code'] ?? null,
                'exam_type_id' => $validated['exam_type_id'],
                'class_id' => $validated['class_id'],
                'academic_year_id' => $validated['academic_year_id'],
                'exam_date' => $validated['exam_date'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'duration_minutes' => $validated['duration_minutes'],
                'assessment_category' => $validated['assessment_category'],
                'weightage_percentage' => $validated['weightage_percentage'] ?? $examType->default_weightage,
                'venue' => $validated['venue'],
                'instructions' => $validated['instructions'],
                'grading_system_id' => $validated['grading_system_id'],
                'auto_load_subjects' => $validated['auto_load_subjects'] ?? false,
                'auto_enroll_students' => $validated['auto_enroll_students'] ?? true,
                'minimum_pass_percentage' => $validated['minimum_pass_percentage'],
                'overall_pass_percentage' => $validated['overall_pass_percentage'],
                'requires_attendance' => $validated['requires_attendance'] ?? true,
                'minimum_attendance_percentage' => $validated['minimum_attendance_percentage'] ?? 75,
                'status' => 'scheduled',
                'created_by' => auth()->id()
            ]);

            // Auto-load subjects if requested
            if ($exam->auto_load_subjects) {
                $exam->autoLoadSubjectsNepali();
            }

            // Auto-enroll students if requested
            if ($exam->auto_enroll_students) {
                $enrolledCount = $exam->autoEnrollStudents();
                // Log the enrollment count for reference
            }

            DB::commit();

            return redirect()
                ->route('nepali-exams.show', $exam)
                ->with('success', 'Exam created successfully with Nepali system standards.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create exam: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified exam
     */
    public function show(Exam $exam)
    {
        $exam->load([
            'examType',
            'class.course.department.faculty',
            'academicYear',
            'gradingSystem.gradeScales',
            'subjects',
            'componentMarks.examComponent',
            'componentMarks.student',
            'creator'
        ]);

        return view('nepali-exams.show', compact('exam'));
    }

    /**
     * Get courses by faculty (AJAX)
     */
    public function getCoursesByFaculty(Request $request)
    {
        $facultyId = $request->faculty_id;
        $educationLevel = $request->education_level;

        $courses = Course::whereHas('department', function ($query) use ($facultyId) {
            $query->where('faculty_id', $facultyId);
        })->active()->get();

        return response()->json($courses);
    }

    /**
     * Get classes by course (AJAX)
     */
    public function getClassesByCourse(Request $request)
    {
        $courseId = $request->course_id;
        $academicYearId = $request->academic_year_id;

        $classes = ClassSection::where('course_id', $courseId)
            ->where('academic_year_id', $academicYearId)
            ->active()
            ->get();

        return response()->json($classes);
    }

    /**
     * Get exam types by education level (AJAX)
     */
    public function getExamTypesByLevel(Request $request)
    {
        $educationLevel = $request->education_level;
        $assessmentCategory = $request->assessment_category;

        $query = ExamType::active()->forEducationLevel($educationLevel);

        if ($assessmentCategory) {
            $query->forAssessmentCategory($assessmentCategory);
        }

        $examTypes = $query->ordered()->get();

        return response()->json($examTypes);
    }

    /**
     * Get subjects by class and education level (AJAX)
     */
    public function getSubjectsByClass(Request $request)
    {
        $classId = $request->class_id;
        $educationLevel = $request->education_level;
        $stream = $request->stream;
        $programCode = $request->program_code;

        $query = Subject::query();

        // Filter by education level
        if ($educationLevel) {
            $query->where(function ($q) use ($educationLevel) {
                $q->where('education_level', $educationLevel)
                  ->orWhere('education_level', 'both');
            });
        }

        // Filter by stream for +2
        if ($educationLevel === 'plus_two' && $stream) {
            $query->where(function ($q) use ($stream) {
                $q->whereJsonContains('applicable_streams', $stream)
                  ->orWhereNull('applicable_streams');
            });
        }

        // Filter by program for Bachelor's
        if ($educationLevel === 'bachelors' && $programCode) {
            $query->where(function ($q) use ($programCode) {
                $q->whereJsonContains('applicable_programs', $programCode)
                  ->orWhereNull('applicable_programs');
            });
        }

        $subjects = $query->where('status', 'active')->get();

        return response()->json($subjects);
    }
}
