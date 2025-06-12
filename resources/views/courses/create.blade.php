@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create Course</h1>
            <p class="mt-1 text-sm text-gray-500">Add a new course to a faculty</p>
        </div>
        <div class="mt-4 sm:mt-0">
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

    <!-- Create Form -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Course Information</h3>
        </div>

        <form action="{{ route('courses.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

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
                           value="{{ old('title') }}"
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
                           value="{{ old('code') }}"
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

            <!-- Faculty Selection (Primary) -->
            <div>
                <label for="faculty_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Faculty <span class="text-red-500">*</span>
                </label>
                <select name="faculty_id" 
                        id="faculty_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('faculty_id') border-red-300 @enderror"
                        required>
                    <option value="">Select a faculty</option>
                    @foreach($faculties as $faculty)
                        <option value="{{ $faculty->id }}" {{ old('faculty_id') == $faculty->id ? 'selected' : '' }}>
                            {{ $faculty->name }} ({{ $faculty->code }})
                        </option>
                    @endforeach
                </select>
                @error('faculty_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Primary faculty responsible for this course</p>
            </div>

            <!-- Department Selection (Optional) -->
            <div>
                <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Department <span class="text-gray-400">(Optional)</span>
                </label>
                <select name="department_id" 
                        id="department_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('department_id') border-red-300 @enderror">
                    <option value="">No department (optional)</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                            {{ $department->faculty->name }} - {{ $department->name }} ({{ $department->code }})
                        </option>
                    @endforeach
                </select>
                @error('department_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Optional department for additional organization</p>
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
                          placeholder="Brief description of the course...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
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
                           value="{{ old('credit_units', 3) }}"
                           min="1" 
                           max="10"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('credit_units') border-red-300 @enderror"
                           required>
                    @error('credit_units')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Level -->
                <div>
                    <label for="level" class="block text-sm font-medium text-gray-700 mb-2">
                        Level <span class="text-red-500">*</span>
                    </label>
                    <select name="level" 
                            id="level"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('level') border-red-300 @enderror"
                            required>
                        <option value="">Select level</option>
                        @foreach($levels as $level)
                            <option value="{{ $level }}" {{ old('level') == $level ? 'selected' : '' }}>
                                {{ $level }} Level
                            </option>
                        @endforeach
                    </select>
                    @error('level')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Semester -->
                <div>
                    <label for="semester" class="block text-sm font-medium text-gray-700 mb-2">
                        Semester <span class="text-red-500">*</span>
                    </label>
                    <select name="semester" 
                            id="semester"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('semester') border-red-300 @enderror"
                            required>
                        <option value="">Select semester</option>
                        @foreach($semesters as $semester)
                            <option value="{{ $semester }}" {{ old('semester') == $semester ? 'selected' : '' }}>
                                {{ ucfirst($semester) }} Semester
                            </option>
                        @endforeach
                    </select>
                    @error('semester')
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
                            <option value="{{ $type }}" {{ old('course_type') == $type ? 'selected' : '' }}>
                                {{ ucfirst($type) }} Course
                            </option>
                        @endforeach
                    </select>
                    @error('course_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Prerequisites -->
            <div>
                <label for="prerequisites" class="block text-sm font-medium text-gray-700 mb-2">
                    Prerequisites
                </label>
                <select name="prerequisites[]" 
                        id="prerequisites"
                        multiple
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('prerequisites') border-red-300 @enderror">
                    @foreach($existingCourses as $existingCourse)
                        <option value="{{ $existingCourse->id }}" 
                                {{ in_array($existingCourse->id, old('prerequisites', [])) ? 'selected' : '' }}>
                            {{ $existingCourse->code }} - {{ $existingCourse->title }}
                        </option>
                    @endforeach
                </select>
                @error('prerequisites')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Hold Ctrl/Cmd to select multiple prerequisites</p>
            </div>

            <!-- Status -->
            <div>
                <div class="flex items-center">
                    <input type="checkbox" 
                           name="is_active" 
                           id="is_active" 
                           value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                        Active Course
                    </label>
                </div>
                <p class="mt-1 text-sm text-gray-500">Uncheck to create an inactive course</p>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('courses.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-save mr-2"></i>
                    Create Course
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Auto-uppercase the code field
    document.getElementById('code').addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
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
            const facultyName = optionText.split(' - ')[0];
            
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
