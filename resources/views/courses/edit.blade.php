@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Course</h1>
            <p class="mt-1 text-sm text-gray-500">Update course information</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('courses.show', $course) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-eye mr-2"></i>
                View Course
            </a>
            <a href="{{ route('courses.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Courses
            </a>
        </div>
    </div>

    <!-- Info Alert -->
    <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-md">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm">
                    <strong>New Structure:</strong> Courses are now directly managed under Faculties. 
                    Department assignment is optional for additional organization.
                </p>
            </div>
        </div>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium">Please correct the following errors:</h3>
                    <ul class="mt-2 text-sm list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Edit Form -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Course Information</h3>
        </div>

        <form action="{{ route('courses.update', $course) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Course Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Course Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           value="{{ old('title', $course->title) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('title') border-red-300 @enderror"
                           placeholder="e.g., Introduction to Computer Science"
                           required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Course Code -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                        Course Code <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="code" 
                           id="code" 
                           value="{{ old('code', $course->code) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('code') border-red-300 @enderror"
                           placeholder="e.g., CSC101"
                           maxlength="20"
                           style="text-transform: uppercase;"
                           required>
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Unique course code (max 20 characters)</p>
                </div>
            </div>

            <!-- Department Selection (Required) -->
            <div>
                <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Department <span class="text-red-500">*</span>
                </label>
                <select name="department_id"
                        id="department_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('department_id') border-red-300 @enderror"
                        required>
                    <option value="">Select a department</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ old('department_id', $course->department_id) == $department->id ? 'selected' : '' }}>
                            {{ $department->faculty ? $department->faculty->name : 'No Faculty' }} - {{ $department->name }} ({{ $department->code }})
                        </option>
                    @endforeach
                </select>
                @error('department_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Department determines the faculty for this course</p>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description
                </label>
                <textarea name="description" 
                          id="description" 
                          rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('description') border-red-300 @enderror"
                          placeholder="Brief description of the course...">{{ old('description', $course->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Organization Type Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Course Organization <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex items-center">
                        <input type="radio"
                               name="organization_type"
                               id="organization_yearly"
                               value="yearly"
                               {{ old('organization_type', $course->organization_type ?? 'yearly') === 'yearly' ? 'checked' : '' }}
                               class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300">
                        <label for="organization_yearly" class="ml-2 block text-sm text-gray-900">
                            Yearly Based (1-4 Years)
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input type="radio"
                               name="organization_type"
                               id="organization_semester"
                               value="semester"
                               {{ old('organization_type', $course->organization_type) === 'semester' ? 'checked' : '' }}
                               class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300">
                        <label for="organization_semester" class="ml-2 block text-sm text-gray-900">
                            Semester Based (1-8 Semesters)
                        </label>
                    </div>
                </div>
                @error('organization_type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Choose how this course is organized</p>
            </div>

            <!-- Course Details -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Credit Units -->
                <div>
                    <label for="credit_units" class="block text-sm font-medium text-gray-700 mb-2">
                        Credit Units <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           name="credit_units"
                           id="credit_units"
                           value="{{ old('credit_units', $course->credit_units) }}"
                           min="1"
                           max="10"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('credit_units') border-red-300 @enderror"
                           required>
                    @error('credit_units')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Year (for yearly organization) -->
                <div id="year_field" class="yearly-field">
                    <label for="year" class="block text-sm font-medium text-gray-700 mb-2">
                        Year <span class="text-red-500">*</span>
                    </label>
                    <select name="year"
                            id="year"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('year') border-red-300 @enderror">
                        <option value="">Select year</option>
                        @foreach($yearlyOptions as $year)
                            <option value="{{ $year }}" {{ old('year', $course->year) == $year ? 'selected' : '' }}>
                                {{ $year }}{{ $year == 1 ? 'st' : ($year == 2 ? 'nd' : ($year == 3 ? 'rd' : 'th')) }} Year
                            </option>
                        @endforeach
                    </select>
                    @error('year')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Semester Number (for semester organization) -->
                <div id="semester_period_field" class="semester-field" style="display: none;">
                    <label for="semester_period" class="block text-sm font-medium text-gray-700 mb-2">
                        Semester Number <span class="text-red-500">*</span>
                    </label>
                    <select name="semester_period"
                            id="semester_period"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('semester_period') border-red-300 @enderror">
                        <option value="">Select semester number</option>
                        @foreach($semesterOptions as $sem)
                            <option value="{{ $sem }}" {{ old('semester_period', $course->semester_period) == $sem ? 'selected' : '' }}>
                                Semester {{ $sem }}
                            </option>
                        @endforeach
                    </select>
                    @error('semester_period')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Course Type -->
                <div>
                    <label for="course_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Course Type <span class="text-red-500">*</span>
                    </label>
                    <select name="course_type" 
                            id="course_type"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('course_type') border-red-300 @enderror"
                            required>
                        <option value="">Select type</option>
                        @foreach($courseTypes as $type)
                            <option value="{{ $type }}" {{ old('course_type', $course->course_type) == $type ? 'selected' : '' }}>
                                {{ ucfirst($type) }} Course
                            </option>
                        @endforeach
                    </select>
                    @error('course_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Status -->
            <div>
                <div class="flex items-center">
                    <input type="checkbox" 
                           name="is_active" 
                           id="is_active" 
                           value="1"
                           {{ old('is_active', $course->is_active) ? 'checked' : '' }}
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                        Active Course
                    </label>
                </div>
                <p class="mt-1 text-sm text-gray-500">Uncheck to deactivate this course</p>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('courses.show', $course) }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-save mr-2"></i>
                    Update Course
                </button>
            </div>
        </form>
    </div>

    <!-- Danger Zone -->
    <div class="bg-white shadow-sm rounded-lg border border-red-200">
        <div class="px-6 py-4 border-b border-red-200">
            <h3 class="text-lg font-medium text-red-900">Danger Zone</h3>
        </div>
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-medium text-red-900">Delete Course</h4>
                    <p class="text-sm text-red-700">
                        Permanently delete this course. This action cannot be undone and will affect all associated classes.
                    </p>
                </div>
                <form action="{{ route('courses.destroy', $course) }}" 
                      method="POST" 
                      onsubmit="return confirm('Are you sure you want to delete this course? This action cannot be undone and will affect all associated classes.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-trash mr-2"></i>
                        Delete Course
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-uppercase the code field
    document.getElementById('code').addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
    });

    // Handle organization type change
    function toggleOrganizationFields() {
        const organizationType = document.querySelector('input[name="organization_type"]:checked').value;
        const yearField = document.getElementById('year_field');
        const semesterPeriodField = document.getElementById('semester_period_field');

        if (organizationType === 'yearly') {
            // Show only Year field for yearly organization
            yearField.style.display = 'block';
            semesterPeriodField.style.display = 'none';

            // Make year field required, semester_period not required
            document.getElementById('year').required = true;
            document.getElementById('semester_period').required = false;

            // Clear semester_period field
            document.getElementById('semester_period').value = '';
        } else { // organizationType === 'semester'
            // Show only Semester Period field for semester organization
            yearField.style.display = 'none';
            semesterPeriodField.style.display = 'block';

            // Make semester_period field required, year not required
            document.getElementById('year').required = false;
            document.getElementById('semester_period').required = true;

            // Clear year field
            document.getElementById('year').value = '';
        }
    }

    // Initialize organization fields on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleOrganizationFields();
    });

    // Add event listeners to organization type radio buttons
    document.querySelectorAll('input[name="organization_type"]').forEach(radio => {
        radio.addEventListener('change', toggleOrganizationFields);
    });

    // Filter departments by selected faculty
    document.getElementById('faculty_id').addEventListener('change', function() {
        const facultyId = this.value;
        const departmentSelect = document.getElementById('department_id');
        const options = departmentSelect.querySelectorAll('option');

        options.forEach(option => {
            if (option.value === '') {
                option.style.display = 'block';
                return;
            }

            const optionText = option.textContent;

            // Show/hide based on selected faculty
            if (facultyId === '') {
                option.style.display = 'block';
            } else {
                const selectedFaculty = document.querySelector(`#faculty_id option[value="${facultyId}"]`);
                if (selectedFaculty && optionText.includes(selectedFaculty.textContent.split(' (')[0])) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            }
        });

        // Reset department selection if current selection is not visible
        if (departmentSelect.value && departmentSelect.querySelector(`option[value="${departmentSelect.value}"]`).style.display === 'none') {
            departmentSelect.value = '';
        }
    });
</script>
@endsection
