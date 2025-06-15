<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademicYearController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the academic years.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Check if user has permission to manage settings
        $this->authorize('manage-settings');

        $academicYears = AcademicYear::orderBy('start_date', 'desc')->paginate(15);
        return view('academic-years.index', compact('academicYears'));
    }

    /**
     * Show the form for creating a new academic year.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Check if user has permission to manage settings
        $this->authorize('manage-settings');
        
        return view('academic-years.create');
    }

    /**
     * Store a newly created academic year in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Check if user has permission to manage settings
        $this->authorize('manage-settings');

        $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'code' => ['required', 'string', 'max:20', 'unique:academic_years'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'description' => ['nullable', 'string'],
            'is_current' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        // Start a transaction to ensure data integrity
        DB::beginTransaction();

        try {
            // If the new academic year is set as current, update all others to not current
            if ($request->has('is_current') && $request->is_current) {
                AcademicYear::where('is_current', true)
                    ->update(['is_current' => false]);
            }

            // If the new academic year is set as active, update all others to not active
            if ($request->has('is_active') && $request->is_active) {
                AcademicYear::where('is_active', true)
                    ->update(['is_active' => false]);
            }

            AcademicYear::create([
                'name' => $request->name,
                'code' => $request->code,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'description' => $request->description,
                'is_current' => $request->has('is_current') ? $request->is_current : false,
                'is_active' => $request->has('is_active') ? $request->is_active : true,
            ]);

            DB::commit();

            return redirect()->route('academic-years.index')
                ->with('success', 'Academic year created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Error creating academic year: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified academic year.
     *
     * @param  \App\Models\AcademicYear  $academicYear
     * @return \Illuminate\Http\Response
     */
    public function show(AcademicYear $academicYear)
    {
        // Check if user has permission to manage settings
        $this->authorize('manage-settings');
        
        return view('academic-years.show', compact('academicYear'));
    }

    /**
     * Show the form for editing the specified academic year.
     *
     * @param  \App\Models\AcademicYear  $academicYear
     * @return \Illuminate\Http\Response
     */
    public function edit(AcademicYear $academicYear)
    {
        // Check if user has permission to manage settings
        $this->authorize('manage-settings');
        
        return view('academic-years.edit', compact('academicYear'));
    }

    /**
     * Update the specified academic year in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AcademicYear  $academicYear
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AcademicYear $academicYear)
    {
        // Check if user has permission to manage settings
        $this->authorize('manage-settings');

        $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'code' => ['required', 'string', 'max:20', 'unique:academic_years,code,' . $academicYear->id],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'description' => ['nullable', 'string'],
            'is_current' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        // Start a transaction to ensure data integrity
        DB::beginTransaction();

        try {
            // If this academic year is being set as current, update all others to not current
            if ($request->has('is_current') && $request->is_current && !$academicYear->is_current) {
                AcademicYear::where('is_current', true)
                    ->update(['is_current' => false]);
            }

            // If this academic year is being set as active, update all others to not active
            if ($request->has('is_active') && $request->is_active && !$academicYear->is_active) {
                AcademicYear::where('is_active', true)
                    ->update(['is_active' => false]);
            }

            $academicYear->update([
                'name' => $request->name,
                'code' => $request->code,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'description' => $request->description,
                'is_current' => $request->has('is_current') ? $request->is_current : false,
                'is_active' => $request->has('is_active') ? $request->is_active : $academicYear->is_active,
            ]);

            DB::commit();

            return redirect()->route('academic-years.index')
                ->with('success', 'Academic year updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Error updating academic year: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified academic year from storage.
     *
     * @param  \App\Models\AcademicYear  $academicYear
     * @return \Illuminate\Http\Response
     */
    public function destroy(AcademicYear $academicYear)
    {
        // Check if user has permission to manage settings
        $this->authorize('manage-settings');
        
        // Don't allow deletion of the current academic year
        if ($academicYear->is_current) {
            return redirect()->route('academic-years.index')
                ->with('error', 'Cannot delete the current academic year.');
        }
        
        // Check if there are any dependencies (classes, enrollments, etc.)
        // This should be implemented based on your specific relationships
        
        $academicYear->delete();
        
        return redirect()->route('academic-years.index')
            ->with('success', 'Academic year deleted successfully.');
    }

    /**
     * Set the specified academic year as current.
     *
     * @param  \App\Models\AcademicYear  $academicYear
     * @return \Illuminate\Http\Response
     */
    public function setCurrent(AcademicYear $academicYear)
    {
        // Check if user has permission to manage settings
        $this->authorize('manage-settings');

        try {
            $academicYear->setAsCurrent();

            return redirect()->route('academic-years.index')
                ->with('success', 'Academic year set as current successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error setting academic year as current: ' . $e->getMessage());
        }
    }

    /**
     * Set the specified academic year as active.
     *
     * @param  \App\Models\AcademicYear  $academicYear
     * @return \Illuminate\Http\Response
     */
    public function setActive(AcademicYear $academicYear)
    {
        // Check if user has permission to manage settings
        $this->authorize('manage-settings');

        try {
            $academicYear->setAsActive();

            return redirect()->route('academic-years.index')
                ->with('success', 'Academic year set as active successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error setting academic year as active: ' . $e->getMessage());
        }
    }

    /**
     * Set active year using the improved method
     */
    public function setActiveYear($id)
    {
        try {
            $academicYear = AcademicYear::findOrFail($id);
            $academicYear->setAsCurrent();

            return response()->json([
                'success' => true,
                'message' => 'Academic year set as current successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error setting academic year as current: ' . $e->getMessage()
            ], 500);
        }
    }
} 