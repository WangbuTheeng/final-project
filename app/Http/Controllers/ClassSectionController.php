<?php

namespace App\Http\Controllers;

use App\Models\ClassSection;
use App\Models\Course;
use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Support\Facades\Log;
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
        $this->authorize('manage-classes');

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
            } else {
                // If no current academic year, try to get any active academic year
                $activeYear = AcademicYear::where('is_active', true)->first();
                if ($activeYear) {
                    $query->where('academic_year_id', $activeYear->id);
                }
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
        $academicYears = AcademicYear::where('is_active', true)->orderBy('start_date', 'desc')->get();
        $instructors = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['Teacher', 'Admin', 'Super Admin']);
        })->orderBy('name')->get();
        $semesters = [1, 2, 3, 4, 5, 6, 7, 8];
        $statuses = ['active', 'completed', 'cancelled'];

        return view('classes.index', compact('classes', 'academicYears', 'instructors', 'semesters', 'statuses'));
    }

    /**
     * Show the form for creating a new class section.
     */
    public function create()
    {
        $this->authorize('manage-classes');

        $courses = Course::active()->with('department')->orderBy('code')->get();
        $academicYears = AcademicYear::where('is_active', true)->orderBy('start_date', 'desc')->get();
        $instructors = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['Teacher', 'Admin', 'Super Admin']);
        })->orderBy('name')->get();
        $semesters = [1, 2, 3, 4, 5, 6, 7, 8];

        return view('classes.create', compact('courses', 'academicYears', 'instructors', 'semesters'));
    }

    public function getCourseType(Request $request)
    {
        $courseId = $request->input('course_id');
        $course = Course::find($courseId);

        if ($course) {
            return response()->json(['type' => $course->organization_type]);
        }

        return response()->json(['type' => null]);
    }

    /**
     * Store a newly created class section in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('manage-classes');

        // Determine if the course is semester-based or yearly-based before validation
        $course = Course::find($request->course_id);
        $isSemesterBased = ($course && $course->organization_type === 'semester');

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'course_id' => ['required', 'exists:courses,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'instructor_id' => ['nullable', 'exists:users,id'],
            'room' => ['nullable', 'string', 'max:100'],
            'capacity' => ['required', 'integer', 'min:1', 'max:500'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'schedule' => ['nullable', 'array'],
            'schedule.*.day' => ['required_with:schedule', 'string'],
            'schedule.*.time' => ['required_with:schedule', 'string'],
            'schedule.*.duration' => ['nullable', 'string'],
        ];

        if ($isSemesterBased) {
            $rules['semester'] = ['required', 'integer', 'in:1,2,3,4,5,6,7,8'];
            $rules['year'] = ['nullable']; // Ensure year is not required
        } else {
            $rules['year'] = ['required', 'integer', 'in:1,2,3,4'];
            $rules['semester'] = ['nullable']; // Ensure semester is not required
        }

        $request->validate($rules);

        try {
            // Check for duplicate class in same academic year and semester/year
            $existingClass = ClassSection::where('course_id', $request->course_id)
                ->where('academic_year_id', $request->academic_year_id)
                ->where(function ($query) use ($request, $isSemesterBased) {
                    if ($isSemesterBased) {
                        $query->where('semester', $request->semester);
                    } else {
                        $query->where('year', $request->year);
                    }
                })
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
                'semester' => $request->semester ?? null,
                'year' => $request->year ?? null,
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
    public function show(ClassSection $class)
    {
        $this->authorize('manage-classes');

        // Load relationships with null safety
        $class->load(['course.department', 'academicYear', 'instructor', 'enrollments.student.user']);

        // Check if critical relationships exist
        if (!$class->course) {
            Log::warning('Class section accessed with missing course', [
                'class_id' => $class->id,
                'class_name' => $class->name,
                'course_id' => $class->course_id
            ]);
        }

        if (!$class->academicYear) {
            Log::warning('Class section accessed with missing academic year', [
                'class_id' => $class->id,
                'class_name' => $class->name,
                'academic_year_id' => $class->academic_year_id
            ]);
        }

        return view('classes.show', compact('class'));
    }

    /**
     * Show the form for editing the specified class section.
     */
    public function edit(ClassSection $class)
    {
        $this->authorize('manage-classes');

        $courses = Course::active()->with('department')->orderBy('code')->get();
        $academicYears = AcademicYear::where('is_active', true)->orderBy('start_date', 'desc')->get();
        $instructors = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['Teacher', 'Admin', 'Super Admin']);
        })->orderBy('name')->get();
        $semesters = [1, 2, 3, 4, 5, 6, 7, 8];
        $statuses = ['active', 'completed', 'cancelled'];

        return view('classes.edit', compact('class', 'courses', 'academicYears', 'instructors', 'semesters', 'statuses'));
    }

    /**
     * Update the specified class section in storage.
     */
    public function update(Request $request, ClassSection $class)
    {
        $this->authorize('manage-classes');

        // Determine if the course is semester-based or yearly-based before validation
        $course = Course::find($request->course_id);
        $isSemesterBased = ($course && $course->organization_type === 'semester');

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'course_id' => ['required', 'exists:courses,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'instructor_id' => ['nullable', 'exists:users,id'],
            'room' => ['nullable', 'string', 'max:100'],
            'capacity' => ['required', 'integer', 'min:1', 'max:500'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'status' => ['required', 'in:active,completed,cancelled'],
            'schedule' => ['nullable', 'array'],
            'schedule.*.day' => ['required_with:schedule', 'string'],
            'schedule.*.time' => ['required_with:schedule', 'string'],
            'schedule.*.duration' => ['nullable', 'string'],
        ];

        if ($isSemesterBased) {
            $rules['semester'] = ['required', 'integer', 'in:1,2,3,4,5,6,7,8'];
            $rules['year'] = ['nullable']; // Ensure year is not required
        } else {
            $rules['year'] = ['required', 'integer', 'in:1,2,3,4'];
            $rules['semester'] = ['nullable']; // Ensure semester is not required
        }

        $request->validate($rules);

        try {
            // Check for duplicate class in same academic year and semester/year (excluding current)
            $existingClass = ClassSection::where('course_id', $request->course_id)
                ->where('academic_year_id', $request->academic_year_id)
                ->where(function ($query) use ($request, $isSemesterBased) {
                    if ($isSemesterBased) {
                        $query->where('semester', $request->semester);
                    } else {
                        $query->where('year', $request->year);
                    }
                })
                ->where('name', $request->name)
                ->where('id', '!=', $class->id)
                ->first();

            if ($existingClass) {
                return redirect()->back()
                    ->with('error', 'A class with this name already exists for this course in the selected academic year and ' . ($isSemesterBased ? 'semester' : 'year') . '.')
                    ->withInput();
            }

            $class->update([
                'name' => $request->name,
                'course_id' => $request->course_id,
                'academic_year_id' => $request->academic_year_id,
                'instructor_id' => $request->instructor_id,
                'semester' => $request->semester ?? null,
                'year' => $request->year ?? null,
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
                ->with('error', 'Error updating class section: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified class section from storage.
     */
    public function destroy(ClassSection $class)
    {
        $this->authorize('manage-classes');

        if (!$class->canBeDeleted()) {
            return redirect()->route('classes.index')
                ->with('error', 'Cannot delete class section. It has enrollments or exams.');
        }

        try {
            $class->delete();

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
    public function assignInstructor(Request $request, ClassSection $class)
    {
        $this->authorize('manage-classes');

        $request->validate([
            'instructor_id' => ['required', 'exists:users,id'],
        ]);

        try {
            $class->update([
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
