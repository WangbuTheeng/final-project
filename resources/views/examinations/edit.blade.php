@extends('layouts.dashboard')

@section('title', '🇳🇵 Edit Examination')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">🇳🇵 Edit Examination</h1>
            <p class="mt-1 text-sm text-gray-500">Edit examination following Nepali educational standards</p>
        </div>
        <div class="mt-4 sm:mt-0 space-x-3">
            <a href="{{ route('examinations.show', $examination) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Examination
            </a>
            <a href="{{ route('examinations.index') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-list mr-2"></i>
                All Examinations
            </a>
        </div>
    </div>

    <!-- Current Examination Info -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex items-center mb-4">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                    <i class="fas fa-edit text-white"></i>
                </div>
            </div>
            <div class="ml-3">
                <h3 class="text-lg font-medium text-blue-900">Editing: {{ $examination->title }}</h3>
                <p class="text-sm text-blue-700">
                    {{ $examination->class->name ?? 'N/A' }} • 
                    {{ $examination->academicYear->name ?? 'N/A' }} • 
                    Status: {{ ucfirst($examination->status) }}
                </p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <form action="{{ route('examinations.update', $examination) }}" method="POST" id="examForm" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Examination Details</h3>
                <p class="mt-1 text-sm text-gray-500">Update your examination details</p>
            </div>

            <div class="px-6 pb-6 space-y-6">
                <!-- Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                            Examination Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('title') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                               id="title" name="title" value="{{ old('title', $examination->title) }}" required>
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="exam_type_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Exam Type <span class="text-red-500">*</span>
                        </label>
                        <select class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('exam_type_id') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                id="exam_type_id" name="exam_type_id" required>
                            <option value="">Select Exam Type</option>
                            @foreach($examTypes as $examType)
                                <option value="{{ $examType->id }}"
                                        {{ old('exam_type_id', $examination->exam_type_id) == $examType->id ? 'selected' : '' }}>
                                    {{ $examType->name }}
                                    @if($examType->default_weightage)
                                        ({{ $examType->default_weightage }}%)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('exam_type_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('status') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                id="status" name="status" required>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}"
                                        {{ old('status', $examination->status) == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="class_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Class <span class="text-red-500">*</span>
                        </label>
                        <select class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('class_id') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                id="class_id" name="class_id" required>
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('class_id', $examination->class_id) == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="academic_year_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Academic Year <span class="text-red-500">*</span>
                        </label>
                        <select class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('academic_year_id') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                id="academic_year_id" name="academic_year_id" required>
                            <option value="">Select Academic Year</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ old('academic_year_id', $examination->academic_year_id) == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('academic_year_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Schedule Information -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="exam_date" class="block text-sm font-medium text-gray-700 mb-1">
                            Exam Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('exam_date') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                               id="exam_date" name="exam_date" value="{{ old('exam_date', $examination->exam_date ? $examination->exam_date->format('Y-m-d') : '') }}" required>
                        @error('exam_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-1">
                            Duration (Minutes) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('duration_minutes') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                               id="duration_minutes" name="duration_minutes" value="{{ old('duration_minutes', $examination->duration_minutes) }}" min="30" max="480" required>
                        @error('duration_minutes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="venue" class="block text-sm font-medium text-gray-700 mb-1">
                            Venue
                        </label>
                        <input type="text" 
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('venue') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                               id="venue" name="venue" value="{{ old('venue', $examination->venue) }}">
                        @error('venue')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Marks Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="total_marks" class="block text-sm font-medium text-gray-700 mb-1">
                            Total Marks <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('total_marks') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                               id="total_marks" name="total_marks" value="{{ old('total_marks', $examination->total_marks) }}" min="1" step="0.01" required>
                        @error('total_marks')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="pass_mark" class="block text-sm font-medium text-gray-700 mb-1">
                            Pass Mark <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('pass_mark') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                               id="pass_mark" name="pass_mark" value="{{ old('pass_mark', $examination->pass_mark) }}" min="1" step="0.01" required>
                        @error('pass_mark')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Instructions -->
                <div>
                    <label for="instructions" class="block text-sm font-medium text-gray-700 mb-1">
                        Instructions
                    </label>
                    <textarea class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('instructions') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                              id="instructions" name="instructions" rows="4" placeholder="Enter examination instructions...">{{ old('instructions', $examination->instructions) }}</textarea>
                    @error('instructions')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('examinations.show', $examination) }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-save mr-2"></i>
                        Update Examination
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
