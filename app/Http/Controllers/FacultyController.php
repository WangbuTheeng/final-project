<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FacultyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the faculties.
     */
    public function index(Request $request)
    {
        $this->authorize('manage-settings');

        $query = Faculty::with(['dean', 'departments'])->withCount('departments');

        // Handle search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('code', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%");
            });
        }

        $faculties = $query->orderBy('id', 'asc')->paginate(15);
        $faculties->appends($request->query());

        return view('faculties.index', compact('faculties'));
    }

    /**
     * Show the form for creating a new faculty.
     */
    public function create()
    {
        $this->authorize('manage-settings');

        // Get users who can be deans (teachers, admins, etc.)
        $potentialDeans = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['Teacher', 'Admin', 'Super Admin']);
        })->orderBy('name')->get();

        return view('faculties.create', compact('potentialDeans'));
    }

    /**
     * Store a newly created faculty in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('manage-settings');

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:10', 'unique:faculties'],
            'description' => ['nullable', 'string'],
            'dean_id' => ['nullable', 'exists:users,id'],
            'location' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        try {
            Faculty::create([
                'name' => $request->name,
                'code' => strtoupper($request->code),
                'description' => $request->description,
                'dean_id' => $request->dean_id,
                'location' => $request->location,
                'phone' => $request->phone,
                'email' => $request->email,
                'is_active' => $request->has('is_active') ? $request->is_active : true,
            ]);

            return redirect()->route('faculties.index')
                ->with('success', 'Faculty created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating faculty: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified faculty.
     */
    public function show(Faculty $faculty)
    {
        $this->authorize('manage-settings');

        $faculty->load(['dean', 'departments.hod']);

        return view('faculties.show', compact('faculty'));
    }

    /**
     * Show the form for editing the specified faculty.
     */
    public function edit(Faculty $faculty)
    {
        $this->authorize('manage-settings');

        // Get users who can be deans
        $potentialDeans = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['Teacher', 'Admin', 'Super Admin']);
        })->orderBy('name')->get();

        return view('faculties.edit', compact('faculty', 'potentialDeans'));
    }

    /**
     * Update the specified faculty in storage.
     */
    public function update(Request $request, Faculty $faculty)
    {
        $this->authorize('manage-settings');

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:10', 'unique:faculties,code,' . $faculty->id],
            'description' => ['nullable', 'string'],
            'dean_id' => ['nullable', 'exists:users,id'],
            'location' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        try {
            $faculty->update([
                'name' => $request->name,
                'code' => strtoupper($request->code),
                'description' => $request->description,
                'dean_id' => $request->dean_id,
                'location' => $request->location,
                'phone' => $request->phone,
                'email' => $request->email,
                'is_active' => $request->has('is_active') ? $request->is_active : $faculty->is_active,
            ]);

            return redirect()->route('faculties.index')
                ->with('success', 'Faculty updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating faculty: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified faculty from storage.
     */
    public function destroy(Faculty $faculty)
    {
        $this->authorize('manage-settings');

        if (!$faculty->canBeDeleted()) {
            return redirect()->route('faculties.index')
                ->with('error', 'Cannot delete faculty. It has associated departments.');
        }

        try {
            $faculty->delete();

            return redirect()->route('faculties.index')
                ->with('success', 'Faculty deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('faculties.index')
                ->with('error', 'Error deleting faculty: ' . $e->getMessage());
        }
    }
}
