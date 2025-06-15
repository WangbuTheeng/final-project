@extends('layouts.app')

@section('title', 'Edit Exam - ' . $exam->title)

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Edit Exam</h1>
                        <p class="text-sm text-gray-600 mt-1">{{ $exam->title }}</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('exams.show', $exam) }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to Exam
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white shadow rounded-lg">
            <form method="POST" action="{{ route('exams.update', $exam) }}">
                @csrf
                @method('PUT')
                
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Exam Information</h2>
                </div>

                <div class="px-6 py-4 space-y-6">
                    <!-- Basic Information -->
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700">
                                Exam Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="title" 
                                   id="title" 
                                   value="{{ old('title', $exam->title) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('title') border-red-300 @enderror"
                                   required>
                            @error('title')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="class_section_id" class="block text-sm font-medium text-gray-700">
                                Class <span class="text-red-500">*</span>
                            </label>
                            <select name="class_section_id" 
                                    id="class_section_id" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('class_section_id') border-red-300 @enderror"
                                    required>
                                <option value="">Select a class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_section_id', $exam->class_section_id) == $class->id ? 'selected' : '' }}>
                                        {{ $class->course->title }} - {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('class_section_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="academic_year_id" class="block text-sm font-medium text-gray-700">
                                Academic Year <span class="text-red-500">*</span>
                            </label>
                            <select name="academic_year_id" 
                                    id="academic_year_id" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('academic_year_id') border-red-300 @enderror"
                                    required>
                                <option value="">Select academic year</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ old('academic_year_id', $exam->academic_year_id) == $year->id ? 'selected' : '' }}>
                                        {{ $year->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('academic_year_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="semester" class="block text-sm font-medium text-gray-700">
                                Semester <span class="text-red-500">*</span>
                            </label>
                            <select name="semester" 
                                    id="semester" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('semester') border-red-300 @enderror"
                                    required>
                                <option value="">Select semester</option>
                                <option value="first" {{ old('semester', $exam->semester) == 'first' ? 'selected' : '' }}>First Semester</option>
                                <option value="second" {{ old('semester', $exam->semester) == 'second' ? 'selected' : '' }}>Second Semester</option>
                            </select>
                            @error('semester')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="exam_type" class="block text-sm font-medium text-gray-700">
                                Exam Type <span class="text-red-500">*</span>
                            </label>
                            <select name="exam_type" 
                                    id="exam_type" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('exam_type') border-red-300 @enderror"
                                    required>
                                <option value="">Select exam type</option>
                                <option value="quiz" {{ old('exam_type', $exam->exam_type) == 'quiz' ? 'selected' : '' }}>Quiz</option>
                                <option value="test" {{ old('exam_type', $exam->exam_type) == 'test' ? 'selected' : '' }}>Test</option>
                                <option value="midterm" {{ old('exam_type', $exam->exam_type) == 'midterm' ? 'selected' : '' }}>Midterm</option>
                                <option value="final" {{ old('exam_type', $exam->exam_type) == 'final' ? 'selected' : '' }}>Final</option>
                            </select>
                            @error('exam_type')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Date and Time -->
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-3">
                        <div>
                            <label for="exam_date" class="block text-sm font-medium text-gray-700">
                                Exam Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   name="exam_date" 
                                   id="exam_date" 
                                   value="{{ old('exam_date', $exam->exam_date->format('Y-m-d')) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('exam_date') border-red-300 @enderror"
                                   required>
                            @error('exam_date')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="exam_time" class="block text-sm font-medium text-gray-700">
                                Start Time <span class="text-red-500">*</span>
                            </label>
                            <input type="time" 
                                   name="exam_time" 
                                   id="exam_time" 
                                   value="{{ old('exam_time', $exam->exam_date->format('H:i')) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('exam_time') border-red-300 @enderror"
                                   required>
                            @error('exam_time')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="duration_minutes" class="block text-sm font-medium text-gray-700">
                                Duration (minutes) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   name="duration_minutes" 
                                   id="duration_minutes" 
                                   value="{{ old('duration_minutes', $exam->duration_minutes) }}"
                                   min="15"
                                   max="480"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('duration_minutes') border-red-300 @enderror"
                                   required>
                            @error('duration_minutes')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Marks and Venue -->
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-3">
                        <div>
                            <label for="total_marks" class="block text-sm font-medium text-gray-700">
                                Total Marks <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   name="total_marks" 
                                   id="total_marks" 
                                   value="{{ old('total_marks', $exam->total_marks) }}"
                                   min="1"
                                   max="1000"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('total_marks') border-red-300 @enderror"
                                   required>
                            @error('total_marks')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="pass_mark" class="block text-sm font-medium text-gray-700">
                                Pass Mark <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   name="pass_mark" 
                                   id="pass_mark" 
                                   value="{{ old('pass_mark', $exam->pass_mark) }}"
                                   min="1"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('pass_mark') border-red-300 @enderror"
                                   required>
                            @error('pass_mark')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="venue" class="block text-sm font-medium text-gray-700">
                                Venue
                            </label>
                            <input type="text" 
                                   name="venue" 
                                   id="venue" 
                                   value="{{ old('venue', $exam->venue) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('venue') border-red-300 @enderror"
                                   placeholder="e.g., Room 101, Main Hall">
                            @error('venue')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status" 
                                    id="status" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('status') border-red-300 @enderror"
                                    required>
                                <option value="scheduled" {{ old('status', $exam->status) == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="ongoing" {{ old('status', $exam->status) == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                <option value="completed" {{ old('status', $exam->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status', $exam->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Instructions -->
                    <div>
                        <label for="instructions" class="block text-sm font-medium text-gray-700">
                            Instructions
                        </label>
                        <textarea name="instructions" 
                                  id="instructions" 
                                  rows="4"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('instructions') border-red-300 @enderror"
                                  placeholder="Enter any special instructions for the exam...">{{ old('instructions', $exam->instructions) }}</textarea>
                        @error('instructions')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-sm text-gray-500">Optional instructions for students taking the exam</p>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('exams.show', $exam) }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Update Exam
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Auto-calculate pass mark percentage
document.getElementById('total_marks').addEventListener('input', function() {
    const totalMarks = parseInt(this.value) || 0;
    const passMarkInput = document.getElementById('pass_mark');
    
    // Set max attribute for pass mark
    passMarkInput.setAttribute('max', totalMarks);
});

// Validate pass mark doesn't exceed total marks
document.getElementById('pass_mark').addEventListener('input', function() {
    const totalMarks = parseInt(document.getElementById('total_marks').value) || 0;
    const passMark = parseInt(this.value) || 0;
    
    if (passMark > totalMarks) {
        this.value = totalMarks;
    }
});

// Initialize max attribute on page load
document.addEventListener('DOMContentLoaded', function() {
    const totalMarks = parseInt(document.getElementById('total_marks').value) || 0;
    document.getElementById('pass_mark').setAttribute('max', totalMarks);
});
</script>
@endpush
@endsection
