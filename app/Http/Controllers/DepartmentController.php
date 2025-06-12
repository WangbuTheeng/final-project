<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the departments.
     */
    public function index(Request $request)
    {
        $this->authorize('manage-settings');

        $query = Department::with(['faculty', 'hod'])->withCount(['courses', 'students']);

        // Handle search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('code', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                  ->orWhereHas('faculty', function ($facultyQuery) use ($searchTerm) {
                      $facultyQuery->where('name', 'LIKE', "%{$searchTerm}%");
                  });
            });
        }

        $departments = $query->orderBy('id', 'asc')->paginate(15);
        $departments->appends($request->query());

        return view('departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new department.
     */
    public function create()
    {
        $this->authorize('manage-settings');

        $faculties = Faculty::active()->orderBy('name')->get();

        // Get users who can be HODs
        $potentialHods = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['Teacher', 'Admin', 'Super Admin']);
        })->orderBy('name')->get();

        return view('departments.create', compact('faculties', 'potentialHods'));
    }

    /**
     * Store a newly created department in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('manage-settings');

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:10', 'unique:departments'],
            'description' => ['nullable', 'string'],
            'faculty_id' => ['required', 'exists:faculties,id'],
            'hod_id' => ['nullable', 'exists:users,id'],
            'location' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'duration_years' => ['nullable', 'integer', 'min:1', 'max:10'],
            'degree_type' => ['nullable', 'in:bachelor,master,phd,diploma,certificate'],
            'is_active' => ['boolean'],
        ]);

        try {
            Department::create([
                'name' => $request->name,
                'code' => strtoupper($request->code),
                'description' => $request->description,
                'faculty_id' => $request->faculty_id,
                'hod_id' => $request->hod_id,
                'location' => $request->location,
                'phone' => $request->phone,
                'email' => $request->email,
                'duration_years' => $request->duration_years,
                'degree_type' => $request->degree_type,
                'is_active' => $request->has('is_active') ? $request->is_active : true,
            ]);

            return redirect()->route('departments.index')
                ->with('success', 'Department created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating department: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified department.
     */
    public function show(Department $department)
    {
        $this->authorize('manage-settings');

        $department->load(['faculty', 'hod', 'courses', 'students']);

        return view('departments.show', compact('department'));
    }

    /**
     * Show the form for editing the specified department.
     */
    public function edit(Department $department)
    {
        $this->authorize('manage-settings');

        $faculties = Faculty::active()->orderBy('name')->get();

        // Get users who can be HODs
        $potentialHods = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['Teacher', 'Admin', 'Super Admin']);
        })->orderBy('name')->get();

        return view('departments.edit', compact('department', 'faculties', 'potentialHods'));
    }

    /**
     * Update the specified department in storage.
     */
    public function update(Request $request, Department $department)
    {
        $this->authorize('manage-settings');

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:10', 'unique:departments,code,' . $department->id],
            'description' => ['nullable', 'string'],
            'faculty_id' => ['required', 'exists:faculties,id'],
            'hod_id' => ['nullable', 'exists:users,id'],
            'location' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'duration_years' => ['nullable', 'integer', 'min:1', 'max:10'],
            'degree_type' => ['nullable', 'in:bachelor,master,phd,diploma,certificate'],
            'is_active' => ['boolean'],
        ]);

        try {
            $department->update([
                'name' => $request->name,
                'code' => strtoupper($request->code),
                'description' => $request->description,
                'faculty_id' => $request->faculty_id,
                'hod_id' => $request->hod_id,
                'location' => $request->location,
                'phone' => $request->phone,
                'email' => $request->email,
                'duration_years' => $request->duration_years,
                'degree_type' => $request->degree_type,
                'is_active' => $request->has('is_active') ? $request->is_active : $department->is_active,
            ]);

            return redirect()->route('departments.index')
                ->with('success', 'Department updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating department: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified department from storage.
     */
    public function destroy(Department $department)
    {
        $this->authorize('manage-settings');

        if (!$department->canBeDeleted()) {
            return redirect()->route('departments.index')
                ->with('error', 'Cannot delete department. It has associated courses or students.');
        }

        try {
            $department->delete();

            return redirect()->route('departments.index')
                ->with('success', 'Department deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('departments.index')
                ->with('error', 'Error deleting department: ' . $e->getMessage());
        }
    }

    /**
     * Get departments by faculty (AJAX)
     */
    public function getByFaculty(Faculty $faculty)
    {
        $departments = $faculty->activeDepartments()
            ->select('id', 'name', 'code')
            ->orderBy('name')
            ->get();

        return response()->json($departments);
    }
}
