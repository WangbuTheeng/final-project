@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Subject</h1>
            <p class="mt-1 text-sm text-gray-500">Update subject information</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('subjects.show', $subject) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-eye mr-2"></i>
                View Subject
            </a>
            <a href="{{ route('subjects.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Subjects
            </a>
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
            <h3 class="text-lg font-medium text-gray-900">Subject Information</h3>
        </div>

        <form action="{{ route('subjects.update', $subject) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Subject Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Subject Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name', $subject->name) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-300 @enderror"
                           placeholder="e.g., Variables and Data Types"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Subject Code -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                        Subject Code <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="code"
                           id="code"
                           value="{{ old('code', $subject->code) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('code') border-red-300 @enderror"
                           placeholder="e.g., ENG101-S01"
                           maxlength="20"
                           style="text-transform: uppercase;"
                           required>
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <div class="mt-1 text-sm text-gray-500">
                        <p><strong>Must be globally unique.</strong> Examples: ENG101-GR01, MTH201-AL01, BUS301-FN01</p>
                        <p class="text-blue-600">💡 Change carefully - this affects all references to this subject</p>
                    </div>
                </div>
            </div>

            <!-- Class Selection -->
            <div>
                <label for="class_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Class <span class="text-red-500">*</span>
                </label>
                <select name="class_id" 
                        id="class_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('class_id') border-red-300 @enderror"
                        required>
                    <option value="">Select a class</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" 
                                {{ old('class_id', $subject->class_id) == $class->id ? 'selected' : '' }}
                                data-course="{{ $class->course->title }}"
                                data-faculty="{{ $class->course->faculty->name }}">
                            {{ $class->name }} - {{ $class->course->title }} ({{ $class->course->faculty->name }})
                        </option>
                    @endforeach
                </select>
                @error('class_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Select the class this subject belongs to</p>
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
                          placeholder="Brief description of the subject...">{{ old('description', $subject->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Subject Details -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Order Sequence -->
                <div>
                    <label for="order_sequence" class="block text-sm font-medium text-gray-700 mb-2">
                        Order Sequence <span class="text-red-500">*</span>
                    </label>
                    <input type="number"
                           name="order_sequence"
                           id="order_sequence"
                           value="{{ old('order_sequence', $subject->order_sequence) }}"
                           min="1"
                           max="100"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('order_sequence') border-red-300 @enderror"
                           required>
                    @error('order_sequence')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <div class="mt-1 text-sm text-gray-500">
                        <p><strong>Must be unique within the class.</strong> Current value: {{ $subject->order_sequence }}</p>
                        <p class="text-amber-600">⚠️ Changing this affects the subject order in the class</p>
                    </div>
                </div>

                <!-- Duration Hours -->
                <div>
                    <label for="duration_hours" class="block text-sm font-medium text-gray-700 mb-2">
                        Duration (Hours)
                    </label>
                    <input type="number" 
                           name="duration_hours" 
                           id="duration_hours" 
                           value="{{ old('duration_hours', $subject->duration_hours) }}"
                           min="1" 
                           max="1000"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('duration_hours') border-red-300 @enderror"
                           placeholder="e.g., 8">
                    @error('duration_hours')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Credit Weight -->
                <div>
                    <label for="credit_weight" class="block text-sm font-medium text-gray-700 mb-2">
                        Credit Weight (%)
                    </label>
                    <input type="number" 
                           name="credit_weight" 
                           id="credit_weight" 
                           value="{{ old('credit_weight', $subject->credit_weight) }}"
                           min="1" 
                           max="100"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('credit_weight') border-red-300 @enderror"
                           placeholder="e.g., 20">
                    @error('credit_weight')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Instructor -->
                <div>
                    <label for="instructor_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Instructor
                    </label>
                    <select name="instructor_id" 
                            id="instructor_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('instructor_id') border-red-300 @enderror">
                        <option value="">Select instructor (optional)</option>
                        @foreach($instructors as $instructor)
                            <option value="{{ $instructor->id }}" {{ old('instructor_id', $subject->instructor_id) == $instructor->id ? 'selected' : '' }}>
                                {{ $instructor->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('instructor_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Subject Properties -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Difficulty Level -->
                <div>
                    <label for="difficulty_level" class="block text-sm font-medium text-gray-700 mb-2">
                        Difficulty Level <span class="text-red-500">*</span>
                    </label>
                    <select name="difficulty_level" 
                            id="difficulty_level"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('difficulty_level') border-red-300 @enderror"
                            required>
                        <option value="">Select difficulty</option>
                        @foreach($difficultyLevels as $level)
                            <option value="{{ $level }}" {{ old('difficulty_level', $subject->difficulty_level) == $level ? 'selected' : '' }}>
                                {{ ucfirst($level) }}
                            </option>
                        @endforeach
                    </select>
                    @error('difficulty_level')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Subject Type -->
                <div>
                    <label for="subject_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Subject Type <span class="text-red-500">*</span>
                    </label>
                    <select name="subject_type" 
                            id="subject_type"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('subject_type') border-red-300 @enderror"
                            required>
                        <option value="">Select type</option>
                        @foreach($subjectTypes as $type)
                            <option value="{{ $type }}" {{ old('subject_type', $subject->subject_type) == $type ? 'selected' : '' }}>
                                {{ ucfirst($type) }}
                            </option>
                        @endforeach
                    </select>
                    @error('subject_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Is Practical -->
                <div class="flex items-center">
                    <input type="checkbox"
                           name="is_practical"
                           id="is_practical"
                           value="1"
                           {{ old('is_practical', $subject->is_practical) ? 'checked' : '' }}
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    <label for="is_practical" class="ml-2 block text-sm text-gray-900">
                        Has Practical Component
                    </label>
                </div>
            </div>

            <!-- Exam Marks Configuration -->
            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-calculator mr-2"></i>
                    Exam Marks Configuration
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Theory Marks -->
                    <div class="space-y-4">
                        <h4 class="text-md font-medium text-gray-700">Theory Component</h4>

                        <div>
                            <label for="full_marks_theory" class="block text-sm font-medium text-gray-700 mb-2">
                                Full Marks (Theory)
                            </label>
                            <input type="number"
                                   name="full_marks_theory"
                                   id="full_marks_theory"
                                   value="{{ old('full_marks_theory', $subject->full_marks_theory) }}"
                                   min="0"
                                   max="1000"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('full_marks_theory') border-red-300 @enderror"
                                   placeholder="e.g., 80">
                            @error('full_marks_theory')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="pass_marks_theory" class="block text-sm font-medium text-gray-700 mb-2">
                                Pass Marks (Theory)
                            </label>
                            <input type="number"
                                   name="pass_marks_theory"
                                   id="pass_marks_theory"
                                   value="{{ old('pass_marks_theory', $subject->pass_marks_theory) }}"
                                   min="0"
                                   max="1000"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('pass_marks_theory') border-red-300 @enderror"
                                   placeholder="e.g., 32">
                            @error('pass_marks_theory')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Practical Marks -->
                    <div class="space-y-4">
                        <h4 class="text-md font-medium text-gray-700">Practical Component</h4>

                        <div>
                            <label for="full_marks_practical" class="block text-sm font-medium text-gray-700 mb-2">
                                Full Marks (Practical)
                            </label>
                            <input type="number"
                                   name="full_marks_practical"
                                   id="full_marks_practical"
                                   value="{{ old('full_marks_practical', $subject->full_marks_practical) }}"
                                   min="0"
                                   max="1000"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('full_marks_practical') border-red-300 @enderror"
                                   placeholder="e.g., 20">
                            @error('full_marks_practical')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="pass_marks_practical" class="block text-sm font-medium text-gray-700 mb-2">
                                Pass Marks (Practical)
                            </label>
                            <input type="number"
                                   name="pass_marks_practical"
                                   id="pass_marks_practical"
                                   value="{{ old('pass_marks_practical', $subject->pass_marks_practical) }}"
                                   min="0"
                                   max="1000"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('pass_marks_practical') border-red-300 @enderror"
                                   placeholder="e.g., 8">
                            @error('pass_marks_practical')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-blue-50 rounded-md">
                    <p class="text-sm text-blue-700">
                        <i class="fas fa-info-circle mr-1"></i>
                        <strong>Note:</strong> Leave marks fields empty if the subject doesn't have that component.
                        For example, if it's a theory-only subject, leave practical marks empty.
                    </p>
                </div>
            </div>

            <!-- Dates -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Start Date
                    </label>
                    <input type="date"
                           name="start_date"
                           id="start_date"
                           value="{{ old('start_date', $subject->start_date?->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('start_date') border-red-300 @enderror">
                    @error('start_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            <!-- End Date -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                        End Date
                    </label>
                    <input type="date" 
                           name="end_date" 
                           id="end_date" 
                           value="{{ old('end_date', $subject->end_date?->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('end_date') border-red-300 @enderror">
                    @error('end_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Learning Objectives -->
            <div>
                <label for="learning_objectives" class="block text-sm font-medium text-gray-700 mb-2">
                    Learning Objectives
                </label>
                <div id="learning-objectives-container">
                    @if(old('learning_objectives') || ($subject->learning_objectives && count($subject->learning_objectives) > 0))
                        @foreach(old('learning_objectives', $subject->learning_objectives ?? []) as $index => $objective)
                            <div class="flex items-center space-x-2 mb-2">
                                <input type="text" 
                                       name="learning_objectives[]" 
                                       value="{{ $objective }}"
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="Learning objective...">
                                <button type="button" onclick="removeObjective(this)" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @endforeach
                    @else
                        <div class="flex items-center space-x-2 mb-2">
                            <input type="text" 
                                   name="learning_objectives[]" 
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="Learning objective...">
                            <button type="button" onclick="removeObjective(this)" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    @endif
                </div>
                <button type="button" onclick="addObjective()" class="mt-2 text-primary-600 hover:text-primary-800 text-sm">
                    <i class="fas fa-plus mr-1"></i> Add Learning Objective
                </button>
            </div>

            <!-- Status Options -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex items-center">
                    <input type="checkbox" 
                           name="is_mandatory" 
                           id="is_mandatory" 
                           value="1"
                           {{ old('is_mandatory', $subject->is_mandatory) ? 'checked' : '' }}
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    <label for="is_mandatory" class="ml-2 block text-sm text-gray-900">
                        Mandatory Subject
                    </label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" 
                           name="is_active" 
                           id="is_active" 
                           value="1"
                           {{ old('is_active', $subject->is_active) ? 'checked' : '' }}
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                        Active Subject
                    </label>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('subjects.show', $subject) }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-save mr-2"></i>
                    Update Subject
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
                    <h4 class="text-sm font-medium text-red-900">Delete Subject</h4>
                    <p class="text-sm text-red-700">
                        Permanently delete this subject. This action cannot be undone.
                    </p>
                </div>
                <form action="{{ route('subjects.destroy', $subject) }}" 
                      method="POST" 
                      onsubmit="return confirm('Are you sure you want to delete this subject? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-trash mr-2"></i>
                        Delete Subject
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

    // Learning objectives management
    function addObjective() {
        const container = document.getElementById('learning-objectives-container');
        const div = document.createElement('div');
        div.className = 'flex items-center space-x-2 mb-2';
        div.innerHTML = `
            <input type="text" 
                   name="learning_objectives[]" 
                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                   placeholder="Learning objective...">
            <button type="button" onclick="removeObjective(this)" class="text-red-600 hover:text-red-800">
                <i class="fas fa-times"></i>
            </button>
        `;
        container.appendChild(div);
    }

    function removeObjective(button) {
        const container = document.getElementById('learning-objectives-container');
        if (container.children.length > 1) {
            button.parentElement.remove();
        }
    }
</script>
@endsection
