@extends('layouts.dashboard')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-blue-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-edit text-white text-xl"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Marks Entry</h1>
                        <p class="mt-1 text-sm text-gray-500">Search and select exam for bulk marks entry</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if(session('info'))
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
                {{ session('info') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Search Form -->
        <div class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-green-50 to-blue-50 px-6 py-5 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-search text-green-600 mr-3"></i>
                    Search Exam for Marks Entry
                </h3>
                <p class="mt-1 text-sm text-gray-600">Select course, class, and exam to enter marks</p>
            </div>

            <form method="POST" action="{{ route('marks.search') }}" class="p-8">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Course Selection -->
                    <div>
                        <label for="course_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-graduation-cap text-gray-400 mr-1"></i>
                            Course <span class="text-red-500">*</span>
                        </label>
                        <select name="course_id" 
                                id="course_id" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 @error('course_id') border-red-300 ring-red-200 @enderror"
                                required>
                            <option value="">Select Course</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->title }} ({{ $course->faculty->name ?? 'No Faculty' }})
                                </option>
                            @endforeach
                        </select>
                        @error('course_id')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Class Selection -->
                    <div>
                        <label for="class_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-users text-gray-400 mr-1"></i>
                            Class/Semester <span class="text-red-500">*</span>
                        </label>
                        <select name="class_id" 
                                id="class_id" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 @error('class_id') border-red-300 ring-red-200 @enderror"
                                required disabled>
                            <option value="">Select Class</option>
                        </select>
                        @error('class_id')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Select course first</p>
                    </div>

                    <!-- Exam Selection -->
                    <div>
                        <label for="exam_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-clipboard-list text-gray-400 mr-1"></i>
                            Exam <span class="text-red-500">*</span>
                        </label>
                        <select name="exam_id" 
                                id="exam_id" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 @error('exam_id') border-red-300 ring-red-200 @enderror"
                                required disabled>
                            <option value="">Select Exam</option>
                        </select>
                        @error('exam_id')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Select class first</p>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-8 flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-green-500 to-blue-600 text-white text-sm font-semibold rounded-lg hover:from-green-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                            id="search-btn" disabled>
                        <i class="fas fa-search mr-2"></i>
                        Search & Enter Marks
                    </button>
                </div>
            </form>
        </div>

        <!-- Instructions -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h4 class="text-lg font-medium text-blue-900 mb-3 flex items-center">
                <i class="fas fa-info-circle mr-2"></i>
                Instructions
            </h4>
            <div class="text-sm text-blue-800 space-y-2">
                <p>• <strong>Step 1:</strong> Select the course for which you want to enter marks</p>
                <p>• <strong>Step 2:</strong> Choose the specific class/semester within that course</p>
                <p>• <strong>Step 3:</strong> Select the exam for which marks need to be entered</p>
                <p>• <strong>Step 4:</strong> Click "Search & Enter Marks" to proceed to the marks entry form</p>
                <p class="mt-3 text-blue-700">
                    <i class="fas fa-lightbulb mr-1"></i>
                    <strong>Note:</strong> Only active exams with enrolled students will be available for selection.
                </p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const courseSelect = document.getElementById('course_id');
    const classSelect = document.getElementById('class_id');
    const examSelect = document.getElementById('exam_id');
    const searchBtn = document.getElementById('search-btn');

    // Handle course selection
    courseSelect.addEventListener('change', function() {
        const courseId = this.value;
        
        // Reset dependent selects
        classSelect.innerHTML = '<option value="">Select Class</option>';
        classSelect.disabled = !courseId;
        examSelect.innerHTML = '<option value="">Select Exam</option>';
        examSelect.disabled = true;
        searchBtn.disabled = true;

        if (courseId) {
            // Load classes for selected course
            fetch(`{{ route('marks.classes-by-course') }}?course_id=${courseId}`)
                .then(response => response.json())
                .then(classes => {
                    classes.forEach(classItem => {
                        const option = document.createElement('option');
                        option.value = classItem.id;
                        option.textContent = `${classItem.name} (${classItem.academic_year.name})`;
                        classSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading classes:', error);
                });
        }
    });

    // Handle class selection
    classSelect.addEventListener('change', function() {
        const classId = this.value;
        
        // Reset exam select
        examSelect.innerHTML = '<option value="">Select Exam</option>';
        examSelect.disabled = !classId;
        searchBtn.disabled = true;

        if (classId) {
            // Load exams for selected class
            fetch(`{{ route('marks.exams-by-class') }}?class_id=${classId}`)
                .then(response => response.json())
                .then(exams => {
                    exams.forEach(exam => {
                        const option = document.createElement('option');
                        option.value = exam.id;
                        option.textContent = `${exam.title} (${exam.exam_type})`;
                        examSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading exams:', error);
                });
        }
    });

    // Handle exam selection
    examSelect.addEventListener('change', function() {
        searchBtn.disabled = !this.value;
    });
});
</script>
@endpush
@endsection
