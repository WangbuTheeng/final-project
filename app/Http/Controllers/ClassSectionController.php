<?php

namespace App\Http\Controllers;

use App\Models\ClassSection;
use App\Models\Course;
use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassSectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the class sections.
     */
    public function index(Request $request)
    {
        $this->authorize('view-classes');

        $query = ClassSection::with(['course.department', 'academicYear', 'instructor'])
            ->withCount('enrollments');

        // Filter by academic year
        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        } else {
            // Default to current academic year
            $currentYear = AcademicYear::current();
            if ($currentYear) {
                $query->where('academic_year_id', $currentYear->id);
            }
        }

        // Filter by semester
        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        // Filter by instructor
        if ($request->filled('instructor_id')) {
            $query->where('instructor_id', $request->instructor_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by class name or course code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('course', function($courseQuery) use ($search) {
                      $courseQuery->where('code', 'like', "%{$search}%")
                               ->orWhere('title', 'like', "%{$search}%");
                  });
            });
        }

        $classes = $query->orderBy('name')->paginate(15);

        // Get filter options
        $academicYears = AcademicYear::active()->orderBy('start_date', 'desc')->get();
        $instructors = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['Teacher', 'Admin', 'Super Admin']);
        })->orderBy('name')->get();
        $semesters = ['first', 'second'];
        $statuses = ['active', 'completed', 'cancelled'];

        return view('classes.index', compact('classes', 'academicYears', 'instructors', 'semesters', 'statuses'));
    }

    /**
     * Show the form for creating a new class section.
     */
    public function create()
    {
        $this->authorize('create-classes');

        $courses = Course::active()->with('department')->orderBy('code')->get();
        $academicYears = AcademicYear::active()->orderBy('start_date', 'desc')->get();
        $instructors = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['Teacher', 'Admin', 'Super Admin']);
        })->orderBy('name')->get();
        $semesters = ['first', 'second'];

        return view('classes.create', compact('courses', 'academicYears', 'instructors', 'semesters'));
    }

    /**
     * Store a newly created class section in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create-classes');

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'course_id' => ['required', 'exists:courses,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'instructor_id' => ['nullable', 'exists:users,id'],
            'semester' => ['required', 'in:first,second'],
            'room' => ['nullable', 'string', 'max:100'],
            'capacity' => ['required', 'integer', 'min:1', 'max:500'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'schedule' => ['nullable', 'array'],
            'schedule.*.day' => ['required_with:schedule', 'string'],
            'schedule.*.time' => ['required_with:schedule', 'string'],
            'schedule.*.duration' => ['nullable', 'string'],
        ]);

        try {
            // Check for duplicate class in same academic year and semester
            $existingClass = ClassSection::where('course_id', $request->course_id)
                ->where('academic_year_id', $request->academic_year_id)
                ->where('semester', $request->semester)
                ->where('name', $request->name)
                ->first();

            if ($existingClass) {
                return redirect()->back()
                    ->with('error', 'A class with this name already exists for this course in the selected academic year and semester.')
                    ->withInput();
            }

            ClassSection::create([
                'name' => $request->name,
                'course_id' => $request->course_id,
                'academic_year_id' => $request->academic_year_id,
                'instructor_id' => $request->instructor_id,
                'semester' => $request->semester,
                'room' => $request->room,
                'capacity' => $request->capacity,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'schedule' => $request->schedule ?? [],
                'status' => 'active',
            ]);

            return redirect()->route('classes.index')
                ->with('success', 'Class section created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating class section: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified class section.
     */
    public function show(ClassSection $classSection)
    {
        $this->authorize('view-classes');

        $classSection->load(['course.department', 'academicYear', 'instructor', 'enrollments.student.user']);

        return view('classes.show', compact('classSection'));
    }

    /**
     * Show the form for editing the specified class section.
     */
    public function edit(ClassSection $classSection)
    {
        $this->authorize('edit-classes');

        $courses = Course::active()->with('department')->orderBy('code')->get();
        $academicYears = AcademicYear::active()->orderBy('start_date', 'desc')->get();
        $instructors = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['Teacher', 'Admin', 'Super Admin']);
        })->orderBy('name')->get();
        $semesters = ['first', 'second'];
        $statuses = ['active', 'completed', 'cancelled'];

        return view('classes.edit', compact('classSection', 'courses', 'academicYears', 'instructors', 'semesters', 'statuses'));
    }

    /**
     * Update the specified class section in storage.
     */
    public function update(Request $request, ClassSection $classSection)
    {
        $this->authorize('edit-classes');

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'course_id' => ['required', 'exists:courses,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'instructor_id' => ['nullable', 'exists:users,id'],
            'semester' => ['required', 'in:first,second'],
            'room' => ['nullable', 'string', 'max:100'],
            'capacity' => ['required', 'integer', 'min:1', 'max:500'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'status' => ['required', 'in:active,completed,cancelled'],
            'schedule' => ['nullable', 'array'],
            'schedule.*.day' => ['required_with:schedule', 'string'],
            'schedule.*.time' => ['required_with:schedule', 'string'],
            'schedule.*.duration' => ['nullable', 'string'],
        ]);

        try {
            // Check for duplicate class in same academic year and semester (excluding current)
            $existingClass = ClassSection::where('course_id', $request->course_id)
                ->where('academic_year_id', $request->academic_year_id)
                ->where('semester', $request->semester)
                ->where('name', $request->name)
                ->where('id', '!=', $classSection->id)
                ->first();

            if ($existingClass) {
                return redirect()->back()
                    ->with('error', 'A class with this name already exists for this course in the selected academic year and semester.')
                    ->withInput();
            }

            $classSection->update([
                'name' => $request->name,
                'course_id' => $request->course_id,
                'academic_year_id' => $request->academic_year_id,
                'instructor_id' => $request->instructor_id,
                'semester' => $request->semester,
                'room' => $request->room,
                'capacity' => $request->capacity,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status,
                'schedule' => $request->schedule ?? [],
            ]);

            return redirect()->route('classes.index')
                ->with('success', 'Class section updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating class section: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified class section from storage.
     */
    public function destroy(ClassSection $classSection)
    {
        $this->authorize('delete-classes');

        if (!$classSection->canBeDeleted()) {
            return redirect()->route('classes.index')
                ->with('error', 'Cannot delete class section. It has enrollments or exams.');
        }

        try {
            $classSection->delete();

            return redirect()->route('classes.index')
                ->with('success', 'Class section deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('classes.index')
                ->with('error', 'Error deleting class section: ' . $e->getMessage());
        }
    }

    /**
     * Assign instructor to class section
     */
    public function assignInstructor(Request $request, ClassSection $classSection)
    {
        $this->authorize('edit-classes');

        $request->validate([
            'instructor_id' => ['required', 'exists:users,id'],
        ]);

        try {
            $classSection->update([
                'instructor_id' => $request->instructor_id,
            ]);

            return redirect()->back()
                ->with('success', 'Instructor assigned successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error assigning instructor: ' . $e->getMessage());
        }
    }
}
