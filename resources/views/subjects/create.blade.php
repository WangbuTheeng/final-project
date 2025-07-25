@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create Subject</h1>
            <p class="mt-1 text-sm text-gray-500">Add a new subject/topic to a class</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('subjects.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Subjects
            </a>
        </div>
    </div>

    <!-- Hierarchy Info -->
    <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-md">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm">
                    <strong>Structure:</strong> Faculty → Course → Class → Subject
                    <br>Subjects are specific topics or modules within a class.
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

    <!-- Subject Preview -->
    <div id="subject-preview" class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4 hidden">
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0">
                <i class="fas fa-eye text-blue-500 text-lg"></i>
            </div>
            <div class="flex-1">
                <h4 class="text-sm font-medium text-blue-900 mb-2">Subject Preview</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-blue-700 font-medium">Name:</span>
                        <span id="preview-name" class="text-blue-900 ml-1">-</span>
                    </div>
                    <div>
                        <span class="text-blue-700 font-medium">Code:</span>
                        <span id="preview-code" class="text-blue-900 ml-1 font-mono">-</span>
                    </div>
                    <div>
                        <span class="text-blue-700 font-medium">Class:</span>
                        <span id="preview-class" class="text-blue-900 ml-1">-</span>
                    </div>
                    <div>
                        <span class="text-blue-700 font-medium">Order:</span>
                        <span id="preview-order" class="text-blue-900 ml-1">#-</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Form -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Subject Information</h3>
        </div>

        <form action="{{ route('subjects.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

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
                           value="{{ old('name') }}"
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
                    <div class="flex space-x-2">
                        <input type="text"
                               name="code"
                               id="code"
                               value="{{ old('code') }}"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('code') border-red-300 @enderror"
                               placeholder="e.g., ENG101-S01"
                               maxlength="20"
                               style="text-transform: uppercase;"
                               required>
                        <button type="button"
                                id="generate-code-btn"
                                class="px-3 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50"
                                disabled
                                title="Select class and enter subject name first">
                            <i class="fas fa-magic"></i>
                        </button>
                    </div>
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <div class="mt-1 text-sm text-gray-500">
                        <p><strong>Must be globally unique.</strong> Examples: ENG101-GR01, MTH201-AL01, BUS301-FN01</p>
                        <p class="text-blue-600">💡 Click the magic button to auto-generate based on class and subject name</p>
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
                                {{ old('class_id', $selectedClass?->id) == $class->id ? 'selected' : '' }}
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
                          placeholder="Brief description of the subject...">{{ old('description') }}</textarea>
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
                    <div class="relative">
                        <input type="number"
                               name="order_sequence"
                               id="order_sequence"
                               value="{{ old('order_sequence', $nextOrderSequence ?? 1) }}"
                               min="1"
                               max="100"
                               class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('order_sequence') border-red-300 @enderror"
                               readonly
                               required>
                        <button type="button"
                                id="toggle-manual-order"
                                class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600"
                                title="Click to manually edit order sequence">
                            <i class="fas fa-lock text-sm"></i>
                        </button>
                    </div>
                    @error('order_sequence')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <div class="mt-1 text-sm text-gray-500">
                        <p><strong>Auto-managed:</strong> Next available sequence is <span id="suggested-order" class="font-semibold text-green-600">{{ $nextOrderSequence ?? 1 }}</span></p>
                        <p class="text-blue-600">🔒 Click the lock icon to manually edit if needed</p>
                        <p id="manual-order-warning" class="text-amber-600 hidden">⚠️ Manual mode: Ensure the number is unique within this class</p>
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
                           value="{{ old('duration_hours') }}"
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
                           value="{{ old('credit_weight') }}"
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
                            <option value="{{ $instructor->id }}" {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>
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
                            <option value="{{ $level }}" {{ old('difficulty_level') == $level ? 'selected' : '' }}>
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
                            <option value="{{ $type }}" {{ old('subject_type') == $type ? 'selected' : '' }}>
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
                           {{ old('is_practical') ? 'checked' : '' }}
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
                                   value="{{ old('full_marks_theory') }}"
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
                                   value="{{ old('pass_marks_theory') }}"
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
                                   value="{{ old('full_marks_practical') }}"
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
                                   value="{{ old('pass_marks_practical') }}"
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
                           value="{{ old('start_date') }}"
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
                           value="{{ old('end_date') }}"
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
                    @if(old('learning_objectives'))
                        @foreach(old('learning_objectives') as $index => $objective)
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
                           {{ old('is_mandatory', true) ? 'checked' : '' }}
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
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                        Active Subject
                    </label>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('subjects.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-save mr-2"></i>
                    Create Subject
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const classSelect = document.getElementById('class_id');
    const nameInput = document.getElementById('name');
    const codeInput = document.getElementById('code');
    const orderSequenceInput = document.getElementById('order_sequence');
    const generateCodeBtn = document.getElementById('generate-code-btn');
    const suggestedOrderSpan = document.getElementById('suggested-order');
    const toggleManualOrderBtn = document.getElementById('toggle-manual-order');
    const manualOrderWarning = document.getElementById('manual-order-warning');
    const subjectPreview = document.getElementById('subject-preview');
    const previewName = document.getElementById('preview-name');
    const previewCode = document.getElementById('preview-code');
    const previewClass = document.getElementById('preview-class');
    const previewOrder = document.getElementById('preview-order');
    let isManualOrderMode = false;

    // Auto-uppercase the code field
    codeInput.addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
    });

    // Toggle manual order sequence mode
    toggleManualOrderBtn.addEventListener('click', function() {
        isManualOrderMode = !isManualOrderMode;

        if (isManualOrderMode) {
            orderSequenceInput.readOnly = false;
            orderSequenceInput.focus();
            toggleManualOrderBtn.innerHTML = '<i class="fas fa-unlock text-sm"></i>';
            toggleManualOrderBtn.title = 'Click to return to automatic mode';
            toggleManualOrderBtn.classList.remove('text-gray-400');
            toggleManualOrderBtn.classList.add('text-amber-500');
            manualOrderWarning.classList.remove('hidden');
        } else {
            orderSequenceInput.readOnly = true;
            toggleManualOrderBtn.innerHTML = '<i class="fas fa-lock text-sm"></i>';
            toggleManualOrderBtn.title = 'Click to manually edit order sequence';
            toggleManualOrderBtn.classList.remove('text-amber-500');
            toggleManualOrderBtn.classList.add('text-gray-400');
            manualOrderWarning.classList.add('hidden');

            // Reset to suggested order
            if (classSelect.value) {
                updateOrderSequence(classSelect.value);
            }
        }
    });

    // Update order sequence when class changes
    classSelect.addEventListener('change', function() {
        const classId = this.value;

        if (classId) {
            updateOrderSequence(classId);
            // Enable generate code button if name is also filled
            updateGenerateCodeButton();
        } else {
            if (!isManualOrderMode) {
                orderSequenceInput.value = 1;
                suggestedOrderSpan.textContent = 1;
            }
            generateCodeBtn.disabled = true;
        }
        updatePreview();
    });

    // Function to update order sequence
    function updateOrderSequence(classId) {
        if (!isManualOrderMode) {
            // Get next order sequence
            fetch(`{{ route('subjects.next-order-sequence') }}?class_id=${classId}`)
                .then(response => response.json())
                .then(data => {
                    orderSequenceInput.value = data.order_sequence;
                    suggestedOrderSpan.textContent = data.order_sequence;
                    updatePreview();
                })
                .catch(error => {
                    console.error('Error fetching order sequence:', error);
                    // Fallback to manual calculation
                    orderSequenceInput.value = parseInt(suggestedOrderSpan.textContent) || 1;
                });
        } else {
            // Just update the suggestion display
            fetch(`{{ route('subjects.next-order-sequence') }}?class_id=${classId}`)
                .then(response => response.json())
                .then(data => {
                    suggestedOrderSpan.textContent = data.order_sequence;
                })
                .catch(error => {
                    console.error('Error fetching order sequence:', error);
                });
        }
    }

    // Enable generate code button when name changes
    nameInput.addEventListener('input', function() {
        updateGenerateCodeButton();
        updatePreview();
    });

    // Update preview when code changes
    codeInput.addEventListener('input', function() {
        updatePreview();
    });

    // Generate code button click
    generateCodeBtn.addEventListener('click', function() {
        const classId = classSelect.value;
        const subjectName = nameInput.value;

        if (classId && subjectName) {
            generateCodeBtn.disabled = true;
            generateCodeBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            fetch(`{{ route('subjects.generate-code') }}?class_id=${classId}&subject_name=${encodeURIComponent(subjectName)}`)
                .then(response => response.json())
                .then(data => {
                    codeInput.value = data.code;
                    generateCodeBtn.disabled = false;
                    generateCodeBtn.innerHTML = '<i class="fas fa-magic"></i>';
                })
                .catch(error => {
                    console.error('Error generating code:', error);
                    generateCodeBtn.disabled = false;
                    generateCodeBtn.innerHTML = '<i class="fas fa-magic"></i>';
                    alert('Error generating code. Please try again.');
                });
        }
    });

    function updateGenerateCodeButton() {
        const hasClass = classSelect.value !== '';
        const hasName = nameInput.value.trim() !== '';

        generateCodeBtn.disabled = !(hasClass && hasName);

        if (hasClass && hasName) {
            generateCodeBtn.title = 'Click to generate subject code';
        } else {
            generateCodeBtn.title = 'Select class and enter subject name first';
        }
    }

    // Update preview function
    function updatePreview() {
        const hasName = nameInput.value.trim() !== '';
        const hasCode = codeInput.value.trim() !== '';
        const hasClass = classSelect.value !== '';
        const hasOrder = orderSequenceInput.value !== '';

        if (hasName || hasCode || hasClass) {
            subjectPreview.classList.remove('hidden');

            previewName.textContent = nameInput.value.trim() || '-';
            previewCode.textContent = codeInput.value.trim() || '-';
            previewOrder.textContent = hasOrder ? `#${orderSequenceInput.value}` : '#-';

            if (hasClass) {
                const selectedOption = classSelect.options[classSelect.selectedIndex];
                previewClass.textContent = selectedOption.text || '-';
            } else {
                previewClass.textContent = '-';
            }
        } else {
            subjectPreview.classList.add('hidden');
        }
    }

    // Initialize on page load
    if (classSelect.value) {
        classSelect.dispatchEvent(new Event('change'));
    }
    updatePreview();
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
