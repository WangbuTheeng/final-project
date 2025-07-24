<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Subject;
use App\Models\ClassSection;
use App\Models\AcademicYear;
use App\Models\ExamType;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExaminationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of examinations
     */
    public function index(Request $request)
    {
        try {
            $query = Exam::with(['class', 'academicYear']);

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('exam_type')) {
                $query->where('exam_type', $request->exam_type);
            }

            if ($request->filled('academic_year_id')) {
                $query->where('academic_year_id', $request->academic_year_id);
            }

            if ($request->filled('search')) {
                $query->where('title', 'like', '%' . $request->search . '%');
            }

            $examinations = $query->orderBy('exam_date', 'desc')->paginate(15);

            // Get filter options with error handling
            $academicYears = AcademicYear::where('is_active', true)->get();
            $statuses = ['scheduled', 'incomplete', 'completed', 'cancelled'];
            $examTypes = ['first_assessment', 'first_terminal', 'second_assessment', 'second_terminal', 'third_assessment', 'final_term', 'monthly_term', 'weekly_test'];

            return view('examinations.index', compact('examinations', 'academicYears', 'statuses', 'examTypes'));
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Examinations index error: ' . $e->getMessage());

            // Return with empty collections to prevent errors
            $examinations = collect()->paginate(15);
            $academicYears = collect();
            $statuses = ['scheduled', 'incomplete', 'completed', 'cancelled'];
            $examTypes = ['first_assessment', 'first_terminal', 'second_assessment', 'second_terminal', 'third_assessment', 'final_term', 'monthly_term', 'weekly_test'];

            return view('examinations.index', compact('examinations', 'academicYears', 'statuses', 'examTypes'))
                ->with('error', 'There was an issue loading examinations. Please check your database configuration.');
        }
    }

    /**
     * Show the form for creating a new examination
     */
    public function create()
    {
        try {
            $subjects = Subject::where('is_active', true)->get();
            $academicYears = AcademicYear::where('is_active', true)->get();

            // Get exam types from database
            $examTypes = ExamType::active()->ordered()->get();

            // Get courses for course selection (instead of programs)
            $courses = Course::where('is_active', true)->with('department')->get();

            // Classes will be loaded dynamically based on course selection
            $classes = collect(); // Empty collection initially

            return view('examinations.create', compact('subjects', 'classes', 'academicYears', 'examTypes', 'courses'));
        } catch (\Exception $e) {
            \Log::error('Examinations create error: ' . $e->getMessage());

            // Return with empty collections to prevent errors
            $subjects = collect();
            $classes = collect();
            $academicYears = collect();
            $examTypes = collect();

            return view('examinations.create', compact('subjects', 'classes', 'academicYears', 'examTypes'))
                ->with('error', 'There was an issue loading form data. Please check your database configuration.');
        }
    }

    /**
     * Get classes by course ID (AJAX endpoint)
     */
    public function getClassesByCourse(Request $request)
    {
        try {
            // Debug logging
            \Log::info('getClassesByCourse called', [
                'user_id' => auth()->id(),
                'user_authenticated' => auth()->check(),
                'course_id' => $request->get('course_id'),
                'all_params' => $request->all(),
                'method' => $request->method(),
                'url' => $request->url()
            ]);

            $courseId = $request->get('course_id');

            if (!$courseId) {
                \Log::warning('No course ID provided');
                return response()->json(['classes' => [], 'message' => 'No course ID provided']);
            }

            $classes = ClassSection::where('course_id', $courseId)
                ->where('status', 'active')
                ->with(['course', 'academicYear'])
                ->get()
                ->map(function ($class) {
                    return [
                        'id' => $class->id,
                        'name' => $class->name,
                        'course_name' => $class->course->title ?? '',
                        'academic_year' => $class->academicYear->name ?? ''
                    ];
                });

            \Log::info('Classes found', ['count' => $classes->count()]);

            return response()->json([
                'classes' => $classes,
                'message' => 'Classes loaded successfully',
                'count' => $classes->count()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading classes by course: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'classes' => [],
                'error' => true,
                'message' => 'Error loading classes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created examination in storage
     */
    public function store(Request $request)
    {
        // Base validation rules
        $rules = [
            'title' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'exam_type_id' => 'required|exists:exam_types,id',
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'exam_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'total_marks' => 'required|numeric|min:1|max:10000',
            'pass_mark' => 'required|numeric|min:1',
            'exam_hall' => 'nullable|string|max:255',
            'max_students' => 'nullable|integer|min:1',
            'instructions' => 'nullable|string',
            'is_published' => 'boolean',
            'send_notifications' => 'boolean',
            'allow_late_submission' => 'boolean'
        ];

        // Add subject validation - either single subject or multiple subjects
        if ($request->has('selected_subjects') && is_array($request->selected_subjects)) {
            // Multi-subject exam
            $rules['selected_subjects'] = 'required|array|min:1';
            $rules['selected_subjects.*'] = 'exists:subjects,id';
            $rules['subject_theory_marks'] = 'required|array';
            $rules['subject_practical_marks'] = 'required|array';
            $rules['subject_total_marks'] = 'required|array';
        } else {
            // Single subject exam (backward compatibility)
            $rules['subject_id'] = 'required|exists:subjects,id';
        }

        // Get class and course to determine organization type
        $class = ClassSection::with('course')->findOrFail($request->class_id);
        $course = Course::findOrFail($request->course_id);

        // Validate semester or year based on course organization type
        if ($course->organization_type === 'semester') {
            $rules['semester'] = 'required|integer|min:1|max:8';
        } else {
            $rules['year'] = 'required|integer|min:1|max:4';
        }

        $request->validate($rules);

        try {
            DB::beginTransaction();

            // Calculate duration in minutes
            $startTime = Carbon::createFromFormat('H:i', $request->start_time);
            $endTime = Carbon::createFromFormat('H:i', $request->end_time);
            $durationMinutes = $endTime->diffInMinutes($startTime);

            // Determine if this is a multi-subject exam
            $isMultiSubject = $request->has('selected_subjects') && is_array($request->selected_subjects);

            $exam = Exam::create([
                'title' => $request->title,
                'course_id' => $request->course_id,
                'exam_type' => $request->exam_type_id, // Map to existing field
                'class_id' => $request->class_id,
                'academic_year_id' => $request->academic_year_id,
                'subject_id' => $isMultiSubject ? ($request->selected_subjects[0] ?? null) : $request->subject_id,
                'semester' => $request->semester,
                'year' => $request->year,
                'exam_date' => $request->exam_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'duration_minutes' => $durationMinutes,
                'total_marks' => $request->total_marks,
                'pass_mark' => $request->pass_mark,
                'venue' => $request->exam_hall, // Map to existing field
                'max_students' => $request->max_students,
                'instructions' => $request->instructions,
                'is_published' => $request->boolean('is_published'),
                'send_notifications' => $request->boolean('send_notifications'),
                'allow_late_submission' => $request->boolean('allow_late_submission'),
                'status' => $request->boolean('is_published') ? 'published' : 'scheduled',
                'is_multi_subject' => $isMultiSubject,
                'auto_load_subjects' => $isMultiSubject,
                'auto_enroll_students' => true, // Enable auto-enrollment by default
                'created_by' => auth()->id()
            ]);

            // Attach subjects to the exam with their marks (only for multi-subject exams)
            if ($isMultiSubject) {
                foreach ($request->selected_subjects as $subjectId) {
                    $theoryMarks = $request->subject_theory_marks[$subjectId] ?? 0;
                    $practicalMarks = $request->subject_practical_marks[$subjectId] ?? 0;
                    $totalMarks = $request->subject_total_marks[$subjectId] ?? 0;

                    $exam->subjects()->attach($subjectId, [
                        'theory_marks' => $theoryMarks,
                        'practical_marks' => $practicalMarks,
                        'pass_marks_theory' => round($theoryMarks * 0.4), // 40% pass marks
                        'pass_marks_practical' => round($practicalMarks * 0.4),
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            // Auto-enroll students if auto_enroll_students is enabled
            if ($exam->auto_enroll_students ?? true) {
                $enrolledCount = $exam->autoEnrollStudents();
            }

            DB::commit();

            // Send notifications if requested
            if ($request->boolean('send_notifications')) {
                // TODO: Implement notification logic
                // $this->sendExamNotifications($exam);
            }

            return redirect()->route('examinations.index')
                           ->with('success', 'Examination created successfully following Nepali educational standards.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->with('error', 'Error creating examination: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Display the specified examination
     */
    public function show(Exam $examination)
    {
        $examination->load(['class', 'academicYear']);
        
        return view('examinations.show', compact('examination'));
    }

    /**
     * Show the form for editing the specified examination
     */
    public function edit(Exam $examination)
    {
        try {
            $subjects = Subject::where('is_active', true)->get();
            $classes = ClassSection::where('status', 'active')->get();
            $academicYears = AcademicYear::where('is_active', true)->get();
            $examTypes = ExamType::active()->ordered()->get();
            $statuses = ['scheduled', 'incomplete', 'completed', 'cancelled'];

            return view('examinations.edit', compact('examination', 'subjects', 'classes', 'academicYears', 'examTypes', 'statuses'));
        } catch (\Exception $e) {
            \Log::error('Examinations edit error: ' . $e->getMessage());

            return redirect()->route('examinations.index')
                           ->with('error', 'There was an issue loading the edit form. Please try again.');
        }
    }

    /**
     * Update the specified examination in storage
     */
    public function update(Request $request, Exam $examination)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'exam_type_id' => 'required|exists:exam_types,id',
            'status' => 'required|in:scheduled,incomplete,completed,cancelled',
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'exam_date' => 'required|date',
            'duration_minutes' => 'required|integer|min:30|max:480',
            'total_marks' => 'required|numeric|min:1',
            'pass_mark' => 'required|numeric|min:1',
            'venue' => 'nullable|string|max:255',
            'instructions' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $examination->update([
                'title' => $request->title,
                'exam_type_id' => $request->exam_type_id,
                'status' => $request->status,
                'class_id' => $request->class_id,
                'academic_year_id' => $request->academic_year_id,
                'exam_date' => $request->exam_date,
                'duration_minutes' => $request->duration_minutes,
                'total_marks' => $request->total_marks,
                'pass_mark' => $request->pass_mark,
                'venue' => $request->venue,
                'instructions' => $request->instructions
            ]);

            DB::commit();

            return redirect()->route('examinations.show', $examination)
                           ->with('success', 'Examination updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->with('error', 'Error updating examination: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Remove the specified examination from storage
     */
    public function destroy(Exam $examination)
    {
        // Only allow deletion if examination is scheduled
        if ($examination->status !== 'scheduled') {
            return redirect()->route('examinations.index')
                           ->with('error', 'Cannot delete examination that is not in scheduled status.');
        }

        try {
            $examination->delete();
            
            return redirect()->route('examinations.index')
                           ->with('success', 'Examination deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('examinations.index')
                           ->with('error', 'Error deleting examination: ' . $e->getMessage());
        }
    }

    /**
     * Get classes filtered by program code
     */
    public function getClassesByProgram($programCode)
    {
        try {
            $classes = ClassSection::with('course')
                ->whereHas('course', function ($query) use ($programCode) {
                    $query->where('code', $programCode);
                })
                ->get()
                ->map(function ($class) {
                    return [
                        'id' => $class->id,
                        'name' => $class->name,
                        'semester' => $class->semester,
                        'year' => $class->year,
                        'course_title' => $class->course->title,
                        'organization_type' => $class->course->organization_type,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $classes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading classes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get course details including organization type
     */
    public function getCourseDetails($courseId)
    {
        try {
            $course = Course::findOrFail($courseId);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $course->id,
                    'title' => $course->title,
                    'code' => $course->code,
                    'organization_type' => $course->organization_type,
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading course details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading course details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get class details including subjects and organization type
     */
    public function getClassDetails($classId)
    {
        try {
            $class = ClassSection::with(['course', 'activeSubjects'])->findOrFail($classId);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $class->id,
                    'name' => $class->name,
                    'semester' => $class->semester,
                    'year' => $class->year,
                    'organization_type' => $class->course->organization_type,
                    'course' => [
                        'id' => $class->course->id,
                        'title' => $class->course->title,
                        'code' => $class->course->code,
                        'organization_type' => $class->course->organization_type,
                    ],
                    'subjects' => $class->activeSubjects->map(function ($subject) {
                        return [
                            'id' => $subject->id,
                            'name' => $subject->name,
                            'code' => $subject->code,
                            'theory_marks' => $subject->full_marks_theory ?? 0,
                            'practical_marks' => $subject->full_marks_practical ?? 0,
                            'total_marks' => ($subject->full_marks_theory ?? 0) + ($subject->full_marks_practical ?? 0),
                            'is_practical' => $subject->is_practical,
                            'is_mandatory' => $subject->is_mandatory,
                        ];
                    })
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Class not found or error occurred: ' . $e->getMessage()
            ], 404);
        }
    }
}
