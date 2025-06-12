@extends('layouts.app')

@section('title', 'Edit Academic Year')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <nav class="flex mb-4" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            <li class="inline-flex items-center">
                                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                                    <i class="fas fa-home w-4 h-4 mr-2"></i>
                                    Dashboard
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <i class="fas fa-chevron-right text-gray-400 w-4 h-4"></i>
                                    <a href="{{ route('academic-years.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">Academic Years</a>
                                </div>
                            </li>
                            <li aria-current="page">
                                <div class="flex items-center">
                                    <i class="fas fa-chevron-right text-gray-400 w-4 h-4"></i>
                                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Edit</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Academic Year</h1>
                    <p class="mt-2 text-sm text-gray-600">Update the academic year information and settings</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('academic-years.index') }}"
                       class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <i class="fas fa-arrow-left w-4 h-4 mr-2"></i>
                        Back to List
                    </a>
                    <a href="{{ route('academic-years.show', $academicYear) }}"
                       class="inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <i class="fas fa-eye w-4 h-4 mr-2"></i>
                        View Details
                    </a>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="mb-6 rounded-lg bg-green-50 border border-green-200 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400 w-5 h-5"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400 w-5 h-5"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-400 w-5 h-5"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">There were some errors with your submission</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form Card -->
        <div class="bg-white shadow-xl rounded-xl border border-gray-200 overflow-hidden">
            <form method="POST" action="{{ route('academic-years.update', $academicYear) }}" class="space-y-0">
                @csrf
                @method('PUT')

                <!-- Form Header -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-edit text-white w-5 h-5"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-white">Academic Year Information</h3>
                            <p class="text-blue-100 text-sm">Update the details for {{ $academicYear->name }}</p>
                        </div>
                    </div>
                </div>

                <!-- Form Body -->
                <div class="p-6 space-y-8">
                    <!-- Basic Information Section -->
                    <div class="space-y-6">
                        <div class="border-l-4 border-blue-500 pl-4">
                            <h4 class="text-lg font-medium text-gray-900">Basic Information</h4>
                            <p class="text-sm text-gray-600">Enter the basic details for the academic year</p>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="name" class="block text-sm font-semibold text-gray-700">
                                    Academic Year Name <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-calendar-alt text-gray-400 w-4 h-4"></i>
                                    </div>
                                    <input type="text"
                                           id="name"
                                           name="name"
                                           value="{{ old('name', $academicYear->name) }}"
                                           placeholder="e.g., 2023/2024"
                                           required
                                           class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('name') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                                </div>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle w-4 h-4 mr-1"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="code" class="block text-sm font-semibold text-gray-700">
                                    Academic Year Code <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-tag text-gray-400 w-4 h-4"></i>
                                    </div>
                                    <input type="text"
                                           id="code"
                                           name="code"
                                           value="{{ old('code', $academicYear->code) }}"
                                           placeholder="e.g., 2023-24"
                                           required
                                           class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('code') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                                </div>
                                @error('code')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle w-4 h-4 mr-1"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Date Range Section -->
                    <div class="space-y-6">
                        <div class="border-l-4 border-green-500 pl-4">
                            <h4 class="text-lg font-medium text-gray-900">Date Range</h4>
                            <p class="text-sm text-gray-600">Set the start and end dates for the academic year</p>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="start_date" class="block text-sm font-semibold text-gray-700">
                                    Start Date <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-calendar-plus text-gray-400 w-4 h-4"></i>
                                    </div>
                                    <input type="date"
                                           id="start_date"
                                           name="start_date"
                                           value="{{ old('start_date', $academicYear->start_date?->format('Y-m-d')) }}"
                                           required
                                           class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('start_date') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                                </div>
                                @error('start_date')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle w-4 h-4 mr-1"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="end_date" class="block text-sm font-semibold text-gray-700">
                                    End Date <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-calendar-minus text-gray-400 w-4 h-4"></i>
                                    </div>
                                    <input type="date"
                                           id="end_date"
                                           name="end_date"
                                           value="{{ old('end_date', $academicYear->end_date?->format('Y-m-d')) }}"
                                           required
                                           class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('end_date') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                                </div>
                                @error('end_date')
                                    <p class="mt-1 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle w-4 h-4 mr-1"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Description Section -->
                    <div class="space-y-6">
                        <div class="border-l-4 border-purple-500 pl-4">
                            <h4 class="text-lg font-medium text-gray-900">Description</h4>
                            <p class="text-sm text-gray-600">Add an optional description for this academic year</p>
                        </div>

                        <div class="space-y-2">
                            <label for="description" class="block text-sm font-semibold text-gray-700">
                                Description <span class="text-gray-400">(Optional)</span>
                            </label>
                            <div class="relative">
                                <textarea id="description"
                                          name="description"
                                          rows="4"
                                          placeholder="Enter a description for this academic year (optional)"
                                          class="block w-full px-3 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 resize-none @error('description') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">{{ old('description', $academicYear->description) }}</textarea>
                            </div>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle w-4 h-4 mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <!-- Settings Section -->
                    <div class="space-y-6">
                        <div class="border-l-4 border-orange-500 pl-4">
                            <h4 class="text-lg font-medium text-gray-900">Settings</h4>
                            <p class="text-sm text-gray-600">Configure the status and behavior of this academic year</p>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Current Academic Year Setting -->
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5 mt-1">
                                        <input id="is_current"
                                               name="is_current"
                                               type="checkbox"
                                               value="1"
                                               {{ old('is_current', $academicYear->is_current) ? 'checked' : '' }}
                                               class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded transition-colors duration-200">
                                    </div>
                                    <div class="ml-3">
                                        <label for="is_current" class="text-sm font-semibold text-gray-900 cursor-pointer">
                                            Set as Current Academic Year
                                        </label>
                                        <p class="text-xs text-gray-600 mt-1">
                                            <i class="fas fa-info-circle w-3 h-3 mr-1"></i>
                                            Only one academic year can be current at a time. This will be used as the default year for new operations.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Active Academic Year Setting -->
                            <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5 mt-1">
                                        <input id="is_active"
                                               name="is_active"
                                               type="checkbox"
                                               value="1"
                                               {{ old('is_active', $academicYear->is_active) ? 'checked' : '' }}
                                               class="focus:ring-yellow-500 h-4 w-4 text-yellow-600 border-gray-300 rounded transition-colors duration-200">
                                    </div>
                                    <div class="ml-3">
                                        <label for="is_active" class="text-sm font-semibold text-gray-900 cursor-pointer">
                                            Active Academic Year
                                        </label>
                                        <p class="text-xs text-gray-600 mt-1">
                                            <i class="fas fa-exclamation-triangle w-3 h-3 mr-1 text-yellow-600"></i>
                                            Only one academic year can be active at a time. Setting this as active will automatically deactivate all other academic years.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="text-sm text-gray-600">
                            <i class="fas fa-info-circle w-4 h-4 mr-1"></i>
                            Last updated: {{ $academicYear->updated_at->format('M d, Y \a\t g:i A') }}
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <a href="{{ route('academic-years.index') }}"
                               class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                <i class="fas fa-times w-4 h-4 mr-2"></i>
                                Cancel
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center justify-center px-6 py-3 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                <i class="fas fa-save w-4 h-4 mr-2"></i>
                                Update Academic Year
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Custom JavaScript for enhanced UX -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add confirmation for active checkbox
    const activeCheckbox = document.getElementById('is_active');
    const currentCheckbox = document.getElementById('is_current');

    if (activeCheckbox) {
        activeCheckbox.addEventListener('change', function() {
            if (this.checked && !{{ $academicYear->is_active ? 'true' : 'false' }}) {
                if (!confirm('Setting this academic year as active will deactivate all other academic years. Are you sure you want to continue?')) {
                    this.checked = false;
                }
            }
        });
    }

    if (currentCheckbox) {
        currentCheckbox.addEventListener('change', function() {
            if (this.checked && !{{ $academicYear->is_current ? 'true' : 'false' }}) {
                if (!confirm('Setting this academic year as current will make it the default for new operations. Are you sure?')) {
                    this.checked = false;
                }
            }
        });
    }

    // Form validation
    const form = document.querySelector('form');
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');

    function validateDates() {
        if (startDate.value && endDate.value) {
            const start = new Date(startDate.value);
            const end = new Date(endDate.value);

            if (start >= end) {
                endDate.setCustomValidity('End date must be after start date');
            } else {
                endDate.setCustomValidity('');
            }
        }
    }

    startDate.addEventListener('change', validateDates);
    endDate.addEventListener('change', validateDates);
});
</script>
@endsection
