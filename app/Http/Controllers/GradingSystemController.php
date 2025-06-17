<?php

namespace App\Http\Controllers;

use App\Models\GradingSystem;
use App\Models\GradeScale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class GradingSystemController extends Controller
{
    /**
     * Display a listing of grading systems.
     */
    public function index()
    {
        $this->authorize('manage-settings');

        $gradingSystems = GradingSystem::with('gradeScales')
            ->ordered()
            ->paginate(10);

        return view('grading-systems.index', compact('gradingSystems'));
    }

    /**
     * Show the form for creating a new grading system.
     */
    public function create()
    {
        $this->authorize('manage-settings');

        return view('grading-systems.create');
    }

    /**
     * Store a newly created grading system.
     */
    public function store(Request $request)
    {
        $this->authorize('manage-settings');

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:grading_systems,code'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_default' => ['boolean'],
            'order_sequence' => ['required', 'integer', 'min:1'],
            'grade_scales' => ['required', 'array', 'min:1'],
            'grade_scales.*.grade_letter' => ['required', 'string', 'max:5'],
            'grade_scales.*.grade_point' => ['required', 'numeric', 'min:0', 'max:10'],
            'grade_scales.*.min_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'grade_scales.*.max_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'grade_scales.*.description' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Create grading system
                $gradingSystem = GradingSystem::create([
                    'name' => $request->name,
                    'code' => strtoupper($request->code),
                    'description' => $request->description,
                    'is_default' => $request->boolean('is_default'),
                    'order_sequence' => $request->order_sequence,
                    'status' => 'active',
                ]);

                // Create grade scales
                foreach ($request->grade_scales as $index => $gradeData) {
                    GradeScale::create([
                        'grading_system_id' => $gradingSystem->id,
                        'grade_letter' => $gradeData['grade_letter'],
                        'grade_point' => $gradeData['grade_point'],
                        'min_percent' => $gradeData['min_percent'],
                        'max_percent' => $gradeData['max_percent'],
                        'description' => $gradeData['description'],
                        'order_sequence' => $index + 1,
                        'status' => 'active',
                    ]);
                }
            });

            return redirect()->route('grading-systems.index')
                ->with('success', 'Grading system created successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating grading system: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified grading system.
     */
    public function show(GradingSystem $gradingSystem)
    {
        $this->authorize('manage-settings');

        $gradingSystem->load('gradeScales');

        return view('grading-systems.show', compact('gradingSystem'));
    }

    /**
     * Show the form for editing the specified grading system.
     */
    public function edit(GradingSystem $gradingSystem)
    {
        $this->authorize('manage-settings');

        $gradingSystem->load('gradeScales');

        return view('grading-systems.edit', compact('gradingSystem'));
    }

    /**
     * Update the specified grading system.
     */
    public function update(Request $request, GradingSystem $gradingSystem)
    {
        $this->authorize('manage-settings');

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', Rule::unique('grading_systems', 'code')->ignore($gradingSystem->id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_default' => ['boolean'],
            'order_sequence' => ['required', 'integer', 'min:1'],
            'grade_scales' => ['required', 'array', 'min:1'],
            'grade_scales.*.grade_letter' => ['required', 'string', 'max:5'],
            'grade_scales.*.grade_point' => ['required', 'numeric', 'min:0', 'max:10'],
            'grade_scales.*.min_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'grade_scales.*.max_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'grade_scales.*.description' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            DB::transaction(function () use ($request, $gradingSystem) {
                // Update grading system
                $gradingSystem->update([
                    'name' => $request->name,
                    'code' => strtoupper($request->code),
                    'description' => $request->description,
                    'is_default' => $request->boolean('is_default'),
                    'order_sequence' => $request->order_sequence,
                ]);

                // Delete existing grade scales
                $gradingSystem->gradeScales()->delete();

                // Create new grade scales
                foreach ($request->grade_scales as $index => $gradeData) {
                    GradeScale::create([
                        'grading_system_id' => $gradingSystem->id,
                        'grade_letter' => $gradeData['grade_letter'],
                        'grade_point' => $gradeData['grade_point'],
                        'min_percent' => $gradeData['min_percent'],
                        'max_percent' => $gradeData['max_percent'],
                        'description' => $gradeData['description'],
                        'order_sequence' => $index + 1,
                        'status' => 'active',
                    ]);
                }
            });

            return redirect()->route('grading-systems.index')
                ->with('success', 'Grading system updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating grading system: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified grading system.
     */
    public function destroy(GradingSystem $gradingSystem)
    {
        $this->authorize('manage-settings');

        if (!$gradingSystem->canBeDeleted()) {
            return redirect()->back()
                ->with('error', 'Cannot delete this grading system. It is either the default system or is being used by exams.');
        }

        try {
            $gradingSystem->delete();

            return redirect()->route('grading-systems.index')
                ->with('success', 'Grading system deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting grading system: ' . $e->getMessage());
        }
    }

    /**
     * Set grading system as default
     */
    public function setDefault(GradingSystem $gradingSystem)
    {
        $this->authorize('manage-settings');

        try {
            $gradingSystem->setAsDefault();

            return redirect()->back()
                ->with('success', 'Grading system set as default successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error setting default grading system: ' . $e->getMessage());
        }
    }

    /**
     * Toggle grading system status
     */
    public function toggleStatus(GradingSystem $gradingSystem)
    {
        $this->authorize('manage-settings');

        if ($gradingSystem->is_default && $gradingSystem->status === 'active') {
            return redirect()->back()
                ->with('error', 'Cannot deactivate the default grading system.');
        }

        $gradingSystem->update([
            'status' => $gradingSystem->status === 'active' ? 'inactive' : 'active'
        ]);

        return redirect()->back()
            ->with('success', 'Grading system status updated successfully.');
    }
}
