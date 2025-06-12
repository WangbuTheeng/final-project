<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Course;
use Illuminate\Http\Request;

class ExamController extends Controller
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
     * Display a listing of the exams.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Check if user has permission to view exams
        $this->authorize('view-exams');

        $exams = Exam::with('course')->orderBy('created_at', 'desc')->paginate(15);
        return view('exams.index', compact('exams'));
    }

    /**
     * Show the form for creating a new exam.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Check if user has permission to create exams
        $this->authorize('create-exams');
        
        $courses = Course::all();
        return view('exams.create', compact('courses'));
    }

    /**
     * Store a newly created exam in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Check if user has permission to create exams
        $this->authorize('create-exams');
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'course_id' => ['required', 'exists:courses,id'],
            'total_marks' => ['required', 'numeric', 'min:0'],
            'theory_marks' => ['nullable', 'numeric', 'min:0'],
            'practical_marks' => ['nullable', 'numeric', 'min:0'],
            'passing_marks' => ['required', 'numeric', 'min:0', 'lte:total_marks'],
        ]);

        // Ensure theory + practical marks = total marks if both are provided
        if ($request->filled('theory_marks') && $request->filled('practical_marks')) {
            $totalCalculated = $request->theory_marks + $request->practical_marks;
            if ($totalCalculated != $request->total_marks) {
                return redirect()->back()
                    ->with('error', 'Theory marks + practical marks must equal total marks')
                    ->withInput();
            }
        }

        Exam::create([
            'name' => $request->name,
            'course_id' => $request->course_id,
            'total_marks' => $request->total_marks,
            'theory_marks' => $request->theory_marks,
            'practical_marks' => $request->practical_marks,
            'passing_marks' => $request->passing_marks,
        ]);

        return redirect()->route('exams.index')
            ->with('success', 'Exam created successfully.');
    }

    /**
     * Display the specified exam.
     *
     * @param  \App\Models\Exam  $exam
     * @return \Illuminate\Http\Response
     */
    public function show(Exam $exam)
    {
        // Check if user has permission to view exams
        $this->authorize('view-exams');
        
        $exam->load('course');
        return view('exams.show', compact('exam'));
    }

    /**
     * Show the form for editing the specified exam.
     *
     * @param  \App\Models\Exam  $exam
     * @return \Illuminate\Http\Response
     */
    public function edit(Exam $exam)
    {
        // Check if user has permission to edit exams
        $this->authorize('edit-exams');
        
        $courses = Course::all();
        return view('exams.edit', compact('exam', 'courses'));
    }

    /**
     * Update the specified exam in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Exam  $exam
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Exam $exam)
    {
        // Check if user has permission to edit exams
        $this->authorize('edit-exams');
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'course_id' => ['required', 'exists:courses,id'],
            'total_marks' => ['required', 'numeric', 'min:0'],
            'theory_marks' => ['nullable', 'numeric', 'min:0'],
            'practical_marks' => ['nullable', 'numeric', 'min:0'],
            'passing_marks' => ['required', 'numeric', 'min:0', 'lte:total_marks'],
        ]);

        // Ensure theory + practical marks = total marks if both are provided
        if ($request->filled('theory_marks') && $request->filled('practical_marks')) {
            $totalCalculated = $request->theory_marks + $request->practical_marks;
            if ($totalCalculated != $request->total_marks) {
                return redirect()->back()
                    ->with('error', 'Theory marks + practical marks must equal total marks')
                    ->withInput();
            }
        }

        $exam->name = $request->name;
        $exam->course_id = $request->course_id;
        $exam->total_marks = $request->total_marks;
        $exam->theory_marks = $request->theory_marks;
        $exam->practical_marks = $request->practical_marks;
        $exam->passing_marks = $request->passing_marks;
        $exam->save();

        return redirect()->route('exams.index')
            ->with('success', 'Exam updated successfully.');
    }

    /**
     * Remove the specified exam from storage.
     *
     * @param  \App\Models\Exam  $exam
     * @return \Illuminate\Http\Response
     */
    public function destroy(Exam $exam)
    {
        // Check if user has permission to delete exams
        $this->authorize('delete-exams');
        
        // Check if there are any grades associated with this exam
        // This should be implemented based on your specific relationships
        
        $exam->delete();
        
        return redirect()->route('exams.index')
            ->with('success', 'Exam deleted successfully.');
    }
} 