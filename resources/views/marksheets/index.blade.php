@extends('layouts.dashboard')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-file-pdf text-white text-xl"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Generate Marksheets</h1>
                        <p class="mt-1 text-sm text-gray-500">Generate and download PDF marksheets for students</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Form -->
        <div class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 px-6 py-5 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-search text-purple-600 mr-3"></i>
                    Select Exam for Marksheet Generation
                </h3>
                <p class="mt-1 text-sm text-gray-600">Choose an exam to generate marksheets for students</p>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Exam Selection -->
                    <div>
                        <label for="exam_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-clipboard-list text-gray-400 mr-1"></i>
                            Select Exam <span class="text-red-500">*</span>
                        </label>
                        <select id="exam_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                                required>
                            <option value="">Select Exam</option>
                            @if($exams->count() > 0)
                                @foreach($exams as $exam)
                                    <option value="{{ $exam->id }}"
                                            data-course="{{ $exam->class->course->title }}"
                                            data-class="{{ $exam->class->name }}"
                                            data-year="{{ $exam->academicYear->name }}">
                                        {{ $exam->title }} - {{ $exam->class->course->title }} ({{ $exam->class->name }})
                                    </option>
                                @endforeach
                            @else
                                <option value="" disabled>No completed exams available</option>
                            @endif
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Only completed exams with marks are shown</p>
                    </div>

                    <!-- Student Selection -->
                    <div>
                        <label for="student_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user-graduate text-gray-400 mr-1"></i>
                            Select Student <span class="text-red-500">*</span>
                        </label>
                        <select id="student_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200"
                                required disabled>
                            <option value="">Select Student</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Select exam first</p>
                    </div>
                </div>

                <!-- Exam Info Display -->
                <div id="exam-info" class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg hidden">
                    <h4 class="text-sm font-semibold text-blue-900 mb-2">Selected Exam Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-blue-800">
                        <div>
                            <span class="font-medium">Course:</span>
                            <span id="exam-course">-</span>
                        </div>
                        <div>
                            <span class="font-medium">Class:</span>
                            <span id="exam-class">-</span>
                        </div>
                        <div>
                            <span class="font-medium">Academic Year:</span>
                            <span id="exam-year">-</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-8 space-y-4">
                    <!-- Individual Student Actions -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button type="button"
                                id="preview-btn"
                                class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white text-sm font-semibold rounded-lg hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                                disabled>
                            <i class="fas fa-eye mr-2"></i>
                            Preview Marksheet
                        </button>

                        <button type="button"
                                id="download-btn"
                                class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-500 to-teal-600 text-white text-sm font-semibold rounded-lg hover:from-green-600 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                                disabled>
                            <i class="fas fa-download mr-2"></i>
                            Download PDF
                        </button>
                    </div>

                    <!-- Class-wide Actions -->
                    <div class="border-t border-gray-200 pt-4">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Class-wide Actions</h4>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <button type="button"
                                    id="class-preview-btn"
                                    class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white text-sm font-semibold rounded-lg hover:from-indigo-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                                    disabled>
                                <i class="fas fa-users mr-2"></i>
                                Preview Class Students
                            </button>

                            <button type="button"
                                    id="bulk-btn"
                                    class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-orange-500 to-red-600 text-white text-sm font-semibold rounded-lg hover:from-orange-600 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                                    disabled>
                                <i class="fas fa-file-pdf mr-2"></i>
                                Bulk Generate PDFs
                            </button>

                            <button type="button"
                                    id="results-btn"
                                    class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-600 text-white text-sm font-semibold rounded-lg hover:from-purple-600 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all duration-200 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                                    disabled>
                                <i class="fas fa-chart-line mr-2"></i>
                                View Results & Analytics
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="mt-6 bg-purple-50 border border-purple-200 rounded-lg p-6">
            <h4 class="text-lg font-medium text-purple-900 mb-3 flex items-center">
                <i class="fas fa-info-circle mr-2"></i>
                Instructions
            </h4>
            <div class="text-sm text-purple-800 space-y-2">
                <p>• <strong>Individual Marksheets:</strong> Select exam → Choose student → Preview/Download</p>
                <p>• <strong>Class Preview:</strong> Select exam → Click "Preview Class Students" to see all students</p>
                <p>• <strong>Bulk Generation:</strong> Select exam → Click "Bulk Generate PDFs" for all students</p>
                <p>• <strong>Results Analytics:</strong> Select exam → Click "View Results & Analytics" for comprehensive analysis</p>
                <p class="mt-3 text-purple-700">
                    <i class="fas fa-lightbulb mr-1"></i>
                    <strong>Note:</strong> Only students with entered marks will appear in the lists.
                </p>
            </div>
        </div>

        @if($exams->count() == 0)
        <!-- No Exams Available -->
        <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-6">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                    <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                </div>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No Completed Exams</h3>
                <p class="mt-1 text-sm text-gray-500">
                    No completed exams with marks found. Complete some exams and enter marks to generate marksheets.
                </p>
                <div class="mt-6">
                    <a href="{{ route('exams.index') }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        <i class="fas fa-plus mr-2"></i>
                        View Exams
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const examSelect = document.getElementById('exam_id');
    const studentSelect = document.getElementById('student_id');
    const previewBtn = document.getElementById('preview-btn');
    const downloadBtn = document.getElementById('download-btn');
    const bulkBtn = document.getElementById('bulk-btn');
    const classPreviewBtn = document.getElementById('class-preview-btn');
    const resultsBtn = document.getElementById('results-btn');
    const examInfo = document.getElementById('exam-info');

    // Handle exam selection
    examSelect.addEventListener('change', function() {
        const examId = this.value;
        const selectedOption = this.options[this.selectedIndex];

        // Reset student select
        studentSelect.innerHTML = '<option value="">Select Student</option>';
        studentSelect.disabled = !examId;
        previewBtn.disabled = true;
        downloadBtn.disabled = true;
        bulkBtn.disabled = !examId;
        classPreviewBtn.disabled = !examId;
        resultsBtn.disabled = !examId;

        if (examId) {
            // Show exam info
            document.getElementById('exam-course').textContent = selectedOption.dataset.course || '-';
            document.getElementById('exam-class').textContent = selectedOption.dataset.class || '-';
            document.getElementById('exam-year').textContent = selectedOption.dataset.year || '-';
            examInfo.classList.remove('hidden');

            // Load students for selected exam
            fetch(`{{ route('marksheets.students-by-exam') }}?exam_id=${examId}`)
                .then(response => response.json())
                .then(students => {
                    if (students.length > 0) {
                        students.forEach(student => {
                            const option = document.createElement('option');
                            option.value = student.id;
                            option.textContent = `${student.user.first_name} ${student.user.last_name} (${student.student_id})`;
                            studentSelect.appendChild(option);
                        });
                    } else {
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = 'No students with marks found';
                        option.disabled = true;
                        studentSelect.appendChild(option);
                    }
                })
                .catch(error => {
                    console.error('Error loading students:', error);
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'Error loading students';
                    option.disabled = true;
                    studentSelect.appendChild(option);
                });
        } else {
            examInfo.classList.add('hidden');
        }
    });

    // Handle student selection
    studentSelect.addEventListener('change', function() {
        const hasSelection = examSelect.value && this.value;
        previewBtn.disabled = !hasSelection;
        downloadBtn.disabled = !hasSelection;
    });

    // Handle preview button
    previewBtn.addEventListener('click', function() {
        const examId = examSelect.value;
        const studentId = studentSelect.value;
        
        if (examId && studentId) {
            const url = `{{ route('marksheets.generate', ['exam' => ':exam', 'student' => ':student']) }}`
                .replace(':exam', examId)
                .replace(':student', studentId);
            window.open(url, '_blank');
        }
    });

    // Handle download button
    downloadBtn.addEventListener('click', function() {
        const examId = examSelect.value;
        const studentId = studentSelect.value;
        
        if (examId && studentId) {
            const url = `{{ route('marksheets.generate-pdf', ['exam' => ':exam', 'student' => ':student']) }}`
                .replace(':exam', examId)
                .replace(':student', studentId);
            window.location.href = url;
        }
    });

    // Handle bulk button
    bulkBtn.addEventListener('click', function() {
        const examId = examSelect.value;

        if (examId) {
            const url = `{{ route('marksheets.bulk', ['exam' => ':exam']) }}`
                .replace(':exam', examId);
            window.location.href = url;
        }
    });

    // Handle class preview button
    classPreviewBtn.addEventListener('click', function() {
        const examId = examSelect.value;

        if (examId) {
            const url = `{{ route('marksheets.bulk', ['exam' => ':exam']) }}`
                .replace(':exam', examId);
            window.open(url, '_blank');
        }
    });

    // Handle results button
    resultsBtn.addEventListener('click', function() {
        const examId = examSelect.value;

        if (examId) {
            const url = `{{ route('results.generate', ['exam' => ':exam']) }}`
                .replace(':exam', examId);
            window.open(url, '_blank');
        }
    });
});
</script>
@endpush
@endsection
