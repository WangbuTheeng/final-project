<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the courses.
     */
    public function index(Request $request)
    {
        $this->authorize('manage-courses');

        $query = Course::with(['faculty', 'department'])
            ->withCount('classes');

        // Filter by faculty
        if ($request->filled('faculty_id')) {
            $query->where('faculty_id', $request->faculty_id);
        }

        // Filter by department
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by level
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        // Filter by semester
        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        // Filter by course type
        if ($request->filled('course_type')) {
            $query->where('course_type', $request->course_type);
        }

        // Search by title or code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $courses = $query->orderBy('code')->paginate(15);

        // Get filter options
        $faculties = Faculty::active()->orderBy('name')->get();
        $departments = Department::active()->orderBy('name')->get();
        $levels = [100, 200, 300, 400, 500];
        $semesters = ['first', 'second', 'both'];
        $courseTypes = ['core', 'elective', 'general'];

        return view('courses.index', compact('courses', 'faculties', 'departments', 'levels', 'semesters', 'courseTypes'));
    }

    /**
     * Show the form for creating a new course.
     */
    public function create()
    {
        $this->authorize('manage-courses');

        $faculties = Faculty::active()->orderBy('name')->get();
        $departments = Department::active()->with('faculty')->orderBy('name')->get();
        $levels = [100, 200, 300, 400, 500];
        $semesters = ['first', 'second', 'both'];
        $courseTypes = ['core', 'elective', 'general'];

        // Get existing courses for prerequisites
        $existingCourses = Course::active()->orderBy('code')->get();

        return view('courses.create', compact('faculties', 'departments', 'levels', 'semesters', 'courseTypes', 'existingCourses'));
    }

    /**
     * Store a newly created course in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('manage-courses');

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', 'unique:courses'],
            'description' => ['nullable', 'string'],
            'faculty_id' => ['required', 'exists:faculties,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'credit_units' => ['required', 'integer', 'min:1', 'max:10'],
            'level' => ['required', 'integer', 'in:100,200,300,400,500'],
            'semester' => ['required', 'in:first,second,both'],
            'course_type' => ['required', 'in:core,elective,general'],
            'prerequisites' => ['nullable', 'array'],
            'prerequisites.*' => ['exists:courses,id'],
            'is_active' => ['boolean'],
        ]);

        try {
            Course::create([
                'title' => $request->title,
                'code' => strtoupper($request->code),
                'description' => $request->description,
                'faculty_id' => $request->faculty_id,
                'department_id' => $request->department_id,
                'credit_units' => $request->credit_units,
                'level' => $request->level,
                'semester' => $request->semester,
                'course_type' => $request->course_type,
                'prerequisites' => $request->prerequisites ?? [],
                'is_active' => $request->has('is_active') ? $request->is_active : true,
            ]);

            return redirect()->route('courses.index')
                ->with('success', 'Course created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating course: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified course.
     */
    public function show(Course $course)
    {
        $this->authorize('manage-courses');

        $course->load(['faculty', 'department', 'classes.academicYear', 'prerequisiteCourses']);

        return view('courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified course.
     */
    public function edit(Course $course)
    {
        $this->authorize('manage-courses');

        $faculties = Faculty::active()->orderBy('name')->get();
        $departments = Department::active()->with('faculty')->orderBy('name')->get();
        $levels = [100, 200, 300, 400, 500];
        $semesters = ['first', 'second', 'both'];
        $courseTypes = ['core', 'elective', 'general'];

        // Get existing courses for prerequisites (excluding current course)
        $existingCourses = Course::active()->where('id', '!=', $course->id)->orderBy('code')->get();

        return view('courses.edit', compact('course', 'faculties', 'departments', 'levels', 'semesters', 'courseTypes', 'existingCourses'));
    }

    /**
     * Update the specified course in storage.
     */
    public function update(Request $request, Course $course)
    {
        $this->authorize('manage-courses');

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', 'unique:courses,code,' . $course->id],
            'description' => ['nullable', 'string'],
            'faculty_id' => ['required', 'exists:faculties,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'credit_units' => ['required', 'integer', 'min:1', 'max:10'],
            'level' => ['required', 'integer', 'in:100,200,300,400,500'],
            'semester' => ['required', 'in:first,second,both'],
            'course_type' => ['required', 'in:core,elective,general'],
            'prerequisites' => ['nullable', 'array'],
            'prerequisites.*' => ['exists:courses,id'],
            'is_active' => ['boolean'],
        ]);

        try {
            $course->update([
                'title' => $request->title,
                'code' => strtoupper($request->code),
                'description' => $request->description,
                'faculty_id' => $request->faculty_id,
                'department_id' => $request->department_id,
                'credit_units' => $request->credit_units,
                'level' => $request->level,
                'semester' => $request->semester,
                'course_type' => $request->course_type,
                'prerequisites' => $request->prerequisites ?? [],
                'is_active' => $request->has('is_active') ? $request->is_active : $course->is_active,
            ]);

            return redirect()->route('courses.index')
                ->with('success', 'Course updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating course: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified course from storage.
     */
    public function destroy(Course $course)
    {
        $this->authorize('manage-courses');

        if (!$course->canBeDeleted()) {
            return redirect()->route('courses.index')
                ->with('error', 'Cannot delete course. It has associated classes or is a prerequisite for other courses.');
        }

        try {
            $course->delete();

            return redirect()->route('courses.index')
                ->with('success', 'Course deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('courses.index')
                ->with('error', 'Error deleting course: ' . $e->getMessage());
        }
    }
}
