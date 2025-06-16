@extends('layouts.dashboard')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-edit text-white text-xl"></i>
                            </div>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Edit Exam</h1>
                            <p class="mt-1 text-sm text-gray-500">Update exam details and configuration</p>
                            <div class="mt-2 flex items-center space-x-4 text-sm text-gray-600">
                                <span class="inline-flex items-center">
                                    <i class="fas fa-graduation-cap mr-1"></i>
                                    {{ $exam->class->course->title }}
                                </span>
                                <span class="inline-flex items-center">
                                    <i class="fas fa-users mr-1"></i>
                                    {{ $exam->class->name }}
                                </span>
                                <span class="inline-flex items-center">
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ $exam->academicYear->name }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                        <a href="{{ route('exams.show', $exam) }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg font-medium text-sm text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                            <i class="fas fa-eye mr-2"></i>
                            View Exam
                        </a>
                        <a href="{{ route('exams.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg font-medium text-sm text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                            <i class="fas fa-list mr-2"></i>
                            All Exams
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg shadow-sm mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400 text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg shadow-sm mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400 text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Warning for Graded Exam -->
        @if($exam->grades()->count() > 0)
            <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-lg shadow-sm mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-amber-400 text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <div class="text-sm">
                            <h3 class="font-medium text-amber-800">Important Notice</h3>
                            <p class="mt-1 text-amber-700">
                                This exam has <strong>{{ $exam->grades()->count() }} grade(s)</strong> recorded.
                                Changing marks distribution may affect existing grades and calculations.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Edit Form -->
        <div class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-50 to-purple-50 px-6 py-5 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-edit text-blue-600 mr-3"></i>
                            Exam Information
                        </h3>
                        <p class="mt-1 text-sm text-gray-600">Update the exam details below</p>
                    </div>
                    <div class="flex items-center space-x-2 text-sm text-gray-500">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $exam->getExamTypeLabel() }}
                        </span>
                        @php
                            $statusInfo = $exam->getStatusLabel();
                            $statusColors = [
                                'blue' => 'bg-blue-100 text-blue-800',
                                'yellow' => 'bg-yellow-100 text-yellow-800',
                                'green' => 'bg-green-100 text-green-800',
                                'red' => 'bg-red-100 text-red-800',
                                'gray' => 'bg-gray-100 text-gray-800'
                            ];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$statusInfo['color']] ?? $statusColors['gray'] }}">
                            {{ $statusInfo['label'] }}
                        </span>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('exams.update', $exam) }}" class="p-8">
            @csrf
            @method('PUT')

                <!-- Basic Information Section -->
                <div class="space-y-8">
                    <div class="border-b border-gray-200 pb-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                            Basic Information
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Exam Title -->
                            <div class="md:col-span-2">
                                <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-heading text-gray-400 mr-1"></i>
                                    Exam Title <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="title"
                                       id="title"
                                       value="{{ old('title', $exam->title) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('title') border-red-300 ring-red-200 @enderror"
                                       placeholder="e.g., Final Examination - BCA 8th Semester"
                                       required>
                                @error('title')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Class -->
                            <div>
                                <label for="class_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-users text-gray-400 mr-1"></i>
                                    Class <span class="text-red-500">*</span>
                                </label>
                                <select name="class_id"
                                        id="class_id"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('class_id') border-red-300 ring-red-200 @enderror"
                                        required>
                                    <option value="">Select class</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}"
                                                {{ old('class_id', $exam->class_id) == $class->id ? 'selected' : '' }}
                                                data-organization-type="{{ $class->course->organization_type }}">
                                            {{ $class->name }} - {{ $class->course->title }}
                                            ({{ $class->academicYear->name }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('class_id')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Subject -->
                            <div>
                                <label for="subject_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-book text-gray-400 mr-1"></i>
                                    Subject <span class="text-gray-500 font-normal">(Optional)</span>
                                </label>
                                <select name="subject_id"
                                        id="subject_id"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('subject_id') border-red-300 ring-red-200 @enderror">
                                    <option value="">Select subject</option>
                                    @if($exam->subject)
                                        <option value="{{ $exam->subject->id }}" selected>
                                            {{ $exam->subject->name }} ({{ $exam->subject->code }})
                                        </option>
                                    @endif
                                </select>
                                @error('subject_id')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                                <div class="mt-2 flex items-center justify-between">
                                    <p class="text-xs text-gray-500">Leave empty for class-wide exam</p>
                                    <button type="button"
                                            id="load-marks-btn"
                                            class="text-xs text-blue-600 hover:text-blue-800 disabled:text-gray-400 font-medium transition-colors duration-200"
                                            {{ $exam->subject ? '' : 'disabled' }}>
                                        <i class="fas fa-download mr-1"></i>
                                        Load from Subject
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Academic Details Section -->
                    <div class="border-b border-gray-200 pb-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-calendar-alt text-green-500 mr-2"></i>
                            Academic Details
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Academic Year -->
                            <div>
                                <label for="academic_year_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-calendar text-gray-400 mr-1"></i>
                                    Academic Year <span class="text-red-500">*</span>
                                </label>
                                <select name="academic_year_id"
                                        id="academic_year_id"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('academic_year_id') border-red-300 ring-red-200 @enderror"
                                        required>
                                    <option value="">Select academic year</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}" {{ old('academic_year_id', $exam->academic_year_id) == $year->id ? 'selected' : '' }}>
                                            {{ $year->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('academic_year_id')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Exam Type -->
                            <div>
                                <label for="exam_type" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-clipboard-list text-gray-400 mr-1"></i>
                                    Exam Type <span class="text-red-500">*</span>
                                </label>
                                <select name="exam_type"
                                        id="exam_type"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('exam_type') border-red-300 ring-red-200 @enderror"
                                        required>
                                    <option value="">Select exam type</option>
                                    @foreach($examTypes as $key => $label)
                                        <option value="{{ $key }}" {{ old('exam_type', $exam->exam_type) == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('exam_type')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                <!-- Semester (for semester-based courses) -->
                <div id="semester-group" style="display: {{ $exam->semester ? 'block' : 'none' }};">
                    <label for="semester" class="block text-sm font-medium text-gray-700 mb-2">
                        Semester <span class="text-red-500">*</span>
                    </label>
                    <select name="semester" 
                            id="semester" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('semester') border-red-300 @enderror">
                        <option value="">Select semester</option>
                        @for($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}" {{ old('semester', $exam->semester) == $i ? 'selected' : '' }}>
                                Semester {{ $i }}
                            </option>
                        @endfor
                    </select>
                    @error('semester')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Year (for year-based courses) -->
                <div id="year-group" style="display: {{ $exam->year ? 'block' : 'none' }};">
                    <label for="year" class="block text-sm font-medium text-gray-700 mb-2">
                        Year <span class="text-red-500">*</span>
                    </label>
                    <select name="year" 
                            id="year" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('year') border-red-300 @enderror">
                        <option value="">Select year</option>
                        @for($i = 1; $i <= 4; $i++)
                            <option value="{{ $i }}" {{ old('year', $exam->year) == $i ? 'selected' : '' }}>
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
                           value="{{ old('exam_date', $exam->exam_date ? $exam->exam_date->format('Y-m-d\TH:i') : '') }}"
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
                           value="{{ old('start_date', $exam->start_date ? $exam->start_date->format('Y-m-d') : '') }}"
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
                           value="{{ old('end_date', $exam->end_date ? $exam->end_date->format('Y-m-d') : '') }}"
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
                           value="{{ old('duration_minutes', $exam->duration_minutes) }}"
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

                    <!-- Marks Distribution Section -->
                    <div class="border-b border-gray-200 pb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-medium text-gray-900 flex items-center">
                                <i class="fas fa-calculator text-purple-500 mr-2"></i>
                                Marks Distribution
                            </h4>
                            <button type="button"
                                    id="load-class-marks-btn"
                                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white text-sm font-medium rounded-lg hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:from-gray-400 disabled:to-gray-500 disabled:cursor-not-allowed transition-all duration-200 shadow-md"
                                    {{ $exam->class_id ? '' : 'disabled' }}>
                                <i class="fas fa-magic mr-2"></i>
                                Auto-Calculate from All Subjects
                            </button>
                        </div>

                        <!-- Class Marks Summary (Hidden by default) -->
                        <div id="class-marks-summary" class="mb-6 p-6 bg-gradient-to-r from-blue-50 to-purple-50 border border-blue-200 rounded-xl shadow-sm" style="display: none;">
                            <h5 class="text-lg font-semibold text-blue-900 mb-3 flex items-center">
                                <i class="fas fa-list-alt mr-2"></i>
                                Class Subjects Summary
                            </h5>
                            <div id="subjects-list" class="text-sm text-blue-800 mb-4"></div>
                            <div class="pt-4 border-t border-blue-200">
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div class="bg-white p-3 rounded-lg shadow-sm">
                                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Subjects</div>
                                        <div class="text-2xl font-bold text-gray-900" id="total-subjects">0</div>
                                    </div>
                                    <div class="bg-white p-3 rounded-lg shadow-sm">
                                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Marks</div>
                                        <div class="text-2xl font-bold text-blue-600" id="calculated-total-marks">0</div>
                                    </div>
                                    <div class="bg-white p-3 rounded-lg shadow-sm">
                                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Theory Marks</div>
                                        <div class="text-2xl font-bold text-green-600" id="calculated-theory-marks">0</div>
                                    </div>
                                    <div class="bg-white p-3 rounded-lg shadow-sm">
                                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Practical Marks</div>
                                        <div class="text-2xl font-bold text-purple-600" id="calculated-practical-marks">0</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Total Marks -->
                            <div>
                                <label for="total_marks" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-trophy text-gray-400 mr-1"></i>
                                    Total Marks <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number"
                                           name="total_marks"
                                           id="total_marks"
                                           value="{{ old('total_marks', $exam->total_marks) }}"
                                           min="1"
                                           max="1000"
                                           step="0.01"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('total_marks') border-red-300 ring-red-200 @enderror"
                                           required>
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 text-sm">pts</span>
                                    </div>
                                </div>
                                @error('total_marks')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Use auto-calculate for class-wide exams</p>
                            </div>

                            <!-- Theory Marks -->
                            <div>
                                <label for="theory_marks" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-book-open text-gray-400 mr-1"></i>
                                    Theory Marks <span class="text-gray-500 font-normal">(Optional)</span>
                                </label>
                                <div class="relative">
                                    <input type="number"
                                           name="theory_marks"
                                           id="theory_marks"
                                           value="{{ old('theory_marks', $exam->theory_marks) }}"
                                           min="0"
                                           step="0.01"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('theory_marks') border-red-300 ring-red-200 @enderror">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 text-sm">pts</span>
                                    </div>
                                </div>
                                @error('theory_marks')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Leave empty if no theory component</p>
                            </div>

                            <!-- Practical Marks -->
                            <div>
                                <label for="practical_marks" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-flask text-gray-400 mr-1"></i>
                                    Practical Marks <span class="text-gray-500 font-normal">(Optional)</span>
                                </label>
                                <div class="relative">
                                    <input type="number"
                                           name="practical_marks"
                                           id="practical_marks"
                                           value="{{ old('practical_marks', $exam->practical_marks) }}"
                                           min="0"
                                           step="0.01"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('practical_marks') border-red-300 ring-red-200 @enderror">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 text-sm">pts</span>
                                    </div>
                                </div>
                                @error('practical_marks')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Leave empty if no practical component</p>
                            </div>
                        </div>

                        <!-- Pass Mark -->
                        <div class="mt-6">
                            <label for="pass_mark" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-check-circle text-gray-400 mr-1"></i>
                                Pass Mark <span class="text-red-500">*</span>
                            </label>
                            <div class="relative w-full md:w-1/2">
                                <input type="number"
                                       name="pass_mark"
                                       id="pass_mark"
                                       value="{{ old('pass_mark', $exam->pass_mark) }}"
                                       min="0"
                                       step="0.01"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('pass_mark') border-red-300 ring-red-200 @enderror"
                                       required>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-sm">pts</span>
                                </div>
                            </div>
                            @error('pass_mark')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Minimum marks required to pass (typically 40% of total)</p>
                        </div>
                    </div>

                    <!-- Schedule & Additional Information Section -->
                    <div class="border-b border-gray-200 pb-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-clock text-orange-500 mr-2"></i>
                            Schedule & Additional Information
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Venue -->
                            <div>
                                <label for="venue" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-map-marker-alt text-gray-400 mr-1"></i>
                                    Venue <span class="text-gray-500 font-normal">(Optional)</span>
                                </label>
                                <input type="text"
                                       name="venue"
                                       id="venue"
                                       value="{{ old('venue', $exam->venue) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('venue') border-red-300 ring-red-200 @enderror"
                                       placeholder="e.g., Main Hall, Room 101, Computer Lab">
                                @error('venue')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-flag text-gray-400 mr-1"></i>
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select name="status"
                                        id="status"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('status') border-red-300 ring-red-200 @enderror"
                                        required>
                                    <option value="scheduled" {{ old('status', $exam->status) == 'scheduled' ? 'selected' : '' }}>📅 Scheduled</option>
                                    <option value="ongoing" {{ old('status', $exam->status) == 'ongoing' ? 'selected' : '' }}>⏳ Ongoing</option>
                                    <option value="completed" {{ old('status', $exam->status) == 'completed' ? 'selected' : '' }}>✅ Completed</option>
                                    <option value="cancelled" {{ old('status', $exam->status) == 'cancelled' ? 'selected' : '' }}>❌ Cancelled</option>
                                </select>
                                @error('status')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                    <!-- Instructions Section -->
                    <div>
                        <h4 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-clipboard-list text-indigo-500 mr-2"></i>
                            Instructions & Notes
                        </h4>
                        <div>
                            <label for="instructions" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-file-alt text-gray-400 mr-1"></i>
                                Exam Instructions <span class="text-gray-500 font-normal">(Optional)</span>
                            </label>
                            <textarea name="instructions"
                                      id="instructions"
                                      rows="5"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('instructions') border-red-300 ring-red-200 @enderror"
                                      placeholder="Enter exam instructions, rules, special notes, or guidelines for students...">{{ old('instructions', $exam->instructions) }}</textarea>
                            @error('instructions')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">These instructions will be visible to students and invigilators</p>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('exams.show', $exam) }}"
                           class="inline-flex items-center px-6 py-3 bg-gray-100 border border-gray-300 rounded-lg font-medium text-sm text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </a>
                        <a href="{{ route('exams.grades', $exam) }}"
                           class="inline-flex items-center px-6 py-3 bg-green-100 border border-green-300 rounded-lg font-medium text-sm text-green-700 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200">
                            <i class="fas fa-edit mr-2"></i>
                            Enter Grades
                        </a>
                    </div>
                    <button type="submit"
                            class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white text-sm font-semibold rounded-lg hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-lg">
                        <i class="fas fa-save mr-2"></i>
                        Update Exam
                    </button>
                </div>
            </form>
        </div>
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

    // Initialize form based on current exam data
    const currentClassOption = classSelect.options[classSelect.selectedIndex];
    if (currentClassOption) {
        const organizationType = currentClassOption.getAttribute('data-organization-type');
        updateFormFields(organizationType);
    }

    // Handle class selection
    classSelect.addEventListener('change', function() {
        const classId = this.value;
        const selectedOption = this.options[this.selectedIndex];
        const organizationType = selectedOption.getAttribute('data-organization-type');

        // Clear subjects except current one
        const currentSubjectId = '{{ $exam->subject_id }}';
        subjectSelect.innerHTML = '<option value="">Select subject</option>';

        updateFormFields(organizationType);

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

                        // Select current subject if it matches
                        if (subject.id == currentSubjectId) {
                            option.selected = true;
                        }

                        subjectSelect.appendChild(option);
                    });

                    // Update load marks button state
                    loadMarksBtn.disabled = !subjectSelect.value;
                })
                .catch(error => {
                    console.error('Error loading subjects:', error);
                });
        }
    });

    function updateFormFields(organizationType) {
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
    }

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

                this.disabled = false;
                this.innerHTML = '<i class="fas fa-download mr-1"></i>Load Marks from Subject';
            })
            .catch(error => {
                console.error('Error loading subject marks:', error);
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
            const calculatedTotal = theoryMarks + practicalMarks;
            if (Math.abs(calculatedTotal - totalMarks) > 0.01) {
                totalMarksInput.setCustomValidity('Theory marks + practical marks must equal total marks');
            } else {
                totalMarksInput.setCustomValidity('');
            }
        } else {
            totalMarksInput.setCustomValidity('');
        }
    }

    // Add validation listeners
    [totalMarksInput, theoryMarksInput, practicalMarksInput].forEach(input => {
        input.addEventListener('input', validateMarks);
    });
});
</script>
@endpush
@endsection
