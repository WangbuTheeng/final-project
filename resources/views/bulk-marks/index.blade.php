@extends('layouts.dashboard')

@section('title', 'Bulk Marks Entry')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Bulk Marks Entry</h1>
            <p class="text-gray-600 mt-1">Enter marks for multiple students and subjects at once</p>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Exam Selection -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Select Exam</h2>
        
        <form method="GET" action="{{ route('bulk-marks.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="exam_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Exam <span class="text-red-500">*</span>
                    </label>
                    <select name="exam_id" id="exam_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Select an exam...</option>
                        @foreach($exams as $exam)
                            <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                                {{ $exam->title }} - {{ $exam->class->name }} ({{ $exam->academicYear->name }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Load Exam
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Selected Exam Details -->
    @if($selectedExam)
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Exam Details</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Exam Title</label>
                    <p class="text-gray-900">{{ $selectedExam->title }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Class</label>
                    <p class="text-gray-900">{{ $selectedExam->class->name }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Course</label>
                    <p class="text-gray-900">{{ $selectedExam->class->course->name }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Academic Year</label>
                    <p class="text-gray-900">{{ $selectedExam->academicYear->name }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Exam Type</label>
                    <p class="text-gray-900">{{ ucfirst($selectedExam->exam_type) }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Total Marks</label>
                    <p class="text-gray-900">{{ $selectedExam->total_marks }}</p>
                </div>
            </div>

            <!-- Subjects -->
            @if($selectedExam->is_multi_subject && $selectedExam->subjects->count() > 0)
                <div class="mt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Subjects</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($selectedExam->subjects as $subject)
                            <div class="border border-gray-200 rounded-lg p-3">
                                <h4 class="font-medium text-gray-900">{{ $subject->name }}</h4>
                                <p class="text-sm text-gray-600">Code: {{ $subject->code }}</p>
                                <div class="mt-2 text-sm">
                                    @php
                                        $subjectPivot = $subject->pivot ?? null;
                                        $theoryMarks = $subjectPivot ? $subjectPivot->theory_marks : ($subject->full_marks_theory ?? 0);
                                        $practicalMarks = $subjectPivot ? $subjectPivot->practical_marks : ($subject->full_marks_practical ?? 0);
                                    @endphp
                                    @if($theoryMarks > 0)
                                        <span class="text-blue-600">Theory: {{ $theoryMarks }}</span>
                                    @endif
                                    @if($practicalMarks > 0)
                                        <span class="text-green-600 ml-2">Practical: {{ $practicalMarks }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="mt-6 flex space-x-4">
                <a href="{{ route('bulk-marks.create', ['exam_id' => $selectedExam->id]) }}" 
                   class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                    Enter Marks
                </a>
                
                <a href="{{ route('exams.show', $selectedExam) }}" 
                   class="bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    View Exam Details
                </a>
            </div>
        </div>
    @endif

    <!-- Instructions -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-medium text-blue-900 mb-3">Instructions</h3>
        <ul class="list-disc list-inside text-blue-800 space-y-1">
            <li>Select an exam from the dropdown above to view its details</li>
            <li>Click "Enter Marks" to access the bulk marks entry form</li>
            <li>For multi-subject exams, you can enter marks for all subjects at once</li>
            <li>Marks will be automatically validated against subject maximums</li>
            <li>Grades and GPA will be calculated automatically based on college settings</li>
        </ul>
    </div>
</div>
@endsection
