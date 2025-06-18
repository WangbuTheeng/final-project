@extends('layouts.dashboard')

@section('title', 'Edit Fee')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Fee</h1>
            <p class="text-gray-600 mt-2">Update fee information and settings</p>
        </div>
        <a href="{{ route('finance.fees.show', $fee) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Fee
        </a>
    </div>

    <!-- Edit Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('finance.fees.update', $fee) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                    
                    <!-- Fee Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Fee Name *</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $fee->name) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror" 
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Fee Code -->
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">Fee Code</label>
                        <input type="text" id="code" name="code" value="{{ old('code', $fee->code) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('code') border-red-500 @enderror" 
                               placeholder="Auto-generated if empty">
                        @error('code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Leave empty to auto-generate based on fee type and academic year</p>
                    </div>

                    <!-- Fee Type -->
                    <div>
                        <label for="fee_type" class="block text-sm font-medium text-gray-700 mb-2">Fee Type *</label>
                        <select id="fee_type" name="fee_type" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('fee_type') border-red-500 @enderror" 
                                required>
                            <option value="">Select Fee Type</option>
                            <option value="tuition" {{ old('fee_type', $fee->fee_type) == 'tuition' ? 'selected' : '' }}>Tuition</option>
                            <option value="library" {{ old('fee_type', $fee->fee_type) == 'library' ? 'selected' : '' }}>Library</option>
                            <option value="laboratory" {{ old('fee_type', $fee->fee_type) == 'laboratory' ? 'selected' : '' }}>Laboratory</option>
                            <option value="sports" {{ old('fee_type', $fee->fee_type) == 'sports' ? 'selected' : '' }}>Sports</option>
                            <option value="medical" {{ old('fee_type', $fee->fee_type) == 'medical' ? 'selected' : '' }}>Medical</option>
                            <option value="accommodation" {{ old('fee_type', $fee->fee_type) == 'accommodation' ? 'selected' : '' }}>Accommodation</option>
                            <option value="registration" {{ old('fee_type', $fee->fee_type) == 'registration' ? 'selected' : '' }}>Registration</option>
                            <option value="examination" {{ old('fee_type', $fee->fee_type) == 'examination' ? 'selected' : '' }}>Examination</option>
                            <option value="other" {{ old('fee_type', $fee->fee_type) == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('fee_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Amount -->
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Amount (₹) *</label>
                        <input type="number" id="amount" name="amount" value="{{ old('amount', $fee->amount) }}" 
                               step="0.01" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('amount') border-red-500 @enderror" 
                               required>
                        @error('amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea id="description" name="description" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror" 
                                  placeholder="Optional description of the fee">{{ old('description', $fee->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Academic Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Academic Information</h3>
                    
                    <!-- Academic Year -->
                    <div>
                        <label for="academic_year_id" class="block text-sm font-medium text-gray-700 mb-2">Academic Year *</label>
                        <select id="academic_year_id" name="academic_year_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('academic_year_id') border-red-500 @enderror" 
                                required>
                            <option value="">Select Academic Year</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ old('academic_year_id', $fee->academic_year_id) == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('academic_year_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Course -->
                    <div>
                        <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">Course</label>
                        <select id="course_id" name="course_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('course_id') border-red-500 @enderror">
                            <option value="">All Courses</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ old('course_id', $fee->course_id) == $course->id ? 'selected' : '' }}>
                                    {{ $course->title }} ({{ $course->code }}) - {{ $course->faculty->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('course_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Leave empty to apply to all courses</p>
                    </div>

                    <!-- Department -->
                    <div>
                        <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">Department (Optional)</label>
                        <select id="department_id" name="department_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('department_id') border-red-500 @enderror">
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id', $fee->department_id) == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Leave empty to apply to all departments</p>
                    </div>

                    <!-- Due Date -->
                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                        <input type="date" id="due_date" name="due_date" value="{{ old('due_date', $fee->due_date?->format('Y-m-d')) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('due_date') border-red-500 @enderror">
                        @error('due_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Settings -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Settings</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex items-center">
                        <input type="checkbox" id="is_mandatory" name="is_mandatory" value="1" 
                               {{ old('is_mandatory', $fee->is_mandatory) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_mandatory" class="ml-2 block text-sm text-gray-900">
                            Mandatory Fee
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="is_active" name="is_active" value="1" 
                               {{ old('is_active', $fee->is_active) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">
                            Active
                        </label>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('finance.fees.show', $fee) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg font-medium transition duration-200">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                    Update Fee
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
