@extends('layouts.dashboard')

@section('title', 'Create Fee')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center space-x-4">
            <a href="{{ route('finance.fees.index') }}" class="text-gray-600 hover:text-gray-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Create Fee</h1>
                <p class="text-gray-600 mt-2">Add a new fee structure</p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-md">
        <form action="{{ route('finance.fees.store') }}" method="POST" class="p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Fee Name -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Fee Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                           placeholder="Enter fee name">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fee Code -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">Fee Code</label>
                    <input type="text" name="code" id="code" value="{{ old('code') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('code') border-red-500 @enderror"
                           placeholder="Auto-generated if empty">
                    <p class="mt-1 text-xs text-gray-500">Leave empty to auto-generate based on fee type and year</p>
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fee Type -->
                <div>
                    <label for="fee_type" class="block text-sm font-medium text-gray-700 mb-2">Fee Type *</label>
                    <select name="fee_type" id="fee_type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('fee_type') border-red-500 @enderror">
                        <option value="">Select Fee Type</option>
                        <option value="tuition" {{ old('fee_type') == 'tuition' ? 'selected' : '' }}>Tuition Fee</option>
                        <option value="library" {{ old('fee_type') == 'library' ? 'selected' : '' }}>Library Fee</option>
                        <option value="laboratory" {{ old('fee_type') == 'laboratory' ? 'selected' : '' }}>Laboratory Fee</option>
                        <option value="sports" {{ old('fee_type') == 'sports' ? 'selected' : '' }}>Sports Fee</option>
                        <option value="medical" {{ old('fee_type') == 'medical' ? 'selected' : '' }}>Medical Fee</option>
                        <option value="accommodation" {{ old('fee_type') == 'accommodation' ? 'selected' : '' }}>Accommodation Fee</option>
                        <option value="registration" {{ old('fee_type') == 'registration' ? 'selected' : '' }}>Registration Fee</option>
                        <option value="examination" {{ old('fee_type') == 'examination' ? 'selected' : '' }}>Examination Fee</option>
                        <option value="other" {{ old('fee_type') == 'other' ? 'selected' : '' }}>Other Fee</option>
                    </select>
                    @error('fee_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Amount -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Amount (NRs) *</label>
                    <input type="number" name="amount" id="amount" value="{{ old('amount') }}" required min="0" step="0.01"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('amount') border-red-500 @enderror"
                           placeholder="0.00">
                    @error('amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Academic Year -->
                <div>
                    <label for="academic_year_id" class="block text-sm font-medium text-gray-700 mb-2">Academic Year *</label>
                    <select name="academic_year_id" id="academic_year_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('academic_year_id') border-red-500 @enderror">
                        <option value="">Select Academic Year</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
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
                    <select name="course_id" id="course_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('course_id') border-red-500 @enderror">
                        <option value="">All Courses</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->title }} ({{ $course->code }}) - {{ $course->faculty->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Leave empty to apply to all courses</p>
                    @error('course_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Department -->
                <div>
                    <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">Department (Optional)</label>
                    <select name="department_id" id="department_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('department_id') border-red-500 @enderror">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Leave empty to apply to all departments</p>
                    @error('department_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Due Date -->
                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                    <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('due_date') border-red-500 @enderror">
                    @error('due_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                              placeholder="Enter fee description">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Checkboxes -->
                <div class="md:col-span-2">
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input type="checkbox" name="is_mandatory" id="is_mandatory" value="1" 
                                   {{ old('is_mandatory') ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="is_mandatory" class="ml-2 block text-sm text-gray-900">
                                Mandatory Fee
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1" 
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                Active Fee
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('finance.fees.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 font-medium transition duration-200">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium transition duration-200">
                    Create Fee
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
