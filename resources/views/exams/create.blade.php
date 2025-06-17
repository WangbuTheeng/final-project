@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create Exam</h1>
            <p class="mt-1 text-sm text-gray-500">Create a new exam with theory and practical components</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('exams.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Exams
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Create Form -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Exam Information</h3>
            <p class="mt-1 text-sm text-gray-500">Fill in the exam details below</p>
        </div>

        <form method="POST" action="{{ route('exams.store') }}" class="p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Exam Title -->
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Exam Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           value="{{ old('title') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('title') border-red-300 @enderror"
                           placeholder="e.g., Midterm Examination - Mathematics"
                           required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Class -->
                <div>
                    <label for="class_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Class <span class="text-red-500">*</span>
                    </label>
                    <select name="class_id" 
                            id="class_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('class_id') border-red-300 @enderror"
                            required>
                        <option value="">Select class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" 
                                    {{ old('class_id', $selectedClass?->id) == $class->id ? 'selected' : '' }}
                                    data-organization-type="{{ $class->course->organization_type }}">
                                {{ $class->name }} - {{ $class->course->title }}
                                ({{ $class->academicYear->name }})
                            </option>
                        @endforeach
                    </select>
                    @error('class_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Subject -->
                <div>
                    <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Subject <span class="text-gray-400">(Optional)</span>
                    </label>
                    <select name="subject_id"
                            id="subject_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('subject_id') border-red-300 @enderror">
                        <option value="">Select subject</option>
                    </select>
                    @error('subject_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <div class="mt-1 flex items-center justify-between">
                        <p class="text-sm text-gray-500">Leave empty for general class exam</p>
                        <button type="button"
                                id="load-marks-btn"
                                class="text-sm text-primary-600 hover:text-primary-800 disabled:text-gray-400"
                                disabled>
                            <i class="fas fa-download mr-1"></i>
                            Load Marks from Subject
                        </button>
                    </div>
                </div>

                <!-- Course (for course-level exams) -->
                <div>
                    <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Course <span class="text-gray-400">(Optional)</span>
                    </label>
                    <select name="course_id"
                            id="course_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('course_id') border-red-300 @enderror">
                        <option value="">Select course</option>
                    </select>
                    @error('course_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">For course-level comprehensive exams</p>
                </div>

                <!-- Academic Year -->
                <div>
                    <label for="academic_year_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Academic Year <span class="text-red-500">*</span>
                    </label>
                    <select name="academic_year_id" 
                            id="academic_year_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('academic_year_id') border-red-300 @enderror"
                            required>
                        <option value="">Select academic year</option>
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

                <!-- Exam Type -->
                <div>
                    <label for="exam_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Exam Type <span class="text-red-500">*</span>
                    </label>
                    <select name="exam_type"
                            id="exam_type"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('exam_type') border-red-300 @enderror"
                            required>
                        <option value="">Select exam type</option>
                        @foreach($examTypes as $key => $label)
                            <option value="{{ $key }}" {{ old('exam_type') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('exam_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Grading System -->
                <div>
                    <label for="grading_system_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Grading System <span class="text-gray-400">(Optional)</span>
                    </label>
                    <select name="grading_system_id"
                            id="grading_system_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('grading_system_id') border-red-300 @enderror">
                        <option value="">Use default grading system</option>
                        @foreach($gradingSystems as $gradingSystem)
                            <option value="{{ $gradingSystem->id }}" {{ old('grading_system_id') == $gradingSystem->id ? 'selected' : '' }}>
                                {{ $gradingSystem->formatted_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('grading_system_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Choose a specific grading system for this exam</p>
                </div>

                <!-- Semester (for semester-based courses) -->
                <div id="semester-group" style="display: none;">
                    <label for="semester" class="block text-sm font-medium text-gray-700 mb-2">
                        Semester <span class="text-red-500">*</span>
                    </label>
                    <select name="semester" 
                            id="semester" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('semester') border-red-300 @enderror">
                        <option value="">Select semester</option>
                        @for($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}" {{ old('semester') == $i ? 'selected' : '' }}>
                                Semester {{ $i }}
                            </option>
                        @endfor
                    </select>
                    @error('semester')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Year (for year-based courses) -->
                <div id="year-group" style="display: none;">
                    <label for="year" class="block text-sm font-medium text-gray-700 mb-2">
                        Year <span class="text-red-500">*</span>
                    </label>
                    <select name="year" 
                            id="year" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('year') border-red-300 @enderror">
                        <option value="">Select year</option>
                        @for($i = 1; $i <= 4; $i++)
                            <option value="{{ $i }}" {{ old('year') == $i ? 'selected' : '' }}>
                                {{ $i }}{{ $i == 1 ? 'st' : ($i == 2 ? 'nd' : ($i == 3 ? 'rd' : 'th')) }} Year
                            </option>
                        @endfor
                    </select>
                    @error('year')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Exam Date -->
                <div>
                    <label for="exam_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Exam Date & Time <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local"
                           name="exam_date"
                           id="exam_date"
                           value="{{ old('exam_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('exam_date') border-red-300 @enderror"
                           required>
                    @error('exam_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Exam Period Start Date -->
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Exam Period Start <span class="text-gray-400">(Optional)</span>
                    </label>
                    <input type="date"
                           name="start_date"
                           id="start_date"
                           value="{{ old('start_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('start_date') border-red-300 @enderror">
                    @error('start_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">For exam periods spanning multiple days</p>
                </div>

                <!-- Exam Period End Date -->
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Exam Period End <span class="text-gray-400">(Optional)</span>
                    </label>
                    <input type="date"
                           name="end_date"
                           id="end_date"
                           value="{{ old('end_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('end_date') border-red-300 @enderror">
                    @error('end_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Must be after or equal to start date</p>
                </div>

                <!-- Duration -->
                <div>
                    <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                        Duration (minutes) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           name="duration_minutes" 
                           id="duration_minutes" 
                           value="{{ old('duration_minutes', 120) }}"
                           min="15" 
                           max="480"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('duration_minutes') border-red-300 @enderror"
                           required>
                    @error('duration_minutes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Duration in minutes (15-480)</p>
                </div>
            </div>

            <!-- Exam Configuration -->
            <div class="mt-8">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Exam Configuration</h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Multi-Subject Exam -->
                    <div class="flex items-center">
                        <input type="checkbox"
                               name="is_multi_subject"
                               id="is_multi_subject"
                               value="1"
                               {{ old('is_multi_subject') ? 'checked' : '' }}
                               class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        <label for="is_multi_subject" class="ml-2 block text-sm text-gray-900">
                            Multi-Subject Exam
                        </label>
                    </div>

                    <!-- Auto-Load Subjects -->
                    <div class="flex items-center">
                        <input type="checkbox"
                               name="auto_load_subjects"
                               id="auto_load_subjects"
                               value="1"
                               {{ old('auto_load_subjects') ? 'checked' : '' }}
                               class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        <label for="auto_load_subjects" class="ml-2 block text-sm text-gray-900">
                            Auto-Load All Course Subjects
                        </label>
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Exam Configuration Options</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li><strong>Multi-Subject Exam:</strong> Check this for exams covering multiple subjects</li>
                                    <li><strong>Auto-Load All Course Subjects:</strong> Automatically include all subjects from the selected class/course</li>
                                    <li><strong>Single Subject:</strong> Leave both unchecked and select a specific subject for single-subject exams</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Marks Section -->
            <div class="mt-8">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-medium text-gray-900">Marks Distribution</h4>
                    <button type="button"
                            id="load-class-marks-btn"
                            class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:bg-gray-400 disabled:cursor-not-allowed"
                            disabled>
                        <i class="fas fa-calculator mr-2"></i>
                        Auto-Calculate from All Subjects
                    </button>
                </div>

                <!-- Class Marks Summary (Hidden by default) -->
                <div id="class-marks-summary" class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-md" style="display: none;">
                    <h5 class="text-sm font-medium text-blue-900 mb-2">Class Subjects Summary</h5>
                    <div id="subjects-list" class="text-sm text-blue-800"></div>
                    <div class="mt-2 pt-2 border-t border-blue-200">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="font-medium">Total Subjects:</span>
                                <span id="total-subjects">0</span>
                            </div>
                            <div>
                                <span class="font-medium">Total Marks:</span>
                                <span id="calculated-total-marks">0</span>
                            </div>
                            <div>
                                <span class="font-medium">Theory Marks:</span>
                                <span id="calculated-theory-marks">0</span>
                            </div>
                            <div>
                                <span class="font-medium">Practical Marks:</span>
                                <span id="calculated-practical-marks">0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Total Marks -->
                    <div>
                        <label for="total_marks" class="block text-sm font-medium text-gray-700 mb-2">
                            Total Marks <span class="text-red-500">*</span>
                            <span class="text-xs text-gray-500 font-normal">(Use auto-calculate for class-wide exams)</span>
                        </label>
                        <input type="number" 
                               name="total_marks" 
                               id="total_marks" 
                               value="{{ old('total_marks', 100) }}"
                               min="1" 
                               max="1000"
                               step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('total_marks') border-red-300 @enderror"
                               required>
                        @error('total_marks')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Theory Marks -->
                    <div>
                        <label for="theory_marks" class="block text-sm font-medium text-gray-700 mb-2">
                            Theory Marks <span class="text-gray-400">(Optional)</span>
                        </label>
                        <input type="number" 
                               name="theory_marks" 
                               id="theory_marks" 
                               value="{{ old('theory_marks') }}"
                               min="0" 
                               step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('theory_marks') border-red-300 @enderror">
                        @error('theory_marks')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Leave empty if no theory component</p>
                    </div>

                    <!-- Practical Marks -->
                    <div>
                        <label for="practical_marks" class="block text-sm font-medium text-gray-700 mb-2">
                            Practical Marks <span class="text-gray-400">(Optional)</span>
                        </label>
                        <input type="number" 
                               name="practical_marks" 
                               id="practical_marks" 
                               value="{{ old('practical_marks') }}"
                               min="0" 
                               step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('practical_marks') border-red-300 @enderror">
                        @error('practical_marks')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Leave empty if no practical component</p>
                    </div>
                </div>

                <!-- Pass Mark -->
                <div class="mt-6">
                    <label for="pass_mark" class="block text-sm font-medium text-gray-700 mb-2">
                        Pass Mark <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           name="pass_mark" 
                           id="pass_mark" 
                           value="{{ old('pass_mark', 40) }}"
                           min="0" 
                           step="0.01"
                           class="w-full md:w-1/3 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('pass_mark') border-red-300 @enderror"
                           required>
                    @error('pass_mark')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Minimum marks required to pass</p>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="mt-8">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Venue -->
                    <div>
                        <label for="venue" class="block text-sm font-medium text-gray-700 mb-2">
                            Venue <span class="text-gray-400">(Optional)</span>
                        </label>
                        <input type="text" 
                               name="venue" 
                               id="venue" 
                               value="{{ old('venue') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('venue') border-red-300 @enderror"
                               placeholder="e.g., Main Hall, Room 101">
                        @error('venue')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Instructions -->
                <div class="mt-6">
                    <label for="instructions" class="block text-sm font-medium text-gray-700 mb-2">
                        Instructions <span class="text-gray-400">(Optional)</span>
                    </label>
                    <textarea name="instructions" 
                              id="instructions" 
                              rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('instructions') border-red-300 @enderror"
                              placeholder="Enter exam instructions, rules, or special notes...">{{ old('instructions') }}</textarea>
                    @error('instructions')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex items-center justify-end space-x-4">
                <a href="{{ route('exams.index') }}"
                   class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    Cancel
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    <i class="fas fa-save mr-2"></i>
                    Create Exam
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const classSelect = document.getElementById('class_id');
    const subjectSelect = document.getElementById('subject_id');
    const semesterGroup = document.getElementById('semester-group');
    const yearGroup = document.getElementById('year-group');
    const semesterSelect = document.getElementById('semester');
    const yearSelect = document.getElementById('year');
    const totalMarksInput = document.getElementById('total_marks');
    const theoryMarksInput = document.getElementById('theory_marks');
    const practicalMarksInput = document.getElementById('practical_marks');
    const passMarkInput = document.getElementById('pass_mark');
    const loadMarksBtn = document.getElementById('load-marks-btn');
    const loadClassMarksBtn = document.getElementById('load-class-marks-btn');
    const classMarksSummary = document.getElementById('class-marks-summary');

    // Handle class selection
    classSelect.addEventListener('change', function() {
        const classId = this.value;
        const selectedOption = this.options[this.selectedIndex];
        const organizationType = selectedOption.getAttribute('data-organization-type');

        // Clear subjects
        subjectSelect.innerHTML = '<option value="">Select subject</option>';

        // Show/hide semester or year fields
        if (organizationType === 'semester') {
            semesterGroup.style.display = 'block';
            yearGroup.style.display = 'none';
            semesterSelect.setAttribute('required', 'required');
            yearSelect.removeAttribute('required');
        } else if (organizationType === 'yearly') {
            semesterGroup.style.display = 'none';
            yearGroup.style.display = 'block';
            yearSelect.setAttribute('required', 'required');
            semesterSelect.removeAttribute('required');
        } else {
            semesterGroup.style.display = 'none';
            yearGroup.style.display = 'none';
            semesterSelect.removeAttribute('required');
            yearSelect.removeAttribute('required');
        }

        // Enable/disable auto-calculate button
        loadClassMarksBtn.disabled = !classId;

        // Hide class marks summary when class changes
        classMarksSummary.style.display = 'none';

        // Load subjects for the selected class
        if (classId) {
            fetch(`{{ route('exams.subjects.by-class') }}?class_id=${classId}`)
                .then(response => response.json())
                .then(subjects => {
                    subjects.forEach(subject => {
                        const option = document.createElement('option');
                        option.value = subject.id;
                        option.textContent = `${subject.name} (${subject.code})`;
                        option.dataset.theoryMarks = subject.full_marks_theory || '';
                        option.dataset.practicalMarks = subject.full_marks_practical || '';
                        option.dataset.passMarksTheory = subject.pass_marks_theory || '';
                        option.dataset.passMarksPractical = subject.pass_marks_practical || '';
                        option.dataset.isPractical = subject.is_practical || false;
                        subjectSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading subjects:', error);
                });
        }
    });

    // Handle subject selection
    subjectSelect.addEventListener('change', function() {
        const subjectId = this.value;
        loadMarksBtn.disabled = !subjectId;
    });

    // Handle load marks button
    loadMarksBtn.addEventListener('click', function() {
        const subjectId = subjectSelect.value;
        if (!subjectId) return;

        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Loading...';

        fetch(`{{ route('exams.subject-marks') }}?subject_id=${subjectId}`)
            .then(response => response.json())
            .then(data => {
                // Load marks from subject
                if (data.theory_marks) {
                    theoryMarksInput.value = data.theory_marks;
                }
                if (data.practical_marks) {
                    practicalMarksInput.value = data.practical_marks;
                }
                if (data.total_marks) {
                    totalMarksInput.value = data.total_marks;
                }
                if (data.total_pass_marks) {
                    passMarkInput.value = data.total_pass_marks;
                }

                // Show success message
                const successMsg = document.createElement('div');
                successMsg.className = 'mt-2 p-2 bg-green-50 border border-green-200 text-green-700 rounded text-sm';
                successMsg.innerHTML = '<i class="fas fa-check mr-1"></i>Marks loaded successfully from subject!';
                this.parentNode.appendChild(successMsg);

                setTimeout(() => {
                    successMsg.remove();
                }, 3000);

                this.disabled = false;
                this.innerHTML = '<i class="fas fa-download mr-1"></i>Load Marks from Subject';
            })
            .catch(error => {
                console.error('Error loading marks:', error);
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-download mr-1"></i>Load Marks from Subject';
            });
    });

    // Handle auto-calculate from all subjects button
    loadClassMarksBtn.addEventListener('click', function() {
        const classId = classSelect.value;
        if (!classId) return;

        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Calculating...';

        fetch(`{{ route('exams.class-marks') }}?class_id=${classId}`)
            .then(response => response.json())
            .then(data => {
                if (data.subject_count === 0) {
                    alert('No subjects found for this class. Please add subjects first.');
                    return;
                }

                // Update marks inputs
                totalMarksInput.value = data.total_marks;
                theoryMarksInput.value = data.total_theory_marks;
                practicalMarksInput.value = data.total_practical_marks;

                // Calculate suggested pass mark (40% of total)
                const suggestedPassMark = Math.round(data.total_marks * 0.4);
                passMarkInput.value = suggestedPassMark;

                // Show summary
                displayClassMarksSummary(data);

                this.disabled = false;
                this.innerHTML = '<i class="fas fa-calculator mr-2"></i>Auto-Calculate from All Subjects';
            })
            .catch(error => {
                console.error('Error loading class marks:', error);
                alert('Error calculating marks. Please try again.');
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-calculator mr-2"></i>Auto-Calculate from All Subjects';
            });
    });

    // Function to display class marks summary
    function displayClassMarksSummary(data) {
        const subjectsList = document.getElementById('subjects-list');
        const totalSubjects = document.getElementById('total-subjects');
        const calculatedTotalMarks = document.getElementById('calculated-total-marks');
        const calculatedTheoryMarks = document.getElementById('calculated-theory-marks');
        const calculatedPracticalMarks = document.getElementById('calculated-practical-marks');

        // Update summary numbers
        totalSubjects.textContent = data.subject_count;
        calculatedTotalMarks.textContent = data.total_marks;
        calculatedTheoryMarks.textContent = data.total_theory_marks;
        calculatedPracticalMarks.textContent = data.total_practical_marks;

        // Create subjects list
        let subjectsHtml = '<div class="grid grid-cols-1 md:grid-cols-2 gap-2">';
        data.subjects.forEach(subject => {
            subjectsHtml += `
                <div class="flex justify-between items-center py-1 px-2 bg-white rounded border">
                    <span class="font-medium">${subject.name} (${subject.code})</span>
                    <span class="text-sm">
                        ${subject.theory_marks > 0 ? `T:${subject.theory_marks}` : ''}
                        ${subject.theory_marks > 0 && subject.practical_marks > 0 ? ' + ' : ''}
                        ${subject.practical_marks > 0 ? `P:${subject.practical_marks}` : ''}
                        = ${subject.total_marks}
                    </span>
                </div>
            `;
        });
        subjectsHtml += '</div>';

        subjectsList.innerHTML = subjectsHtml;
        classMarksSummary.style.display = 'block';
    }

    // Validate marks distribution
    function validateMarks() {
        const totalMarks = parseFloat(totalMarksInput.value) || 0;
        const theoryMarks = parseFloat(theoryMarksInput.value) || 0;
        const practicalMarks = parseFloat(practicalMarksInput.value) || 0;

        if (theoryMarks > 0 && practicalMarks > 0) {
            const sum = theoryMarks + practicalMarks;
            if (Math.abs(sum - totalMarks) > 0.01) {
                theoryMarksInput.setCustomValidity('Theory + Practical marks must equal Total marks');
                practicalMarksInput.setCustomValidity('Theory + Practical marks must equal Total marks');
            } else {
                theoryMarksInput.setCustomValidity('');
                practicalMarksInput.setCustomValidity('');
            }
        } else {
            theoryMarksInput.setCustomValidity('');
            practicalMarksInput.setCustomValidity('');
        }
    }

    // Add event listeners for marks validation
    [totalMarksInput, theoryMarksInput, practicalMarksInput].forEach(input => {
        input.addEventListener('input', validateMarks);
    });

    // Initialize on page load
    if (classSelect.value) {
        classSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
@endsection
