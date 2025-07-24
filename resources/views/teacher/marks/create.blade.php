@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Mark Entry</h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ $exam->title }} - {{ $subject->name }}
            </p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
            <a href="{{ route('teacher.marks.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Exam Information -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Exam Information</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Exam Details</h4>
                    <div class="mt-2">
                        <p class="text-lg font-semibold text-gray-900">{{ $exam->title }}</p>
                        <p class="text-sm text-gray-600">{{ $exam->examType ? $exam->examType->name : $exam->getExamTypeLabel() }}</p>
                        <p class="text-sm text-gray-500">{{ $exam->exam_date->format('M d, Y') }}</p>
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Class & Subject</h4>
                    <div class="mt-2">
                        <p class="text-sm font-medium text-gray-900">{{ $exam->class->name ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-600">{{ $subject->name }} ({{ $subject->code }})</p>
                        <p class="text-sm text-gray-500">{{ $exam->class->course->title ?? 'N/A' }}</p>
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Students</h4>
                    <div class="mt-2">
                        <p class="text-2xl font-bold text-blue-600">{{ $enrollments->count() }}</p>
                        <p class="text-sm text-gray-500">Total Enrolled</p>
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Progress</h4>
                    <div class="mt-2">
                        <p class="text-2xl font-bold text-green-600">{{ $existingMarks->count() }}</p>
                        <p class="text-sm text-gray-500">Marks Entered</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mark Entry Form -->
    <form method="POST" action="{{ route('teacher.marks.store') }}" id="markEntryForm">
        @csrf
        <input type="hidden" name="exam_id" value="{{ $exam->id }}">
        <input type="hidden" name="subject_id" value="{{ $subject->id }}">

        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Student Marks</h3>
                    <div class="flex items-center space-x-2">
                        <button type="submit" name="save_draft" value="1"
                                class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-save mr-2"></i>
                            Save Draft
                        </button>
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-check mr-2"></i>
                            Submit Marks
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                @if($exam->isInternal() && $exam->examType && $exam->examType->code === 'IA_BACH')
                    <!-- Component-wise Mark Entry for Bachelor's Internal Assessment -->
                    @include('teacher.marks.partials.component-marks')
                @else
                    <!-- Regular Mark Entry -->
                    @include('teacher.marks.partials.regular-marks')
                @endif
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-calculate totals for component marks
    if (document.querySelector('.component-marks-table')) {
        const componentInputs = document.querySelectorAll('.component-mark-input');
        componentInputs.forEach(input => {
            input.addEventListener('input', function() {
                calculateStudentTotal(this.dataset.studentId);
            });
        });
    }

    // Auto-calculate totals for regular marks
    if (document.querySelector('.regular-marks-table')) {
        const theoryInputs = document.querySelectorAll('.theory-mark-input');
        const practicalInputs = document.querySelectorAll('.practical-mark-input');
        
        [...theoryInputs, ...practicalInputs].forEach(input => {
            input.addEventListener('input', function() {
                calculateRegularTotal(this.dataset.studentId);
            });
        });
    }

    function calculateStudentTotal(studentId) {
        const componentInputs = document.querySelectorAll(`[data-student-id="${studentId}"].component-mark-input`);
        let total = 0;
        
        componentInputs.forEach(input => {
            const value = parseFloat(input.value) || 0;
            total += value;
        });
        
        const totalDisplay = document.querySelector(`#total-${studentId}`);
        if (totalDisplay) {
            totalDisplay.textContent = total.toFixed(1);
        }
    }

    function calculateRegularTotal(studentId) {
        const theoryInput = document.querySelector(`[data-student-id="${studentId}"].theory-mark-input`);
        const practicalInput = document.querySelector(`[data-student-id="${studentId}"].practical-mark-input`);
        const totalInput = document.querySelector(`[data-student-id="${studentId}"].total-mark-input`);
        
        const theory = parseFloat(theoryInput?.value) || 0;
        const practical = parseFloat(practicalInput?.value) || 0;
        const total = theory + practical;
        
        if (totalInput) {
            totalInput.value = total;
        }
    }

    // Form validation
    document.getElementById('markEntryForm').addEventListener('submit', function(e) {
        const hasMarks = Array.from(document.querySelectorAll('input[type="number"]')).some(input => {
            return parseFloat(input.value) > 0;
        });

        if (!hasMarks) {
            e.preventDefault();
            alert('Please enter marks for at least one student before submitting.');
            return false;
        }

        return confirm('Are you sure you want to submit these marks? This action cannot be undone.');
    });
});
</script>
@endpush
@endsection
