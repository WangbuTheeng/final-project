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
                            <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-white text-xl"></i>
                            </div>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $exam->title }} - Class Students</h1>
                            <p class="mt-1 text-sm text-gray-500">Preview and manage marksheets for all students</p>
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
                        <a href="{{ route('results.generate', $exam) }}"
                           class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all duration-200">
                            <i class="fas fa-chart-line mr-2"></i>
                            View Results
                        </a>
                        <a href="{{ route('marksheets.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg font-medium text-sm text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Marksheets
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-blue-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Students</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $studentsWithMarks->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file-pdf text-green-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Marksheets Ready</p>
                            <p class="text-2xl font-bold text-green-600">{{ $studentsWithMarks->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-clipboard-list text-purple-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Exam Type</p>
                            <p class="text-lg font-bold text-purple-600">{{ ucfirst($exam->exam_type) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-calendar text-yellow-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Exam Date</p>
                            <p class="text-sm font-bold text-yellow-600">{{ $exam->exam_date->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Bulk Actions</h3>
                <p class="text-sm text-gray-500">Perform actions on multiple students</p>
            </div>
            <div class="px-6 py-4">
                <div class="flex flex-col sm:flex-row gap-4">
                    <button type="button"
                            id="select-all-btn"
                            class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg font-medium text-sm text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                        <i class="fas fa-check-square mr-2"></i>
                        Select All
                    </button>
                    
                    <button type="button"
                            id="bulk-download-btn"
                            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                            disabled>
                        <i class="fas fa-download mr-2"></i>
                        Download Selected PDFs
                    </button>

                    <button type="button"
                            id="bulk-download-all-btn"
                            class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all duration-200">
                        <i class="fas fa-file-archive mr-2"></i>
                        Download All PDFs (ZIP)
                    </button>
                </div>
            </div>
        </div>

        <!-- Students List -->
        <div class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 px-6 py-5 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-list text-purple-600 mr-3"></i>
                    Students with Marksheets
                </h3>
                <p class="mt-1 text-sm text-gray-600">Select students to generate marksheets for</p>
            </div>

            @if($studentsWithMarks->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" id="select-all-checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Student
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Student ID
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Marks Status
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($studentsWithMarks as $student)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" 
                                               class="student-checkbox rounded border-gray-300 text-purple-600 focus:ring-purple-500" 
                                               value="{{ $student->id }}"
                                               data-student-name="{{ $student->user->first_name }} {{ $student->user->last_name }}">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-white">
                                                        {{ substr($student->user->first_name, 0, 1) }}{{ substr($student->user->last_name, 0, 1) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $student->user->first_name }} {{ $student->user->last_name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $student->user->email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="text-sm font-medium text-gray-900">{{ $student->student_id }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Marks Available
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('marksheets.generate', [$exam, $student]) }}"
                                               class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-xs font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200"
                                               title="Preview Marksheet"
                                               target="_blank">
                                                <i class="fas fa-eye mr-1"></i>
                                                Preview
                                            </a>
                                            <a href="{{ route('marksheets.generate-pdf', [$exam, $student]) }}"
                                               class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-xs font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200"
                                               title="Download PDF">
                                                <i class="fas fa-download mr-1"></i>
                                                PDF
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <!-- No Students -->
                <div class="text-center py-12">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100">
                        <i class="fas fa-users text-gray-400 text-xl"></i>
                    </div>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No Students Found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        No students with marks found for this exam.
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('marks.index') }}"
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            <i class="fas fa-edit mr-2"></i>
                            Enter Marks
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Instructions -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h4 class="text-lg font-medium text-blue-900 mb-3 flex items-center">
                <i class="fas fa-info-circle mr-2"></i>
                Bulk Operations Guide
            </h4>
            <div class="text-sm text-blue-800 space-y-2">
                <p>• <strong>Individual Actions:</strong> Use "Preview" to view marksheet or "PDF" to download individual marksheets</p>
                <p>• <strong>Select Multiple:</strong> Check boxes next to students and use "Download Selected PDFs"</p>
                <p>• <strong>Download All:</strong> Use "Download All PDFs (ZIP)" to get all marksheets in a single ZIP file</p>
                <p>• <strong>Results Analysis:</strong> Click "View Results" for comprehensive class performance analysis</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    const studentCheckboxes = document.querySelectorAll('.student-checkbox');
    const selectAllBtn = document.getElementById('select-all-btn');
    const bulkDownloadBtn = document.getElementById('bulk-download-btn');
    const bulkDownloadAllBtn = document.getElementById('bulk-download-all-btn');

    // Handle select all checkbox
    selectAllCheckbox.addEventListener('change', function() {
        studentCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkDownloadButton();
    });

    // Handle select all button
    selectAllBtn.addEventListener('click', function() {
        const allChecked = Array.from(studentCheckboxes).every(cb => cb.checked);
        studentCheckboxes.forEach(checkbox => {
            checkbox.checked = !allChecked;
        });
        selectAllCheckbox.checked = !allChecked;
        updateBulkDownloadButton();
    });

    // Handle individual checkboxes
    studentCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAllCheckbox();
            updateBulkDownloadButton();
        });
    });

    // Update select all checkbox state
    function updateSelectAllCheckbox() {
        const checkedCount = Array.from(studentCheckboxes).filter(cb => cb.checked).length;
        selectAllCheckbox.checked = checkedCount === studentCheckboxes.length;
        selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < studentCheckboxes.length;
    }

    // Update bulk download button state
    function updateBulkDownloadButton() {
        const checkedCount = Array.from(studentCheckboxes).filter(cb => cb.checked).length;
        bulkDownloadBtn.disabled = checkedCount === 0;
        
        if (checkedCount > 0) {
            bulkDownloadBtn.innerHTML = `<i class="fas fa-download mr-2"></i>Download ${checkedCount} Selected PDFs`;
        } else {
            bulkDownloadBtn.innerHTML = `<i class="fas fa-download mr-2"></i>Download Selected PDFs`;
        }
    }

    // Handle bulk download selected
    bulkDownloadBtn.addEventListener('click', function() {
        const selectedStudents = Array.from(studentCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);
        
        if (selectedStudents.length > 0) {
            // Create a form to submit selected student IDs
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("results.bulk-generate", $exam) }}';
            
            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Add selected student IDs
            selectedStudents.forEach(studentId => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'student_ids[]';
                input.value = studentId;
                form.appendChild(input);
            });
            
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }
    });

    // Handle download all button
    bulkDownloadAllBtn.addEventListener('click', function() {
        const allStudentIds = Array.from(studentCheckboxes).map(cb => cb.value);
        
        // Create a form to submit all student IDs
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("results.bulk-generate", $exam) }}';
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Add all student IDs
        allStudentIds.forEach(studentId => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'student_ids[]';
            input.value = studentId;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    });
});
</script>
@endpush
@endsection
