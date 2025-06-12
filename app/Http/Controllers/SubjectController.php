<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\ClassSection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the subjects.
     */
    public function index(Request $request)
    {
        $this->authorize('view-courses'); // Using course permission as subjects are part of classes

        $query = Subject::with(['class.course.faculty', 'instructor'])
            ->withCount(['class']);

        // Filter by class
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        // Filter by difficulty level
        if ($request->filled('difficulty_level')) {
            $query->where('difficulty_level', $request->difficulty_level);
        }

        // Filter by subject type
        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->subject_type);
        }

        // Filter by mandatory/optional
        if ($request->filled('is_mandatory')) {
            $query->where('is_mandatory', $request->is_mandatory);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by active status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $subjects = $query->orderBy('order_sequence')->paginate(15);

        // Get filter options
        $classes = ClassSection::with('course')->active()->orderBy('name')->get();
        $difficultyLevels = ['beginner', 'intermediate', 'advanced'];
        $subjectTypes = ['theory', 'practical', 'mixed'];

        return view('subjects.index', compact('subjects', 'classes', 'difficultyLevels', 'subjectTypes'));
    }

    /**
     * Show the form for creating a new subject.
     */
    public function create(Request $request)
    {
        $this->authorize('create-courses');

        $classes = ClassSection::with(['course.faculty', 'instructor'])->active()->orderBy('name')->get();
        
        // Get instructors (teachers and admins)
        $instructors = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['Teacher', 'Admin', 'Super Admin']);
        })->orderBy('name')->get();

        $difficultyLevels = ['beginner', 'intermediate', 'advanced'];
        $subjectTypes = ['theory', 'practical', 'mixed'];

        // Pre-select class if provided
        $selectedClass = $request->filled('class_id') ? 
            ClassSection::find($request->class_id) : null;

        return view('subjects.create', compact('classes', 'instructors', 'difficultyLevels', 'subjectTypes', 'selectedClass'));
    }

    /**
     * Store a newly created subject in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create-courses');

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', 'unique:subjects'],
            'description' => ['nullable', 'string'],
            'class_id' => ['required', 'exists:classes,id'],
            'instructor_id' => ['nullable', 'exists:users,id'],
            'order_sequence' => ['required', 'integer', 'min:1'],
            'duration_hours' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'credit_weight' => ['nullable', 'integer', 'min:1', 'max:100'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'learning_objectives' => ['nullable', 'array'],
            'learning_objectives.*' => ['string'],
            'resources' => ['nullable', 'array'],
            'resources.*' => ['string'],
            'difficulty_level' => ['required', 'in:beginner,intermediate,advanced'],
            'subject_type' => ['required', 'in:theory,practical,mixed'],
            'is_mandatory' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        try {
            DB::beginTransaction();

            Subject::create([
                'name' => $request->name,
                'code' => strtoupper($request->code),
                'description' => $request->description,
                'class_id' => $request->class_id,
                'instructor_id' => $request->instructor_id,
                'order_sequence' => $request->order_sequence,
                'duration_hours' => $request->duration_hours,
                'credit_weight' => $request->credit_weight,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'learning_objectives' => $request->learning_objectives ?? [],
                'resources' => $request->resources ?? [],
                'difficulty_level' => $request->difficulty_level,
                'subject_type' => $request->subject_type,
                'is_mandatory' => $request->has('is_mandatory') ? $request->is_mandatory : true,
                'is_active' => $request->has('is_active') ? $request->is_active : true,
            ]);

            DB::commit();

            return redirect()->route('subjects.index')
                ->with('success', 'Subject created successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Failed to create subject. Please try again.');
        }
    }

    /**
     * Display the specified subject.
     */
    public function show(Subject $subject)
    {
        $this->authorize('view-courses');

        $subject->load(['class.course.faculty', 'class.academicYear', 'instructor']);

        // Get other subjects in the same class
        $classSubjects = Subject::where('class_id', $subject->class_id)
            ->where('id', '!=', $subject->id)
            ->orderBy('order_sequence')
            ->get();

        return view('subjects.show', compact('subject', 'classSubjects'));
    }

    /**
     * Show the form for editing the specified subject.
     */
    public function edit(Subject $subject)
    {
        $this->authorize('edit-courses');

        $classes = ClassSection::with(['course.faculty', 'instructor'])->active()->orderBy('name')->get();
        
        // Get instructors (teachers and admins)
        $instructors = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['Teacher', 'Admin', 'Super Admin']);
        })->orderBy('name')->get();

        $difficultyLevels = ['beginner', 'intermediate', 'advanced'];
        $subjectTypes = ['theory', 'practical', 'mixed'];

        return view('subjects.edit', compact('subject', 'classes', 'instructors', 'difficultyLevels', 'subjectTypes'));
    }

    /**
     * Update the specified subject in storage.
     */
    public function update(Request $request, Subject $subject)
    {
        $this->authorize('edit-courses');

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', 'unique:subjects,code,' . $subject->id],
            'description' => ['nullable', 'string'],
            'class_id' => ['required', 'exists:classes,id'],
            'instructor_id' => ['nullable', 'exists:users,id'],
            'order_sequence' => ['required', 'integer', 'min:1'],
            'duration_hours' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'credit_weight' => ['nullable', 'integer', 'min:1', 'max:100'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'learning_objectives' => ['nullable', 'array'],
            'learning_objectives.*' => ['string'],
            'resources' => ['nullable', 'array'],
            'resources.*' => ['string'],
            'difficulty_level' => ['required', 'in:beginner,intermediate,advanced'],
            'subject_type' => ['required', 'in:theory,practical,mixed'],
            'is_mandatory' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        try {
            DB::beginTransaction();

            $subject->update([
                'name' => $request->name,
                'code' => strtoupper($request->code),
                'description' => $request->description,
                'class_id' => $request->class_id,
                'instructor_id' => $request->instructor_id,
                'order_sequence' => $request->order_sequence,
                'duration_hours' => $request->duration_hours,
                'credit_weight' => $request->credit_weight,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'learning_objectives' => $request->learning_objectives ?? [],
                'resources' => $request->resources ?? [],
                'difficulty_level' => $request->difficulty_level,
                'subject_type' => $request->subject_type,
                'is_mandatory' => $request->has('is_mandatory') ? $request->is_mandatory : $subject->is_mandatory,
                'is_active' => $request->has('is_active') ? $request->is_active : $subject->is_active,
            ]);

            DB::commit();

            return redirect()->route('subjects.show', $subject)
                ->with('success', 'Subject updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Failed to update subject. Please try again.');
        }
    }

    /**
     * Remove the specified subject from storage.
     */
    public function destroy(Subject $subject)
    {
        $this->authorize('delete-courses');

        if (!$subject->canBeDeleted()) {
            return back()->with('error', 'This subject cannot be deleted as it has associated data.');
        }

        try {
            $subject->delete();
            return redirect()->route('subjects.index')
                ->with('success', 'Subject deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete subject. Please try again.');
        }
    }

    /**
     * Get subjects by class (AJAX endpoint)
     */
    public function getByClass(ClassSection $class)
    {
        $this->authorize('view-courses');

        $subjects = $class->activeSubjects()->get();
        return response()->json($subjects);
    }
}
