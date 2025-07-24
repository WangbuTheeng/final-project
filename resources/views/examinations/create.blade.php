@extends('layouts.dashboard')

@section('title', '🇳🇵 Create New Examination')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">🇳🇵 Create New Examination</h1>
            <p class="mt-1 text-sm text-gray-500">Create examination following Nepali educational standards</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('examinations.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Examinations
            </a>
        </div>
    </div>

    <!-- Educational System Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-white"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-blue-900">+2 Level (NEB Standard)</h3>
                </div>
            </div>
            <div class="text-sm text-blue-700 space-y-1">
                <p><strong>Exam Types:</strong> First Assessment, First Terminal, Second Assessment, Second Terminal, Third Assessment, Final Term, Monthly Term, Weekly Test</p>
                <p><strong>Grading:</strong> A+ to NG (35% minimum pass)</p>
                <p><strong>Streams:</strong> Science, Management, Humanities</p>
            </div>
        </div>

        <div class="bg-green-50 border border-green-200 rounded-lg p-6">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                        <i class="fas fa-university text-white"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-green-900">Bachelor's Level (University)</h3>
                </div>
            </div>
            <div class="text-sm text-green-700 space-y-1">
                <p><strong>Assessment:</strong> Internal (40%) + External (60%)</p>
                <p><strong>Grading:</strong> A+ to F (40% minimum pass)</p>
                <p><strong>Programs:</strong> BBS, BCA, BSc CSIT, BA, BSc</p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <form action="{{ route('examinations.store') }}" method="POST" id="examForm" class="space-y-6">
            @csrf
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Examination Details</h3>
                <p class="mt-1 text-sm text-gray-500">Configure your examination according to Nepali educational standards</p>
            </div>

            <div class="px-6 pb-6 space-y-6">
                <!-- Step 1: Course Selection -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Step 1: Select Course</h4>
                    <div>
                        <label for="course_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Course <span class="text-red-500">*</span>
                        </label>
                        <select class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('course_id') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                id="course_id" name="course_id">
                            <option value="">Select Course</option>
                            @if(isset($courses))
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->title }}
                                        @if($course->department)
                                            ({{ $course->department->name }})
                                        @endif
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('course_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Step 2: Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                                Exam Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('title') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                   id="title" name="title" value="{{ old('title') }}"
                                   placeholder="e.g., Class 11 First Term Exam - Science">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="exam_type_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Exam Type <span class="text-red-500">*</span>
                            </label>
                            <select class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('exam_type_id') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                    id="exam_type_id" name="exam_type_id">
                                <option value="">Select Exam Type</option>
                                @if(isset($examTypes))
                                    @foreach($examTypes as $examType)
                                        <option value="{{ $examType->id }}"
                                                data-education-level="{{ $examType->education_level }}"
                                                data-weightage="{{ $examType->default_weightage }}"
                                                data-duration="{{ $examType->default_duration_minutes }}"
                                                {{ old('exam_type_id') == $examType->id ? 'selected' : '' }}>
                                            {{ $examType->name }}
                                            @if($examType->default_weightage)
                                                ({{ $examType->default_weightage }}%)
                                            @endif
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('exam_type_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="class_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Class <span class="text-red-500">*</span>
                            </label>
                            <select class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('class_id') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                    id="class_id" name="class_id">
                                <option value="">Select Course First</option>
                            </select>
                            @error('class_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Classes will be loaded based on selected course</p>
                        </div>

                        <div>
                            <label for="academic_year_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Academic Year <span class="text-red-500">*</span>
                            </label>
                            <select class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('academic_year_id') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                    id="academic_year_id" name="academic_year_id">
                                <option value="">Select Academic Year</option>
                                @if(isset($academicYears))
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                            {{ $year->name }} {{ $year->is_current ? '(Current)' : '' }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('academic_year_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Schedule & Configuration -->
                    <div class="space-y-6">
                        <div>
                            <label for="exam_date" class="block text-sm font-medium text-gray-700 mb-1">
                                Exam Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('exam_date') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                   id="exam_date" name="exam_date" value="{{ old('exam_date') }}">
                            @error('exam_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">
                                    Start Time <span class="text-red-500">*</span>
                                </label>
                                <input type="time"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('start_time') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                       id="start_time" name="start_time" value="{{ old('start_time', '10:00') }}">
                                @error('start_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">
                                    End Time <span class="text-red-500">*</span>
                                </label>
                                <input type="time"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('end_time') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                       id="end_time" name="end_time" value="{{ old('end_time', '13:00') }}">
                                @error('end_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="total_marks" class="block text-sm font-medium text-gray-700 mb-1">
                                    Total Marks <span class="text-red-500">*</span>
                                </label>
                                <input type="number"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('total_marks') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                       id="total_marks" name="total_marks" value="{{ old('total_marks', 100) }}"
                                       min="1" max="1000">
                                @error('total_marks')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="pass_mark" class="block text-sm font-medium text-gray-700 mb-1">
                                    Pass Marks <span class="text-red-500">*</span>
                                </label>
                                <input type="number"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('pass_mark') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                       id="pass_mark" name="pass_mark" value="{{ old('pass_mark', 35) }}"
                                       min="1" readonly>
                                @error('pass_mark')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Auto-calculated based on education level</p>
                            </div>
                        </div>

                        <div>
                            <label for="exam_hall" class="block text-sm font-medium text-gray-700 mb-1">
                                Exam Hall
                            </label>
                            <input type="text"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('exam_hall') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                   id="exam_hall" name="exam_hall" value="{{ old('exam_hall') }}"
                                   placeholder="e.g., Hall A, Room 101">
                            @error('exam_hall')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="max_students" class="block text-sm font-medium text-gray-700 mb-1">
                                Maximum Students
                            </label>
                            <input type="number"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('max_students') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                   id="max_students" name="max_students" value="{{ old('max_students') }}"
                                   min="1" placeholder="Leave empty for no limit">
                            @error('max_students')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Dynamic Semester/Year Selection -->
                        <div id="semester_section" class="hidden">
                            <label for="semester" class="block text-sm font-medium text-gray-700 mb-1">
                                Semester <span class="text-red-500">*</span>
                            </label>
                            <select class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('semester') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                    id="semester" name="semester">
                                <option value="">Select Semester</option>
                                <option value="1" {{ old('semester') == '1' ? 'selected' : '' }}>First Semester</option>
                                <option value="2" {{ old('semester') == '2' ? 'selected' : '' }}>Second Semester</option>
                                <option value="3" {{ old('semester') == '3' ? 'selected' : '' }}>Third Semester</option>
                                <option value="4" {{ old('semester') == '4' ? 'selected' : '' }}>Fourth Semester</option>
                                <option value="5" {{ old('semester') == '5' ? 'selected' : '' }}>Fifth Semester</option>
                                <option value="6" {{ old('semester') == '6' ? 'selected' : '' }}>Sixth Semester</option>
                                <option value="7" {{ old('semester') == '7' ? 'selected' : '' }}>Seventh Semester</option>
                                <option value="8" {{ old('semester') == '8' ? 'selected' : '' }}>Eighth Semester</option>
                            </select>
                            @error('semester')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="year_section" class="hidden">
                            <label for="year" class="block text-sm font-medium text-gray-700 mb-1">
                                Year <span class="text-red-500">*</span>
                            </label>
                            <select class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('year') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                    id="year" name="year">
                                <option value="">Select Year</option>
                                <option value="1" {{ old('year') == '1' ? 'selected' : '' }}>First Year</option>
                                <option value="2" {{ old('year') == '2' ? 'selected' : '' }}>Second Year</option>
                                <option value="3" {{ old('year') == '3' ? 'selected' : '' }}>Third Year</option>
                                <option value="4" {{ old('year') == '4' ? 'selected' : '' }}>Fourth Year</option>
                            </select>
                            @error('year')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Subject Selection -->
                <div id="subjects_section">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-medium text-gray-900">Exam Subjects</h4>
                        <div class="text-sm text-gray-500">
                            <span id="subjects_info">Select a class to load subjects automatically</span>
                        </div>
                    </div>

                    <!-- Single Subject Selection (fallback) -->
                    <div id="single_subject_selection">
                        <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Subject <span class="text-red-500">*</span>
                        </label>
                        <select class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('subject_id') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                id="subject_id" name="subject_id">
                            <option value="">Select Subject</option>
                            @if(isset($subjects))
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}"
                                        data-code="{{ $subject->code }}"
                                        data-credit-hours="{{ $subject->credit_hours ?? '' }}"
                                        {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }} ({{ $subject->code }})
                                    @if($subject->credit_hours)
                                        - {{ $subject->credit_hours }} Credit Hours
                                    @endif
                                </option>
                            @endforeach
                        @endif
                    </select>
                        @error('subject_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Multi-Subject Selection (for class-based exams) -->
                    <div id="multi_subject_selection" class="hidden">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-blue-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-sm text-blue-700">
                                    All subjects for the selected class will be automatically included in this exam. You can configure marks for each subject below.
                                </p>
                            </div>
                        </div>

                        <div id="subjects_list" class="space-y-4">
                            <!-- Subjects will be loaded dynamically -->
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <div>
                    <label for="instructions" class="block text-sm font-medium text-gray-700 mb-1">
                        Exam Instructions
                    </label>
                    <textarea class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('instructions') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                              id="instructions" name="instructions" rows="4"
                              placeholder="Enter specific instructions for this examination...">{{ old('instructions', 'Please read all questions carefully before answering. Write clearly and legibly. Mobile phones and electronic devices are strictly prohibited.') }}</textarea>
                    @error('instructions')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Additional Settings -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Additional Settings</h4>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input type="checkbox"
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                   id="is_published" name="is_published" value="1"
                                   {{ old('is_published') ? 'checked' : '' }}>
                            <label for="is_published" class="ml-2 block text-sm text-gray-900">
                                Publish immediately (students can see this exam)
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox"
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                   id="send_notifications" name="send_notifications" value="1"
                                   {{ old('send_notifications', true) ? 'checked' : '' }}>
                            <label for="send_notifications" class="ml-2 block text-sm text-gray-900">
                                Send notifications to students and teachers
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox"
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                   id="allow_late_submission" name="allow_late_submission" value="1"
                                   {{ old('allow_late_submission') ? 'checked' : '' }}>
                            <label for="allow_late_submission" class="ml-2 block text-sm text-gray-900">
                                Allow late submission (with penalty)
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('examinations.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-save mr-2"></i>
                        Create Examination
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const courseSelect = document.getElementById('course_id');
    const classSelect = document.getElementById('class_id');
    const semesterSection = document.getElementById('semester_section');
    const yearSection = document.getElementById('year_section');
    const totalMarksInput = document.getElementById('total_marks');
    const passMarkInput = document.getElementById('pass_mark');
    const examTypeSelect = document.getElementById('exam_type_id');
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    const singleSubjectSection = document.getElementById('single_subject_selection');
    const multiSubjectSection = document.getElementById('multi_subject_selection');
    const subjectsList = document.getElementById('subjects_list');
    const subjectsInfo = document.getElementById('subjects_info');

    // Store current class and course data
    let currentClassData = null;
    let currentCourseData = null;

    // Handle course selection change
    async function handleCourseChange() {
        const courseId = courseSelect.value;

        if (!courseId) {
            // Reset class selection
            classSelect.innerHTML = '<option value="">Select Course First</option>';
            resetSubjectSelection();
            resetSemesterYearSection();
            return;
        }

        try {
            // Load course details to determine organization type
            await loadCourseDetails(courseId);

            classSelect.innerHTML = '<option value="">Loading classes...</option>';

            const response = await fetch(`{{ route('examinations.get-classes-by-course') }}?course_id=${courseId}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('Classes response:', data); // Debug log

            if (data.error) {
                throw new Error(data.message || 'Server error occurred');
            }

            if (data.classes) {
                // Clear and populate class options
                classSelect.innerHTML = '<option value="">Select Class</option>';

                data.classes.forEach(classItem => {
                    const option = document.createElement('option');
                    option.value = classItem.id;
                    option.textContent = `${classItem.name}`;
                    if (classItem.academic_year) {
                        option.textContent += ` (${classItem.academic_year})`;
                    }
                    classSelect.appendChild(option);
                });

                if (data.classes.length === 0) {
                    classSelect.innerHTML = '<option value="">No classes found for this course</option>';
                }
            } else {
                throw new Error('Invalid response format');
            }
        } catch (error) {
            console.error('Error loading classes:', error);
            console.error('Course ID:', courseId);
            console.error('Full error:', error);
            classSelect.innerHTML = `<option value="">Error: ${error.message}</option>`;

            // Show alert for debugging
            alert(`Error loading classes for course ${courseId}: ${error.message}`);
        }

        // Reset subject selection when course changes
        resetSubjectSelection();
    }

    // Load course details to determine organization type
    async function loadCourseDetails(courseId) {
        try {
            const response = await fetch(`/api/courses/${courseId}/details`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('Course details response:', data); // Debug log

            if (data.success && data.data) {
                // Store course data and update semester/year section
                currentCourseData = data.data;
                updateSemesterYearSection();
            } else {
                throw new Error(data.message || 'Failed to load course details');
            }
        } catch (error) {
            console.error('Error loading course details:', error);
            // Don't show alert for course details error, just log it
        }
    }

    // Handle class selection change
    async function handleClassChange() {
        const classId = classSelect.value;

        if (!classId) {
            resetSubjectSelection();
            return;
        }

        try {
            subjectsInfo.textContent = 'Loading class details...';

            const response = await fetch(`/api/classes/${classId}/details`);
            const data = await response.json();

            if (data.success) {
                currentClassData = data.data;
                updateSemesterYearSection();
                loadClassSubjects();
                updateTotalMarks();
            } else {
                throw new Error(data.message || 'Failed to load class details');
            }
        } catch (error) {
            console.error('Error loading class details:', error);
            subjectsInfo.textContent = 'Error loading class details. Please try again.';
            resetSubjectSelection();
        }
    }

    // Update pass marks calculation (simplified)
    function updatePassMarks() {
        const totalMarks = parseFloat(totalMarksInput.value) || 100;
        const passMarks = Math.round(totalMarks * 0.35); // 35% pass mark
        passMarkInput.value = passMarks;
    }

    // Update semester/year section based on course organization type
    function updateSemesterYearSection() {
        if (!currentCourseData) return;

        if (currentCourseData.organization_type === 'semester') {
            semesterSection.classList.remove('hidden');
            yearSection.classList.add('hidden');
            subjectsInfo.textContent = `Semester-based course: ${currentCourseData.title}`;
        } else {
            semesterSection.classList.add('hidden');
            yearSection.classList.remove('hidden');
            subjectsInfo.textContent = `Yearly course: ${currentCourseData.title}`;
        }
    }

    // Load subjects for the selected class
    function loadClassSubjects() {
        if (!currentClassData || !currentClassData.subjects) {
            resetSubjectSelection();
            return;
        }

        const subjects = currentClassData.subjects;

        if (subjects.length === 0) {
            subjectsInfo.textContent = 'No subjects found for this class.';
            resetSubjectSelection();
            return;
        }

        // Show multi-subject selection
        singleSubjectSection.classList.add('hidden');
        multiSubjectSection.classList.remove('hidden');

        // Clear existing subjects
        subjectsList.innerHTML = '';

        // Add each subject with marks configuration
        subjects.forEach((subject, index) => {
            const subjectDiv = createSubjectCard(subject, index);
            subjectsList.appendChild(subjectDiv);
        });

        subjectsInfo.textContent = `${subjects.length} subjects loaded for ${currentClassData.name}`;
    }

    // Create subject card with marks configuration
    function createSubjectCard(subject, index) {
        const div = document.createElement('div');
        div.className = 'border border-gray-200 rounded-lg p-4 bg-gray-50';

        div.innerHTML = `
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h5 class="font-medium text-gray-900">${subject.name}</h5>
                    <p class="text-sm text-gray-500">${subject.code}</p>
                </div>
                <div class="flex items-center">
                    <input type="checkbox"
                           name="selected_subjects[]"
                           value="${subject.id}"
                           id="subject_${subject.id}"
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded subject-checkbox"
                           checked>
                    <label for="subject_${subject.id}" class="ml-2 text-sm text-gray-700">Include</label>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Theory Marks</label>
                    <input type="number"
                           name="subject_theory_marks[${subject.id}]"
                           value="${subject.theory_marks || 80}"
                           min="0" max="1000"
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm subject-marks-input">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Practical Marks</label>
                    <input type="number"
                           name="subject_practical_marks[${subject.id}]"
                           value="${subject.practical_marks || (subject.is_practical ? 20 : 0)}"
                           min="0" max="1000"
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm subject-marks-input"
                           ${!subject.is_practical ? 'readonly' : ''}>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Total Marks</label>
                    <input type="number"
                           name="subject_total_marks[${subject.id}]"
                           value="${subject.total_marks || 100}"
                           readonly
                           class="block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm text-sm subject-total-marks">
                </div>
            </div>
        `;

        // Add event listeners for marks calculation
        const theoryInput = div.querySelector(`input[name="subject_theory_marks[${subject.id}]"]`);
        const practicalInput = div.querySelector(`input[name="subject_practical_marks[${subject.id}]"]`);
        const totalInput = div.querySelector(`input[name="subject_total_marks[${subject.id}]"]`);

        function updateSubjectTotal() {
            const theory = parseInt(theoryInput.value) || 0;
            const practical = parseInt(practicalInput.value) || 0;
            totalInput.value = theory + practical;
            updateTotalMarks();
        }

        theoryInput.addEventListener('input', updateSubjectTotal);
        practicalInput.addEventListener('input', updateSubjectTotal);

        // Add checkbox change listener
        const checkbox = div.querySelector('.subject-checkbox');
        checkbox.addEventListener('change', updateTotalMarks);

        return div;
    }

    // Reset semester/year section
    function resetSemesterYearSection() {
        semesterSection.classList.add('hidden');
        yearSection.classList.add('hidden');
        currentCourseData = null;
        subjectsInfo.textContent = 'Select a course to see organization type';
    }

    // Reset subject selection
    function resetSubjectSelection() {
        singleSubjectSection.classList.remove('hidden');
        multiSubjectSection.classList.add('hidden');
        subjectsList.innerHTML = '';
        subjectsInfo.textContent = 'Select a class to load subjects automatically';
        currentClassData = null;

        // Reset total marks to default
        totalMarksInput.value = 100;
        updatePassMarks();
    }

    // Update total marks based on selected subjects
    function updateTotalMarks() {
        if (!currentClassData) return;

        let totalMarks = 0;
        const checkedSubjects = document.querySelectorAll('.subject-checkbox:checked');

        checkedSubjects.forEach(checkbox => {
            const subjectId = checkbox.value;
            const totalInput = document.querySelector(`input[name="subject_total_marks[${subjectId}]"]`);
            if (totalInput) {
                totalMarks += parseInt(totalInput.value) || 0;
            }
        });

        totalMarksInput.value = totalMarks;
        updatePassMarks();
    }

    // Filter exam types based on education level
    function filterExamTypes(educationLevel) {
        const options = examTypeSelect.querySelectorAll('option[data-education-level]');

        options.forEach(option => {
            if (!educationLevel || option.dataset.educationLevel === educationLevel || option.dataset.educationLevel === 'both') {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });

        // Reset selection if current selection is not valid for new education level
        const currentOption = examTypeSelect.querySelector('option:checked');
        if (currentOption && currentOption.dataset.educationLevel &&
            currentOption.dataset.educationLevel !== educationLevel &&
            currentOption.dataset.educationLevel !== 'both') {
            examTypeSelect.value = '';
        }
    }

    // Update pass marks based on education level and total marks
    function updatePassMarks() {
        const selectedLevel = document.querySelector('input[name="education_level"]:checked')?.value;
        const totalMarks = parseFloat(totalMarksInput.value) || 100;
        let passPercentage = 0.35; // Default 35% for +2 level

        if (selectedLevel === 'bachelors') {
            passPercentage = 0.40; // 40% for bachelor's level
        }

        const passMarks = Math.round(totalMarks * passPercentage);
        passMarkInput.value = passMarks;
    }

    // Auto-calculate end time based on exam type duration
    function updateEndTime() {
        const selectedOption = examTypeSelect.options[examTypeSelect.selectedIndex];
        const duration = selectedOption.dataset.duration;
        const startTime = startTimeInput.value;

        if (duration && startTime) {
            const [hours, minutes] = startTime.split(':').map(Number);
            const startDate = new Date();
            startDate.setHours(hours, minutes, 0, 0);

            const endDate = new Date(startDate.getTime() + (parseInt(duration) * 60000));
            const endHours = endDate.getHours().toString().padStart(2, '0');
            const endMinutes = endDate.getMinutes().toString().padStart(2, '0');

            endTimeInput.value = `${endHours}:${endMinutes}`;
        }
    }

    // Event listeners
    courseSelect.addEventListener('change', handleCourseChange);
    classSelect.addEventListener('change', handleClassChange);
    totalMarksInput.addEventListener('input', updatePassMarks);

    examTypeSelect.addEventListener('change', updateEndTime);
    startTimeInput.addEventListener('change', updateEndTime);

    // Form validation
    document.getElementById('examForm').addEventListener('submit', function(e) {
        // Validate course selection
        if (!courseSelect.value) {
            e.preventDefault();
            alert('Please select a course.');
            return false;
        }

        // Validate class selection
        if (!classSelect.value) {
            e.preventDefault();
            alert('Please select a class.');
            return false;
        }

        // Validate subject selection
        const selectedSubjects = document.querySelectorAll('.subject-checkbox:checked');
        const singleSubjectSelect = document.getElementById('subject_id');

        if (currentClassData && selectedSubjects.length === 0) {
            e.preventDefault();
            alert('Please select at least one subject for the exam.');
            return false;
        } else if (!currentClassData && (!singleSubjectSelect.value)) {
            e.preventDefault();
            alert('Please select a subject for the exam.');
            return false;
        }

        // Additional validation can be added here
    });

    // Initialize
    updatePassMarks();
});
</script>

@push('styles')
<style>
    .radio-indicator {
        transition: all 0.2s ease-in-out;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .form-section {
        transition: all 0.3s ease-in-out;
    }

    .hidden {
        display: none !important;
    }

    /* Custom styling for better UX */
    .exam-type-card {
        transition: all 0.2s ease-in-out;
    }

    .exam-type-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush
@endsection
