<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Added for logging

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
        // Check if user has Super Admin, Admin, or Teacher role
        if (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('Teacher')) {
            abort(403, 'Unauthorized access to Courses.');
        }

        $query = Course::with(['department.faculty', 'department'])
            ->withCount('classes');

        // Filter by faculty
        if ($request->filled('faculty_id')) {
            $query->byFaculty($request->faculty_id);
        }

        // Filter by department
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }



        // Filter by organization type
        if ($request->filled('organization_type')) {
            $query->where('organization_type', $request->organization_type);
        }

        // Filter by year (for yearly organization)
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        // Filter by semester period (for semester organization)
        if ($request->filled('semester_period')) {
            $query->where('semester_period', $request->semester_period);
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
        $organizationTypes = ['yearly', 'semester'];
        $yearlyOptions = Course::getYearlyOptions(); // 1-4 years
        $semesterOptions = Course::getSemesterOptions(); // 1-8 semesters
        $courseTypes = ['core', 'elective', 'general'];

        return view('courses.index', compact('courses', 'faculties', 'departments', 'organizationTypes', 'yearlyOptions', 'semesterOptions', 'courseTypes'));
    }

    /**
     * Show the form for creating a new course.
     */
    public function create()
    {
        // Check if user has Super Admin or Admin role
        if (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('Admin')) {
            abort(403, 'Unauthorized access to create Courses.');
        }

        $faculties = Faculty::active()->orderBy('name')->get();
        $departments = Department::active()->with('faculty')->orderBy('name')->get();
        $courseTypes = ['core', 'elective', 'general'];

        return view('courses.create', compact('faculties', 'departments', 'courseTypes'));
    }

    /**
     * Store a newly created course in storage.
     */
    public function store(Request $request)
    {
        // Check if user has Super Admin or Admin role
        if (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('Admin')) {
            abort(403, 'Unauthorized access to create Courses.');
        }

        $validationRules = [
            'title' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', 'unique:courses'],
            'description' => ['nullable', 'string'],
            'department_id' => ['required', 'exists:departments,id'],
            'credit_units' => ['required', 'integer', 'min:1', 'max:10'],
            'course_type' => ['required', 'in:core,elective,general'],
            'is_active' => ['boolean'],
        ];

        $request->validate($validationRules);

        try {
            $courseData = [
                'title' => $request->title,
                'code' => strtoupper($request->code),
                'description' => $request->description,
                'department_id' => $request->department_id,
                'credit_units' => $request->credit_units,
                'organization_type' => 'semester', // Default to semester for TU
                'course_type' => $request->course_type,
                'semester_period' => 1, // Default value, can be updated later when assigning to programs
                'year' => null, // Not used for TU semester system
                'is_active' => $request->has('is_active') ? $request->is_active : true,
            ];

            Course::create($courseData);

            return redirect()->route('courses.index')
                ->with('success', 'Course created successfully. You can now assign this course to specific program semesters.');
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
        // Check if user has Super Admin, Admin, or Teacher role
        if (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('Teacher')) {
            abort(403, 'Unauthorized access to view Course details.');
        }

        $course->load(['faculty', 'department', 'classes.academicYear']);

        return view('courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified course.
     */
    public function edit(Course $course)
    {
        // Check if user has Super Admin or Admin role
        if (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('Admin')) {
            abort(403, 'Unauthorized access to edit Courses.');
        }

        $faculties = Faculty::active()->orderBy('name')->get();
        $departments = Department::active()->with('faculty')->orderBy('name')->get();
        $yearlyOptions = Course::getYearlyOptions(); // 1-4 years
        $semesterOptions = Course::getSemesterOptions(); // 1-8 semesters
        $organizationTypes = ['yearly', 'semester'];
        $courseTypes = ['core', 'elective', 'general'];
        $semesterTypes = []; // Added to prevent undefined variable error, as it's no longer used in the form

        return view('courses.edit', compact('course', 'faculties', 'departments', 'yearlyOptions', 'semesterOptions', 'organizationTypes', 'courseTypes', 'semesterTypes'));
    }

    /**
     * Update the specified course in storage.
     */
    public function update(Request $request, Course $course)
    {
        // Check if user has Super Admin or Admin role
        if (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('Admin')) {
            abort(403, 'Unauthorized access to update Courses.');
        }

        // Log the incoming request for debugging
        Log::info('Course update request received', [
            'course_id' => $course->id,
            'request_data' => $request->all()
        ]);

        // Validate the request
        $validationRules = [
            'title' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:courses,code,' . $course->id,
            'description' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'credit_units' => 'required|integer|min:1|max:10',
            'organization_type' => 'required|in:yearly,semester',
            'course_type' => 'required|in:core,elective,general',
            'is_active' => 'boolean',
        ];

        // Add conditional validation based on organization type
        if ($request->organization_type === 'yearly') {
            $validationRules['year'] = 'required|integer|in:1,2,3,4';
        } else {
            $validationRules['semester_period'] = 'required|integer|in:1,2,3,4,5,6,7,8';
        }

        $validated = $request->validate($validationRules);
        Log::info('Validation passed', ['validated_data' => $validated]);

        try {
            // Prepare the data for update
            $courseData = [
                'title' => $request->title,
                'code' => strtoupper($request->code),
                'description' => $request->description,
                'department_id' => $request->department_id,
                'credit_units' => $request->credit_units,
                'organization_type' => $request->organization_type,
                'course_type' => $request->course_type,
                'is_active' => $request->boolean('is_active'),
            ];

            // Add organization-specific fields and clear the other type's fields
            if ($request->organization_type === 'yearly') {
                $courseData['year'] = $request->year;
                $courseData['semester_period'] = null;
            } else {
                $courseData['semester_period'] = $request->semester_period;
                $courseData['year'] = null;
            }

            Log::info('Attempting to update course', [
                'course_id' => $course->id,
                'course_data' => $courseData
            ]);

            // Update the course
            $updated = $course->update($courseData);

            Log::info('Course update result', [
                'course_id' => $course->id,
                'update_result' => $updated,
                'course_after_update' => $course->fresh()->toArray()
            ]);

            if ($updated) {
                return redirect()->route('courses.index')
                    ->with('success', 'Course updated successfully.');
            } else {
                Log::error('Course update returned false', ['course_id' => $course->id]);
                return redirect()->back()
                    ->with('error', 'Failed to update course. No changes were made.')
                    ->withInput();
            }

        } catch (\Exception $e) {
            Log::error('Error updating course: ' . $e->getMessage(), [
                'exception' => $e,
                'course_id' => $course->id,
                'request_data' => $request->all()
            ]);
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
        // Check if user has Super Admin or Admin role
        if (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('Admin')) {
            abort(403, 'Unauthorized access to delete Courses.');
        }

        if (!$course->canBeDeleted()) {
            return redirect()->route('courses.index')
                ->with('error', 'Cannot delete course. It has associated classes.');
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
